<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DataFormatter;
use App\Http\Controllers\Traits\ApiResourceOperations;

class UserController extends Controller
{
    use ApiResourceOperations;

    /**
     * Display a listing of roles and users.
     */
    public function index(Request $request)
    {
        $extraParams = [];

        // Status filter
        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $extraParams['is_active'] = "true";
            } else if ($request->input('status') === 'inactive') {
                $extraParams['is_active'] = "false";
            }
        }

        // Search query
        if ($request->filled('search')) {
            $extraParams['search'] = $request->input('search');
        }

        // Custom sort mappings
        $sortMappings = [
            'id_asc' => ['sort_by' => 'user_id', 'sort_order' => 'asc'],
            'id_desc' => ['sort_by' => 'user_id', 'sort_order' => 'desc'],
            'emp_asc' => ['sort_by' => 'employee_number', 'sort_order' => 'asc'],
            'emp_desc' => ['sort_by' => 'employee_number', 'sort_order' => 'desc'],
        ];

        return $this->getResourceList(
            $request,
            '/users',
            'users',
            'Account.User',
            'user_id',
            $extraParams,
            $sortMappings
        );
    }

    /**
     * Get users endpoint
     */
    public function getUsers(Request $request)
    {
        // This method can be removed as it's redundant with index()
        // For backward compatibility, just call index()
        return $this->index($request);
    }

    /**
     * Store a newly created user.
     */
    public function storeUser(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'employee_number' => 'required|string',
                'employee_name' => 'required|string',
                'password' => 'required|string',
                'role_ids' => 'required|array',
            ]);

            // Prepare data for API
            $data = [
                'employee_number' => $validated['employee_number'],
                'employee_name' => $validated['employee_name'],
                'password' => $validated['password'],
                'role_ids' => array_map('intval', (array) $validated['role_ids']),
                    'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true
            ];

            return $this->storeResource(
                $request,
                '/users',
                $data,
                'User berhasil dibuat',
                'user'
            );
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Account.User', [
                'users' => [],
                'user_pagination' => null
            ]);
        }
    }

    /**
     * Update the specified user.
     */
    public function updateUser(Request $request, $id)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'employee_number' => 'required|string',
                'employee_name' => 'required|string',
                'role_ids' => 'required|array',
            ]);

            // Prepare data for API
            $data = [
                'employee_number' => $validated['employee_number'],
                'employee_name' => $validated['employee_name'],
                'role_ids' => array_map('intval', (array) $validated['role_ids']),
                'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true
            ];

            // Add password only if provided
            if ($request->filled('password')) {
                $data['password'] = $request->input('password');
            }

            return $this->updateResource(
                $request,
                "/users/{$id}",
                $data,
                'User berhasil diperbarui',
                'user'
            );
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Account.User', [
                'users' => [],
                'user_pagination' => null
            ]);
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroyUser($id)
    {
        return $this->deleteResource(
            request(),
            "/users/{$id}",
            'User berhasil dihapus',
            'user'
        );
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

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            $apiError = $this->handleApiError(
                $result,
                $request,
                'Account.User',
                "Gagal mengambil data user dengan permission: {$permissionName}"
            );
            if ($apiError) {
                return $apiError;
            }

            // Successfully retrieved data
            $users = $result['data'] ?? [];
            $pagination = $this->formatPagination($result['pagination'] ?? null);

            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Data berhasil diambil',
                'data' => $users,
                'pagination' => $pagination
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Account.User');
        }
    }
}
