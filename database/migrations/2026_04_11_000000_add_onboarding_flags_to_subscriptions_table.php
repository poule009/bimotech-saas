<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->boolean('onboarding_j1_envoye')->default(false)->after('rappel_1j_envoye');
            $table->boolean('onboarding_j7_envoye')->default(false)->after('onboarding_j1_envoye');
            $table->boolean('onboarding_j25_envoye')->default(false)->after('onboarding_j7_envoye');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['onboarding_j1_envoye', 'onboarding_j7_envoye', 'onboarding_j25_envoye']);
        });
    }
};
