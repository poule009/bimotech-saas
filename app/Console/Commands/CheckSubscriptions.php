<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckSubscriptions extends Command
{
    protected $signature   = 'app:check-subscriptions';
    protected $description = "Marque 'expiré' les abonnements dont la période de grâce est terminée (sans supprimer ni désactiver l'agence)";

    public function handle(): int
    {
        $grace = Subscription::GRACE_JOURS;

        // On ne marque « expiré » qu'APRÈS la période de grâce (5 j). Pendant la
        // grâce, l'accès reste en lecture seule via etatEffectif()/CheckSubscription.
        //
        // IMPORTANT : on ne met JAMAIS agency.actif = false ici. Ce flag est réservé
        // à une désactivation manuelle par le SuperAdmin. Confondre les deux couperait
        // l'agence entièrement (au lieu de la lecture seule / suspension calculée) et
        // exposerait les données à la purge de agencies:clean-inactive. Le brief exige
        // que les données d'une agence suspendue soient conservées.
        $aExpirer = Subscription::query()
            ->whereIn('statut', ['essai', 'actif'])
            ->where(function ($q) use ($grace) {
                $q->where(function ($qq) use ($grace) {
                    $qq->where('statut', 'essai')
                        ->whereNotNull('date_fin_essai')
                        ->where('date_fin_essai', '<', now()->subDays($grace));
                })->orWhere(function ($qq) use ($grace) {
                    $qq->where('statut', 'actif')
                        ->whereNotNull('date_fin_abonnement')
                        ->where('date_fin_abonnement', '<', now()->subDays($grace));
                });
            })
            ->get();

        $this->info("Abonnements dont la grâce est terminée : {$aExpirer->count()}");

        $updated = 0;
        foreach ($aExpirer as $subscription) {
            try {
                $subscription->marquerExpire(); // statut = 'expiré' (record) — pas de actif=false
                $updated++;
            } catch (\Throwable $e) {
                Log::error('Erreur check subscriptions', [
                    'subscription_id' => $subscription->id,
                    'error'           => $e->getMessage(),
                ]);
            }
        }

        $this->info("✅ Abonnements marqués expirés : {$updated}");

        return self::SUCCESS;
    }
}
