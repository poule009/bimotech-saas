<?php

namespace App\Console\Commands;

use App\Models\TvaDeclaration;
use App\Models\User;
use App\Notifications\BrsTrimestrielReminderNotification;
use App\Notifications\DGIDReminderNotification;
use App\Notifications\TvaMensuelleReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * SendDGIDReminders — Envoie des rappels aux propriétaires et admins pour les échéances DGID.
 *
 * Échéances fiscales sénégalaises gérées :
 *  - BRS annuel (état récap) : 31 janvier  (CGI art. 200 §5 — état nominatif des retenues N)
 *  - IRPP : 1er mars    (déclaration revenus locatifs — gouv.sn, IR-04)
 *  - CFPB : 31 janvier  (déclaration contribution foncière — CFPB-05)
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

    // Échéances fixes (mois-jour).
    // 'destinataire' : 'proprietaire' (défaut) | 'admin'
    // 'rappels'      : jours avant échéance (défaut : [30, 7])
    // 'formes_juridiques' : filtre sur agency.forme_juridique (null = toutes)
    private const ECHEANCES = [
        'brs_annuel'   => ['mois' => 1, 'jour' => 31],                                                                                            // proprietaires — CGI art. 200 §5
        'cel_vl'       => ['mois' => 1, 'jour' => 31, 'destinataire' => 'admin'],                                                                 // admins — Art. 320-338 CGI SN
        'is_acompte_1' => ['mois' => 2, 'jour' => 15, 'rappels' => [15, 7], 'destinataire' => 'admin', 'formes_juridiques' => ['sarl','sa','sas']], // Art. 36-37 CGI SN
        'irpp'         => ['mois' => 3, 'jour' => 1, 'proprietaire_particulier' => true],                                                          // proprietaires PARTICULIERS — 1er mars (gouv.sn), IR-04
        'cel_va'       => ['mois' => 4, 'jour' => 30, 'destinataire' => 'admin'],                                                                 // admins — Art. 320-338 CGI SN
        'is_acompte_2' => ['mois' => 4, 'jour' => 30, 'rappels' => [15, 7], 'destinataire' => 'admin', 'formes_juridiques' => ['sarl','sa','sas']], // Art. 36-37 CGI SN
        'is_solde'     => ['mois' => 6, 'jour' => 15, 'rappels' => [15, 7], 'destinataire' => 'admin', 'formes_juridiques' => ['sarl','sa','sas']], // Art. 37 CGI SN (IMF)
        'cfpb'         => ['mois' => 1, 'jour' => 31],                                                                                            // proprietaires — déclaration 31 janvier (CFPB-05)
    ];

    // Rappels par défaut (jours avant l'échéance) — surchargeable par entrée
    private const JOURS_AVANT_RAPPEL = [30, 7];

    public function handle(): int
    {
        if (! config('features.fiscalite')) {
            $this->line('Module fiscal désactivé — aucun rappel envoyé.');
            return self::SUCCESS;
        }

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
            $rappels       = $echeance['rappels'] ?? self::JOURS_AVANT_RAPPEL;

            if (! in_array($joursRestants, $rappels)) {
                $this->line("  [{$type}] J-{$joursRestants} — pas de rappel aujourd'hui");
                continue;
            }

            $this->line("  [{$type}] J-{$joursRestants} — envoi des rappels...");

            $destinataire     = $echeance['destinataire']     ?? 'proprietaire';
            $formesJuridiques = $echeance['formes_juridiques'] ?? null;

            if ($destinataire === 'admin') {
                $users = User::where('role', 'admin')
                    ->whereNotNull('email')
                    ->whereHas('agency', function ($q) use ($formesJuridiques) {
                        $q->where('actif', true);
                        if ($formesJuridiques !== null) {
                            $q->whereIn('forme_juridique', $formesJuridiques);
                        }
                    })
                    ->get();
            } else {
                $query = User::where('role', 'proprietaire')
                    ->whereNotNull('email')
                    ->whereHas('agency', fn($q) => $q->where('actif', true));

                // IRPP : uniquement les propriétaires PARTICULIERS (les personnes
                // morales relèvent de l'IS, pas de l'IRPP). Les autres échéances
                // propriétaire (CFPB, BRS annuel) concernent tous les bailleurs.
                // Défaut = particulier : on n'exclut QUE les personnes morales IS
                // explicites (profil absent ou est_personne_morale_is false/null → inclus).
                if ($echeance['proprietaire_particulier'] ?? false) {
                    $query->where(fn ($q) => $q
                        ->whereDoesntHave('proprietaire')
                        ->orWhereHas('proprietaire', fn ($p) => $p
                            ->where('est_personne_morale_is', false)
                            ->orWhereNull('est_personne_morale_is')));
                }

                $users = $query->get();
            }

            foreach ($users as $user) {
                try {
                    $user->notify(new DGIDReminderNotification(
                        typeEcheance:  $type,
                        dateEcheance:  $dateEcheance->translatedFormat('d F Y'),
                        joursRestants: $joursRestants,
                        annee:         $annee - 1,
                    ));
                    $envoyes++;
                } catch (\Throwable $e) {
                    $this->warn("    ⚠ #{$user->id} : {$e->getMessage()}");
                    Log::warning('Rappel DGID non envoyé', [
                        'user_id' => $user->id,
                        'type'    => $type,
                        'error'   => $e->getMessage(),
                    ]);
                }
            }

            $this->line("    ✅ {$users->count()} rappel(s) envoyé(s)");
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

        // ── Rappel TVA mensuelle (J-3 avant le 15 du mois) — admins agence ──
        $this->newLine();
        $this->line('Vérification du rappel TVA mensuelle...');

        $dateEcheanceTva = Carbon::create($annee, $aujourd_hui->month, 15)
            ->timezone('Africa/Dakar')
            ->endOfDay();

        $joursAvantTva = (int) $aujourd_hui->diffInDays($dateEcheanceTva, false);

        if ($joursAvantTva === 3) {
            // On déclare le mois précédent
            $moisDeclaration  = $aujourd_hui->month === 1 ? 12 : $aujourd_hui->month - 1;
            $anneeDeclaration = $aujourd_hui->month === 1 ? $annee - 1 : $annee;

            $admins = User::where('role', 'admin')
                ->whereNotNull('email')
                ->whereHas('agency', fn ($q) => $q->where('actif', true))
                ->get();

            $envoyesTva = 0;

            foreach ($admins as $admin) {
                $declaration = TvaDeclaration::where('agency_id', $admin->agency_id)
                    ->where('mois', $moisDeclaration)
                    ->where('annee', $anneeDeclaration)
                    ->where('statut', '!=', 'deposee')
                    ->first();

                if (! $declaration) {
                    continue; // Pas de déclaration en attente pour cette agence
                }

                try {
                    $admin->notify(new TvaMensuelleReminderNotification(
                        moisDeclaration:  $moisDeclaration,
                        anneeDeclaration: $anneeDeclaration,
                        dateEcheance:     $dateEcheanceTva->translatedFormat('d F Y'),
                        tvaNetteDue:      (float) $declaration->tva_nette_due,
                    ));
                    $envoyes++;
                    $envoyesTva++;
                } catch (\Throwable $e) {
                    $this->warn("    ⚠ Admin #{$admin->id} : {$e->getMessage()}");
                    Log::warning('Rappel TVA mensuelle non envoyé', [
                        'admin_id'  => $admin->id,
                        'mois'      => $moisDeclaration,
                        'annee'     => $anneeDeclaration,
                        'error'     => $e->getMessage(),
                    ]);
                }
            }

            $this->line("    ✅ {$envoyesTva} rappel(s) TVA mensuelle J-3 envoyé(s)");
        } else {
            $this->line("  [tva_mensuelle] J-{$joursAvantTva} avant le 15 — pas de rappel aujourd'hui");
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
