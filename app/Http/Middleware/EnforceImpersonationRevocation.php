<?php

namespace App\Http\Middleware;

use App\Models\ImpersonationSession;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applique la coupure à distance d'une impersonation.
 *
 * Quand un super-admin termine la session d'un collègue depuis Support / Debug,
 * on pose seulement `ended_at` en base — on ne peut pas déconnecter un autre
 * navigateur instantanément. Ce middleware ferme la boucle : au prochain hit de
 * l'admin impersonnant, il détecte que sa session a été révoquée, restaure le
 * compte super-admin réel et affiche un bandeau immédiat « au moment de la coupure ».
 */
class EnforceImpersonationRevocation
{
    public function handle(Request $request, Closure $next): Response
    {
        $sessionId = Session::get('impersonation_session_id');

        // On n'agit que pendant une impersonation réellement tracée.
        if ($sessionId && Session::has('impersonating_id')) {
            $impersonation = ImpersonationSession::whereKey($sessionId)
                ->whereNotNull('ended_at')
                ->where('end_reason', 'revoked')
                ->first();

            if ($impersonation) {
                return $this->forceStop($request, $impersonation);
            }
        }

        return $next($request);
    }

    /**
     * Restaure le super-admin réel (miroir de SuperAdminController::stopImpersonation)
     * et redirige avec un message explicite.
     */
    private function forceStop(Request $request, ImpersonationSession $impersonation): Response
    {
        $superAdminId = Session::pull('impersonating_id');
        Session::forget('impersonation_session_id');

        $superAdmin = $superAdminId
            ? User::withoutGlobalScopes()->find($superAdminId)
            : null;

        if (! $superAdmin || ! $superAdmin->isSuperAdmin()) {
            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();

            return redirect()->route('login');
        }

        Auth::login($superAdmin);

        $par = $impersonation->endedBy?->name;
        $message = $par
            ? "Votre session « connecté en tant que {$impersonation->agency?->name} » a été terminée à distance par {$par}."
            : "Votre session « connecté en tant que {$impersonation->agency?->name} » a été terminée à distance.";

        return redirect()
            ->route('superadmin.dashboard')
            ->with('warning', $message);
    }
}
