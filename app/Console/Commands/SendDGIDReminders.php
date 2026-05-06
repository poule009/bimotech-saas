<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\BrsTrimestrielReminderNotification;
use App\Notifications\DGIDReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * SendDGIDReminders — Envoie des rappels aux propriétaires et admins pour les échéances DGID.
 *
 * Échéances fiscales sénégalaises gérées :
 *  - BRS annuel (état récap) : 31 janvier  (CGI art. 200 §5 — état nominatif des retenues N)
 *  - IRPP : 30 avril    (déclaration revenus locatifs)
 *  - CFPB : 30 septembre (contribution foncière)
 *  - BRS trimestriel : 15 avril, 15 juillet, 15 octobre, 15 janvier N+1 (Art. 200 §5 CGI SN)
 *
 * Rappels annuels envoyés : J-30 et J-7 avant chaque échéance.
 * Rappels trimestriels envoyés : J-7 et J-3 avant chaque échéance, aux admins d'agence.
 * Planifié quotidiennement à 09h00 (Africa/Dakar).
 */
class SendDGIDReminders extends Command
{
    protected $signature   = 'dgid:reminders {--force : Envoie les rappels même si déjà envoyés}';
    protected $description = 'Envoie les rappels d\'échéances fiscales DGID aux propriétaires';

    // Échéances fixes (mois-jour)
    private const ECHEANCES = [
        'brs_annuel' => ['mois' => 1,  'jour' => 31],   // État récap annuel — CGI art. 200 §5
        'irpp'       => ['mois' => 4,  'jour' => 30],
        'cfpb'       => ['mois' => 9,  'jour' => 30],
    ];

    // On envoie à J-30 et J-7
    private const JOURS_AVANT_RAPPEL = [30, 7];

    public function handle(): int
    {
        $aujourd_hui = now()->timezone('Africa/Dakar')->startOfDay();
        $annee       = (int) $aujourd_hui->year;

        $this->info("Vérification des rappels DGID pour le {$aujourd_hui->format('d/m/Y')}");
        $this->newLine();

        $envoyes = 0;

        foreach (self::ECHEANCES as $type => $echeance) {
            $dateEcheance = Carbon::create($annee, $echeance['mois'], $echeance['jour'])
                ->timezone('Africa/Dakar')
                ->endOfDay();

            // Si l'échéance est déjà passée cette année, ignorer
            if ($dateEcheance->lt($aujourd_hui)) {
                $this->line("  [{$type}] Échéance passée ({$dateEcheance->format('d/m/Y')}) — ignorée");
                continue;
            }

            $joursRestants = (int) $aujourd_hui->diffInDays($dateEcheance, false);

            if (! in_array($joursRestants, self::JOURS_AVANT_RAPPEL)) {
                $this->line("  [{$type}] J-{$joursRestants} — pas de rappel aujourd'hui");
                continue;
            }

            $this->line("  [{$type}] J-{$joursRestants} — envoi des rappels...");

            // Envoyer à tous les propriétaires actifs
            $proprietaires = User::where('role', 'proprietaire')
                ->whereNotNull('email')
                ->whereHas('agency', fn($q) => $q->where('actif', true))
                ->get();

            foreach ($proprietaires as $proprio) {
                try {
                    $proprio->notify(new DGIDReminderNotification(
                        typeEcheance:  $type,
                        dateEcheance:  $dateEcheance->translatedFormat('d F Y'),
                        joursRestants: $joursRestants,
                        annee:         $annee - 1,  // On déclare l'année précédente
                    ));
                    $envoyes++;
                } catch (\Throwable $e) {
                    $this->warn("    ⚠ Proprio #{$proprio->id} : {$e->getMessage()}");
                    Log::warning('Rappel DGID non envoyé', [
                        'proprio_id' => $proprio->id,
                        'type'       => $type,
                        'error'      => $e->getMessage(),
                    ]);
                }
            }

            $this->line("    ✅ {$proprietaires->count()} rappel(s) envoyé(s)");
        }

        // ── Rappels trimestriels BRS (J-7 et J-3) — envoyés aux admins d'agence ──
        $this->newLine();
        $this->line('Vérification des rappels BRS trimestriels...');

        foreach ($this->echeancesTrimestrielles($annee) as $echeance) {
            $dateEcheance  = $echeance['date'];
            $joursRestants = (int) $aujourd_hui->diffInDays($dateEcheance, false);

            if (! in_array($joursRestants, [7, 3])) {
                $label = "BRS T{$echeance['trimestre']}/{$echeance['annee_trimestre']}";
                $this->line("  [{$label}] J-{$joursRestants} — pas de rappel aujourd'hui");
                continue;
            }

            $label = "BRS T{$echeance['trimestre']}/{$echeance['annee_trimestre']}";
            $this->line("  [{$label}] J-{$joursRestants} — envoi aux admins...");

            $admins = User::where('role', 'admin')
                ->whereNotNull('email')
                ->whereHas('agency', fn ($q) => $q->where('actif', true))
                ->get();

            foreach ($admins as $admin) {
                try {
                    $admin->notify(new BrsTrimestrielReminderNotification(
                        trimestre:     $echeance['trimestre'],
                        annee:         $echeance['annee_trimestre'],
                        dateEcheance:  $dateEcheance->translatedFormat('d F Y'),
                        joursRestants: $joursRestants,
                    ));
                    $envoyes++;
                } catch (\Throwable $e) {
                    $this->warn("    ⚠ Admin #{$admin->id} : {$e->getMessage()}");
                    Log::warning('Rappel BRS trimestriel non envoyé', [
                        'admin_id'  => $admin->id,
                        'trimestre' => $echeance['trimestre'],
                        'annee'     => $echeance['annee_trimestre'],
                        'error'     => $e->getMessage(),
                    ]);
                }
            }

            $this->line("    ✅ {$admins->count()} rappel(s) envoyé(s)");
        }

        $this->newLine();
        $this->info("Total notifications envoyées : {$envoyes}");

        return self::SUCCESS;
    }

