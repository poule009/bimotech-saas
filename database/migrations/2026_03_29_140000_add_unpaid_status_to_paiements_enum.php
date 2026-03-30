<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE paiements
            MODIFY statut ENUM('valide', 'en_attente', 'annule', 'unpaid')
            NOT NULL DEFAULT 'valide'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE paiements
            MODIFY statut ENUM('valide', 'en_attente', 'annule')
            NOT NULL DEFAULT 'valide'
        ");
    }
};
