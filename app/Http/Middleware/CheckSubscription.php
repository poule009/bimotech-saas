<?php

namespace App\Http\Middleware;

use App\Models\Subscription;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    // ✅ CORRECTION M2 : noms de routes uniquement (plus de chemins URL)
    // Avant : mélange de paths ("admin/agency/settings") et de noms de routes
    //         → certaines exclusions ne marchaient pas
    // Après : uniquement des noms de routes → fiable même si les URLs changent
    // Routes TOUJOURS accessibles, quel que soit l'état de l'abonnement
    // (authentification, paiement, pages publiques/légales). Volontairement SANS
    // dashboard/settings/profil : ceux-ci sont bloqués quand le compte est suspendu.
    protected array $except = [
        'login', 'logout', 'register',
        'password.*', 'verification.*',
        'agency.register', 'agency.register.store',
        'subscription.*',
        'admin.password.force', 'admin.password.force.update',
        // Google OAuth
        'auth.google', 'auth.google.callback',
        'agency.register.google.complete', 'agency.register.google.store',
        // Pages publiques
        'home', 'landing', 'contact', 'contact.send', 'demo', 'demo.send',
        'faq', 'mentions-legales', 'confidentialite',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->routeIs(...$this->except)) {
            return $next($request);
        }

        $user = Auth::user();

        if (! $user || $user->role === 'superadmin' || ! $user->agency) {
            return $next($request);
        }

        // Seuls les admins d'agence (directeur + collaborateurs) sont soumis à
        // l'abonnement. Les portails propriétaire/locataire ne sont pas redirigés
        // vers l'écran de facturation (qui ne les concerne pas).
        if ($user->role !== 'admin') {
            return $next($request);
        }

        $subscription = $user->agency->subscription;

        if (! $subscription) {
            return redirect()->route('subscription.index')
                ->with('warning', "Aucun abonnement trouvé pour votre agence.");
        }

        $etat = $subscription->etatEffectif();

        // Essai actif / abonnement actif → accès complet
        if (in_array($etat, [Subscription::ETAT_ESSAI, Subscription::ETAT_ACTIF], true)) {
            return $next($request);
        }

        // Suspendu → tout est bloqué (seules les routes $except passent) → écran Abonnement
        if ($etat === Subscription::ETAT_SUSPENDU) {
            return redirect()->route('subscription.index')
                ->with('warning', "Votre compte est suspendu. Déclarez un paiement pour réactiver votre agence — vos données sont conservées.");
        }

        // Grâce → lecture seule : consultation autorisée, création/modification bloquée
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            return $next($request);
        }

        return redirect()->route('subscription.index')
            ->with('warning', "Votre période de grâce est en cours. Déclarez votre paiement pour retrouver un accès complet.");
    }
}