    /**
     * Retourne les 5 échéances trimestrielles pertinentes pour l'année en cours.
     * Inclut T4 de l'année précédente (deadline en janvier) et T4 de l'année courante (deadline en janvier N+1).
     *
     * @return array<int, array{date: Carbon, trimestre: int, annee_trimestre: int}>
     */
    private function echeancesTrimestrielles(int $annee): array
    {
        return [
            // T4 de l'année précédente → deadline le 15 janvier de l'année courante
            [
                'date'            => Carbon::create($annee, 1, 15)->timezone('Africa/Dakar')->endOfDay(),
                'trimestre'       => 4,
                'annee_trimestre' => $annee - 1,
            ],
            // T1 de l'année courante → deadline le 15 avril
            [
                'date'            => Carbon::create($annee, 4, 15)->timezone('Africa/Dakar')->endOfDay(),
                'trimestre'       => 1,
                'annee_trimestre' => $annee,
            ],
            // T2 de l'année courante → deadline le 15 juillet
            [
                'date'            => Carbon::create($annee, 7, 15)->timezone('Africa/Dakar')->endOfDay(),
                'trimestre'       => 2,
                'annee_trimestre' => $annee,
            ],
            // T3 de l'année courante → deadline le 15 octobre
            [
                'date'            => Carbon::create($annee, 10, 15)->timezone('Africa/Dakar')->endOfDay(),
                'trimestre'       => 3,
                'annee_trimestre' => $annee,
            ],
            // T4 de l'année courante → deadline le 15 janvier N+1
            [
                'date'            => Carbon::create($annee + 1, 1, 15)->timezone('Africa/Dakar')->endOfDay(),
                'trimestre'       => 4,
                'annee_trimestre' => $annee,
            ],
        ];
    }
}
