<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\SubscriptionExpiredNotification;
use App\Notifications\SubscriptionReminderNotification;
use Illuminate\Console\Command;

class SendSubscriptionReminders extends Command
{
    protected $signature   = 'subscriptions:reminders';
    protected $description = 'Envoie les emails de relance pour les abonnements qui expirent bientôt';

    public function handle(): void
    {
        $this->info('Vérification des abonnements en cours...');

        $subscriptions = Subscription::with('agency.users')
            ->whereIn('statut', ['essai', 'actif'])
            ->get();

        $rappel7j = 0;
        $rappel1j = 0;
        $expires  = 0;

        foreach ($subscriptions as $sub) {

            // Détermine la date d'expiration selon le type
            $dateFin = $sub->estEnEssai()
                ? $sub->date_fin_essai
                : $sub->date_fin_abonnement;

            if (! $dateFin) continue;

            // Calcul en jours entiers basé sur la date (pas l'heure)
            $joursRestants = (int) now()->startOfDay()
                ->diffInDays($dateFin->startOfDay(), false);

            // Récupère l'admin de l'agence
            $admin = $sub->agency->users
                ->where('role', 'admin')
                ->first();

            if (! $admin) continue;

            // ── Expiré (0 jours ou moins) ─────────────────────────────────
            if ($joursRestants <= 0) {
                $sub->marquerExpire();
                try {
                    $admin->notify(new SubscriptionExpiredNotification($sub));
                } catch (\Exception $e) {
                    $this->error("Email expiration échoué pour {$sub->agency->name} : {$e->getMessage()}");
                }
                $expires++;
                continue;
            }

            // ── Rappel J-7 (entre 6 et 7 jours restants) ─────────────────
            if ($joursRestants <= 7 && $joursRestants > 1 && ! $sub->rappel_7j_envoye) {
                try {
                    $admin->notify(new SubscriptionReminderNotification($sub, $joursRestants));
                    $sub->update(['rappel_7j_envoye' => true]);
                    $rappel7j++;
                } catch (\Exception $e) {
                    $this->error("Email J-7 échoué pour {$sub->agency->name} : {$e->getMessage()}");
                }
                continue;
            }

            // ── Rappel J-1 (1 jour restant) ───────────────────────────────
            if ($joursRestants === 1 && ! $sub->rappel_1j_envoye) {
                try {
                    $admin->notify(new SubscriptionReminderNotification($sub, 1));
                    $sub->update(['rappel_1j_envoye' => true]);
                    $rappel1j++;
                } catch (\Exception $e) {
                    $this->error("Email J-1 échoué pour {$sub->agency->name} : {$e->getMessage()}");
                }
                continue;
            }
        }

        $this->info("✓ Rappels J-7 envoyés  : {$rappel7j}");
        $this->info("✓ Rappels J-1 envoyés  : {$rappel1j}");
        $this->info("✓ Abonnements expirés  : {$expires}");
        $this->info('Traitement terminé.');
    }
}