<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Aggiunge header di sicurezza a ogni risposta HTTP.
     * Protegge da clickjacking, MIME sniffing, XSS e altri attacchi comuni.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Impedisce al browser di interpretare file con un MIME type diverso da quello dichiarato
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Impedisce che il sito venga caricato in un iframe (anti-clickjacking)
        $response->headers->set('X-Frame-Options', 'DENY');

        // Content Security Policy: solo risorse proprie + CDN usate dalle viste (non in locale: Vite dev server)
        if (!app()->environment('local')) {
            $response->headers->set('Content-Security-Policy', implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net",
                "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com",
                "img-src 'self' data:",
                "connect-src 'self'",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'none'",
            ]));
        }

        // Limita le informazioni inviate nell'header Referrer verso siti esterni
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Disabilita l'accesso a sensori e funzionalità non necessarie
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // Forza HTTPS per 1 anno (solo in produzione)
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
