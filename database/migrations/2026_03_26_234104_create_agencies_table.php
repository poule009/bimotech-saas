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
    Schema::create('agencies', function (Blueprint $table) {
        $table->id();
        $table->string('name');                          // "BIMO-Tech Dakar"
        $table->string('slug')->unique();                // "bimo-tech" → identifiant URL
        $table->string('email')->unique();
        $table->string('telephone', 20)->nullable();
        $table->string('logo_path')->nullable();         // chemin vers le logo
        $table->string('couleur_primaire', 7)->nullable(); // ex: "#1a3c5e"
        $table->string('adresse')->nullable();
        $table->decimal('taux_tva', 5, 2)->default(18.00);
        $table->boolean('actif')->default(true);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('agencies');
}
};
