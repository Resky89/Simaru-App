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

            // Check if we got an error response from the ApiService
            if (isset($response['error'])) {
                if (strpos($response['error'], 'login') !== false) {
                    return redirect()->route('login')->with('error', $response['error']);
                }
                throw new \Exception($response['error']);
            }

            // Check status in the new response format
            if (isset($response['status']) && $response['status'] === false) {
                throw new \Exception($response['message'] ?? 'Failed to fetch roles');
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

            $result = $this->apiService->request('POST', '/roles', [
                'json' => [
                    'role_name' => $request->input('role_name'),
                    'description' => $request->input('description'),
                    'permission_ids' => $request->input('permission_ids', [])
                ]
            ]);

            // Log the API response
            \Log::info('API response for role creation:', [
                'api_response' => $result
            ]);

            // Check if we got an auth error response
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during role creation:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (
                isset($result['error']) || (isset($result['success']) && $result['success'] === false) ||
                (isset($result['status']) && $result['status'] === false)
            ) {
                \Log::warning('Error during role creation:', [
                    'error' => $result['error'] ?? null,
                    'message' => $result['message'] ?? 'Failed to create role'
                ]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Gagal membuat role');
            }

            // Successfully created
            \Log::info('Role created successfully');
            return redirect()->route('roles')
                ->with('success', 'Role berhasil dibuat');
        } catch (\Exception $e) {
            \Log::error('Exception during role creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'role_data' => $request->except('_token')
            ]);

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
            $result = $this->apiService->request('PUT', "/roles/{$id}", [
                'json' => [
                    'role_id' => $id,
                    'role_name' => $request->input('role_name'),
                    'description' => $request->input('description'),
                    'permission_ids' => $request->input('permission_ids', [])
                ]
            ]);

            // Check if we got an auth error response
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (
                isset($result['error']) ||
                (isset($result['success']) && $result['success'] === false) ||
                (isset($result['status']) && $result['status'] === false)
            ) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Gagal memperbarui role');
            }

            // Successfully updated
            return redirect()->route('roles')
                ->with('success', 'Role berhasil diperbarui');
        } catch (\Exception $e) {
            \Log::error('Gagal memperbarui role', [
                'error' => $e->getMessage(),
                'role_id' => $id,
                'role_data' => $request->except(['_token', '_method'])
            ]);

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
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (
                isset($result['error']) ||
                (isset($result['success']) && $result['success'] === false) ||
                (isset($result['status']) && $result['status'] === false)
            ) {
                return redirect()->back()
                    ->with('error', $result['message'] ?? 'Failed to delete role');
            }

            // Successfully deleted
            return redirect()->route('roles')
                ->with('success', 'Role berhasil dihapus');
        } catch (\Exception $e) {
            \Log::error('Gagal menghapus role', [
                'error' => $e->getMessage(),
                'role_id' => $id
            ]);

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

            // Check if we got an error response from the ApiService
            if (isset($response['error'])) {
                if (strpos($response['error'], 'login') !== false) {
                    return response()->json(['error' => 'Authentication failed'], 401);
                }
                throw new \Exception($response['error']);
            }

            // Check status in the response format
            if (isset($response['status']) && $response['status'] === false) {
                throw new \Exception($response['message'] ?? 'Failed to fetch permissions');
            }

            $permissions = $response['data'] ?? [];
            $pagination = $response['pagination'] ?? null;

            return response()->json([
                'status' => true,
                'data' => $permissions,
                'pagination' => $pagination
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal mengambil data permission', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data permission: ' . $e->getMessage()
            ], 500);
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

            // Check if we got an error response from the ApiService
            if (isset($response['error'])) {
                if (strpos($response['error'], 'login') !== false) {
                    return response()->json(['status' => false, 'error' => 'Authentication failed'], 401);
                }
                throw new \Exception($response['error']);
            }

            // Check status in the response format
            if (isset($response['status']) && $response['status'] === false) {
                throw new \Exception($response['message'] ?? 'Failed to fetch role');
            }

            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch role details', [
                'error' => $e->getMessage(),
                'role_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch role details: ' . $e->getMessage()
            ], 500);
        }
    }
}
