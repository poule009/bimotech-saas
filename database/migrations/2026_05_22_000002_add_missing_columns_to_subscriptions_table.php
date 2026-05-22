<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (! Schema::hasColumn('subscriptions', 'reference_paytech')) {
                $table->string('reference_paytech')->nullable()->after('date_fin_abonnement');
            }
            if (! Schema::hasColumn('subscriptions', 'rappel_7j_envoye')) {
                $table->boolean('rappel_7j_envoye')->default(false)->after('reference_paytech');
            }
            if (! Schema::hasColumn('subscriptions', 'rappel_1j_envoye')) {
                $table->boolean('rappel_1j_envoye')->default(false)->after('rappel_7j_envoye');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('subscriptions', 'reference_paytech') ? 'reference_paytech' : null,
                Schema::hasColumn('subscriptions', 'rappel_7j_envoye')  ? 'rappel_7j_envoye'  : null,
                Schema::hasColumn('subscriptions', 'rappel_1j_envoye')  ? 'rappel_1j_envoye'  : null,
            ]));
        });
    }
};
