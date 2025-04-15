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

            // Fetch users
            $userPage = $request->input('user_page', 1);
            $userLimit = $request->input('user_limit', 10);

            $userResult = $this->apiService->request('GET', '/users', [
                'query' => [
                    'page' => $userPage,
                    'limit' => $userLimit,
                    'sort_by' => 'user_id',
                    'sort_order' => 'asc'
                ]
            ]);

            // Fetch employees for dropdown and data linking
            $employeeResult = $this->apiService->request('GET', '/employees', [
                'query' => [
                    'page' => 1,
                    'limit' => 100 // Get a reasonable number of employees for the dropdown
                ]
            ]);

            // Check if we got an error response from the ApiService
            if (isset($roleResult['error']) || isset($userResult['error']) || isset($employeeResult['error'])) {
                $error = isset($roleResult['error']) ? $roleResult['error'] :
                    (isset($userResult['error']) ? $userResult['error'] : $employeeResult['error']);

                if (strpos($error, 'login') !== false) {
                    return redirect()->route('login')->with('error', $error);
                }

                throw new \Exception($error);
            }

            $roles = $roleResult['data'] ?? [];
            $users = $userResult['data'] ?? [];
            $employees = $employeeResult['data'] ?? [];

            // Link employee names to users
            foreach ($users as &$user) {
                $employeeId = $user['employee_id'];
                $employee = collect($employees)->first(function ($emp) use ($employeeId) {
                    return $emp['employee_id'] == $employeeId;
                });

                $user['employee_name'] = $employee ? ($employee['first_name'] . ' ' . $employee['last_name']) : 'Unknown';

                // Also link role name
                $roleId = $user['role_id'];
                $role = collect($roles)->first(function ($rol) use ($roleId) {
                    return $rol['role_id'] == $roleId;
                });

                $user['role_name'] = $role ? $role['role_name'] : 'Unknown';
            }

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

            return view('Account.User', [
                'roles' => [
                    'data' => $roles,
                    'pagination' => $rolePagination
                ],
                'users' => $users,
                'user_pagination' => $userPagination,
                'employees' => $employees
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch user data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('Account.User', [
                'roles' => [
                    'data' => [],
                    'pagination' => null
                ],
                'users' => [],
                'user_pagination' => null,
                'employees' => [],
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
                    'sort_order' => 'asc'
                ]
            ]);

            // Check if we got an error response from the ApiService
            if (isset($result['error'])) {
                if (strpos($result['error'], 'login') !== false) {
                    return redirect()->route('login')->with('error', $result['error']);
                }

                throw new \Exception($result['error']);
            }

            // Adapt to the new response structure
            return view('Account.User', [
                'users' => $result['data'] ?? [],
                'pagination' => $result['pagination'] ?? null
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
                'request_data' => $request->all()
            ]);

            // Updated endpoint for user creation with number conversions
            $result = $this->apiService->request('POST', '/auth/create-user', [
                'json' => [
                    'email' => $request->input('email'),
                    'role_id' => (int) $request->input('role_id'),
                    'employee_id' => (int) $request->input('employee_id')
                ]
            ]);

            // Log the API response
            \Log::info('API response for user creation:', [
                'api_response' => $result
            ]);

            // Check if we got an auth error response
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during user creation:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors based on status flag
            if (!isset($result['status']) || $result['status'] !== true) {
                \Log::warning('Error during user creation:', [
                    'status' => $result['status'] ?? false,
                    'message' => $result['message'] ?? 'Failed to create user'
                ]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Failed to create user');
            }

            // Successfully created
            \Log::info('User created successfully');
            return redirect()->route('user')
                ->with('success', $result['message'] ?? 'User created successfully');
        } catch (\Exception $e) {
            \Log::error('Exception during user creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_data' => $request->except('_token')
            ]);

            return redirect()->back()
                ->withInput()
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
                'request_data' => $request->all()
            ]);

            // Convert is_active to boolean - true if 1, false otherwise
            $isActive = $request->input('is_active') == '1' ? true : false;

            $result = $this->apiService->request('PUT', "/users/{$id}", [
                'json' => [
                    'email' => $request->input('email'),
                    'role_id' => (int) $request->input('role_id'),
                    'employee_id' => (int) $request->input('employee_id'),
                    'is_active' => $isActive
                ]
            ]);

            // Log the API response
            \Log::info('API response for user update:', [
                'api_response' => $result
            ]);

            // Check if we got an auth error response
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during user update:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors based on status flag
            if (!isset($result['status']) || $result['status'] !== true) {
                \Log::warning('Error during user update:', [
                    'status' => $result['status'] ?? false,
                    'message' => $result['message'] ?? 'Failed to update user'
                ]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Failed to update user');
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
                'user_data' => $request->except(['_token', '_method'])
            ]);

            return redirect()->back()
                ->withInput()
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

            // Log the API response
            \Log::info('API response for user deletion:', [
                'api_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during user deletion:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors based on status flag
            if (!isset($result['status']) || $result['status'] !== true) {
                \Log::warning('Error during user deletion:', [
                    'status' => $result['status'] ?? false,
                    'message' => $result['message'] ?? 'Failed to delete user'
                ]);
                return redirect()->back()
                    ->with('error', $result['message'] ?? 'Failed to delete user');
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
