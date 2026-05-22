<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Convertir les cycles supprimés avant de modifier l'ENUM
        DB::statement("UPDATE subscriptions        SET plan = 'mensuel' WHERE plan = 'trimestriel'");
        DB::statement("UPDATE subscriptions        SET plan = 'annuel'  WHERE plan = 'semestriel'");
        DB::statement("UPDATE subscription_payments SET plan = 'mensuel' WHERE plan = 'trimestriel'");
        DB::statement("UPDATE subscription_payments SET plan = 'annuel'  WHERE plan = 'semestriel'");

        DB::statement("ALTER TABLE subscriptions         MODIFY COLUMN plan ENUM('mensuel','annuel') NULL");
        DB::statement("ALTER TABLE subscription_payments MODIFY COLUMN plan ENUM('mensuel','annuel') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE subscriptions         MODIFY COLUMN plan ENUM('mensuel','trimestriel','semestriel','annuel') NULL");
        DB::statement("ALTER TABLE subscription_payments MODIFY COLUMN plan ENUM('mensuel','trimestriel','semestriel','annuel') NOT NULL");
    }
};
