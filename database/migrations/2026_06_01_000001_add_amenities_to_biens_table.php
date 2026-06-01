<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->unsignedTinyInteger('nombre_chambres')->nullable()->after('nombre_pieces');
            $table->unsignedTinyInteger('nombre_sdb')->nullable()->after('nombre_chambres');
            $table->boolean('parking')->default(false)->after('nombre_sdb');
            $table->boolean('climatise')->default(false)->after('parking');
            $table->tinyInteger('etage')->nullable()->after('climatise');
            $table->decimal('latitude', 10, 7)->nullable()->after('etage');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->json('amenites')->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->dropColumn(['amenites', 'longitude', 'latitude', 'etage', 'climatise', 'parking', 'nombre_sdb', 'nombre_chambres']);
        });
    }
};
