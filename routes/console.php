<?php

use Illuminate\Support\Facades\Schedule;

// Vérifie les rappels abonnements tous les jours à 8h00
Schedule::command('subscriptions:reminders')
    ->dailyAt('08:00')
    ->timezone('Africa/Dakar')
    ->withoutOverlapping()
    ->runInBackground();

// Génération des loyers mensuels (impayés) chaque 1er du mois à 01:00
Schedule::command('app:generate-monthly-rent')
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
