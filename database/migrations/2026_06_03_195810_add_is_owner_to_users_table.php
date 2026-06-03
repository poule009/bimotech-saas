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
            $table->boolean('is_owner')->default(false)->after('role');
        });

        // Rétrocompatibilité : marquer owner=true sur le premier admin de chaque agence
        \Illuminate\Support\Facades\DB::statement("
            UPDATE users u
            INNER JOIN (
                SELECT MIN(id) AS id
                FROM users
                WHERE role = 'admin'
                  AND deleted_at IS NULL
                GROUP BY agency_id
            ) oldest ON u.id = oldest.id
            SET u.is_owner = 1
        ");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_owner');
        });
    }
};
