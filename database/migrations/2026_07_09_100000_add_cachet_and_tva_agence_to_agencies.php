<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            if (! Schema::hasColumn('agencies', 'cachet_path')) {
                $table->string('cachet_path')->nullable()->after('signature_path');
            }
            // TVA propre à l'AGENCE (commissions/frais) — distincte de la TVA du loyer
            // gérée sur chaque propriétaire. taux_tva existe déjà.
            if (! Schema::hasColumn('agencies', 'assujetti_tva')) {
                $table->boolean('assujetti_tva')->default(false)->after('taux_tva');
            }
        });
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            foreach (['cachet_path', 'assujetti_tva'] as $col) {
                if (Schema::hasColumn('agencies', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
