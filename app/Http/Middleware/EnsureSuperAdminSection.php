<?php

namespace App\Http\Middleware;

use App\Support\SuperAdminContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autorise l'accès à une section du Super Admin selon le périmètre du compte.
 *
 * L'admin principal passe partout. Un collaborateur restreint ne franchit une
 * section que si son toggle correspondant est activé (facturation, règles fiscales),
 * et jamais les sections de gouvernance (équipe interne, paramètres système).
 *
 * Usage : ->middleware('sa.section:facturation')
 */
class EnsureSuperAdminSection
{
    public function __construct(private SuperAdminContext $context) {}

    public function handle(Request $request, Closure $next, string $section): Response
    {
        abort_unless($this->context->peutVoirSection($section), 403,
            'Cette section du Super Admin ne fait pas partie de votre périmètre.');

        return $next($request);
    }
}
