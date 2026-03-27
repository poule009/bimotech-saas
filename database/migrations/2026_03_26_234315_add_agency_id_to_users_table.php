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
    Schema::table('users', function (Blueprint $table) {
        $table->foreignId('agency_id')
              ->nullable()           // nullable pour ne pas bloquer les données existantes
              ->after('id')
              ->constrained('agencies')
              ->onDelete('cascade');

        // On ajoute aussi le rôle superadmin (propriétaire de la plateforme)
        $table->enum('role', ['superadmin', 'admin', 'proprietaire', 'locataire'])
              ->default('locataire')
              ->change();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['agency_id']);
        $table->dropColumn('agency_id');
    });
}
};
