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
            // Logo version fond sombre (blanc/transparent) pour les PDF à en-tête noir
            $table->string('logo_dark_path')->nullable()->after('logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn('logo_dark_path');
        });
    }
};
