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
        Schema::create('reversements_proprietaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained()->onDelete('cascade');
            $table->foreignId('proprietaire_id')->constrained('users')->onDelete('cascade');
            $table->decimal('montant', 12, 2);
            $table->date('date_reversement');
            $table->enum('mode_paiement', ['virement', 'wave', 'orange_money', 'especes', 'cheque']);
            $table->string('reference')->nullable();
            $table->string('periode_debut', 7)->nullable(); // Y-m
            $table->string('periode_fin', 7)->nullable();   // Y-m
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['agency_id', 'proprietaire_id']);
            $table->index(['agency_id', 'date_reversement']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reversements_proprietaires');
    }
};
