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
        // biens : filtrage par proprietaire_id seul (UserController::show, BailleurPortfolioService)
        Schema::table('biens', function (Blueprint $table) {
            $table->index('proprietaire_id', 'biens_proprietaire_idx');
        });

        // users : filtrage par agency_id seul (dashboards, counts)
        Schema::table('users', function (Blueprint $table) {
            if (!$this->indexExists('users', 'users_agency_idx')) {
                $table->index('agency_id', 'users_agency_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->dropIndex('biens_proprietaire_idx');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_agency_idx');
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        return collect(\DB::select("SHOW INDEX FROM `{$table}`"))
            ->pluck('Key_name')
            ->contains($index);
    }
};
