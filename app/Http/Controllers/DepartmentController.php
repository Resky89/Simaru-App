<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Services\ApiService;

class DepartmentController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display a listing of departments.
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            $result = $this->apiService->request('GET', '/departments', [
                'query' => [
                    'page' => $page,
                    'limit' => $limit,
                    'sort_by' => 'department_id',
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

            return view('Department', [
                'departments' => $result['data'] ?? [],
                'pagination' => $result['pagination'] ?? null
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch departments', [
                'error' => $e->getMessage()
            ]);

            return view('Department', [
                'departments' => [],
                'pagination' => null,
                'error' => 'Failed to fetch departments: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request)
    {
        try {
            $result = $this->apiService->request('POST', '/departments', [
                'json' => [
                    'department_name' => $request->input('department_name')
                ]
            ]);

            // Check if we got an auth error response
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', $result['message']);
            }

            // Check for other API errors
            if (isset($result['error'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Failed to create department');
            }

            return redirect()->route('departments')
                ->with('success', 'Department created successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to create department', [
                'error' => $e->getMessage(),
                'department_name' => $request->input('department_name')
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create department: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified department.
     */
    public function update(Request $request, $id)
    {
        try {
            $result = $this->apiService->request('PUT', "/departments/{$id}", [
                'json' => [
                    'department_id' => $request->input('department_id'),
                    'department_name' => $request->input('department_name')
                ]
            ]);

            // Check if we got an auth error response
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', $result['message']);
            }

            // Check for other API errors
            if (isset($result['error'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Failed to update department');
            }

            return redirect()->route('departments')
                ->with('success', 'Department updated successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to update department', [
                'error' => $e->getMessage(),
                'department_id' => $id,
                'department_name' => $request->input('department_name')
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update department: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified department.
     */
    public function destroy($id)
    {
        try {
            $result = $this->apiService->request('DELETE', "/departments/{$id}");

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', $result['message']);
            }

            // Check for other API errors
            if (isset($result['error'])) {
                return redirect()->back()
                    ->with('error', $result['message'] ?? 'Failed to delete department');
            }

            return redirect()->route('departments')
                ->with('success', 'Department deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to delete department', [
                'error' => $e->getMessage(),
                'department_id' => $id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete department: ' . $e->getMessage());
        }
    }
}
