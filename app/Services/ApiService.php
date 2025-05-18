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
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE)
     * @param string $endpoint API endpoint
     * @param array $options Request options
     * @return array Response data
     */
    public function request(string $method, string $endpoint, array $options = []): array
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
            Log::info('No access token but refresh token exists, needs refresh');
            return true;
        }

        // Check expiration time from JWT payload if available
        $accessTokenExpiry = session('access_token_expiry');
        if ($accessTokenExpiry) {
            $currentTime = time();
            $timeRemaining = $accessTokenExpiry - $currentTime;

            // Define a threshold (20% of token lifetime or 2 minutes, whichever is greater)
            $refreshThreshold = max(
                (session('access_token_expires_in', 3600) * 0.2), // 20% of total lifetime
                120 // 2 minutes minimum
            );

            // If token will expire soon, refresh it
            if ($timeRemaining <= $refreshThreshold) {
                Log::info('Access token approaching expiry based on JWT payload', [
                    'expiry_timestamp' => $accessTokenExpiry,
                    'current_time' => $currentTime,
                    'time_remaining_seconds' => $timeRemaining,
                    'refresh_threshold' => $refreshThreshold
                ]);
                return true;
            }

            // If token is already expired, definitely refresh
            if ($timeRemaining <= 0) {
                Log::warning('Access token is expired based on JWT payload', [
                    'expiry_timestamp' => $accessTokenExpiry,
                    'current_time' => $currentTime,
                    'time_remaining_seconds' => $timeRemaining
                ]);
                return true;
            }

            Log::info('Access token still valid based on JWT payload', [
                'time_remaining_seconds' => $timeRemaining,
                'time_remaining_minutes' => round($timeRemaining / 60, 1)
            ]);
            return false;
        }

        // Fall back to session timestamp-based method if no JWT expiry is available
        if (session('access_token')) {
            $tokenRefreshedAt = session('token_refreshed_at', 0);
            $tokenLifetime = config('auth.token_lifetime', 3600); // 1 hour default
            $refreshThreshold = $tokenLifetime * 0.8; // Refresh when 80% of lifetime passed

            $timeSinceRefresh = now()->timestamp - $tokenRefreshedAt;

            // If token was refreshed more than threshold ago, refresh it
            if ($timeSinceRefresh > $refreshThreshold) {
                Log::info('Token approaching expiry based on refresh timestamp', [
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
     *
     * @return bool Whether token refresh was successful
     */
    public function refreshToken(): bool
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

        // Check if refresh token is expired based on JWT payload
        $refreshTokenExpiry = session('refresh_token_expiry');
        if ($refreshTokenExpiry && time() >= $refreshTokenExpiry) {
            Log::warning('Refresh token is expired based on JWT payload', [
                'expiry_timestamp' => $refreshTokenExpiry,
                'current_time' => time(),
                'expired_by_seconds' => time() - $refreshTokenExpiry
            ]);
            $this->clearTokens();
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

                // Extract token payloads
                $accessTokenPayload = $this->extractJwtPayload($accessToken);
                $refreshTokenPayload = null;

                // Only extract refresh token payload if it was updated
                if ($newRefreshToken !== $refreshToken) {
                    $refreshTokenPayload = $this->extractJwtPayload($newRefreshToken);
                } else {
                    // Use existing refresh token payload from session
                    $refreshTokenPayload = session('refresh_token_payload');
                }

                // Store in cookies with longer expiration
                $this->storeTokensInCookies($accessToken, $newRefreshToken, $accessTokenPayload, $refreshTokenPayload);

                // Update session for current request handling
                session([
                    'access_token' => $accessToken,
                    'refresh_token' => $newRefreshToken,
                    'token_refreshed_at' => now()->timestamp
                ]);

                // Store access token payload in session
                if ($accessTokenPayload) {
                    // Extract expiration time and calculate remaining time
                    $accessTokenExpiry = $accessTokenPayload['exp'] ?? null;
                    $expiresIn = $accessTokenExpiry ? ($accessTokenExpiry - time()) : null;

                    // Store user identity information
                    session([
                        'access_token_payload' => $accessTokenPayload,
                        'user_id' => $accessTokenPayload['user_id'] ?? null,
                        'is_active' => $accessTokenPayload['is_active'] ?? false,
                        'user_roles' => $accessTokenPayload['roles'] ?? [],
                        'access_token_expiry' => $accessTokenExpiry,
                        'access_token_expires_in' => $expiresIn
                    ]);

                    // Store permissions in a way that's compatible with sidebar
                    $permissions = $accessTokenPayload['permissions'] ?? [];
                    session([
                        'token_permissions' => $permissions,
                        'user_permission_names' => $permissions // Important for sidebar compatibility
                    ]);

                    Log::info('Access token JWT payload extracted and stored during refresh', [
                        'user_id' => $accessTokenPayload['user_id'] ?? null,
                        'roles' => $accessTokenPayload['roles'] ?? [],
                        'permissions' => $permissions,
                        'permissions_count' => count($permissions),
                        'expires_in_minutes' => $expiresIn ? round($expiresIn / 60, 1) : null
                    ]);
                }

                // Store refresh token payload in session if we have a new one
                if ($refreshTokenPayload) {
                    // Extract refresh token expiration
                    $refreshTokenExpiry = $refreshTokenPayload['exp'] ?? null;
                    $refreshExpiresIn = $refreshTokenExpiry ? ($refreshTokenExpiry - time()) : null;

                    session([
                        'refresh_token_payload' => $refreshTokenPayload,
                        'refresh_token_expiry' => $refreshTokenExpiry,
                        'refresh_token_expires_in' => $refreshExpiresIn
                    ]);

                    Log::info('Refresh token JWT payload extracted and stored during refresh', [
                        'expires_in_days' => $refreshExpiresIn ? round($refreshExpiresIn / 86400, 1) : null
                    ]);
                }

                // Fetch updated permissions
                $this->refreshPermissions($accessToken);

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
     * Extract payload from JWT token
     */
    protected function extractJwtPayload($token)
    {
        try {
            $tokenParts = explode('.', $token);

            if (count($tokenParts) !== 3) {
                Log::warning('Invalid JWT token format');
                return null;
            }

            $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1]));
            $decodedPayload = json_decode($payload, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('Error decoding JWT payload', ['error' => json_last_error_msg()]);
                return null;
            }

            Log::info('JWT payload decoded successfully');
            return $decodedPayload;
        } catch (\Exception $e) {
            Log::error('Error extracting JWT payload', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Refresh user permissions after token refresh
     */
    protected function refreshPermissions($accessToken)
    {
        try {
            // First, try to get permissions from the token payload in session
            $tokenPermissions = session('token_permissions', []);
            if (!empty($tokenPermissions)) {
                Log::info('Using permissions from refreshed JWT token payload', [
                    'count' => count($tokenPermissions)
                ]);

                // Store JWT token permissions in session for use in views
                session(['user_permissions' => $tokenPermissions]);
                session(['user_permission_names' => $tokenPermissions]);

                // Use access token expiry time for permissions cookie
                $accessTokenExpiry = session('access_token_expiry');
                $cookieLifetime = null;

                // Calculate cookie lifetime in minutes based on JWT expiry
                if ($accessTokenExpiry) {
                    // Use the actual expiry time from the token, minus 1 minute for safety
                    $cookieLifetime = max(1, ceil(($accessTokenExpiry - time() - 60) / 60));
                    Log::info('Setting permissions cookie lifetime from JWT payload', [
                        'minutes' => $cookieLifetime,
                        'expires_at' => date('Y-m-d H:i:s', $accessTokenExpiry)
                    ]);
                } else {
                    // Fallback only if JWT payload doesn't contain expiration
                    $cookieLifetime = session('remember_user', false)
                        ? config('auth.remembered_access_token_cookie_lifetime', 1440)
                        : config('auth.access_token_cookie_lifetime', 60);
                    Log::warning('Falling back to config for permissions cookie lifetime', [
                        'minutes' => $cookieLifetime,
                        'reason' => 'Missing JWT expiration'
                    ]);
                }

                cookie()->queue(
                    'user_permissions',
                    json_encode($tokenPermissions),
                    $cookieLifetime
                );

                return true;
            }

            // If no permissions in JWT token, fetch from API as fallback
            $client = new Client();
            $apiBaseUrl = config('services.api.base_url');

            Log::info('Refreshing user permissions from API after token refresh');

            $response = $client->get("{$apiBaseUrl}/permissions/by-roles", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken
                ],
                'http_errors' => false
            ]);

            $statusCode = $response->getStatusCode();
            $responseData = json_decode($response->getBody()->getContents(), true);

            if ($statusCode === 200 && isset($responseData['success']) && $responseData['success']) {
                $permissions = $responseData['data'] ?? [];

                // Store permissions in session
                session(['user_permissions' => $permissions]);

                // Extract permission names for easier access checking
                $permissionNames = array_map(function ($permission) {
                    return $permission['permission_name'];
                }, $permissions);

                session(['user_permission_names' => $permissionNames]);

                // Use access token expiry time for permissions cookie
                $accessTokenExpiry = session('access_token_expiry');
                $cookieLifetime = null;

                // Calculate cookie lifetime in minutes based on JWT expiry
                if ($accessTokenExpiry) {
                    // Use the actual expiry time from the token, minus 1 minute for safety
                    $cookieLifetime = max(1, ceil(($accessTokenExpiry - time() - 60) / 60));
                    Log::info('Setting permissions cookie lifetime from JWT payload', [
                        'minutes' => $cookieLifetime,
                        'expires_at' => date('Y-m-d H:i:s', $accessTokenExpiry)
                    ]);
                } else {
                    // Fallback only if JWT payload doesn't contain expiration
                    $cookieLifetime = session('remember_user', false)
                        ? config('auth.remembered_access_token_cookie_lifetime', 1440)
                        : config('auth.access_token_cookie_lifetime', 60);
                    Log::warning('Falling back to config for permissions cookie lifetime', [
                        'minutes' => $cookieLifetime,
                        'reason' => 'Missing JWT expiration'
                    ]);
                }

                cookie()->queue(
                    'user_permissions',
                    json_encode($permissions),
                    $cookieLifetime
                );

                Log::info('Permissions refreshed successfully', ['count' => count($permissions)]);
                return true;
            } else {
                Log::warning('Failed to refresh permissions', [
                    'status' => $statusCode,
                    'response' => $responseData
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Error refreshing permissions', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Store tokens in cookies and session
     */
    public function storeTokensInCookies($accessToken, $refreshToken, $accessTokenPayload = null, $refreshTokenPayload = null)
    {
        // Check if user wants to be remembered
        $rememberUser = session('remember_user', false);

        // Calculate cookie lifetimes based on actual JWT token expiration times
        $accessTokenLifetime = null;
        $refreshTokenLifetime = null;

        // Extract access token expiration from payload
        if ($accessTokenPayload && isset($accessTokenPayload['exp'])) {
            // Use the actual expiry time from the token, minus 1 minute for safety
            $accessTokenLifetime = max(1, ceil(($accessTokenPayload['exp'] - time() - 60) / 60));
            Log::info('Setting access token cookie lifetime from JWT payload', [
                'minutes' => $accessTokenLifetime,
                'expires_at' => date('Y-m-d H:i:s', $accessTokenPayload['exp'])
            ]);
        } else {
            // Fallback only if JWT payload doesn't contain expiration
            $accessTokenLifetime = $rememberUser
                ? config('auth.remembered_access_token_cookie_lifetime', 1440)
                : config('auth.access_token_cookie_lifetime', 60);
            Log::warning('Falling back to config for access token cookie lifetime', [
                'minutes' => $accessTokenLifetime,
                'reason' => 'Missing JWT expiration'
            ]);
        }

        // Extract refresh token expiration from payload
        if ($refreshTokenPayload && isset($refreshTokenPayload['exp'])) {
            // Use the actual expiry time from the token, minus 1 minute for safety
            $refreshTokenLifetime = max(1, ceil(($refreshTokenPayload['exp'] - time() - 60) / 60));
            Log::info('Setting refresh token cookie lifetime from JWT payload', [
                'minutes' => $refreshTokenLifetime,
                'expires_at' => date('Y-m-d H:i:s', $refreshTokenPayload['exp'])
            ]);
        } else {
            // Fallback only if JWT payload doesn't contain expiration
            $refreshTokenLifetime = $rememberUser
                ? config('auth.remembered_refresh_token_cookie_lifetime', 43200*7)
                : config('auth.refresh_token_cookie_lifetime', 43200);
            Log::warning('Falling back to config for refresh token cookie lifetime', [
                'minutes' => $refreshTokenLifetime,
                'reason' => 'Missing JWT expiration'
            ]);
        }

        Log::info('Setting cookies with expiration from JWT payload', [
            'remember_user' => $rememberUser,
            'access_token_lifetime_minutes' => $accessTokenLifetime,
            'refresh_token_lifetime_minutes' => $refreshTokenLifetime
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

        // Store access token payload in cookie if available
        if ($accessTokenPayload) {
            cookie()->queue(
                'access_token_payload',
                json_encode($accessTokenPayload),
                $accessTokenLifetime,
                null, // path
                null, // domain
                config('app.env') === 'production', // secure only in production
                true, // http only
                false, // raw
                config('session.same_site', 'lax') // same site policy matching session config
            );
        }

        // Store refresh token payload in cookie if available
        if ($refreshTokenPayload) {
            cookie()->queue(
                'refresh_token_payload',
                json_encode($refreshTokenPayload),
                $refreshTokenLifetime,
                null, // path
                null, // domain
                config('app.env') === 'production', // secure only in production
                true, // http only
                false, // raw
                config('session.same_site', 'lax') // same site policy matching session config
            );
        }

        Log::info('Tokens and payloads stored in cookies successfully');
    }

    private function clearTokens()
    {
        // Clear session data
        session()->forget([
            'access_token', 'refresh_token', 'token_validated_at',
            'token_refreshed_at', 'user_permissions', 'user_permission_names',
            'access_token_payload', 'refresh_token_payload', 'user_id', 'is_active', 'user_roles',
            'token_permissions', 'access_token_expiry', 'refresh_token_expiry',
            'access_token_expires_in', 'refresh_token_expires_in'
        ]);

        // Clear cookies
        cookie()->queue(cookie()->forget('access_token'));
        cookie()->queue(cookie()->forget('refresh_token'));
        cookie()->queue(cookie()->forget('user_permissions'));
        cookie()->queue(cookie()->forget('access_token_payload'));
        cookie()->queue(cookie()->forget('refresh_token_payload'));
    }
}
