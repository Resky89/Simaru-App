<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class UserController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display a listing of roles and users.
     */
    public function index(Request $request)
    {
        try {
            // Fetch roles
            $rolePage = $request->input('role_page', 1);
            $roleLimit = $request->input('role_limit', 10);

            $roleResult = $this->apiService->request('GET', '/roles', [
                'query' => [
                    'page' => $rolePage,
                    'limit' => $roleLimit,
                    'sort_by' => 'role_id',
                    'sort_order' => 'asc'
                ]
            ]);

            // Fetch users with the new response format
            $userPage = $request->input('user_page', 1);
            $userLimit = $request->input('user_limit', 10);
            $searchQuery = $request->input('query', '');

            $userQueryParams = [
                'page' => $userPage,
                'limit' => $userLimit,
                'sort_by' => 'user_id',
                'sort_order' => 'asc' // Sort from lowest ID (oldest) to highest ID (newest)
            ];

            // Add search parameter if provided
            if (!empty($searchQuery)) {
                $userQueryParams['search'] = $searchQuery;
            }

            $userResult = $this->apiService->request('GET', '/users', [
                'query' => $userQueryParams
            ]);

            // Check if we got an error response from the ApiService
            if (
                (isset($roleResult['errors']) && is_string($roleResult['errors']) &&
                in_array($roleResult['errors'], ['auth_failed', 'session_expired'])) ||
                (isset($userResult['errors']) && is_string($userResult['errors']) &&
                in_array($userResult['errors'], ['auth_failed', 'session_expired']))
            ) {
                $errorMessage = $roleResult['errors'] ?? $userResult['errors'] ?? 'Authentication failed';
                \Log::warning('Authentication error during roles and users retrieval:', [
                    'errors' => $errorMessage
                ]);

                // Return JSON if requested
                if ($request->expectsJson() || $request->is('api/*') || $request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => is_string($errorMessage) ? $errorMessage : 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($errorMessage) ? $errorMessage : 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (
                (!isset($roleResult['success']) || $roleResult['success'] !== true) ||
                (!isset($userResult['success']) || $userResult['success'] !== true)
            ) {
                $errorData = $roleResult['errors'] ?? $userResult['errors'] ?? 'Failed to fetch data';

                \Log::warning('Error during user data retrieval:', [
                    'role_success' => $roleResult['success'] ?? false,
                    'user_success' => $userResult['success'] ?? false,
                    'errors' => $errorData
                ]);

                // Format error message for view
                $errorMessage = '';
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $errorMessage .= implode(', ', $messages) . '; ';
                        } else {
                            $errorMessage .= $messages . '; ';
                        }
                    }
                } else {
                    $errorMessage = $errorData;
                }

                // Return JSON if requested
                if ($request->expectsJson() || $request->is('api/*') || $request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $errorData
                    ], 400);
                }

                return view('Account.User', [
                    'roles' => [
                        'data' => [],
                        'pagination' => null
                    ],
                    'users' => [],
                    'user_pagination' => null,
                    'error' => $errorMessage
                ]);
            }

            $roles = $roleResult['data'] ?? [];
            $users = $userResult['data'] ?? [];

            // Format pagination for roles
            $rolePagination = null;
            if (isset($roleResult['pagination'])) {
                $pagination = $roleResult['pagination'];
                $rolePagination = [
                    'current_page' => $pagination['current_page'] ?? 1,
                    'last_page' => ceil(($pagination['total_items'] ?? 0) / ($pagination['limit'] ?? 10)),
                    'from' => (($pagination['current_page'] ?? 1) - 1) * ($pagination['limit'] ?? 10) + 1,
                    'to' => min(($pagination['current_page'] ?? 1) * ($pagination['limit'] ?? 10), $pagination['total_items'] ?? 0),
                    'total' => $pagination['total_items'] ?? 0,
                    'per_page' => $pagination['limit'] ?? 10,
                    'next_page_url' => $pagination['has_next'] ? url()->current() . '?role_page=' . ($pagination['current_page'] + 1) : null,
                    'prev_page_url' => $pagination['has_prev'] ? url()->current() . '?role_page=' . ($pagination['current_page'] - 1) : null,
                ];
            }

            // Format pagination for users
            $userPagination = null;
            if (isset($userResult['pagination'])) {
                $pagination = $userResult['pagination'];
                $userPagination = [
                    'current_page' => $pagination['current_page'] ?? 1,
                    'last_page' => ceil(($pagination['total_items'] ?? 0) / ($pagination['limit'] ?? 10)),
                    'from' => (($pagination['current_page'] ?? 1) - 1) * ($pagination['limit'] ?? 10) + 1,
                    'to' => min(($pagination['current_page'] ?? 1) * ($pagination['limit'] ?? 10), $pagination['total_items'] ?? 0),
                    'total' => $pagination['total_items'] ?? 0,
                    'per_page' => $pagination['limit'] ?? 10,
                    'next_page_url' => $pagination['has_next'] ? url()->current() . '?user_page=' . ($pagination['current_page'] + 1) : null,
                    'prev_page_url' => $pagination['has_prev'] ? url()->current() . '?user_page=' . ($pagination['current_page'] - 1) : null,
                ];
            }

            // Return JSON if requested
            if ($request->expectsJson() || $request->is('api/*') || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $users,
                    'pagination' => $userPagination
                ]);
            }

            return view('Account.User', [
                'roles' => [
                    'data' => $roles,
                    'pagination' => $rolePagination
                ],
                'users' => $users,
                'user_pagination' => $userPagination
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch user data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return JSON if requested
            if ($request->expectsJson() || $request->is('api/*') || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch data: ' . $e->getMessage()
                ], 500);
            }

            return view('Account.User', [
                'roles' => [
                    'data' => [],
                    'pagination' => null
                ],
                'users' => [],
                'user_pagination' => null,
                'error' => 'Failed to fetch data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get users endpoint
     */
    public function getUsers(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            $result = $this->apiService->request('GET', '/users', [
                'query' => [
                    'page' => $page,
                    'limit' => $limit,
                    'sort_by' => 'user_id',
                    'sort_order' => 'asc' // Sort from lowest ID (oldest) to highest ID (newest)
                ]
            ]);

            // Check if we got an error response from the ApiService
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during users retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to fetch users';

                \Log::warning('Error during users retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                // Format error message for view
                $errorMessage = '';
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $errorMessage .= implode(', ', $messages) . '; ';
                        } else {
                            $errorMessage .= $messages . '; ';
                        }
                    }
                } else {
                    $errorMessage = $errorData;
                }

                return view('Account.User', [
                    'users' => [],
                    'error' => $errorMessage
                ]);
            }

            return view('Account.User', [
                'users' => $result['data'] ?? [],
                'user_pagination' => $result['pagination'] ?? null
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch users', [
                'error' => $e->getMessage()
            ]);

            return view('Account.User', [
                'users' => [],
                'error' => 'Failed to fetch users: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created user.
     */
    public function storeUser(Request $request)
    {
        try {
            // Log the request data
            \Log::info('Attempting to create user with data:', [
                'request_data' => $request->except('password')
            ]);

            // Updated endpoint for user creation with updated fields
            $result = $this->apiService->request('POST', '/users', [
                'json' => [
                    'employee_number' => $request->input('employee_number'),
                    'password' => $request->input('password'),
                    'role_ids' => array_map('intval', (array) $request->input('role_ids', [])),
                    'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true
                ]
            ]);

            // Log the API response (sensitive data redacted)
            \Log::info('API response for user creation:', [
                'api_response_success' => $result['success'] ?? false
            ]);

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during user creation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to create user';

                \Log::warning('Error during user creation:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                // Format error message for redirect
                $errorMessage = '';
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $errorMessage .= implode(', ', $messages) . '; ';
                        } else {
                            $errorMessage .= $messages . '; ';
                        }
                    }
                } else {
                    $errorMessage = $errorData;
                }

                return redirect()->back()
                    ->withInput($request->except('password'))
                    ->with('error', $errorMessage);
            }

            // Successfully created
            \Log::info('User created successfully');
            return redirect()->route('user')
                ->with('success', $result['message'] ?? 'User created successfully');
        } catch (\Exception $e) {
            \Log::error('Exception during user creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_data' => $request->except(['_token', 'password'])
            ]);

            return redirect()->back()
                ->withInput($request->except('password'))
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified user.
     */
    public function updateUser(Request $request, $id)
    {
        try {
            // Log the request data
            \Log::info('Attempting to update user with data:', [
                'user_id' => $id,
                'request_data' => $request->except('password')
            ]);

            // Prepare the JSON payload
            $jsonPayload = [
                'employee_number' => $request->input('employee_number'),
                'role_ids' => array_map('intval', (array) $request->input('role_ids', [])),
                'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true
            ];

            // Add password only if provided
            if ($request->filled('password')) {
                $jsonPayload['password'] = $request->input('password');
            }

            $result = $this->apiService->request('PUT', "/users/{$id}", [
                'json' => $jsonPayload
            ]);

            // Log the API response (sensitive data redacted)
            \Log::info('API response for user update:', [
                'api_response_success' => $result['success'] ?? false
            ]);

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during user update:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to update user';

                \Log::warning('Error during user update:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                // Format error message for redirect
                $errorMessage = '';
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $errorMessage .= implode(', ', $messages) . '; ';
                        } else {
                            $errorMessage .= $messages . '; ';
                        }
                    }
                } else {
                    $errorMessage = $errorData;
                }

                return redirect()->back()
                    ->withInput($request->except('password'))
                    ->with('error', $errorMessage);
            }

            // Successfully updated
            \Log::info('User updated successfully');
            return redirect()->route('user')
                ->with('success', $result['message'] ?? 'User updated successfully');
        } catch (\Exception $e) {
            \Log::error('Exception during user update:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $id,
                'user_data' => $request->except(['_token', '_method', 'password'])
            ]);

            return redirect()->back()
                ->withInput($request->except('password'))
                ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroyUser($id)
    {
        try {
            // Log the request
            \Log::info('Attempting to delete user:', [
                'user_id' => $id
            ]);

            $result = $this->apiService->request('DELETE', "/users/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during user deletion:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to delete user';

                \Log::warning('Error during user deletion:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                // Format error message for redirect
                $errorMessage = '';
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $errorMessage .= implode(', ', $messages) . '; ';
                        } else {
                            $errorMessage .= $messages . '; ';
                        }
                    }
                } else {
                    $errorMessage = $errorData;
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Successfully deleted
            \Log::info('User deleted successfully');
            return redirect()->route('user')
                ->with('success', $result['message'] ?? 'User deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Exception during user deletion:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }
}
