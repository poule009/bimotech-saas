<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            // Chemin vers l'image de signature/tampon de l'agence
            // Affichée automatiquement sur les quittances PDF
            $table->string('signature_path')->nullable()->after('logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn('signature_path');
        });
    }
};
