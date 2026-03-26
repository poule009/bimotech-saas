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
Schema::create('biens', function (Blueprint $table) {
    $table->id();
    $table->foreignId('proprietaire_id')->constrained('users')->onDelete('cascade');
    $table->string('reference')->unique(); // ex: BIEN-2025-001
    $table->string('type'); // appartement, villa, bureau...
    $table->string('adresse');
    $table->string('ville');
    $table->integer('surface_m2')->nullable();
    $table->integer('nombre_pieces')->nullable();
    $table->decimal('loyer_mensuel', 10, 2); // ex: 150000.00 FCFA
    $table->decimal('taux_commission', 5, 2)->default(5.00); // 1% à 20%
    $table->enum('statut', ['disponible', 'loue', 'en_travaux'])->default('disponible');
    $table->text('description')->nullable();
    $table->timestamps();
    $table->softDeletes();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biens');
    }
};
