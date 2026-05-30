<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecureHeaders — Ajoute les en-têtes HTTP de sécurité sur toutes les réponses.
 *
 * Ces en-têtes protègent contre les attaques XSS, clickjacking et MIME-sniffing.
 * En production, HSTS force le navigateur à utiliser HTTPS pour tous les appels futurs.
 */
class SecureHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = base64_encode(random_bytes(16));
        app()->instance('csp-nonce', $nonce);

        $response = $next($request);

        // Empêche l'intégration de la page dans un <iframe> (anti-clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Interdit au navigateur de deviner le type MIME (anti-MIME-sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Active le filtre XSS natif du navigateur
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Contrôle ce que le navigateur envoie dans le header Referer
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restreint les permissions des APIs navigateur (caméra, micro, géolocalisation...)
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // HSTS : force HTTPS pendant 1 an — actif uniquement en production pour éviter
        // de bloquer le développement local en HTTP.
        // preload : soumettre le domaine sur https://hstspreload.org pour protection dès la 1re visite.
        if (app()->isProduction()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Content-Security-Policy — limite les sources de scripts, styles et médias.
        // 'unsafe-inline' conservé pour les styles Blade inline et Chart.js.
        // Affiner progressivement en remplaçant 'unsafe-inline' par des nonces si besoin.
        $response->headers->set(
            'Content-Security-Policy',
            implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
                "img-src 'self' data: blob: https:",
                "font-src 'self' data: https://fonts.gstatic.com",
                "connect-src 'self'",
                "frame-src 'none'",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self' https://paytech.sn",
            ])
        );

        // Report-Only : mesure les violations sans rien bloquer.
        // Le navigateur signale tout ce qui serait bloqué en mode strict (nonce requis).
        // Les rapports arrivent sur /csp-report → storage/logs/csp-violations.log.
        $response->headers->set(
            'Content-Security-Policy-Report-Only',
            implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'nonce-{$nonce}' https://cdnjs.cloudflare.com",
                "style-src 'self' 'nonce-{$nonce}' https://fonts.googleapis.com",
                "img-src 'self' data: blob: https:",
                "font-src 'self' data: https://fonts.gstatic.com",
                "connect-src 'self'",
                "frame-src 'none'",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self' https://paytech.sn",
                "report-uri /csp-report",
            ])
        );

        return $response;
    }
}
