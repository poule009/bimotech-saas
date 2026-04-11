<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\OnboardingNotification;
use Illuminate\Console\Command;

class SendOnboardingEmails extends Command
{
    protected $signature   = 'onboarding:emails';
    protected $description = 'Envoie les emails d\'onboarding automatiques à J+1, J+7 et J+25 de l\'essai';

    public function handle(): void
    {
        $this->info('Envoi des emails d\'onboarding...');

        $subscriptions = Subscription::with('agency.users')
            ->where('statut', 'essai')
            ->whereNotNull('date_debut_essai')
            ->get();

        $sent = ['j1' => 0, 'j7' => 0, 'j25' => 0];

        foreach ($subscriptions as $sub) {
            $admin = $sub->agency->users->where('role', 'admin')->first();
            if (! $admin) continue;

            $joursDepuis = (int) $sub->date_debut_essai->startOfDay()
                ->diffInDays(now()->startOfDay());

            // J+1 ─────────────────────────────────────────────────────────
            if ($joursDepuis >= 1 && ! $sub->onboarding_j1_envoye) {
                try {
                    $admin->notify(new OnboardingNotification($sub->agency, 1));
                    $sub->update(['onboarding_j1_envoye' => true]);
                    $sent['j1']++;
                } catch (\Exception $e) {
                    $this->error("Onboarding J+1 échoué ({$sub->agency->name}) : {$e->getMessage()}");
                }
            }

            // J+7 ─────────────────────────────────────────────────────────
            if ($joursDepuis >= 7 && ! $sub->onboarding_j7_envoye) {
                try {
                    $admin->notify(new OnboardingNotification($sub->agency, 7));
                    $sub->update(['onboarding_j7_envoye' => true]);
                    $sent['j7']++;
                } catch (\Exception $e) {
                    $this->error("Onboarding J+7 échoué ({$sub->agency->name}) : {$e->getMessage()}");
                }
            }

            // J+25 ────────────────────────────────────────────────────────
            if ($joursDepuis >= 25 && ! $sub->onboarding_j25_envoye) {
                try {
                    $admin->notify(new OnboardingNotification($sub->agency, 25));
                    $sub->update(['onboarding_j25_envoye' => true]);
                    $sent['j25']++;
                } catch (\Exception $e) {
                    $this->error("Onboarding J+25 échoué ({$sub->agency->name}) : {$e->getMessage()}");
                }
            }
        }

        $this->info("✓ Emails J+1  envoyés : {$sent['j1']}");
        $this->info("✓ Emails J+7  envoyés : {$sent['j7']}");
        $this->info("✓ Emails J+25 envoyés : {$sent['j25']}");
        $this->info('Terminé.');
    }
}
