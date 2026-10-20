<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Rate Limiting: massimo 5 invii del form ogni 10 minuti per IP
        RateLimiter::for('vehicle-form', function (Request $request) {
            return Limit::perMinutes(10, 5)->by($request->ip());
        });

        // Pagine servizi: sfogliare / compilare
        RateLimiter::for('service-form', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Operazioni costose (generazione PDF con soffice): limite più stretto
        RateLimiter::for('service-pdf', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
