<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DataFormatter;
use App\Http\Controllers\Traits\ApiResourceOperations;

class RoleController extends Controller
{
    use ApiResourceOperations;

    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        $extraParams = [];

        // Search query
        if ($request->filled('search')) {
            $extraParams['search'] = $request->input('search');
        }

        // Custom sort mappings
        $sortMappings = [
            'name_asc' => ['sort_by' => 'role_name', 'sort_order' => 'asc'],
            'name_desc' => ['sort_by' => 'role_name', 'sort_order' => 'desc'],
            'id_asc' => ['sort_by' => 'role_id', 'sort_order' => 'asc'],
            'id_desc' => ['sort_by' => 'role_id', 'sort_order' => 'desc'],
        ];

        // For JSON requests, increase the limit
                if ($request->expectsJson() || $request->ajax()) {
            $extraParams['limit'] = $request->input('limit', 100);
        }

        return $this->getResourceList(
            $request,
            '/roles',
            'roles',
            'Account.Role',
            'role_id',
            $extraParams,
            $sortMappings
        );
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'role_name' => 'required|string',
            ]);

            // Convert permission_ids to integers
            $permissionIds = [];
            if ($request->has('permission_ids')) {
                $permissionIds = array_map('intval', (array) $request->input('permission_ids', []));
            }

            // Create request payload
            $data = [
                'role_name' => $validated['role_name'],
                'permission_ids' => $permissionIds
            ];

            // Only include description if it's provided and is a string
            if ($request->has('description') && is_string($request->input('description'))) {
                $description = trim($request->input('description'));
                if (!empty($description)) {
                    $data['description'] = $description;
                }
            }

            return $this->storeResource(
                $request,
                '/roles',
                $data,
                'Role berhasil dibuat',
                'roles'
            );
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Account.Role');
        }
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'role_name' => 'required|string',
            ]);

            // Convert permission_ids to integers
            $permissionIds = [];
            if ($request->has('permission_ids')) {
                $permissionIds = array_map('intval', (array) $request->input('permission_ids', []));
            }

            // Create request payload
            $data = [
                'role_id' => (int) $id,
                'role_name' => $validated['role_name'],
                'permission_ids' => $permissionIds
            ];

            // Only include description if it's provided and is a string
            if ($request->has('description') && is_string($request->input('description'))) {
                $description = trim($request->input('description'));
                if (!empty($description)) {
                    $data['description'] = $description;
                }
            }

            return $this->updateResource(
                $request,
                "/roles/{$id}",
                $data,
                'Role berhasil diperbarui',
                'roles'
            );
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Account.Role');
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy($id)
    {
        return $this->deleteResource(
            request(),
            "/roles/{$id}",
            'Role berhasil dihapus',
            'roles'
        );
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
            $authError = $this->handleAuthError($response, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            $apiError = $this->handleApiError($response, $request, 'Account.Role', 'Gagal mengambil data permission');
            if ($apiError) {
                return $apiError;
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
            return $this->handleException($e, $request, 'Account.Role');
        }
    }

    /**
     * Get a specific role by ID.
     */
    public function show($id)
    {
        try {
            $result = $this->getResource(
                request(),
                "/roles/{$id}",
                'role',
                'Account.Role'
            );

            if (request()->ajax() && $result instanceof \Illuminate\Http\JsonResponse) {
                return $result;
            }

            return $result;
        } catch (\Exception $e) {
            return $this->handleException($e, request(), 'Account.Role');
        }
    }
}
