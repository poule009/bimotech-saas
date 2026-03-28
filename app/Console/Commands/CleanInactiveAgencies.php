<?php

namespace App\Console\Commands;

use App\Models\Agency;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanInactiveAgencies extends Command
{
    protected $signature   = 'agencies:clean-inactive';
    protected $description = 'Supprime les agences en essai inactives depuis plus de 60 jours sans avoir jamais souscrit';

    public function handle(): void
    {
        $this->info('Recherche des agences inactives...');

        // Critères de suppression :
        // 1. Subscription en statut "expiré" (essai terminé)
        // 2. Jamais eu d'abonnement payé (aucun paiement dans subscription_payments)
        // 3. Essai expiré depuis plus de 60 jours
        $subscriptionsASupprimer = Subscription::where('statut', 'expiré')
            ->whereNull('plan') // jamais souscrit à un plan payant
            ->where('date_fin_essai', '<', now()->subDays(60))
            ->whereDoesntHave('payments') // aucun paiement enregistré
            ->with('agency')
            ->get();

        if ($subscriptionsASupprimer->isEmpty()) {
            $this->info('Aucune agence inactive trouvée.');
            return;
        }

        $this->info("{$subscriptionsASupprimer->count()} agence(s) inactive(s) trouvée(s).");

        $supprimees  = 0;
        $erreurs     = 0;

        foreach ($subscriptionsASupprimer as $subscription) {
            $agency = $subscription->agency;

            if (! $agency) {
                continue;
            }

            // Sécurité : ne jamais supprimer une agence qui a des paiements
            if ($subscription->payments()->count() > 0) {
                $this->warn("⚠️  {$agency->name} ignorée — paiements détectés.");
                continue;
            }

            $this->line("→ Suppression de : {$agency->name} (essai expiré le {$subscription->date_fin_essai?->format('d/m/Y')})");

            try {
                DB::transaction(function () use ($agency) {
                    // Suppression en cascade grâce aux foreign keys avec onDelete('cascade')
                    // L'ordre est important pour éviter les contraintes
                    $agency->delete();
                });

                Log::info("Agence supprimée automatiquement : {$agency->name} (ID: {$agency->id})");
                $supprimees++;

            } catch (\Exception $e) {
                $this->error("❌ Erreur pour {$agency->name} : {$e->getMessage()}");
                Log::error("Erreur suppression agence {$agency->id} : {$e->getMessage()}");
                $erreurs++;
            }
        }

        $this->newLine();
        $this->info('════════════════════════════════════════');
        $this->info("  Nettoyage terminé");
        $this->info('════════════════════════════════════════');
        $this->info("  ✅ Agences supprimées : {$supprimees}");
        if ($erreurs > 0) {
            $this->warn("  ⚠️  Erreurs            : {$erreurs}");
        }
        $this->info('════════════════════════════════════════');
    }
}