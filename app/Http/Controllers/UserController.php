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
            // Fetch users with the new response format
            $userPage = $request->input('user_page', 1);
            $userLimit = $request->input('user_limit', 10);
            $searchQuery = $request->input('search', '');
            $status = $request->input('status', '');
            $sort = $request->input('sort', '');

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

            // Add status filter if provided
            if (!empty($status)) {
                $userQueryParams['is_active'] = $status === 'active' ? true : false;
            }

            // Apply custom sorting
            if (!empty($sort)) {
                switch ($sort) {
                    case 'id_asc':
                        $userQueryParams['sort_by'] = 'user_id';
                        $userQueryParams['sort_order'] = 'asc';
                        break;
                    case 'id_desc':
                        $userQueryParams['sort_by'] = 'user_id';
                        $userQueryParams['sort_order'] = 'desc';
                        break;
                }
            }

            $userResult = $this->apiService->request('GET', '/users', [
                'query' => $userQueryParams
            ]);

            // Check if we got an error response from the ApiService
            if (isset($userResult['errors']) && is_string($userResult['errors']) &&
                in_array($userResult['errors'], ['auth_failed', 'session_expired']))
            {
                $errorMessage = $userResult['errors'] ?? 'Authentication failed';
                \Log::warning('Authentication error during users retrieval:', [
                    'errors' => $errorMessage
                ]);

                // Return JSON if requested
                if ($request->expectsJson() ||  $request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => is_string($errorMessage) ? $errorMessage : 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($errorMessage) ? $errorMessage : 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($userResult['success']) || $userResult['success'] !== true)
            {
                $errorData = $userResult['errors'] ?? 'Failed to fetch data';

                \Log::warning('Error during user data retrieval:', [
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
                if ($request->expectsJson() ||  $request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $errorData
                    ], 400);
                }

                return view('Account.User', [
                    'users' => [],
                    'user_pagination' => null,
                    'error' => $errorMessage
                ]);
            }

            $users = $userResult['data'] ?? [];

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
                    'next_page_url' => $pagination['has_next'] ? url()->current() . '?' . http_build_query(array_merge($request->except('user_page'), ['user_page' => ($pagination['current_page'] + 1)])) : null,
                    'prev_page_url' => $pagination['has_prev'] ? url()->current() . '?' . http_build_query(array_merge($request->except('user_page'), ['user_page' => ($pagination['current_page'] - 1)])) : null,
                ];
            }

            // Return JSON if requested
            if ($request->expectsJson() ||  $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'users' => $users,
                    'success' => true,
                    'data' => $users,
                    'pagination' => $userPagination
                ]);
            }

            // Get roles for the view
            $roles = [];
            try {
                $roleResult = $this->apiService->request('GET', '/roles', [
                    'query' => [
                        'page' => 1,
                        'limit' => 100, // Get enough roles for dropdowns
                        'sort_by' => 'role_id',
                        'sort_order' => 'asc'
                    ]
                ]);

                if (isset($roleResult['success']) && $roleResult['success'] === true) {
                    $roles = $roleResult['data'] ?? [];
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to fetch roles for dropdown', [
                    'error' => $e->getMessage()
                ]);
            }

            return view('Account.User', [
                'roles' => [
                    'data' => $roles,
                    'pagination' => null
                ],
                'users' => $users,
                'user_pagination' => $userPagination,
                'filters' => [
                    'search' => $searchQuery,
                    'status' => $status,
                    'sort' => $sort
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch user data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return JSON if requested
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
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
            $searchQuery = $request->input('search', '');
            $status = $request->input('status', '');
            $sort = $request->input('sort', '');

            $queryParams = [
                'page' => $page,
                'limit' => $limit,
                'sort_by' => 'user_id',
                'sort_order' => 'asc' // Sort from lowest ID (oldest) to highest ID (newest)
            ];

            // Add search parameter if provided
            if (!empty($searchQuery)) {
                $queryParams['search'] = $searchQuery;
            }

            // Add status filter if provided
            if (!empty($status)) {
                $queryParams['is_active'] = $status === 'active' ? true : false;
            }

            // Apply custom sorting
            if (!empty($sort)) {
                switch ($sort) {
                    case 'id_asc':
                        $queryParams['sort_by'] = 'user_id';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'id_desc':
                        $queryParams['sort_by'] = 'user_id';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    case 'emp_asc':
                        $queryParams['sort_by'] = 'employee_number';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'emp_desc':
                        $queryParams['sort_by'] = 'employee_number';
                        $queryParams['sort_order'] = 'desc';
                        break;
                }
            }

            $result = $this->apiService->request('GET', '/users', [
                'query' => $queryParams
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
                'user_pagination' => $result['pagination'] ?? null,
                'filters' => [
                    'search' => $searchQuery,
                    'status' => $status,
                    'sort' => $sort
                ]
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
                if (is_array($errorData)) {
                    // If it's a nested array of field => [messages]
                    $errorArray = [];
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            foreach ($messages as $message) {
                                $errorArray[] = $message;
                            }
                        } else {
                            $errorArray[] = $messages;
                        }
                    }

                    // Return the formatted array for the view
                    return redirect()->back()
                        ->withInput($request->except('password'))
                        ->with('error', $errorArray);
                } else {
                    return redirect()->back()
                        ->withInput($request->except('password'))
                        ->with('error', $errorData);
                }
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
                if (is_array($errorData)) {
                    // If it's a nested array of field => [messages]
                    $errorArray = [];
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            foreach ($messages as $message) {
                                $errorArray[] = $message;
                            }
                        } else {
                            $errorArray[] = $messages;
                        }
                    }

                    // Return the formatted array for the view
                    return redirect()->back()
                        ->withInput($request->except('password'))
                        ->with('error', $errorArray);
                } else {
                    return redirect()->back()
                        ->withInput($request->except('password'))
                        ->with('error', $errorData);
                }
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
                if (is_array($errorData)) {
                    // If it's a nested array of field => [messages]
                    $errorArray = [];
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            foreach ($messages as $message) {
                                $errorArray[] = $message;
                            }
                        } else {
                            $errorArray[] = $messages;
                        }
                    }

                    return redirect()->back()->with('error', $errorArray);
                } else {
                    return redirect()->back()->with('error', $errorData);
                }
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
