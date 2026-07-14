<?php

use Illuminate\Support\Facades\Schedule;

// Vérifie les rappels abonnements tous les jours à 8h00
Schedule::command('subscriptions:reminders')
    ->dailyAt('08:00')
    ->timezone('Africa/Dakar')
    ->withoutOverlapping()
    ->runInBackground();

// Génération des loyers mensuels (impayés) chaque 1er du mois à 01:00
Schedule::command('rent:generate')
    ->monthlyOn(1, '01:00')
    ->timezone('Africa/Dakar')
    ->withoutOverlapping()
    ->runInBackground()
    ->emailOutputOnFailure(env('MAIL_FROM_ADDRESS'));

// Vérification des abonnements expirés chaque jour à 00:30
Schedule::command('app:check-subscriptions')
    ->dailyAt('00:30')
    ->timezone('Africa/Dakar')
    ->withoutOverlapping()
    ->runInBackground()
    ->emailOutputOnFailure(env('MAIL_FROM_ADDRESS'));

// Snapshot quotidien du MRR (00:45). Le mois courant se rafraîchit chaque jour ;
// les mois passés se figent sur leur dernière valeur (≈ fin de mois) → vraie
// courbe MRR du dashboard Super Admin.
Schedule::command('mrr:snapshot')
    ->dailyAt('00:45')
    ->timezone('Africa/Dakar')
    ->withoutOverlapping()
    ->runInBackground();

 // Rapport hebdomadaire des paiements pour Super Admin (lundi 07:00)
 Schedule::command('app:weekly-payments-report')
     ->weeklyOn(1, '07:00')
     ->timezone('Africa/Dakar')
     ->withoutOverlapping()
     ->runInBackground()
     ->emailOutputOnFailure(env('MAIL_FROM_ADDRESS'));

 // Nettoyage des agences inactives tous les lundis à 3h00 du matin
 Schedule::command('agencies:clean-inactive')
     ->weeklyOn(1, '03:00')
     ->timezone('Africa/Dakar')
     ->withoutOverlapping()
     ->runInBackground()
     ->emailOutputOnFailure(env('MAIL_FROM_ADDRESS'));

// Indexation annuelle des loyers — 1er janvier à 02:00 (après les loyers de minuit)
Schedule::command('loyers:indexation')
    ->yearlyOn(1, 1, '02:00')
    ->timezone('Africa/Dakar')
    ->withoutOverlapping()
    ->runInBackground()
    ->emailOutputOnFailure(env('MAIL_FROM_ADDRESS'));

// Emails d'onboarding essai gratuit — J+1, J+7, J+25 — quotidien à 09:30
Schedule::command('onboarding:emails')
    ->dailyAt('09:30')
    ->timezone('Africa/Dakar')
    ->withoutOverlapping()
    ->runInBackground();

// Rappels échéances fiscales DGID — quotidien à 09:00
if (config('features.fiscalite')) {
    Schedule::command('dgid:reminders')
        ->dailyAt('09:00')
        ->timezone('Africa/Dakar')
        ->withoutOverlapping()
        ->runInBackground();

    // Rappel versement BRS mensuel DGI — avant le 15 du mois (Art. 200 §4 CGI SN)
    Schedule::command('brs:mensuel-reminder')
        ->dailyAt('09:00')
        ->timezone('Africa/Dakar')
        ->withoutOverlapping()
        ->runInBackground();
}
