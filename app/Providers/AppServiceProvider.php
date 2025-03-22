<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register API base URL configuration
        $this->app->singleton('api.baseUrl', function ($app) {
            return env('API_BASE_URL', 'http://localhost:5000');
        });

        // Configure API service
        config(['services.api.base_url' => env('API_BASE_URL', 'http://localhost:5000')]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure tokens are synchronized from cookies to session on every request
        $this->app->booted(function () {
            if (request()->hasCookie('access_token') && !session('access_token')) {
                session(['access_token' => request()->cookie('access_token')]);
            }

            if (request()->hasCookie('refresh_token') && !session('refresh_token')) {
                session(['refresh_token' => request()->cookie('refresh_token')]);
            }
        });
    }
}
