<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->enum('forme_juridique', ['sarl', 'sa', 'sas', 'ei', 'autre'])
                ->default('sarl')
                ->after('actif');
            $table->string('is_annee_reference')->nullable()
                ->comment('Dernier IS payé — pour calcul acomptes')
                ->after('forme_juridique');
            $table->decimal('is_montant_n1', 15, 2)->nullable()
                ->comment('IS N-1 pour calcul acomptes 1/3 + 1/3')
                ->after('is_annee_reference');
        });
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropColumn(['forme_juridique', 'is_annee_reference', 'is_montant_n1']);
        });
    }
};
