<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Services\ApiService;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use App\Helpers\PermissionHelper;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin(Request $request)
    {
        // Check if we already have a refresh token - try auto-login first
        if ($request->cookie('refresh_token')) {
            $apiService = app(ApiService::class);
            session(['refresh_token' => $request->cookie('refresh_token')]);

            try {
                if ($apiService->refreshToken()) {
                    \Log::info('Auto-login successful via refresh token from login page');

                    // Check if user has dashboard permission
                    if (hasPermission('dashboard:view')) {
                        return redirect()->route('dashboard');
                    } else {
                        \Log::info('User does not have dashboard permission, looking for first accessible menu');

                        // Define menu routes based on permissions
                        $menuRoutes = [
                            'asset-subcategory:view' => 'categories',
                            'brand:view' => 'brands',
                            'building:view' => 'buildings',
                            'room:view' => 'rooms',
                            'vendor:view' => 'vendors',
                            'asset:view' => 'assets',
                            'asset-master:view' => 'asset-masters',
                            'calibration:view' => 'calibration',
                            'maintenance:view|maintenance-report:medical|maintenance-report:non-medical' => 'maintenance',
                            'complaint:view' => 'complaints',
                            'procurement:view' => 'procurements',
                            'report:finance' => 'report.finance',
                            'report:opname' => 'report.opname',
                            'report:depreciation' => 'report.depreciation',
                            'user:view' => 'users',
                            'role:view' => 'roles'
                        ];

                        // Loop through menu routes to find first accessible
                        foreach ($menuRoutes as $permission => $route) {
                            if (hasPermission($permission)) {
                                \Log::info("Redirecting user to first accessible menu: {$route}");
                                return redirect()->route($route);
                            }
                        }

                        // If no accessible menus found
                        \Log::warning('User has no accessible menus, logging out');
                        $this->clearAuthSession($request);
                        return redirect()->route('login')->with('error', 'Anda tidak memiliki akses ke menu apapun.');
                    }
                } else {
                    \Log::warning('Auto-login failed - invalid refresh token');
                    // If refresh token is invalid, forget the cookie
                    cookie()->queue(cookie()->forget('refresh_token'));
                }
            } catch (\Exception $e) {
                \Log::error('Auto-login error:', [
                    'error' => $e->getMessage()
                ]);
                cookie()->queue(cookie()->forget('refresh_token'));
            }
        }

        return view('Auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        try {
            // Check if we have a refresh token in cookie - if so, try to auto-login
            if (!$request->filled('employee_number') && !$request->filled('password') && $request->cookie('refresh_token')) {
                $apiService = app(ApiService::class);
                session(['refresh_token' => $request->cookie('refresh_token')]);

                try {
                    if ($apiService->refreshToken()) {
                        \Log::info('Auto-login successful via refresh token');

                        // Check if user has dashboard permission
                        if (hasPermission('dashboard:view')) {
                            return redirect()->route('dashboard');
                        } else {
                            \Log::info('User does not have dashboard permission, looking for first accessible menu');

                            // Define menu routes based on permissions
                            $menuRoutes = [
                                'asset-subcategory:view' => 'categories',
                                'brand:view' => 'brands',
                                'building:view' => 'buildings',
                                'room:view' => 'rooms',
                                'vendor:view' => 'vendors',
                                'asset:view' => 'assets',
                                'asset-master:view' => 'asset-masters',
                                'calibration:view' => 'calibration',
                                'maintenance:view|maintenance-report:medical|maintenance-report:non-medical' => 'maintenance',
                                'complaint:view' => 'complaints',
                                'procurement:view' => 'procurements',
                                'report:finance' => 'report.finance',
                                'report:opname' => 'report.opname',
                                'report:depreciation' => 'report.depreciation',
                                'user:view' => 'users',
                                'role:view' => 'roles'
                            ];

                            // Loop through menu routes to find first accessible
                            foreach ($menuRoutes as $permission => $route) {
                                if (hasPermission($permission)) {
                                    \Log::info("Redirecting user to first accessible menu: {$route}");
                                    return redirect()->route($route);
                                }
                            }

                            // If no accessible menus found
                            \Log::warning('User has no accessible menus, logging out');
                            $this->clearAuthSession($request);
                            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses ke menu apapun.');
                        }
                    } else {
                        \Log::warning('Auto-login failed - invalid refresh token during login attempt');
                        // If refresh token is invalid, forget the cookie
                        cookie()->queue(cookie()->forget('refresh_token'));
                        return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
                    }
                } catch (\Exception $e) {
                    \Log::error('Auto-login error during login attempt:', [
                        'error' => $e->getMessage()
                    ]);
                    cookie()->queue(cookie()->forget('refresh_token'));
                    return redirect()->route('login')->with('error', 'Gagal login otomatis: ' . $e->getMessage());
                }
            }

            \Log::info('Login attempt', ['employee_number' => $request->employee_number]);

            $validator = Validator::make($request->all(), [
                'employee_number' => 'required',
                'password' => 'required'
            ], [
                'employee_number.required' => 'Kode Karyawan harus diisi',
                'password.required' => 'Password harus diisi'
            ]);

            if ($validator->fails()) {
                \Log::warning('Login validation failed', ['errors' => $validator->errors()->toArray()]);
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput($request->except('password'));
            }

            $client = new Client([
                'timeout' => 15, // 15 second timeout
                'connect_timeout' => 5, // 5 second connection timeout
            ]);
            \Log::info('Sending login request to API');

            try {
            $response = $client->post(config('services.api.base_url') . '/auth/login', [
                'json' => [
                    'employee_number' => $request->employee_number,
                    'password' => $request->password,
                    ],
                    'http_errors' => false
            ]);

                $statusCode = $response->getStatusCode();
            $result = json_decode($response->getBody()->getContents(), true);

            \Log::info('Login API response', [
                    'status_code' => $statusCode,
                    'success' => $result['success'] ?? false,
                'errors' => $result['errors'] ?? null
            ]);

                if (isset($result['success']) && $result['success']) {
                // Store tokens in both session and cookies
                    $accessToken = $result['data']['accessToken'];
                    $refreshToken = $result['data']['refreshToken'];

                    $request->session()->put('access_token', $accessToken);
                    $request->session()->put('refresh_token', $refreshToken);
                $request->session()->put('employee_number', $request->employee_number);
                $request->session()->put('token_validated_at', now()->timestamp);

                    // Extract and store JWT payload data from access token
                    $accessTokenPayload = $this->extractJwtPayload($accessToken);
                    if ($accessTokenPayload) {
                        $request->session()->put('access_token_payload', $accessTokenPayload);

                        // Store specific payload items for easier access
                        $request->session()->put('user_id', $accessTokenPayload['user_id'] ?? null);
                        $request->session()->put('is_active', $accessTokenPayload['is_active'] ?? false);
                        $request->session()->put('user_roles', $accessTokenPayload['roles'] ?? []);

                        // Store token permissions in a format compatible with the sidebar
                        $permissions = $accessTokenPayload['permissions'] ?? [];
                        $request->session()->put('token_permissions', $permissions);
                        // Important: also store in user_permission_names for compatibility with sidebar code
                        $request->session()->put('user_permission_names', $permissions);

                        \Log::info('JWT permissions stored in session', [
                            'permissions' => $permissions,
                            'count' => count($permissions)
                        ]);

                        // Store expiration time and calculate remaining time
                        $accessTokenExpiry = $accessTokenPayload['exp'] ?? null;
                        $request->session()->put('access_token_expiry', $accessTokenExpiry);

                        if ($accessTokenExpiry) {
                            $expiresIn = $accessTokenExpiry - time();
                            $request->session()->put('access_token_expires_in', $expiresIn);

                            \Log::info('Access token expiry info', [
                                'expiry_timestamp' => $accessTokenExpiry,
                                'expires_in_seconds' => $expiresIn,
                                'expires_in_minutes' => round($expiresIn / 60, 1)
                            ]);
                        }

                        \Log::info('Access token JWT payload extracted and stored', [
                            'user_id' => $accessTokenPayload['user_id'] ?? null,
                            'roles' => $accessTokenPayload['roles'] ?? [],
                            'permissions_count' => count($accessTokenPayload['permissions'] ?? [])
                        ]);
                    }

                    // Extract and store JWT payload data from refresh token
                    $refreshTokenPayload = $this->extractJwtPayload($refreshToken);
                    if ($refreshTokenPayload) {
                        $request->session()->put('refresh_token_payload', $refreshTokenPayload);

                        // Store refresh token expiration
                        $refreshTokenExpiry = $refreshTokenPayload['exp'] ?? null;
                        $request->session()->put('refresh_token_expiry', $refreshTokenExpiry);

                        if ($refreshTokenExpiry) {
                            $refreshExpiresIn = $refreshTokenExpiry - time();
                            $request->session()->put('refresh_token_expires_in', $refreshExpiresIn);

                            \Log::info('Refresh token expiry info', [
                                'expiry_timestamp' => $refreshTokenExpiry,
                                'expires_in_seconds' => $refreshExpiresIn,
                                'expires_in_days' => round($refreshExpiresIn / 86400, 1)
                            ]);
                        }
                    }

                    // Remember user preference
                    $rememberUser = $request->has('remember') || config('app.remember_users_by_default', true);
                    $request->session()->put('remember_user', $rememberUser);

                    // Calculate cookie lifetimes based on actual token expiration times
                    $accessTokenCookieMinutes = null;
                    $refreshTokenCookieMinutes = null;

                    if ($accessTokenExpiry) {
                        // Use the actual expiry time from the token, minus 1 minute for safety
                        $accessTokenCookieMinutes = max(1, ceil(($accessTokenExpiry - time() - 60) / 60));
                        \Log::info('Setting access token cookie lifetime from JWT payload', [
                            'minutes' => $accessTokenCookieMinutes
                        ]);
                    } else {
                        // Fallback only if JWT payload doesn't contain expiration
                        $accessTokenCookieMinutes = config('auth.access_token_cookie_lifetime', 60);
                        \Log::warning('Falling back to config for access token cookie lifetime');
                    }

                    if ($refreshTokenExpiry) {
                        // Use the actual expiry time from the token, minus 1 minute for safety
                        $refreshTokenCookieMinutes = max(1, ceil(($refreshTokenExpiry - time() - 60) / 60));
                        \Log::info('Setting refresh token cookie lifetime from JWT payload', [
                            'minutes' => $refreshTokenCookieMinutes
                        ]);
                    } else {
                        // Fallback only if JWT payload doesn't contain expiration
                        $refreshTokenCookieMinutes = config('auth.refresh_token_cookie_lifetime', 43200);
                        \Log::warning('Falling back to config for refresh token cookie lifetime');
                    }

                    // Store tokens in cookies with expiration based on JWT payload expiry times
                cookie()->queue(
                    'access_token',
                        $accessToken,
                        $accessTokenCookieMinutes
                );

                cookie()->queue(
                    'refresh_token',
                        $refreshToken,
                        $refreshTokenCookieMinutes
                    );

                    // Store JWT payloads in cookies
                    if ($accessTokenPayload) {
                        cookie()->queue(
                            'access_token_payload',
                            json_encode($accessTokenPayload),
                            $accessTokenCookieMinutes
                        );
                    }

                    if ($refreshTokenPayload) {
                        cookie()->queue(
                            'refresh_token_payload',
                            json_encode($refreshTokenPayload),
                            $refreshTokenCookieMinutes
                        );
                    }

                    // Get user permissions
                    $this->getPermissionsByRole($request);

                // Regenerate session and redirect to dashboard
                $request->session()->regenerate();

                // Check if user has dashboard permission
                if (hasPermission('dashboard:view')) {
                    return redirect()->route('dashboard');
                } else {
                    \Log::info('User does not have dashboard permission, looking for first accessible menu');

                    // Define menu routes based on permissions
                    $menuRoutes = [
                        'asset-subcategory:view' => 'categories',
                        'brand:view' => 'brands',
                        'building:view' => 'buildings',
                        'room:view' => 'rooms',
                        'vendor:view' => 'vendors',
                        'asset:view' => 'assets',
                        'asset-master:view' => 'asset-masters',
                        'calibration:view' => 'calibration',
                        'maintenance:view|maintenance-report:medical|maintenance-report:non-medical' => 'maintenance',
                        'complaint:view' => 'complaints',
                        'procurement:view' => 'procurements',
                        'report:finance' => 'report.finance',
                        'report:opname' => 'report.opname',
                        'report:depreciation' => 'report.depreciation',
                        'user:view' => 'users',
                        'role:view' => 'roles'
                    ];

                    // Loop through menu routes to find first accessible
                    foreach ($menuRoutes as $permission => $route) {
                        if (hasPermission($permission)) {
                            \Log::info("Redirecting user to first accessible menu: {$route}");
                            return redirect()->route($route);
                        }
                    }

                    // If no accessible menus found
                    \Log::warning('User has no accessible menus, logging out');
                    $this->clearAuthSession($request);
                    return redirect()->route('login')->with('error', 'Anda tidak memiliki akses ke menu apapun.');
                }
            }

                // If we reached here, login was unsuccessful
                \Log::warning('Login failed - API response indicates failure', [
                'employee_number' => $request->employee_number,
                    'errors' => $result['errors'] ?? null,
                    'message' => $result['message'] ?? null,
                    'status_code' => $statusCode
                ]);

                // Check for error in the format: {"success": false, "errors": "Pengguna sudah login."}
                if (isset($result['errors']) && is_string($result['errors'])) {
                    \Log::info('Login error - string error message received', [
                        'error_message' => $result['errors']
                    ]);
                    return redirect()->back()
                        ->withInput($request->except('password'))
                        ->with('error', $result['errors']);
                }

                // Check if there are specific error messages from the API (when errors is an array or object)
                if (isset($result['errors']) && is_array($result['errors'])) {
                $errors = [];

                    // Process each error field
                    foreach ($result['errors'] as $field => $messages) {
                        if (is_array($messages)) {
                            $errors[$field] = $messages;
                        } else {
                            $errors[$field] = [$messages];
                        }
                    }

                    // If the errors array is empty after processing, add a generic error
                    if (empty($errors)) {
                        return redirect()->back()
                            ->withInput($request->except('password'))
                            ->with('error', 'Gagal login. Silakan coba lagi.');
                    }

                    return redirect()->back()
                        ->withErrors($errors)
                        ->withInput($request->except('password'));
                }

                // If there's a specific message but no errors
                if (isset($result['message'])) {
                    return redirect()->back()
                        ->withInput($request->except('password'))
                        ->with('error', $result['message']);
                }

                // Default error message if no specific info provided
                return redirect()->back()
                    ->withInput($request->except('password'))
                    ->with('error', 'Gagal login. Silakan coba lagi.');

            } catch (ClientException $e) {
                \Log::error('Login API client exception', [
                    'employee_number' => $request->employee_number,
                    'error' => $e->getMessage(),
                    'status' => $e->getCode()
                ]);

                // Parse response if available
                $response = $e->getResponse();
                $errorMessage = 'Gagal terhubung ke server autentikasi';

                if ($response) {
                    $statusCode = $response->getStatusCode();
                    $body = (string) $response->getBody();
                    $result = json_decode($body, true);

                    // Process API errors similarly to the main code path
                    if (isset($result['errors']) && is_string($result['errors'])) {
                        $errorMessage = $result['errors'];
                    } elseif (isset($result['message'])) {
                        $errorMessage = $result['message'];
                    } elseif (isset($result['errors']) && is_array($result['errors'])) {
                        $errorMessage = is_array($result['errors'])
                            ? implode(', ', array_map(fn($v) => is_array($v) ? implode(', ', $v) : $v, $result['errors']))
                            : $result['errors'];
                    } elseif ($statusCode === 429) {
                        $errorMessage = 'Terlalu banyak percobaan login. Silakan coba lagi nanti.';
                    }
                }

                return redirect()
                    ->back()
                    ->with('error', $errorMessage)
                    ->withInput($request->except('password'));

            } catch (ConnectException $e) {
            \Log::error('Login API connection error', [
                'employee_number' => $request->employee_number,
                'error' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                    ->with('error', 'Gagal terhubung ke server. Periksa koneksi Anda dan coba lagi.')
                ->withInput($request->except('password'));
            }

        } catch (\Exception $e) {
            \Log::error('Login unexpected error', [
                'employee_number' => $request->employee_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat proses login: ' . $e->getMessage())
                ->withInput($request->except('password'));
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
                \Log::warning('Invalid JWT token format');
                return null;
            }

            $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1]));
            $decodedPayload = json_decode($payload, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::warning('Error decoding JWT payload', ['error' => json_last_error_msg()]);
                return null;
            }

            \Log::info('JWT payload structure:', [
                'keys' => array_keys($decodedPayload),
                'payload' => $decodedPayload,
                'permissions_type' => gettype($decodedPayload['permissions'] ?? null),
                'permissions' => $decodedPayload['permissions'] ?? null
            ]);

            return $decodedPayload;
        } catch (\Exception $e) {
            \Log::error('Error extracting JWT payload', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get permissions by role for the logged-in user
     */
    public function getPermissionsByRole(Request $request)
    {
        try {
            $accessToken = $request->session()->get('access_token');

            if (!$accessToken) {
                \Log::warning('Cannot fetch permissions - no access token available');
                return false;
            }

            // Check if we already have permissions from the JWT token payload
            $tokenPermissions = $request->session()->get('token_permissions', []);
            if (!empty($tokenPermissions)) {
                \Log::info('Using permissions from JWT token payload', [
                    'count' => count($tokenPermissions)
                ]);

                // Store JWT token permissions in session for use in views
                $request->session()->put('user_permissions', $tokenPermissions);
                $request->session()->put('user_permission_names', $tokenPermissions);

                // Use the access token expiry time for the permissions cookie
                $accessTokenExpiry = $request->session()->get('access_token_expiry');
                $cookieLifetime = null;

                // Calculate cookie lifetime in minutes based on JWT expiry
                if ($accessTokenExpiry) {
                    // Use the actual expiry time from the token, minus 1 minute for safety
                    $cookieLifetime = max(1, ceil(($accessTokenExpiry - time() - 60) / 60));
                    \Log::info('Setting permissions cookie lifetime from JWT payload', [
                        'minutes' => $cookieLifetime,
                        'expires_at' => date('Y-m-d H:i:s', $accessTokenExpiry)
                    ]);
                } else {
                    // Fallback only if JWT payload doesn't contain expiration
                    $cookieLifetime = $request->session()->get('remember_user', false)
                        ? config('auth.remembered_access_token_cookie_lifetime', 1440)
                        : config('auth.access_token_cookie_lifetime', 60);
                    \Log::warning('Falling back to config for permissions cookie lifetime', [
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

            // If no permissions in JWT, fetch them from API as fallback
            $client = new Client();
            $apiBaseUrl = config('services.api.base_url');

            \Log::info('Fetching permissions by role from API');

            $response = $client->get("{$apiBaseUrl}/permissions/by-roles", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken
                ],
                'http_errors' => false
            ]);

            $statusCode = $response->getStatusCode();
            $responseData = json_decode($response->getBody()->getContents(), true);

            \Log::info('Permissions API response:', [
                'status' => $statusCode,
                'success' => $responseData['success'] ?? false
            ]);

            if ($statusCode === 200 && isset($responseData['success']) && $responseData['success']) {
                $permissions = $responseData['data'] ?? [];

                // Store permissions in session
                $request->session()->put('user_permissions', $permissions);

                // Extract permission names for easier access checking
                $permissionNames = array_map(function ($permission) {
                    return $permission['permission_name'];
                }, $permissions);

                $request->session()->put('user_permission_names', $permissionNames);

                // Use the access token expiry time for the permissions cookie
                $accessTokenExpiry = $request->session()->get('access_token_expiry');
                $cookieLifetime = null;

                // Calculate cookie lifetime in minutes based on JWT expiry
                if ($accessTokenExpiry) {
                    // Use the actual expiry time from the token, minus 1 minute for safety
                    $cookieLifetime = max(1, ceil(($accessTokenExpiry - time() - 60) / 60));
                    \Log::info('Setting permissions cookie lifetime from JWT payload', [
                        'minutes' => $cookieLifetime,
                        'expires_at' => date('Y-m-d H:i:s', $accessTokenExpiry)
                    ]);
                } else {
                    // Fallback only if JWT payload doesn't contain expiration
                    $cookieLifetime = $request->session()->get('remember_user', false)
                        ? config('auth.remembered_access_token_cookie_lifetime', 1440)
                        : config('auth.access_token_cookie_lifetime', 60);
                    \Log::warning('Falling back to config for permissions cookie lifetime', [
                        'minutes' => $cookieLifetime,
                        'reason' => 'Missing JWT expiration'
                    ]);
                }

                cookie()->queue(
                    'user_permissions',
                    json_encode($permissions),
                    $cookieLifetime
                );

                \Log::info('Permissions stored successfully', ['count' => count($permissions)]);
                return true;
            } else {
                \Log::warning('Failed to fetch permissions', [
                    'status' => $statusCode,
                    'response' => $responseData
                ]);
                return false;
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching permissions', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Logout the user
     */
    public function logout(Request $request)
    {
        try {
            $accessToken = $request->session()->get('access_token');

            if ($accessToken) {
                $client = new Client();
                $apiBaseUrl = config('services.api.base_url');

                // Make the logout request with bearer token authentication
                $response = $client->post("{$apiBaseUrl}/auth/logout", [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken
                    ],
                    'http_errors' => false // Prevent exceptions on error responses
                ]);

                $statusCode = $response->getStatusCode();
                $responseData = json_decode($response->getBody()->getContents(), true);

                \Log::info('Logout API response:', [
                    'status' => $statusCode,
                    'success' => $responseData['success'] ?? false,
                    'errors' => $responseData['errors'] ?? null
                ]);

                // If token expired, try refreshing it and retry logout
                if ($statusCode === 401 || $statusCode === 403) {
                    \Log::info('Access token expired during logout, attempting to refresh');

                    // Try to refresh the token
                    $apiService = app(ApiService::class);
                    if ($apiService->refreshToken()) {
                        // Get the new access token
                        $newAccessToken = $request->session()->get('access_token');

                        // Retry logout with new token
                        $retryResponse = $client->post("{$apiBaseUrl}/auth/logout", [
                            'headers' => [
                                'Authorization' => 'Bearer ' . $newAccessToken
                            ],
                            'http_errors' => false
                        ]);

                        $retryStatusCode = $retryResponse->getStatusCode();
                        $retryData = json_decode($retryResponse->getBody()->getContents(), true);

                        \Log::info('Logout retry API response:', [
                            'status' => $retryStatusCode,
                            'success' => $retryData['success'] ?? false,
                            'errors' => $retryData['errors'] ?? null
                        ]);
                    } else {
                        \Log::warning('Token refresh failed during logout');
                    }
                } else if ($statusCode !== 200) {
                    \Log::warning('Logout API returned non-200 status code:', [
                        'status_code' => $statusCode,
                        'response' => $responseData
                    ]);
                }
            } else {
                \Log::warning('Logout attempted without access token in session');
            }

            // Clear session data regardless of API response
            $this->clearAuthSession($request);
            return redirect()->route('login');
        } catch (GuzzleException $e) {
            \Log::error('Logout API connection error', [
                'error' => $e->getMessage()
            ]);

            // Even if API call fails, clear session data
            $this->clearAuthSession($request);
            return redirect()->route('login')->withErrors(['error' => 'Failed to logout properly, but session has been cleared']);
        }
    }

    /**
     * Helper method to clear authentication session data
     */
    private function clearAuthSession(Request $request)
    {
        // Update to also forget token payload data
        $request->session()->forget([
            'access_token', 'refresh_token', 'employee_number',
            'token_validated_at', 'user_id', 'remember_user',
            'user_permissions', 'user_permission_names',
            'access_token_payload', 'refresh_token_payload', 'is_active', 'user_roles',
            'token_permissions', 'access_token_expiry', 'refresh_token_expiry',
            'access_token_expires_in', 'refresh_token_expires_in'
        ]);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Also clear the cookies
        if ($request->cookie('refresh_token')) {
            cookie()->queue(cookie()->forget('refresh_token'));
        }
        if ($request->cookie('access_token')) {
            cookie()->queue(cookie()->forget('access_token'));
        }
        if ($request->cookie('user_permissions')) {
            cookie()->queue(cookie()->forget('user_permissions'));
        }
        if ($request->cookie('access_token_payload')) {
            cookie()->queue(cookie()->forget('access_token_payload'));
        }
        if ($request->cookie('refresh_token_payload')) {
            cookie()->queue(cookie()->forget('refresh_token_payload'));
        }
    }
}
