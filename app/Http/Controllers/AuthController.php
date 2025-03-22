<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

            if ($apiService->refreshToken()) {
                \Log::info('Auto-login successful via refresh token from login page');
                return redirect()->route('dashboard');
            } else {
                // If refresh token is invalid, forget the cookie
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
            if (!$request->filled('email') && !$request->filled('password') && $request->cookie('refresh_token')) {
                $apiService = app(ApiService::class);
                session(['refresh_token' => $request->cookie('refresh_token')]);

                if ($apiService->refreshToken()) {
                    \Log::info('Auto-login successful via refresh token');
                    return redirect()->route('dashboard');
                } else {
                    // If refresh token is invalid, forget the cookie
                    cookie()->queue(cookie()->forget('refresh_token'));
                }
            }

            \Log::info('Login attempt', ['email' => $request->email]);

            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required'
            ], [
                'email.required' => 'User ID is required',
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
                    'email' => $request->email,
                    'password' => $request->password,
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            \Log::info('Login API response', ['status' => $result['status']]);

            if ($result['status']) {
                // Store tokens in both session and cookies
                $request->session()->put('access_token', $result['data']['accessToken']);
                $request->session()->put('refresh_token', $result['data']['refreshToken']);
                $request->session()->put('email', $request->email);
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

            \Log::warning('Login failed - invalid credentials', ['email' => $request->email]);
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);

        } catch (GuzzleException $e) {
            \Log::error('Login API connection error', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to connect to authentication server'])
                ->withInput($request->except('password'));
        } catch (\Exception $e) {
            \Log::error('Login error', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput($request->except('password'));
        }
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        try {
            // Get user ID from session
            $userId = $request->session()->get('user_id');

            if ($userId) {
                \Log::info('Logging out user', ['user_id' => $userId]);

                $client = new Client();
                $response = $client->post(config('services.api.base_url') . '/auth/logout/' . $userId, [
                    'http_errors' => false // Prevent exceptions on error responses
                ]);

                $statusCode = $response->getStatusCode();
                \Log::info('Logout API response status: ' . $statusCode);

                if ($statusCode !== 200) {
                    \Log::warning('Logout API returned non-200 status code: ' . $statusCode);
                }
            } else {
                \Log::warning('Logout attempted without user_id in session');
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
        $request->session()->forget(['access_token', 'refresh_token', 'email', 'token_validated_at', 'user_id', 'remember_user']);
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

    /**
     * Refresh the access token
     */
    // public function refreshToken(Request $request)
    // {
    //     try {
    //         $refreshToken = $request->session()->get('refresh_token') ?: $request->cookie('refresh_token');
    //         if (!$refreshToken) {
    //             // No refresh token available, clear session and indicate authentication failure
    //             $this->clearAuthSession($request);
    //             return response()->json(['success' => false, 'message' => 'No refresh token found', 'redirect' => route('login')], 401);
    //         }

    //         $client = new Client();

    //         \Log::info('Attempting to refresh token with refresh token');

    //         $response = $client->post(config('services.api.base_url') . '/auth/refresh-token', [
    //             'json' => [
    //                 'refreshToken' => $refreshToken
    //             ],
    //             'http_errors' => false
    //         ]);

    //         $statusCode = $response->getStatusCode();
    //         \Log::info('Refresh token response status: ' . $statusCode);

    //         if ($statusCode !== 200) {
    //             // Refresh token is invalid or expired, clear session and redirect to login
    //             \Log::warning('Refresh token failed with status: ' . $statusCode);
    //             $this->clearAuthSession($request);
    //             return response()->json(['success' => false, 'message' => 'Token refresh failed', 'redirect' => route('login')], 401);
    //         }

    //         $result = json_decode($response->getBody()->getContents(), true);
    //         \Log::info('Refresh token API response', ['status' => $result['status'] ?? 'unknown']);

    //         if (isset($result['status']) && $result['status']) {
    //             // Update the tokens in session
    //             $request->session()->put('access_token', $result['data']['accessToken']);
    //             $request->session()->put('refresh_token', $result['data']['refreshToken']);
    //             $request->session()->put('token_refreshed_at', now()->timestamp);

    //             // Also update the refresh token cookie if remember me was enabled or if cookie already exists
    //             if (session('remember_user') || $request->cookie('refresh_token')) {
    //                 \Log::info('Setting refresh token cookie');
    //                 $cookie = cookie(
    //                     'refresh_token',
    //                     $result['data']['refreshToken'],
    //                     config('auth.refresh_token_cookie_lifetime', 43200) // 30 days in minutes
    //                 );

    //                 cookie()->queue($cookie);
    //             }

    //             return response()->json(['success' => true]);
    //         }

    //         // Refresh token request was processed but returned unsuccessful status
    //         \Log::warning('Refresh token request was unsuccessful');
    //         $this->clearAuthSession($request);
    //         return response()->json(['success' => false, 'message' => 'Failed to refresh token', 'redirect' => route('login')], 401);
    //     } catch (\Exception $e) {
    //         \Log::error('Token refresh exception', ['error' => $e->getMessage()]);
    //         $this->clearAuthSession($request);
    //         return response()->json(['success' => false, 'message' => 'Token refresh failed', 'redirect' => route('login')], 500);
    //     }
    // }

    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        return view('Auth.forget_password');
    }

    /**
     * Handle forgot password request
     */
    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ], [
                'email.required' => 'Email is required',
                'email.email' => 'Please enter a valid email address',
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $client = new Client();
            \Log::info('Sending forgot password request to API', ['email' => $request->email]);

            $response = $client->post(config('services.api.base_url') . '/auth/forgot-password', [
                'json' => [
                    'email' => $request->email,
                    'reset_url' => route('password.reset', ['token' => 'TOKEN'])
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            \Log::info('Forgot password API response', ['status' => $result['status'] ?? null]);

            if (isset($result['status']) && $result['status']) {
                return redirect()
                    ->back()
                    ->with('success', 'Password reset link has been sent to your email');
            }

            return redirect()
                ->back()
                ->withErrors(['email' => $result['message'] ?? 'Failed to process your request'])
                ->withInput();

        } catch (GuzzleException $e) {
            \Log::error('Forgot password API connection error', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to connect to authentication server'])
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Forgot password error', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show reset password form
     */
    public function showResetPassword(Request $request, $token)
    {
        return view('Auth.create_password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Handle reset password request
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ], [
                'token.required' => 'Token is missing',
                'email.required' => 'Email is required',
                'email.email' => 'Please enter a valid email address',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 8 characters',
                'password.confirmed' => 'Password confirmation does not match',
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $client = new Client();
            \Log::info('Sending reset password request to API');

            $response = $client->post(config('services.api.base_url') . '/auth/reset-password', [
                'json' => [
                    'email' => $request->email,
                    'token' => $request->token,
                    'password' => $request->password
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            \Log::info('Reset password API response', ['status' => $result['status'] ?? null]);

            if (isset($result['status']) && $result['status']) {
                return redirect()
                    ->route('login')
                    ->with('success', 'Your password has been reset successfully');
            }

            return redirect()
                ->back()
                ->withErrors(['error' => $result['message'] ?? 'Failed to reset password'])
                ->withInput();

        } catch (GuzzleException $e) {
            \Log::error('Reset password API connection error', [
                'error' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to connect to authentication server']);
        } catch (\Exception $e) {
            \Log::error('Reset password error', [
                'error' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Verify if the token is valid
     */
    public function verifyToken(Request $request)
    {
        try {
            // Use API service to validate token
            $apiService = app(ApiService::class);
            $isValid = $apiService->validateToken();

            if ($isValid) {
                return response()->json(['status' => true, 'message' => 'Token is valid']);
            }

            // If we reach here, token validation failed but we may have refreshed it
            if (session('access_token')) {
                // We managed to refresh token
                return response()->json(['status' => true, 'message' => 'Token has been refreshed']);
            }

            // Token validation failed and couldn't refresh
            $this->clearAuthSession($request);

            return response()->json([
                'status' => false,
                'message' => 'Your session has expired. Please login again.'
            ], 401);
        } catch (\Exception $e) {
            \Log::error('Token verification failed', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Authentication error'
            ], 500);
        }
    }
}
