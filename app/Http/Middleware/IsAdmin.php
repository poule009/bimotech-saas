<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Autorisé : admin d'une agence OU superadmin de la plateforme
        if (! in_array($user->role, ['admin', 'superadmin'])) {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        // Sécurité supplémentaire : un admin doit appartenir à une agence active
        if ($user->role === 'admin' && (! $user->agency || ! $user->agency->isActif())) {
            abort(403, 'Votre agence est inactive. Contactez le support.');
        }

        return $next($request);
    }
}
// ```

// ---

// ## Récapitulatif de ce qu'on vient de faire
// ```
// app/
// ├── Models/
// │   ├── Agency.php          ← NOUVEAU  : le modèle agence
// │   ├── Scopes/
// │   │   └── AgencyScope.php ← NOUVEAU  : le filtre automatique
// │   ├── User.php            ← MODIFIÉ  : + agency_id, + isSuperAdmin()
// │   ├── Bien.php            ← MODIFIÉ  : + AgencyScope + auto agency_id
// │   ├── Contrat.php         ← MODIFIÉ  : + AgencyScope + auto agency_id
// │   └── Paiement.php        ← MODIFIÉ  : + AgencyScope + auto agency_id
// ├── Http/Middleware/
// │   └── IsAdmin.php         ← MODIFIÉ  : suppression du hardcode email
// └── Providers/
//     └── AppServiceProvider.php ← MODIFIÉ : + isSuperAdmin, + View::composer