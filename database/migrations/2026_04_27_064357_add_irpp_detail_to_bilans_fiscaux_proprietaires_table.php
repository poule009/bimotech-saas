<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bilans_fiscaux_proprietaires') || Schema::hasColumn('bilans_fiscaux_proprietaires', 'irpp_detail')) {
            return;
        }

        Schema::table('bilans_fiscaux_proprietaires', function (Blueprint $table) {
            $table->json('irpp_detail')->nullable()->after('irpp_estime');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('bilans_fiscaux_proprietaires', 'irpp_detail')) {
            return;
        }

        Schema::table('bilans_fiscaux_proprietaires', function (Blueprint $table) {
            $table->dropColumn('irpp_detail');
        });
    }
};
