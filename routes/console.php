<?php

use Illuminate\Support\Facades\Schedule;

// Vérifie les abonnements tous les jours à 8h00
Schedule::command('subscriptions:reminders')
    ->dailyAt('08:00')
    ->timezone('Africa/Dakar')
    ->withoutOverlapping()
    ->runInBackground();

// Nettoyage des agences inactives tous les lundis à 3h00 du matin
Schedule::command('agencies:clean-inactive')
    ->weeklyOn(1, '03:00')
    ->timezone('Africa/Dakar')
    ->withoutOverlapping()
    ->runInBackground()
    ->emailOutputOnFailure(env('MAIL_FROM_ADDRESS'));