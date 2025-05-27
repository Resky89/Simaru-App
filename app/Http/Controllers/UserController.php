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

            // Untuk permintaan JSON, tingkatkan batas untuk memuat lebih banyak item
            if ($request->expectsJson() || $request->ajax()) {
                $userLimit = $request->input('user_limit', 100);
            }

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
                $errorMessage = $userResult['errors'] ?? 'Autentikasi gagal';

                // Return JSON if requested
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($errorMessage) ? $errorMessage : 'Autentikasi gagal');
            }

            // Check for API errors based on success flag
            if (!isset($userResult['success']) || $userResult['success'] !== true)
            {
                $errorData = $userResult['errors'] ?? 'Gagal mengambil data';

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
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
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
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $userResult['message'] ?? 'Data berhasil diambil',
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
                $roles = [];
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
            // Return JSON if requested
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal mengambil data user: ' . $e->getMessage(),
                    'data' => null
                ], 500);
            }

            return view('Account.User', [
                'roles' => [
                    'data' => [],
                    'pagination' => null
                ],
                'users' => [],
                'user_pagination' => null,
                'error' => 'Gagal mengambil data: ' . $e->getMessage()
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

            // Untuk permintaan JSON, tingkatkan batas untuk memuat lebih banyak item
            if ($request->expectsJson() || $request->ajax()) {
                $limit = $request->input('limit', 100);
            }

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

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil data user';

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

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return view('Account.User', [
                    'users' => [],
                    'error' => $errorMessage
                ]);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Data berhasil diambil',
                    'data' => $result['data'] ?? [],
                    'pagination' => $result['pagination'] ?? null
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
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal mengambil data user: ' . $e->getMessage(),
                    'data' => null
                ], 500);
            }

            return view('Account.User', [
                'users' => [],
                'error' => 'Gagal mengambil data user: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created user.
     */
    public function storeUser(Request $request)
    {
        try {
            // Validasi request
            $request->validate([
                'employee_number' => 'required|string|max:255',
                'password' => 'required|string|min:6',
                'role_ids' => 'required|array',
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

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat user';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], 400);
                }

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

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'User berhasil dibuat',
                    'data' => $result['data'] ?? null
                ], 201);
            }

            return redirect()->route('user')
                ->with('success', $result['message'] ?? 'User berhasil dibuat');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal membuat user: ' . $e->getMessage(),
                    'data' => null
                ], 500);
            }

            return redirect()->back()
                ->withInput($request->except('password'))
                ->with('error', 'Gagal membuat user: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified user.
     */
    public function updateUser(Request $request, $id)
    {
        try {
            // Validasi request
            $request->validate([
                'employee_number' => 'required|string|max:255',
                'role_ids' => 'required|array',
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

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui user';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], 400);
                }

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

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'User berhasil diperbarui',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('user')
                ->with('success', $result['message'] ?? 'User berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal memperbarui user: ' . $e->getMessage(),
                    'data' => null
                ], 500);
            }

            return redirect()->back()
                ->withInput($request->except('password'))
                ->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroyUser($id)
    {
        try {
            $result = $this->apiService->request('DELETE', "/users/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if (request()->expectsJson() || request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menghapus user';

                if (request()->expectsJson() || request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], 400);
                }

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

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'User berhasil dihapus',
                    'data' => null
                ]);
            }

            return redirect()->route('user')
                ->with('success', $result['message'] ?? 'User berhasil dihapus');
        } catch (\Exception $e) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal menghapus user: ' . $e->getMessage(),
                    'data' => null
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    /**
     * Get users by permission name
     *
     * @param Request $request
     * @param string $permissionName
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function getUsersByPermission(Request $request, $permissionName)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            // Call the API endpoint to get users with specific permission
            $result = $this->apiService->request('GET', "/users/by-permission/{$permissionName}", [
                'query' => [
                    'page' => $page,
                    'limit' => $limit
                ]
            ]);

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? "Gagal mengambil data user dengan permission: {$permissionName}";

                // Format error message
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

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return response()->json([
                    'success' => false,
                    'errors' => $errorMessage
                ], 400);
            }

            // Successfully retrieved data
            $users = $result['data'] ?? [];
            $pagination = $result['pagination'] ?? null;

            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Data berhasil diambil',
                'data' => $users,
                'pagination' => $pagination
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal mengambil data user dengan permission: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
