<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('lang');
        
        if ($locale && in_array($locale, ['it', 'en'])) {
            session(['locale' => $locale]);
        } else {
            $locale = session('locale', 'it');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
