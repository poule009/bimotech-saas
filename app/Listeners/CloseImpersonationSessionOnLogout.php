<?php

namespace App\Listeners;

use App\Models\ImpersonationSession;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Session;

/**
 * Ferme la session d'impersonation tracée quand l'admin se déconnecte « normalement »
 * (bouton Déconnexion) au lieu de repasser par « retour Super Admin ».
 *
 * Sans ce garde, la ligne resterait `ended_at = NULL` pour toujours et polluerait
 * la liste « Sessions en cours » du module Support / Debug.
 *
 * NB : la sortie volontaire (stopImpersonation) ne déclenche pas d'événement Logout
 * — elle re-login le super-admin — et clôture déjà la session en 'normal'.
 */
class CloseImpersonationSessionOnLogout
{
    public function handle(Logout $event): void
    {
        $sessionId = Session::get('impersonation_session_id');

        if (! $sessionId) {
            return;
        }

        ImpersonationSession::whereKey($sessionId)
            ->whereNull('ended_at')
            ->update([
                'ended_at'   => now(),
                'end_reason' => 'logout',
            ]);

        Session::forget('impersonation_session_id');
        Session::forget('impersonating_id');
    }
}
