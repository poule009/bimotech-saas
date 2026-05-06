<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bilans_fiscaux_proprietaires', function (Blueprint $table) {
            $table->boolean('cgf_applicable')->default(false)->after('cfpb_estimee');
            $table->decimal('cgf_montant', 12, 2)->default(0)->after('cgf_applicable');
            $table->decimal('cgf_taux_applique', 5, 2)->default(0)->after('cgf_montant');
            $table->enum('regime_recommande', ['cgf', 'irpp', 'hors_cgf'])->nullable()->after('cgf_taux_applique');
            $table->decimal('economie_potentielle', 12, 2)->default(0)->after('regime_recommande');
        });
    }

    public function down(): void
    {
        Schema::table('bilans_fiscaux_proprietaires', function (Blueprint $table) {
            $table->dropColumn([
                'cgf_applicable', 'cgf_montant', 'cgf_taux_applique',
                'regime_recommande', 'economie_potentielle',
            ]);
        });
    }
};
