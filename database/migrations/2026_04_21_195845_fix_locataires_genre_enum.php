<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE locataires MODIFY COLUMN genre ENUM('homme','femme','M','F') NULL");
        DB::statement("UPDATE locataires SET genre = 'M' WHERE genre = 'homme'");
        DB::statement("UPDATE locataires SET genre = 'F' WHERE genre = 'femme'");
        DB::statement("ALTER TABLE locataires MODIFY COLUMN genre ENUM('M','F') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE locataires MODIFY COLUMN genre ENUM('homme','femme','M','F') NULL");
        DB::statement("UPDATE locataires SET genre = 'homme' WHERE genre = 'M'");
        DB::statement("UPDATE locataires SET genre = 'femme' WHERE genre = 'F'");
        DB::statement("ALTER TABLE locataires MODIFY COLUMN genre ENUM('homme','femme') NULL");
    }
};
