<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;

class AuthMiddleware
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function handle(Request $request, Closure $next)
    {
        // Check for access token in cookie first, then session
        $accessToken = $request->cookie('access_token') ?: session('access_token');

        // If we have an access token in cookie but not in session, store it in session
        if ($request->cookie('access_token') && !session('access_token')) {
            session(['access_token' => $request->cookie('access_token')]);
        }

        // Check if the user has access token
        if (!$accessToken) {
            // Check for refresh token in cookies first, then session
            $refreshToken = $request->cookie('refresh_token') ?: session('refresh_token');

            if ($refreshToken) {
                // Store refresh token in session if it was in cookies
                if (!session('refresh_token') && $request->cookie('refresh_token')) {
                    session(['refresh_token' => $request->cookie('refresh_token')]);
                }

                // Try to refresh the token
                if ($this->apiService->refreshToken()) {
                    // Token refreshed successfully, continue with the request
                    Log::info('Auto-login successful via refresh token in middleware');

                    // If this was a GET request to login page, redirect to dashboard
                    if ($request->isMethod('get') && $request->route() && $request->route()->getName() === 'login') {
                        return redirect()->route('dashboard');
                    }

                    return $next($request);
                }
            }

            // Clear session and redirect to login
            $this->clearAuthSession($request);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'auth_failed',
                    'message' => 'Your session has expired. Please login again.',
                    'redirect' => route('login')
                ], 401);
            }

            Log::warning('Authentication failed in middleware - redirecting to login');
            return redirect()->route('login')->with('error', 'Your session has expired. Please login again.');
        }

        return $next($request);
    }

    /**
     * Helper method to clear authentication session data
     */
    private function clearAuthSession(Request $request)
    {
        Log::info('Clearing auth session in middleware');
        session()->forget(['access_token', 'refresh_token', 'email', 'token_validated_at', 'token_refreshed_at', 'remember_user']);
        session()->invalidate();
        session()->regenerateToken();

        // Also clear the refresh token cookie
        if ($request->cookie('refresh_token')) {
            cookie()->queue(cookie()->forget('refresh_token'));
        }
    }
}
