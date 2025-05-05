<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Services\ApiService;

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
                    return redirect()->route('dashboard');
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
                        return redirect()->route('dashboard');
                    } else {
                        \Log::warning('Auto-login failed - invalid refresh token during login attempt');
                        // If refresh token is invalid, forget the cookie
                        cookie()->queue(cookie()->forget('refresh_token'));
                    }
                } catch (\Exception $e) {
                    \Log::error('Auto-login error during login attempt:', [
                        'error' => $e->getMessage()
                    ]);
                    cookie()->queue(cookie()->forget('refresh_token'));
                }
            }

            \Log::info('Login attempt', ['employee_number' => $request->employee_number]);

            $validator = Validator::make($request->all(), [
                'employee_number' => 'required',
                'password' => 'required'
            ], [
                'employee_number.required' => 'Employee Number is required',
                'password.required' => 'Password is required'
            ]);

            if ($validator->fails()) {
                \Log::warning('Login validation failed', ['errors' => $validator->errors()->toArray()]);
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput($request->except('password'));
            }

            $client = new Client();
            \Log::info('Sending login request to API');

            $response = $client->post(config('services.api.base_url') . '/auth/login', [
                'json' => [
                    'employee_number' => $request->employee_number,
                    'password' => $request->password,
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            \Log::info('Login API response', [
                'success' => $result['success'],
                'errors' => $result['errors'] ?? null
            ]);

            if ($result['success']) {
                // Store tokens in both session and cookies
                $request->session()->put('access_token', $result['data']['accessToken']);
                $request->session()->put('refresh_token', $result['data']['refreshToken']);
                $request->session()->put('employee_number', $request->employee_number);
                $request->session()->put('token_validated_at', now()->timestamp);

                // Store user ID in session for logout functionality
                if (isset($result['data']['user']) && isset($result['data']['user']['user_id'])) {
                    $request->session()->put('user_id', $result['data']['user']['user_id']);
                }

                // Always store tokens in cookies for better persistence
                cookie()->queue(
                    'access_token',
                    $result['data']['accessToken'],
                    config('auth.access_token_cookie_lifetime', 60) // 1 hour
                );

                cookie()->queue(
                    'refresh_token',
                    $result['data']['refreshToken'],
                    config('auth.refresh_token_cookie_lifetime', 43200) // 30 days
                );

                // Remember user preference
                $rememberUser = $request->has('remember') || config('app.remember_users_by_default', true);
                $request->session()->put('remember_user', $rememberUser);

                // Regenerate session and redirect to dashboard
                $request->session()->regenerate();
                return redirect()->route('dashboard');
            }

            \Log::warning('Login failed - invalid credentials', [
                'employee_number' => $request->employee_number,
                'errors' => $result['errors'] ?? null
            ]);

            // Check if there are specific error messages from the API
            if (isset($result['errors'])) {
                $errors = [];

                // Handle different error formats (string or array)
                if (is_array($result['errors'])) {
                    foreach ($result['errors'] as $field => $messages) {
                        if (is_array($messages)) {
                            $errors[$field] = $messages;
                        } else {
                            $errors[$field] = [$messages];
                        }
                    }
                } else {
                    // If errors is a string, assign it to a general field
                    $errors['error'] = [$result['errors']];
                }

                throw ValidationException::withMessages($errors);
            }

            // Default error message if no specific errors are provided
            throw ValidationException::withMessages([
                'employee_number' => ['The provided credentials are incorrect.'],
            ]);

        } catch (GuzzleException $e) {
            \Log::error('Login API connection error', [
                'employee_number' => $request->employee_number,
                'error' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to connect to authentication server'])
                ->withInput($request->except('password'));
        } catch (\Exception $e) {
            \Log::error('Login error', [
                'employee_number' => $request->employee_number,
                'error' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput($request->except('password'));
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
        // Update to also forget remember_user
        $request->session()->forget(['access_token', 'refresh_token', 'employee_number', 'token_validated_at', 'user_id', 'remember_user']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Also clear the refresh token cookie
        if ($request->cookie('refresh_token')) {
            cookie()->queue(cookie()->forget('refresh_token'));
        }
        if ($request->cookie('access_token')) {
            cookie()->queue(cookie()->forget('access_token'));
        }
    }
}
