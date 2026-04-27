<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bilans_fiscaux_proprietaires', function (Blueprint $table) {
            // Détail IRPP par tranche — évite de recalculer en Blade
            // Format : [{"min":0,"max":1500000,"taux":0,"assiette":xxx,"impot":0}, ...]
            $table->json('irpp_detail')->nullable()->after('irpp_estime');
        });
    }

    public function down(): void
    {
        Schema::table('bilans_fiscaux_proprietaires', function (Blueprint $table) {
            $table->dropColumn('irpp_detail');
        });
    }
};
