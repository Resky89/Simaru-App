<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class RoleController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        try {
            // Fetch roles
            $rolePage = $request->input('role_page', 1);
            $roleLimit = $request->input('role_limit', 10);
            $search = $request->input('search', '');
            $sort = $request->input('sort', '');

            // Untuk permintaan JSON, tingkatkan batas untuk memuat lebih banyak item
            if ($request->expectsJson() || $request->ajax()) {
                $roleLimit = $request->input('role_limit', 100);
            }

            $queryParams = [
                'page' => $rolePage,
                'limit' => $roleLimit,
                'sort_by' => 'role_id',
                'sort_order' => 'asc'
            ];

            // Add search parameter if provided
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Handle sorting
            if (!empty($sort)) {
                switch ($sort) {
                    case 'name_asc':
                        $queryParams['sort_by'] = 'role_name';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'name_desc':
                        $queryParams['sort_by'] = 'role_name';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    case 'id_asc':
                        $queryParams['sort_by'] = 'role_id';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'id_desc':
                        $queryParams['sort_by'] = 'role_id';
                        $queryParams['sort_order'] = 'desc';
                        break;
                }
            }

            $response = $this->apiService->request('GET', '/roles', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($response['errors']) && is_string($response['errors']) &&
                in_array($response['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $response['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($response['errors']) ? $response['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($response['success']) || $response['success'] !== true) {
                $errorData = $response['errors'] ?? 'Gagal mengambil data';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], 400);
                }

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

                return view('Account.Role', [
                    'roles' => [
                        'data' => [],
                        'pagination' => null
                    ],
                    'error' => $errorMessage,
                    'search' => $search,
                    'sort' => $sort
                ]);
            }

            $roles = $response['data'] ?? [];

            // Format pagination based on the new response format
            $rolePagination = null;
            if (isset($response['pagination'])) {
                $pagination = $response['pagination'];

                // Preserve query parameters
                $queryParams = $request->all();

                // Create next and previous page URLs with all current parameters
                $nextPageParams = array_merge($queryParams, ['role_page' => ($pagination['current_page'] ?? 1) + 1]);
                $prevPageParams = array_merge($queryParams, ['role_page' => ($pagination['current_page'] ?? 1) - 1]);

                $rolePagination = [
                    'current_page' => $pagination['current_page'] ?? 1,
                    'last_page' => $pagination['total_pages'] ?? 1,
                    'from' => (($pagination['current_page'] ?? 1) - 1) * ($pagination['limit'] ?? 10) + 1,
                    'to' => min(($pagination['current_page'] ?? 1) * ($pagination['limit'] ?? 10), $pagination['total_items'] ?? 0),
                    'total' => $pagination['total_items'] ?? 0,
                    'per_page' => $pagination['limit'] ?? 10,
                    'next_page_url' => $pagination['has_next'] ? url()->current() . '?' . http_build_query($nextPageParams) : null,
                    'prev_page_url' => $pagination['has_prev'] ? url()->current() . '?' . http_build_query($prevPageParams) : null,
                ];
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $response['message'] ?? 'Data berhasil diambil',
                    'data' => $roles,
                    'pagination' => $rolePagination
                ]);
            }

            return view('Account.Role', [
                'roles' => [
                    'data' => $roles,
                    'pagination' => $rolePagination
                ],
                'search' => $search,
                'sort' => $sort
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal mengambil data role: ' . $e->getMessage()
                ], 500);
            }

            return view('Account.Role', [
                'roles' => [
                    'data' => [],
                    'pagination' => null
                ],
                'error' => 'Gagal mengambil data: ' . $e->getMessage(),
                'search' => $request->input('search', ''),
                'sort' => $request->input('sort', '')
            ]);
        }
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        try {
            // Validasi request
            $request->validate([
                'role_name' => 'required|string|max:255',
            ]);

            // Convert permission_ids to integers
            $permissionIds = [];
            if ($request->has('permission_ids')) {
                $permissionIds = array_map(function ($id) {
                    return (int) $id;
                }, $request->input('permission_ids', []));
            }

            // Create request payload
            $payload = [
                'role_name' => $request->input('role_name'),
                'permission_ids' => $permissionIds
            ];

            // Only include description if it's provided and is a string
            if ($request->has('description') && is_string($request->input('description'))) {
                $description = trim($request->input('description'));
                if (!empty($description)) {
                    $payload['description'] = $description;
                }
            }

            $result = $this->apiService->request('POST', '/roles', [
                'json' => $payload
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

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat role';

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
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Role berhasil dibuat',
                    'data' => $result['data'] ?? null
                ], 201);
            }

            return redirect()->route('roles')
                ->with('success', $result['message'] ?? 'Role berhasil dibuat');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal membuat role: ' . $e->getMessage()],
                    'data' => null
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat role: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, $id)
    {
        try {
            // Validasi request
            $request->validate([
                'role_name' => 'required|string|max:255',
            ]);

            // Convert permission_ids to integers
            $permissionIds = [];
            if ($request->has('permission_ids')) {
                $permissionIds = array_map(function ($id) {
                    return (int) $id;
                }, $request->input('permission_ids', []));
            }

            // Create request payload
            $payload = [
                'role_id' => $id,
                'role_name' => $request->input('role_name'),
                'permission_ids' => $permissionIds
            ];

            // Only include description if it's provided and is a string
            if ($request->has('description') && is_string($request->input('description'))) {
                $description = trim($request->input('description'));
                if (!empty($description)) {
                    $payload['description'] = $description;
                }
            }

            $result = $this->apiService->request('PUT', "/roles/{$id}", [
                'json' => $payload
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

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui role';

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
                        ->withInput()
                    ->with('error', $errorMessage);
                }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Role berhasil diperbarui',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('roles')
                ->with('success', $result['message'] ?? 'Role berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal memperbarui role: ' . $e->getMessage()],
                    'data' => null
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui role: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy($id)
    {
        try {
            $result = $this->apiService->request('DELETE', "/roles/{$id}");

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

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menghapus role';

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

                    return redirect()->back()
                    ->with('error', $errorMessage);
                }

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Role berhasil dihapus',
                    'data' => null
                ]);
            }

            return redirect()->route('roles')
                ->with('success', $result['message'] ?? 'Role berhasil dihapus');
        } catch (\Exception $e) {
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal menghapus role: ' . $e->getMessage()],
                    'data' => null
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus role: ' . $e->getMessage());
        }
    }

    /**
     * Get all permissions
     */
    public function getAllPermissions(Request $request)
    {
        try {
            // Fetch permissions
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            $response = $this->apiService->request('GET', '/permissions', [
                'query' => [
                    'page' => $page,
                    'limit' => $limit
                ]
            ]);

            // Check for auth errors
            if (isset($response['errors']) && is_string($response['errors']) &&
                in_array($response['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => $response['errors'] ?? 'Autentikasi gagal'
                ], 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($response['success']) || $response['success'] !== true) {
                $errorData = $response['errors'] ?? 'Gagal mengambil data permission';

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

                return response()->json([
                    'success' => false,
                    'errors' => $errorMessage
                ], 400);
            }

            $permissions = $response['data'] ?? [];
            $pagination = $response['pagination'] ?? null;

            return response()->json([
                'success' => true,
                'message' => $response['message'] ?? 'Data berhasil diambil',
                'data' => $permissions,
                'pagination' => $pagination
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal mengambil data permission: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Get a specific role by ID.
     */
    public function show($id)
    {
        try {
            $response = $this->apiService->request('GET', "/roles/{$id}");

            // Check for auth errors
            if (isset($response['errors']) && is_string($response['errors']) &&
                in_array($response['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => $response['errors'] ?? 'Autentikasi gagal'
                ], 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($response['success']) || $response['success'] !== true) {
                $errorData = $response['errors'] ?? 'Gagal mengambil data role';

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

                return response()->json([
                    'success' => false,
                    'errors' => $errorMessage
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $response['message'] ?? 'Data berhasil diambil',
                'data' => $response['data'] ?? null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal mengambil data role: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
