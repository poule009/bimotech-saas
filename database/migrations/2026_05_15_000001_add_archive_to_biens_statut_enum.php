<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE biens MODIFY COLUMN statut ENUM('disponible','loue','en_travaux','archive') NOT NULL DEFAULT 'disponible'");
    }

    public function down(): void
    {
        // Remettre les biens archivés à 'disponible' avant de rétrécir l'ENUM
        DB::statement("UPDATE biens SET statut='disponible' WHERE statut='archive'");
        DB::statement("ALTER TABLE biens MODIFY COLUMN statut ENUM('disponible','loue','en_travaux') NOT NULL DEFAULT 'disponible'");
    }
};
