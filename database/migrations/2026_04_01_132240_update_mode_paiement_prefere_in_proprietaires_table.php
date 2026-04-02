<?php

use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    DB::statement("ALTER TABLE proprietaires MODIFY COLUMN mode_paiement_prefere 
        ENUM('virement','cheque','especes','wave','orange_money','free_money','mobile_money') 
        DEFAULT 'virement'");
}

public function down(): void
{
    DB::statement("ALTER TABLE proprietaires MODIFY COLUMN mode_paiement_prefere 
        ENUM('virement','cheque','especes') 
        DEFAULT 'virement'");
}
};
