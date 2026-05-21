<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->enum('plan_niveau', ['starter', 'pro', 'agence', 'legacy'])
                  ->default('legacy')
                  ->after('plan');
        });

        DB::statement("UPDATE subscriptions SET plan_niveau = 'legacy'");
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('plan_niveau');
        });
    }
};
