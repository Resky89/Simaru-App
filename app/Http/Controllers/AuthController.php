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

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('Auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        try {
            \Log::info('Login attempt', ['user_id' => $request->user_id]);

            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'password' => 'required'
            ], [
                'user_id.required' => 'User ID is required',
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

            $response = $client->post('https://12d9aa80-068d-4c47-a664-b62f5cad5ec5.mock.pstmn.io/auth/login', [
                'json' => [
                    'user_id' => $request->user_id,
                    'password' => $request->password,
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            \Log::info('Login API response', ['success' => $result['success']]);

            if ($result['success']) {
                // Store tokens in session
                $request->session()->put('access_token', $result['data']['accessToken']);
                $request->session()->put('refresh_token', $result['data']['refreshToken']);
                $request->session()->put('user_id', $request->user_id);

                \Log::info('Login successful', ['user_id' => $request->user_id]);

                // Regenerate session and redirect to dashboard
                $request->session()->regenerate();
                return redirect()->route('dashboard');
            }

            \Log::warning('Login failed - invalid credentials', ['user_id' => $request->user_id]);
            throw ValidationException::withMessages([
                'user_id' => ['The provided credentials are incorrect.'],
            ]);

        } catch (GuzzleException $e) {
            \Log::error('Login API connection error', [
                'user_id' => $request->user_id,
                'error' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to connect to authentication server'])
                ->withInput($request->except('password'));
        } catch (\Exception $e) {
            \Log::error('Login error', [
                'user_id' => $request->user_id,
                'error' => $e->getMessage()
            ]);
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput($request->except('password'));
        }
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        return view('Auth.register');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        try {
            $client = new Client();
            $client->post('https://12d9aa80-068d-4c47-a664-b62f5cad5ec5.mock.pstmn.io/auth/logout', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('access_token'),
                ]
            ]);

            // Clear session data
            $request->session()->forget(['access_token', 'refresh_token', 'user_id']);
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');
        } catch (GuzzleException $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to logout properly']);
        }
    }
}
