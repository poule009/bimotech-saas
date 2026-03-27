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
    // Biens
    Schema::table('biens', function (Blueprint $table) {
        $table->foreignId('agency_id')->nullable()->after('id')
              ->constrained('agencies')->onDelete('cascade');
    });

    // Contrats
    Schema::table('contrats', function (Blueprint $table) {
        $table->foreignId('agency_id')->nullable()->after('id')
              ->constrained('agencies')->onDelete('cascade');
    });

    // Paiements
    Schema::table('paiements', function (Blueprint $table) {
        $table->foreignId('agency_id')->nullable()->after('id')
              ->constrained('agencies')->onDelete('cascade');
    });
}

public function down(): void
{
    foreach (['paiements', 'contrats', 'biens'] as $table) {
        Schema::table($table, function (Blueprint $blueprint) {
            $blueprint->dropForeign(['agency_id']);
            $blueprint->dropColumn('agency_id');
        });
    }
}
};
