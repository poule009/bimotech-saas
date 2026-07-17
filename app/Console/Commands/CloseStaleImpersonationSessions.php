<?php

namespace App\Console\Commands;

use App\Models\ImpersonationSession;
use Illuminate\Console\Command;

class CloseStaleImpersonationSessions extends Command
{
    protected $signature   = 'impersonation:close-stale';
    protected $description = 'Ferme les sessions d\'impersonation actives depuis trop longtemps (présumées abandonnées : cookie expiré, onglet fermé)';

    public function handle(): int
    {
        // Filet de sécurité complémentaire au listener de logout : couvre les
        // sessions jamais clôturées explicitement (expiration du cookie, onglet
        // fermé, crash navigateur). Sans ça, elles resteraient « actives » à vie.
        $ferme = ImpersonationSession::stale()->update([
            'ended_at'   => now(),
            'end_reason' => 'expired',
        ]);

        $this->info("Sessions d'impersonation abandonnées fermées : {$ferme}");

        return self::SUCCESS;
    }
}
