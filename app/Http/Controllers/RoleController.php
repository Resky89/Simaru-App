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

            $roleResult = $this->apiService->request('GET', '/roles', [
                'query' => [
                    'page' => $rolePage,
                    'limit' => $roleLimit,
                    'sort_by' => 'role_id',
                    'sort_order' => 'asc'
                ]
            ]);

            // Check if we got an error response from the ApiService
            if (isset($roleResult['error'])) {
                if (strpos($roleResult['error'], 'login') !== false) {
                    return redirect()->route('login')->with('error', $roleResult['error']);
                }

                throw new \Exception($roleResult['error']);
            }

            $roles = $roleResult['data'] ?? [];

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

            return view('Account.Role', [
                'roles' => [
                    'data' => $roles,
                    'pagination' => $rolePagination
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch role data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('Account.Role', [
                'roles' => [
                    'data' => [],
                    'pagination' => null
                ],
                'error' => 'Failed to fetch data: ' . $e->getMessage()
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
                    'description' => $request->input('description')
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
            if (isset($result['error']) || (isset($result['success']) && $result['success'] === false)) {
                \Log::warning('Error during role creation:', [
                    'error' => $result['error'] ?? null,
                    'success' => $result['success'] ?? null,
                    'message' => $result['message'] ?? 'Failed to create role'
                ]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Failed to create role');
            }

            // Successfully created
            \Log::info('Role created successfully');
            return redirect()->route('roles')
                ->with('success', 'Role created successfully');
        } catch (\Exception $e) {
            \Log::error('Exception during role creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'role_data' => $request->except('_token')
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create role: ' . $e->getMessage());
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
                    'description' => $request->input('description')
                ]
            ]);

            // Check if we got an auth error response
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (isset($result['error']) || (isset($result['success']) && $result['success'] === false)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Failed to update role');
            }

            // Successfully updated
            return redirect()->route('roles')
                ->with('success', 'Role updated successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to update role', [
                'error' => $e->getMessage(),
                'role_id' => $id,
                'role_data' => $request->except(['_token', '_method'])
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update role: ' . $e->getMessage());
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
            if (isset($result['error']) || (isset($result['success']) && $result['success'] === false)) {
                return redirect()->back()
                    ->with('error', $result['message'] ?? 'Failed to delete role');
            }

            // Successfully deleted
            return redirect()->route('roles')
                ->with('success', 'Role deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to delete role', [
                'error' => $e->getMessage(),
                'role_id' => $id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }
}
