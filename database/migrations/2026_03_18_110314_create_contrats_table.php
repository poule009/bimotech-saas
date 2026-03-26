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
       Schema::create('contrats', function (Blueprint $table) {
    $table->id();
    $table->foreignId('bien_id')->constrained('biens')->onDelete('restrict');
    $table->foreignId('locataire_id')->constrained('users')->onDelete('restrict');
    $table->date('date_debut');
    $table->date('date_fin')->nullable(); // null = contrat ouvert
    $table->decimal('loyer_contractuel', 10, 2); // figé à la signature
    $table->decimal('caution', 10, 2)->default(0);
    $table->enum('statut', ['actif', 'resilié', 'expiré'])->default('actif');
    $table->text('observations')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};
