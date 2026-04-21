<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Helpers\DataFormatter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register API base URL configuration
        $this->app->singleton('api.baseUrl', function ($app) {
            return env('API_BASE_URL');
        });

        // Configure API service
        config(['services.api.base_url' => env('API_BASE_URL')]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (request()->header('x-forwarded-proto') === 'https') {
            URL::forceScheme('https');
        }

        // Ensure tokens are synchronized from cookies to session on every request
        $this->app->booted(function () {
            if (request()->hasCookie('access_token') && !session('access_token')) {
                session(['access_token' => request()->cookie('access_token')]);
            }

            if (request()->hasCookie('refresh_token') && !session('refresh_token')) {
                session(['refresh_token' => request()->cookie('refresh_token')]);
            }
        });

        // Mendaftarkan helpers
        $this->registerHelpers();
    }

    /**
     * Mendaftarkan class helper
     */
    private function registerHelpers(): void
    {
        if (!class_exists('DataFormatter')) {
            class_alias(DataFormatter::class, 'DataFormatter');
        }
    }
}
