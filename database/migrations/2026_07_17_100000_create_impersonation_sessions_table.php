<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Journal des sessions d'impersonation (Super Admin « se connecte en tant qu'agence »).
     *
     * Une session est ACTIVE tant que `ended_at` est NULL. Terminer une session à
     * distance = poser `ended_at` + `end_reason='revoked'` ; le middleware
     * EnforceImpersonationRevocation applique alors la déconnexion réelle au
     * prochain hit de l'admin concerné.
     */
    public function up(): void
    {
        Schema::create('impersonation_sessions', function (Blueprint $table) {
            $table->id();
            // Table d'AUDIT : les FK sont nullOnDelete (pas cascade) pour que
            // l'historique survive à la suppression d'un user/agence. Les noms sont
            // dénormalisés (snapshots) afin de rester lisibles même après coup.
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('agency_id')->nullable()->constrained('agencies')->nullOnDelete();

            // Snapshots au démarrage de la session (résilients à la suppression).
            $table->string('admin_name')->nullable();
            $table->string('agency_name')->nullable();

            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            // Qui a terminé la session (l'admin lui-même en sortie normale, ou un
            // autre super-admin lors d'une coupure à distance).
            $table->foreignId('ended_by')->nullable()->constrained('users')->nullOnDelete();
            // 'normal' (sortie via « retour Super Admin ») | 'logout' (déconnexion
            // classique) | 'revoked' (coupée à distance) | 'expired' (balayage).
            $table->string('end_reason', 20)->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            // Retrouver vite les sessions actives et l'historique par admin.
            $table->index(['ended_at']);
            $table->index(['admin_id', 'started_at']);
            $table->index(['agency_id', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impersonation_sessions');
    }
};
