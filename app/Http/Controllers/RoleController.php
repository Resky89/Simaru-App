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

            $response = $this->apiService->request('GET', '/roles', [
                'query' => [
                    'page' => $rolePage,
                    'limit' => $roleLimit,
                    'sort_by' => 'role_id',
                    'sort_order' => 'asc'
                ]
            ]);

            // Check for auth errors
            if (isset($response['errors']) && is_string($response['errors']) &&
                in_array($response['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during roles retrieval:', [
                    'errors' => $response['errors'] ?? 'Authentication failed'
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], status: 401);
                }

                return redirect()->route('login')->with('error', is_string($response['errors']) ? $response['errors'] : 'Authentication failed');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($response['success']) || $response['success'] !== true) {
                $errorData = $response['errors'] ?? 'Failed to fetch roles';

                \Log::warning('Error during roles retrieval:', [
                    'success' => $response['success'] ?? false,
                    'errors' => $errorData
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], status: 400);
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
                    'error' => $errorMessage
                ]);
            }

            $roles = $response['data'] ?? [];

            // Format pagination based on the new response format
            $rolePagination = null;
            if (isset($response['pagination'])) {
                $pagination = $response['pagination'];
                $rolePagination = [
                    'current_page' => $pagination['current_page'] ?? 1,
                    'last_page' => $pagination['total_pages'] ?? 1,
                    'from' => (($pagination['current_page'] ?? 1) - 1) * ($pagination['limit'] ?? 10) + 1,
                    'to' => min(($pagination['current_page'] ?? 1) * ($pagination['limit'] ?? 10), $pagination['total_items'] ?? 0),
                    'total' => $pagination['total_items'] ?? 0,
                    'per_page' => $pagination['limit'] ?? 10,
                    'next_page_url' => $pagination['has_next'] ? url()->current() . '?role_page=' . ($pagination['current_page'] + 1) : null,
                    'prev_page_url' => $pagination['has_prev'] ? url()->current() . '?role_page=' . ($pagination['current_page'] - 1) : null,
                ];
            }

            return view('Account.Role', [
                'roles' => [
                    'data' => $roles,
                    'pagination' => $rolePagination
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal mengambil data role', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('Account.Role', [
                'roles' => [
                    'data' => [],
                    'pagination' => null
                ],
                'error' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        try {
            // Log the request data
            \Log::info('Attempting to create role with data:', [
                'request_data' => $request->all()
            ]);

            // Convert permission_ids to integers
            $permissionIds = [];
            if ($request->has('permission_ids')) {
                $permissionIds = array_map(function ($id) {
                    return (int) $id;
                }, $request->input('permission_ids', []));
            }

            $result = $this->apiService->request('POST', '/roles', [
                'json' => [
                    'role_name' => $request->input('role_name'),
                    'description' => $request->input('description'),
                    'permission_ids' => $permissionIds
                ]
            ]);

            // Log the API response
            \Log::info('API response for role creation:', [
                'api_response' => $result
            ]);

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during role creation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], status: 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to create role';

                \Log::warning('Error during role creation:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], status: 400);
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

            // Successfully created
            \Log::info('Role created successfully');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Role berhasil dibuat',
                    'data' => $result['data'] ?? null
                ], status: 201);
            }

            return redirect()->route('roles')
                ->with('success', 'Role berhasil dibuat');
        } catch (\Exception $e) {
            \Log::error('Exception during role creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'role_data' => $request->except('_token')
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Failed to create role: ' . $e->getMessage()]
                ], status: 500);
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
            // Log the update attempt
            \Log::info('Attempting to update role with ID: ' . $id, [
                'request_data' => $request->except(['_token', '_method'])
            ]);

            // Convert permission_ids to integers
            $permissionIds = [];
            if ($request->has('permission_ids')) {
                $permissionIds = array_map(function ($id) {
                    return (int) $id;
                }, $request->input('permission_ids', []));
            }

            $result = $this->apiService->request('PUT', "/roles/{$id}", [
                'json' => [
                    'role_id' => $id,
                    'role_name' => $request->input('role_name'),
                    'description' => $request->input('description'),
                    'permission_ids' => $permissionIds
                ]
            ]);

            // Log the API response
            \Log::info('API response for role update:', [
                'api_response' => $result,
                'role_id' => $id
            ]);

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during role update:', [
                    'errors' => $result['errors'] ?? 'Authentication failed',
                    'role_id' => $id
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], status: 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to update role';

                \Log::warning('Error during role update:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'role_id' => $id
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], status: 400);
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

            // Successfully updated
            \Log::info('Role updated successfully', [
                'role_id' => $id
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Role berhasil diperbarui',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('roles')
                ->with('success', 'Role berhasil diperbarui');
        } catch (\Exception $e) {
            \Log::error('Exception during role update:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'role_id' => $id,
                'role_data' => $request->except(['_token', '_method'])
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Failed to update role: ' . $e->getMessage()]
                ], status: 500);
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
            \Log::info('Attempting to delete role with ID: ' . $id);

            $result = $this->apiService->request('DELETE', "/roles/{$id}");

            // Log the API response
            \Log::info('API response for role deletion:', [
                'api_response' => $result,
                'role_id' => $id
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during role deletion:', [
                    'errors' => $result['errors'] ?? 'Authentication failed',
                    'role_id' => $id
                ]);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], status: 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to delete role';

                \Log::warning('Error during role deletion:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'role_id' => $id
                ]);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], status: 400);
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

            // Successfully deleted
            \Log::info('Role deleted successfully', [
                'role_id' => $id
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Role berhasil dihapus'
                ]);
            }

            return redirect()->route('roles')
                ->with('success', 'Role berhasil dihapus');
        } catch (\Exception $e) {
            \Log::error('Exception during role deletion:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'role_id' => $id
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Failed to delete role: ' . $e->getMessage()]
                ], status: 500);
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
                \Log::warning('Authentication error during permissions retrieval:', [
                    'errors' => $response['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], status: 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($response['success']) || $response['success'] !== true) {
                $errorData = $response['errors'] ?? 'Failed to fetch permissions';

                \Log::warning('Error during permissions retrieval:', [
                    'success' => $response['success'] ?? false,
                    'errors' => $errorData
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                ], status: 400);
            }

            $permissions = $response['data'] ?? [];
            $pagination = $response['pagination'] ?? null;

            return response()->json([
                'success' => true,
                'data' => $permissions,
                'pagination' => $pagination
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during permissions retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => 'Failed to retrieve permissions: ' . $e->getMessage()]
            ], status: 500);
        }
    }

    /**
     * Get a specific role by ID.
     */
    public function show($id)
    {
        try {
            \Log::info('Fetching role with ID: ' . $id);

            $response = $this->apiService->request('GET', "/roles/{$id}");

            // Check for auth errors
            if (isset($response['errors']) && is_string($response['errors']) &&
                in_array($response['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during role retrieval:', [
                    'errors' => $response['errors'] ?? 'Authentication failed',
                    'role_id' => $id
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], status: 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($response['success']) || $response['success'] !== true) {
                $errorData = $response['errors'] ?? 'Failed to fetch role';

                \Log::warning('Error during role retrieval:', [
                    'success' => $response['success'] ?? false,
                    'errors' => $errorData,
                    'role_id' => $id
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                ], status: 400);
            }

            // Transform the response to ensure success field is present
            $result = [
                'success' => true,
                'message' => 'Role retrieved successfully',
                'data' => $response['data'] ?? $response
            ];

            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('Exception during role retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'role_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => 'Failed to fetch role details: ' . $e->getMessage()]
            ], status: 500);
        }
    }
}
