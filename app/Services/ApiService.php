<?php

namespace App\Services;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class ApiService
{
    protected $baseUrl;
    protected $client;
    protected $refreshInProgress = false;

    public function __construct()
    {
        $this->baseUrl = config('services.api.base_url', 'http://localhost:5000');
        $this->baseUrl = rtrim($this->baseUrl, '/');
        $this->client = new Client([
            'timeout' => 30,
            'http_errors' => false, // We'll handle errors ourselves
        ]);
    }

    /**
     * Make an authenticated API request with automatic token refresh
     */
    public function request($method, $endpoint, $options = [])
    {
        // Proactively check and refresh token if needed before making any request
        $this->proactiveTokenRefresh();

        try {
            // Ensure we have the Authorization header with the access token
            if (!isset($options['headers'])) {
                $options['headers'] = [];
            }

            // First try to get token from cookie, then session
            $accessToken = request()->cookie('access_token') ?: session('access_token');

            if ($accessToken) {
                $options['headers']['Authorization'] = 'Bearer ' . $accessToken;
            }

            // Make the API request
            $response = $this->client->request($method, $this->baseUrl . $endpoint, $options);

            // Handle 401 unauthorized responses
            if ($response->getStatusCode() === 401 || $response->getStatusCode() === 403) {
                Log::info('Received ' . $response->getStatusCode() . ' from API, attempting token refresh');

                // Try to refresh the token
                if ($this->refreshToken()) {
                    // Get new token (prefer cookie)
                    $newAccessToken = request()->cookie('access_token') ?: session('access_token');

                    // Update authorization header with new token
                    $options['headers']['Authorization'] = 'Bearer ' . $newAccessToken;

                    // Retry the original request with the new token
                    $response = $this->client->request($method, $this->baseUrl . $endpoint, $options);

                    // If still 401/403, redirect to login (handled by caller)
                    if ($response->getStatusCode() === 401 || $response->getStatusCode() === 403) {
                        Log::warning('Still received ' . $response->getStatusCode() . ' after token refresh');
                        return [
                            'error' => 'auth_failed',
                            'message' => 'Authentication failed. Please login again.',
                            'hint' => 'API endpoint to refresh token: POST /auth/refresh-token with body: {"refresh_token": "your_refresh_token_from_login"}'
                        ];
                    }
                } else {
                    // Refresh token failed
                    Log::warning('Token refresh failed');
                    return [
                        'error' => 'session_expired',
                        'message' => 'Session expired. Please login again.',
                        'hint' => 'API endpoint to refresh token: POST /auth/refresh-token with body: {"refresh_token": "your_refresh_token_from_login"}'
                    ];
                }
            }

            // Return the JSON decoded response body
            $body = (string) $response->getBody();
            return json_decode($body, true) ?: [];
        } catch (\Exception $e) {
            Log::error('API request error', [
                'method' => $method,
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);

            return ['error' => 'api_error', 'message' => 'Error connecting to API: ' . $e->getMessage()];
        }
    }

    /**
     * Proactively check token and refresh if needed or nearly expired
     */
    public function proactiveTokenRefresh()
    {
        if ($this->refreshInProgress) {
            return false;
        }

        $this->refreshInProgress = true;

        try {
            // Check if we need to refresh token
            $needsRefresh = $this->tokenNeedsRefresh();

            if ($needsRefresh) {
                Log::info('Token needs proactive refresh, attempting refresh');
                $result = $this->refreshToken();
                $this->refreshInProgress = false;
                return $result;
            }

            $this->refreshInProgress = false;
            return true;
        } catch (\Exception $e) {
            $this->refreshInProgress = false;
            Log::error('Proactive token refresh error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Check if token needs to be refreshed (expired or about to expire)
     */
    protected function tokenNeedsRefresh()
    {
        // If no access token, but refresh token exists, refresh
        if (!session('access_token') && (session('refresh_token') || request()->cookie('refresh_token'))) {
            return true;
        }

        // If we have access token, check if it's about to expire
        if (session('access_token')) {
            $tokenRefreshedAt = session('token_refreshed_at', 0);
            $tokenLifetime = config('auth.token_lifetime', 3600); // 1 hour default
            $refreshThreshold = $tokenLifetime * 0.8; // Refresh when 80% of lifetime passed

            $timeSinceRefresh = now()->timestamp - $tokenRefreshedAt;

            // If token was refreshed more than threshold ago, refresh it
            if ($timeSinceRefresh > $refreshThreshold) {
                Log::info('Token approaching expiry, needs refresh', [
                    'refreshed_at' => $tokenRefreshedAt,
                    'time_since_refresh' => $timeSinceRefresh,
                    'threshold' => $refreshThreshold
                ]);
                return true;
            }
        }

        return false;
    }

    /**
     * Validate the current access token
     */
    public function validateToken()
    {
        $accessToken = session('access_token');
        if (!$accessToken) {
            Log::warning('No access token found in session');
            return false;
        }

        // Skip validation if we refreshed the token recently (last 5 minutes)
        $lastRefreshed = session('token_refreshed_at');
        if ($lastRefreshed && (now()->timestamp - $lastRefreshed) < 300) {
            Log::info('Token was refreshed recently, skipping validation');
            return true;
        }

        try {
            $client = new Client();
            $response = $client->post(config('services.api.base_url') . '/auth/verify-token', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken
                ],
                'http_errors' => false
            ]);

            if ($response->getStatusCode() !== 200) {
                Log::warning('Token validation failed with success code: ' . $response->getStatusCode());

                // Try to refresh the token if validation fails
                return $this->refreshToken();
            }

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['success']) && $result['success']) {
                // Token is valid, update validation timestamp
                session(['token_validated_at' => now()->timestamp]);
                return true;
            }

            // If result success is false, try to refresh token
            return $this->refreshToken();
        } catch (\Exception $e) {
            Log::error('Token validation exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Refresh the access token using refresh token
     */
    public function refreshToken()
    {
        // First try to get refresh token from cookie directly
        $refreshToken = request()->cookie('refresh_token');

        // Fall back to session if not in cookie
        if (!$refreshToken) {
            $refreshToken = session('refresh_token');
        }

        if (!$refreshToken) {
            Log::warning('No refresh token available in session or cookie');
            return false;
        }

        Log::info('Attempting to refresh token with refresh token from ApiService');

        try {
            $client = new Client();
            $response = $client->post(config('services.api.base_url') . '/auth/refresh-token', [
                'json' => [
                    'refresh_token' => $refreshToken,
                    // Some APIs expect 'refreshToken' instead
                    'refreshToken' => $refreshToken
                ],
                'http_errors' => false
            ]);

            $successCode = $response->getStatusCode();
            Log::info('Refresh token response success in ApiService: ' . $successCode);

            if ($successCode !== 200) {
                // Refresh token is invalid or expired
                Log::warning('Refresh token failed with success: ' . $successCode);
                $this->clearTokens();
                return false;
            }

            $result = json_decode($response->getBody()->getContents(), true);
            Log::info('Refresh token API response in ApiService', ['result' => $result]);

            if (isset($result['success']) && $result['success']) {
                // Handle different API response formats - try both camelCase and snake_case
                $accessToken = $result['data']['accessToken'] ??
                               $result['data']['access_token'] ??
                               null;

                $newRefreshToken = $result['data']['refreshToken'] ??
                                   $result['data']['refresh_token'] ??
                                   $refreshToken; // Use existing if not provided

                if (!$accessToken) {
                    Log::warning('Missing access token in refresh response', ['response' => $result]);
                    return false;
                }

                // Store in cookies with longer expiration
                $this->storeTokensInCookies($accessToken, $newRefreshToken);

                // Also update session for current request handling
                session([
                    'access_token' => $accessToken,
                    'refresh_token' => $newRefreshToken,
                    'token_refreshed_at' => now()->timestamp
                ]);

                Log::info('Token refreshed successfully');
                return true;
            }

            Log::warning('Refresh token request was unsuccessful in ApiService');
            return false;
        } catch (\Exception $e) {
            Log::error('Token refresh exception in ApiService', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Store tokens in cookies and session
     */
    public function storeTokensInCookies($accessToken, $refreshToken)
    {
        // Check if user wants to be remembered
        $rememberUser = session('remember_user', false);

        // Access token lifetime - longer if remembered
        $accessTokenLifetime = $rememberUser
            ? config('auth.remembered_access_token_cookie_lifetime', 1440) // 1 day for remembered users
            : config('auth.access_token_cookie_lifetime', 60); // 1 hour default

        // Refresh token lifetime - longer if remembered
        $refreshTokenLifetime = $rememberUser
            ? config('auth.remembered_refresh_token_cookie_lifetime', 43200*7) // 7 months for remembered
            : config('auth.refresh_token_cookie_lifetime', 43200); // 30 days default

        Log::info('Setting cookies with remember preference', [
            'remember_user' => $rememberUser,
            'access_token_lifetime' => $accessTokenLifetime,
            'refresh_token_lifetime' => $refreshTokenLifetime
        ]);

        // Set secure HTTP-only cookies for both tokens with proper configuration
        cookie()->queue(
            'access_token',
            $accessToken,
            $accessTokenLifetime,
            null, // path
            null, // domain
            config('app.env') === 'production', // secure only in production
            true, // http only
            false, // raw
            config('session.same_site', 'lax') // same site policy matching session config
        );

        cookie()->queue(
            'refresh_token',
            $refreshToken,
            $refreshTokenLifetime,
            null, // path
            null, // domain
            config('app.env') === 'production', // secure only in production
            true, // http only
            false, // raw
            config('session.same_site', 'lax') // same site policy matching session config
        );

        Log::info('Tokens stored in cookies successfully');
    }

    private function clearTokens()
    {
        // Clear session data
        session()->forget(['access_token', 'refresh_token', 'token_validated_at', 'token_refreshed_at']);

        // Clear cookies
        cookie()->queue(cookie()->forget('access_token'));
        cookie()->queue(cookie()->forget('refresh_token'));
    }
}
