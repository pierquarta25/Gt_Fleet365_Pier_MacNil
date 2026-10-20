<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Si fida solo dei proxy/load balancer interni (range privati), non di qualunque client:
        // evita lo spoofing di X-Forwarded-For. Sovrascrivibile con TRUSTED_PROXIES (lista separata da virgole).
        $middleware->trustProxies(at: array_filter(array_map('trim', explode(',', (string) env(
            'TRUSTED_PROXIES',
            '10.0.0.0/8,172.16.0.0/12,192.168.0.0/16,127.0.0.1'
        )))));
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\SetLocale::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
