<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Services\ApiService;

class EmployeeController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            $result = $this->apiService->request('GET', '/employees', [
                'query' => [
                    'page' => $page,
                    'limit' => $limit,
                    'sort_by' => 'employee_id',
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

            $employees = $result['data'] ?? [];
            $pagination = isset($result['pagination']) ? $result['pagination'] : null;

            if ($pagination) {
                $pagination = array_merge([
                    'current_page' => $pagination['current_page'] ?? 1,
                    'last_page' => ceil(($pagination['total_items'] ?? 0) / ($pagination['limit'] ?? 10)),
                    'from' => (($pagination['current_page'] ?? 1) - 1) * ($pagination['limit'] ?? 10) + 1,
                    'to' => min(($pagination['current_page'] ?? 1) * ($pagination['limit'] ?? 10), $pagination['total_items'] ?? 0),
                    'total' => $pagination['total_items'] ?? 0,
                    'next_page_url' => $pagination['has_next'] ? url()->current() . '?page=' . ($pagination['current_page'] + 1) : null,
                    'prev_page_url' => $pagination['has_prev'] ? url()->current() . '?page=' . ($pagination['current_page'] - 1) : null,
                ], $pagination);
            }

            // Also fetch departments for the dropdowns
            $departmentResult = $this->apiService->request('GET', '/departments', [
                'query' => [
                    'page' => 1,
                    'limit' => 100 // Get a reasonable number of departments for the dropdown
                ]
            ]);

            $departments = $departmentResult['data'] ?? [];

            return view('Account.Employee', [
                'employees' => $employees,
                'departments' => $departments,
                'pagination' => $pagination
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch data for employee page', [
                'error' => $e->getMessage()
            ]);

            return view('Account.Employee', [
                'employees' => [],
                'departments' => [],
                'pagination' => null,
                'error' => 'Failed to fetch data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        try {
            // Log the request data in a consistent format
            \Log::info('Employee create - Request data:', [
                'request_data' => $request->all(),
                'url' => $request->url(),
                'method' => $request->method()
            ]);

            $result = $this->apiService->request('POST', '/employees', [
               'json' => [
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'department_id' => (int)$request->input('department_id'), // Cast to integer
                    'position' => $request->input('position'),
                    'date_of_birth' => $request->input('date_of_birth'),
                    'phone_number' => $request->input('phone_number'),
                    'address' => $request->input('address')
                ]
            ]);

            // Detailed logging of the API response
            \Log::info('Employee create - API response:', [
                'api_response' => $result,
                'status' => $result['status'] ?? 'unknown'
            ]);

            // Check for auth errors first
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Employee create - Authentication error:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors
            if (isset($result['error'])) {
                \Log::error('Employee create - API error:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Failed to create employee'
                ]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Failed to create employee');
            }

            // Success case
            \Log::info('Employee create - Success');
            return redirect()->route('employees')
                ->with('success', 'Employee created successfully');
        } catch (\Exception $e) {
            \Log::error('Employee create - Exception:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create employee: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, $id)
    {
        try {
            // Log the request data
            \Log::info('Employee update - Request data:', [
                'employee_id' => $id,
                'request_data' => $request->all(),
                'url' => $request->url(),
                'method' => $request->method()
            ]);

            $result = $this->apiService->request('PUT', "/employees/{$id}", [
                'json' => [
                    'employee_id' => $id,
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'department_id' => (int)$request->input('department_id'), // Cast to integer
                    'position' => $request->input('position'),
                    'date_of_birth' => $request->input('date_of_birth'),
                    'phone_number' => $request->input('phone_number'),
                    'address' => $request->input('address')
                ]
            ]);

            // Detailed logging of the API response
            \Log::info('Employee update - API response:', [
                'employee_id' => $id,
                'api_response' => $result,
                'status' => $result['status'] ?? 'unknown'
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Employee update - Authentication error:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors
            if (isset($result['error'])) {
                \Log::error('Employee update - API error:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Failed to update employee',
                    'employee_id' => $id
                ]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Failed to update employee');
            }

            // Success case
            \Log::info('Employee update - Success', ['employee_id' => $id]);
            return redirect()->route('employees')
                ->with('success', 'Employee updated successfully');
        } catch (\Exception $e) {
            \Log::error('Employee update - Exception:', [
                'error' => $e->getMessage(),
                'employee_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update employee: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified employee.
     */
    public function destroy($id)
    {
        try {
            \Log::info('Deleting employee', [
                'id' => $id,
                'url' => request()->url()
            ]);

            $result = $this->apiService->request('DELETE', "/employees/{$id}");

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', $result['message']);
            }

            // Check for other API errors
            if (isset($result['error'])) {
                return redirect()->back()
                    ->with('error', $result['message'] ?? 'Failed to delete employee');
            }

            return redirect()->route('employees')
                ->with('success', 'Employee deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to delete employee', [
                'error' => $e->getMessage(),
                'employee_id' => $id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete employee: ' . $e->getMessage());
        }
    }
}
