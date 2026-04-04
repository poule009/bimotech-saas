<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quittances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('paiement_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('contrat_id')->constrained()->cascadeOnDelete();
            $table->foreignId('generee_par')->nullable()->constrained('users')->nullOnDelete();

            $table->string('numero', 30)->unique();   // QT-04-2025-0087
            $table->date('date_emission');
            $table->string('mois_concerne', 7);       // 2025-06

            $table->timestamps();

            $table->index(['agency_id', 'mois_concerne']);
            $table->index(['contrat_id', 'mois_concerne']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quittances');
    }
};