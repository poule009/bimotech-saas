<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->decimal('charges', 10, 2)->nullable()->default(0)->after('loyer_mensuel');
            $table->decimal('caution', 10, 2)->nullable()->after('charges');
        });
    }

    public function down(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->dropColumn(['charges', 'caution']);
        });
    }
};
