<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\DGIDReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * SendDGIDReminders — Envoie des rappels aux propriétaires pour les échéances DGID.
 *
 * Échéances fiscales sénégalaises gérées :
 *  - BRS  : 31 janvier  (retenue à la source locataires entreprises)
 *  - IRPP : 30 avril    (déclaration revenus locatifs)
 *  - CFPB : 30 septembre (contribution foncière)
 *
 * Rappels envoyés : J-30 et J-7 avant chaque échéance.
 * Planifié quotidiennement à 09h00 (Africa/Dakar).
 */
class SendDGIDReminders extends Command
{
    protected $signature   = 'dgid:reminders {--force : Envoie les rappels même si déjà envoyés}';
    protected $description = 'Envoie les rappels d\'échéances fiscales DGID aux propriétaires';

    // Échéances fixes (mois-jour)
    private const ECHEANCES = [
        'brs'  => ['mois' => 1,  'jour' => 31],
        'irpp' => ['mois' => 4,  'jour' => 30],
        'cfpb' => ['mois' => 9,  'jour' => 30],
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

        $this->newLine();
        $this->info("Total notifications envoyées : {$envoyes}");

        return self::SUCCESS;
    }
}
