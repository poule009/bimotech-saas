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
        if (app()->isProduction()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        return $response;
    }
}
