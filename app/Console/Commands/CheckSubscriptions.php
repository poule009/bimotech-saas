<?php

namespace App\Console\Commands;

use App\Models\Agency;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckSubscriptions extends Command
{
    protected $signature = 'app:check-subscriptions';
    protected $description = "Désactive les agences dont l'essai/abonnement est expiré";

    public function handle(): int
    {
        $expired = Subscription::query()
            ->where(function ($q) {
                $q->where(function ($qq) {
                    $qq->where('statut', 'essai')
                        ->whereNotNull('date_fin_essai')
                        ->where('date_fin_essai', '<', now());
                })->orWhere(function ($qq) {
                    $qq->where('statut', 'actif')
                        ->whereNotNull('date_fin_abonnement')
                        ->where('date_fin_abonnement', '<', now());
                });
            })
            ->with('agency')
            ->get();

        $this->info("Abonnements expirés trouvés: {$expired->count()}");

        $updated = 0;
        $errors = 0;

        foreach ($expired as $subscription) {
            try {
                DB::transaction(function () use ($subscription, &$updated) {
                    $agency = $subscription->agency;

                    $subscription->update([
                        'statut' => 'expiré',
                    ]);

                    if ($agency instanceof Agency) {
                        // `actif` est intentionnellement absent de Agency::$fillable
                        // (seul le SuperAdmin peut le modifier).
                        // On passe par assignation directe + save() pour contourner
                        // la protection mass-assignment de façon explicite et sécurisée.
                        $agency->actif = false;
                        $agency->save();
                    }

                    $updated++;
                });
            } catch (\Throwable $e) {
                $errors++;
                Log::error('Erreur check subscriptions', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info("✅ Agences désactivées: {$updated}");
        if ($errors > 0) {
            $this->warn("⚠️ Erreurs: {$errors}");
        }

        return self::SUCCESS;
    }
}
