<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Barryvdh\DomPDF\Facade\Pdf;

class MaintenanceController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display a listing of maintenance schedules.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');
            $status = $request->input('status', '');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            // Log request info with detailed search info
            \Log::info('Fetching maintenance schedules for view with parameters:', [
                'page' => $page,
                'limit' => $limit,
                'search' => $search,
                'status' => $status,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'request_url' => $request->fullUrl(),
                'ajax' => $request->ajax()
            ]);

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit
            ];

            // Add search parameter if provided
            if (!empty($search)) {
                $queryParams['search'] = $search;
                \Log::debug('Search query added:', ['search' => $search]);
            }

            // Add status filter if provided
            if (!empty($status)) {
                $queryParams['status'] = $status;
            }

            // Add sorting parameters
            if (!empty($sortBy)) {
                $queryParams['sort_by'] = $sortBy;
            }

            if (!empty($sortOrder)) {
                $queryParams['sort_order'] = $sortOrder;
            }

            // Fetch maintenance schedules
            \Log::debug('Sending API request with query params:', $queryParams);
            $result = $this->apiService->request('GET', '/maintenance', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during maintenance schedules retrieval:', [
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

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve maintenance schedules';

                \Log::warning('Error during maintenance schedules retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
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

                return view('Maintenance.Maintenance', [
                    'maintenances' => [],
                    'pagination' => null,
                    'search' => $search,
                    'status' => $status,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder,
                    'users' => [],
                    'vendors' => [],
                    'error' => $errorMessage
                ]);
            }

            // Log API response for debugging
            \Log::info('API response for maintenance schedules view:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'data_count' => isset($result['data']) ? count($result['data']) : 0
            ]);

            // Parse data for view
            $maintenances = $result['data'] ?? [];
            $pagination = $result['pagination'] ?? null;

            // Fetch all users for the dropdown
            $usersResult = $this->apiService->request('GET', '/users', [
                'query' => [
                    'limit' => 1000, // Get only a minimal set of users for fallback, we now use lazy loading
                    'sort_by' => 'employee_number',
                    'sort_order' => 'asc'
                ]
            ]);

            // Fetch vendors for the dropdown
            $vendorsResult = $this->apiService->request('GET', '/vendors', [
                'query' => [
                    'limit' => 1000,
                    'sort_by' => 'vendor_name',
                    'sort_order' => 'asc'
                ]
            ]);

            // Parse users and vendors data
            $users = $usersResult['data'] ?? [];
            $vendors = $vendorsResult['data'] ?? [];

            // For AJAX requests, return JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'maintenances' => $maintenances,
                    'pagination' => $pagination
                ]);
            }

            // Return the view with data for regular requests
            return view('Maintenance.Maintenance', [
                'maintenances' => $maintenances,
                'pagination' => $pagination,
                'search' => $search,
                'status' => $status,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'users' => $users,
                'vendors' => $vendors
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during maintenance schedules view retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['general' => 'Failed to retrieve maintenance schedules: ' . $e->getMessage()]
                ], status: 500);
            }

            return view('Maintenance.Maintenance', [
                'maintenances' => [],
                'pagination' => null,
                'search' => $search,
                'status' => $status,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'users' => [],
                'vendors' => [],
                'error' => 'Failed to retrieve maintenance schedules: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get all maintenance schedules.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllMaintenance(Request $request)
    {
        try {
            // Get pagination parameters
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');
            $status = $request->input('status', '');

            // Log request info
            \Log::info('Fetching all maintenance schedules with parameters:', [
                'page' => $page,
                'limit' => $limit,
                'search' => $search,
                'status' => $status,
                'request_url' => $request->fullUrl()
            ]);

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit
            ];

            // Add search parameter if provided
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Add status filter if provided
            if (!empty($status)) {
                $queryParams['status'] = $status;
            }

            // Fetch maintenance schedules
            $result = $this->apiService->request('GET', '/maintenance', [
                'query' => $queryParams
            ]);

            // Log API response for debugging
            \Log::info('API response for maintenance schedules:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during maintenance schedules retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], status: 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve maintenance schedules';

                \Log::warning('Error during maintenance schedules retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => $errorData
                ], status: 400);
            }

            // Return the response
            return response()->json([
                'success' => true,
                'message' => 'Jadwal pemeliharaan berhasil diambil',
                'data' => $result['data'] ?? [],
                'pagination' => $result['pagination'] ?? [
                    'total_items' => 0,
                    'total_pages' => 0,
                    'current_page' => $page,
                    'limit' => $limit,
                    'has_next' => false,
                    'has_prev' => false
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during maintenance schedules retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['general' => 'Failed to retrieve maintenance schedules: ' . $e->getMessage()]
            ], status: 500);
        }
    }

    /**
     * Get a single maintenance by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMaintenance($id)
    {
        try {
            // Log request info
            \Log::info('Fetching maintenance with ID: ' . $id);

            // Fetch maintenance from API
            $result = $this->apiService->request('GET', '/maintenance/' . $id);

            // Log API response for debugging
            \Log::info('API response for maintenance retrieval:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during maintenance retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], status: 401);
            }

            // Check if the maintenance exists
            if (!isset($result['data']) || empty($result['data'])) {
                \Log::warning('Maintenance not found:', [
                    'id' => $id,
                    'message' => $result['message'] ?? 'Maintenance not found'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['maintenance' => 'Maintenance not found']
                ], status: 404);
            }

            // Return the API response
            return response()->json([
                'success' => true,
                'message' => 'Maintenance retrieved successfully',
                'data' => $result['data']
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during maintenance retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['general' => 'Failed to retrieve maintenance: ' . $e->getMessage()]
            ], status: 500);
        }
    }

    /**
     * Display maintenance details.
     *
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function showMaintenanceDetail($id)
    {
        try {
            // Log request info
            \Log::info('Fetching maintenance details for ID: ' . $id);

            // Fetch maintenance from API
            $result = $this->apiService->request('GET', '/maintenance/' . $id);

            // Log API response for debugging
            \Log::info('API response for maintenance details:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during maintenance details retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check if the maintenance exists
            if (!isset($result['data']) || empty($result['data'])) {
                \Log::warning('Maintenance not found:', [
                    'id' => $id,
                    'message' => $result['message'] ?? 'Maintenance not found'
                ]);

                return redirect()->route('maintenance')->with('error', 'Maintenance not found');
            }

            // Get maintenance history if available
            $maintenanceData = $result['data'];

            // Ensure history is always an array
            if (!isset($maintenanceData['history']) || !is_array($maintenanceData['history'])) {
                $maintenanceData['history'] = [];
            }

            // Try to fetch history if it's not included in the main response
            if (empty($maintenanceData['history'])) {
                try {
                    $historyResult = $this->apiService->request('GET', '/maintenance/' . $id . '/history');
                    if (isset($historyResult['data']) && is_array($historyResult['data'])) {
                        $maintenanceData['history'] = $historyResult['data'];
                    } else {
                        $maintenanceData['history'] = [];
                    }
                } catch (\Exception $e) {
                    \Log::warning('Error fetching maintenance history:', [
                        'error' => $e->getMessage()
                    ]);
                    // Ensure history is an empty array
                    $maintenanceData['history'] = [];
                }
            }

            // Return the view with maintenance data
            return view('Maintenance.MaintenanceDetail', [
                'maintenance' => $maintenanceData
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during maintenance details retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('maintenance')->with('error', 'Failed to retrieve maintenance details: ' . $e->getMessage());
        }
    }

    /**
     * Create maintenance schedules in bulk.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function createBulkMaintenance(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'asset_ids' => 'required|array',
                'asset_ids.*' => 'required|integer',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'interval' => 'required|string',
                'assigned_to' => 'required|integer',
                'vendor_id' => 'nullable|integer'
            ]);

            // Log request info
            \Log::info('Creating bulk maintenance schedules with parameters:', [
                'asset_ids' => $request->input('asset_ids'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'request_url' => $request->fullUrl()
            ]);

            // Remove any duplicate asset IDs to prevent duplicate maintenance
            $assetIds = array_unique($request->input('asset_ids', []));

            // Ensure all asset IDs are integers
            $assetIds = array_map('intval', $assetIds);

            // Prepare request data with deduplicated asset IDs
            $requestData = [
                'asset_ids' => $assetIds,
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'interval' => $request->input('interval'),
                'assigned_to' => (int) $request->input('assigned_to')
            ];

            // Only add vendor_id if it's present and not empty
            if ($request->has('vendor_id') && $request->input('vendor_id') !== null && $request->input('vendor_id') !== '') {
                $requestData['vendor_id'] = (int) $request->input('vendor_id');
            }

            // Send request to API
            $result = $this->apiService->request('POST', '/maintenance/bulk', [
                'json' => $requestData
            ]);

            // Log API response for debugging
            \Log::info('API response for bulk maintenance creation:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during bulk maintenance creation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], status: 401);
            }

            // Check if the request was successful
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to create maintenance schedules';

                \Log::warning('Error during bulk maintenance creation:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => $errorData
                ], status: 400);
            }

            // For AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Maintenance schedules created successfully',
                    'data' => $result['data'] ?? []
                ]);
            }

            // For regular form submissions, redirect with session flash
            return redirect()->route('maintenance')->with('success', 'Maintenance schedules created successfully');

        } catch (\Exception $e) {
            \Log::error('Exception during bulk maintenance creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['general' => 'Failed to create maintenance schedules: ' . $e->getMessage()]
            ], status: 500);
        }
    }

    /**
     * Create maintenance schedules.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createMaintenance(Request $request)
    {
        try {
            // Validate the request
            $validator = \Validator::make($request->all(), [
                'asset_ids' => 'required|array',
                'asset_ids.*' => 'required|integer',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'interval' => 'required|string',
                'assigned_to' => 'required|integer',
                'vendor_id' => 'nullable|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Log request info
            \Log::info('Creating maintenance schedules with parameters:', [
                'asset_ids' => $request->input('asset_ids'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'interval' => $request->input('interval'),
                'request_url' => $request->fullUrl()
            ]);

            // Get asset IDs and make sure they're all integers
            $assetIds = $request->input('asset_ids', []);
            $assetIds = array_map('intval', $assetIds);

            // Prepare request data
            $requestData = [
                'asset_ids' => $assetIds,
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'interval' => $request->input('interval'),
                'assigned_to' => (int) $request->input('assigned_to')
            ];

            // Only add vendor_id if it's present and not empty
            if ($request->has('vendor_id') && $request->input('vendor_id') !== null && $request->input('vendor_id') !== '') {
                $requestData['vendor_id'] = (int) $request->input('vendor_id');
            }

            // Send request to API
            $result = $this->apiService->request('POST', '/maintenance', [
                'json' => $requestData
            ]);

            // Log API response for debugging
            \Log::info('API response for maintenance creation:', [
                'api_response' => $result,
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'api_response_success' => $result['success'] ?? false
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during maintenance creation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], status: 401);
            }

            // Return the response from API
            return response()->json([
                'success' => isset($result['success']) && $result['success'] === true,
                'data' => $result['data'] ?? [],
                'errors' => isset($result['success']) && $result['success'] !== true ? ($result['errors'] ?? ['general' => 'Failed to create maintenance']) : null
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during maintenance creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['general' => 'Failed to create maintenance schedules: ' . $e->getMessage()]
            ], status: 500);
        }
    }

    /**
     * Update a maintenance record.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate the request
            $validator = \Validator::make($request->all(), [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'interval' => 'nullable|string|in:DAILY,WEEKLY,MONTHLY,QUARTERLY,BIANNUAL,ANNUAL',
                'assigned_to' => 'nullable|integer',
                'vendor_id' => 'nullable|integer',
                'status' => 'nullable|string|in:new,scheduled,in_progress,completed,canceled',
                'notes' => 'nullable|string',
                'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
            ]);

            if ($validator->fails()) {
                \Log::warning('Validation error in maintenance update:', [
                    'errors' => $validator->errors()->toArray()
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Log request info
            \Log::info('Updating maintenance with ID: ' . $id, [
                'request_data' => $request->except(['file']),
                'request_url' => $request->fullUrl()
            ]);

            // Check if this is a regular JSON request or a multipart request with file
            if ($request->hasFile('file')) {
                \Log::info('File detected in maintenance update request', [
                    'file_name' => $request->file('file')->getClientOriginalName(),
                    'file_size' => $request->file('file')->getSize(),
                    'file_type' => $request->file('file')->getMimeType()
                ]);

                // Handle file upload via multipart
                $file = $request->file('file');
                $multipart = [
                    [
                        'name' => 'file',
                        'contents' => fopen($file->getPathname(), 'r'),
                        'filename' => $file->getClientOriginalName()
                    ]
                ];

                // Add other fields to the multipart request
                foreach ($request->except(['file']) as $key => $value) {
                    if (!is_null($value) && $value !== '') {
                    $multipart[] = [
                        'name' => $key,
                        'contents' => $value
                    ];
                    }
                }

                \Log::info('Sending multipart request with file to API', [
                    'multipart_fields' => array_map(function($item) {
                        return $item['name'];
                    }, $multipart)
                ]);

                // Send request to API using multipart form data
                $result = $this->apiService->request('PUT', '/maintenance/' . $id, [
                    'multipart' => $multipart
                ]);
            } else {
                // Process JSON request - only include non-empty values
                $requestData = [];

                foreach ($request->all() as $key => $value) {
                    if ($key !== '_method' && $key !== '_token' && !is_null($value) && $value !== '') {
                        $requestData[$key] = $value;
                    }
                }

                \Log::info('Sending maintenance update request without file', [
                    'request_data_keys' => array_keys($requestData)
                ]);

                // Send request to API without file
                $result = $this->apiService->request('PUT', '/maintenance/' . $id, [
                    'json' => $requestData
                ]);
            }

            // Log API response for debugging
            \Log::info('API response for maintenance update:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during maintenance update:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], status: 401);
            }

            // Check if the request was successful
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to update maintenance';

                \Log::warning('Error during maintenance update:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => $errorData
                ], status: 400);
            }

            // For AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jadwal pemeliharaan berhasil diperbarui',
                    'data' => $result['data'] ?? []
                ]);
            }

            // For regular form submissions, redirect with session flash
            return redirect()->route('maintenance')->with('success', 'Jadwal pemeliharaan berhasil diperbarui');

        } catch (\Exception $e) {
            \Log::error('Exception during maintenance update:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['general' => 'Failed to update maintenance: ' . $e->getMessage()]
            ], status: 500);
        }
    }

    /**
     * Delete a maintenance record by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            \Log::info('Deleting maintenance record with ID: ' . $id);

            // Call the API to delete the maintenance record
            $result = $this->apiService->request('DELETE', '/maintenance/' . $id);

            // Log the API response for debugging
            \Log::info('API response for maintenance deletion:', [
                'api_response_success' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during maintenance deletion:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], status: 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check if the request was successful
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to delete maintenance record';

                \Log::warning('Error during maintenance deletion:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], status: 400);
                }

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

                return redirect()->route('maintenance')
                    ->with('error', $errorMessage);
            }

            // For AJAX requests
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Maintenance record deleted successfully'
                ]);
            }

            // For regular form submissions, redirect with session flash
            return redirect()->route('maintenance')->with('success', 'Maintenance record deleted successfully');

        } catch (\Exception $e) {
            \Log::error('Exception during maintenance deletion:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['general' => 'Failed to delete maintenance record: ' . $e->getMessage()]
                ], status: 500);
            }

            return redirect()->route('maintenance')->with('error', 'Failed to delete maintenance record: ' . $e->getMessage());
        }
    }

    /**
     * Export maintenance data to PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportMaintenancePDF(Request $request)
    {
        try {
            // Get filter parameters
            $search = $request->input('search', '');
            $status = $request->input('status', '');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            // Log request info
            \Log::info('Exporting maintenance to PDF with parameters:', [
                'search' => $search,
                'status' => $status,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'request_url' => $request->fullUrl()
            ]);

            // Build query parameters
            $queryParams = [
                'page' => 1,
                'limit' => 1000  // Get a large number for export
            ];

            // Add search parameter if provided
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Add status filter if provided
            if (!empty($status)) {
                $queryParams['status'] = $status;
            }

            // Add sorting parameters
            if (!empty($sortBy)) {
                $queryParams['sort_by'] = $sortBy;
            }

            if (!empty($sortOrder)) {
                $queryParams['sort_order'] = $sortOrder;
            }

            // Fetch maintenance records from API
            $result = $this->apiService->request('GET', '/maintenance', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during maintenance PDF export:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Get maintenance data
            $maintenances = $result['data'] ?? [];

            // Generate PDF
            $pdf = Pdf::loadView('Maintenance.MaintenancePDF', [
                'maintenances' => $maintenances,
                'search' => $search,
                'status' => $status,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'date_generated' => now()->format('d M Y H:i:s')
            ]);

            // Log PDF generation
            \Log::info('Maintenance PDF generated successfully', [
                'maintenances_count' => count($maintenances)
            ]);

            // Stream the PDF to browser
            return $pdf->stream('maintenance_report_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Exception during maintenance PDF export:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to export Maintenance as PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export a single maintenance record to PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportMaintenanceDetailPDF($id)
    {
        try {
            // Log request info
            \Log::info('Exporting single maintenance to PDF with ID: ' . $id);

            // Fetch maintenance from API
            $result = $this->apiService->request('GET', '/maintenance/' . $id);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during maintenance PDF export:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check if the maintenance exists
            if (!isset($result['data']) || empty($result['data'])) {
                \Log::warning('Maintenance not found:', [
                    'id' => $id,
                    'message' => $result['message'] ?? 'Maintenance not found'
                ]);

                return redirect()->route('maintenance')->with('error', 'Maintenance not found');
            }

            // Get maintenance data
            $maintenance = $result['data'];

            // Convert maintenance report attachment to base64 if exists
            if (isset($maintenance['maintenance_report']) && !empty($maintenance['maintenance_report']['attachment_path'])) {
                try {
                    // Use config service instead of directly accessing protected property
                    $baseUrl = rtrim(config('services.api.base_url', 'http://localhost:5000'), '/');
                    $imagePath = $baseUrl . '/public/images/' . basename($maintenance['maintenance_report']['attachment_path']);
                    \Log::info('Attempting to load image for base64 conversion:', [
                        'image_path' => $imagePath
                    ]);

                    $imageData = file_get_contents($imagePath);
                    if ($imageData !== false) {
                        $maintenance['maintenance_report']['attachment_picture_base64'] = base64_encode($imageData);
                        \Log::info('Successfully converted maintenance report image to base64');
                    } else {
                        \Log::warning('Failed to get image data for maintenance report:', [
                            'image_path' => $imagePath
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Exception while converting maintenance report image to base64:', [
                        'error' => $e->getMessage(),
                        'maintenance_id' => $id,
                        'image_path' => $maintenance['maintenance_report']['attachment_path'] ?? 'N/A'
                    ]);
                }
            }

            // Generate PDF
            $pdf = Pdf::loadView('Maintenance.MaintenanceDetailPDF', [
                'maintenance' => $maintenance,
                'date_generated' => now()->format('d M Y H:i:s')
            ]);

            // Log PDF generation
            \Log::info('Single maintenance PDF generated successfully', [
                'maintenance_id' => $maintenance['id'] ?? 'N/A'
            ]);

            // Stream the PDF to browser
            return $pdf->stream('maintenance_report_' . $id . '_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Exception during detail maintenance PDF export:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to export Maintenance as PDF: ' . $e->getMessage());
        }
    }

    /**
     * Create a maintenance report with optional attachment.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function createMaintenanceReport(Request $request)
    {
        try {
            // Log all request data for debugging
            \Log::info('Maintenance report request data:', [
                'all_data' => $request->all(),
                'has_file' => $request->hasFile('file')
            ]);

            // Validate the request
            $validator = \Validator::make($request->all(), [
                'maintenance_id' => 'required|integer',
                'description' => 'required|string',
                'maintenance_date' => 'nullable|date',
                'file' => 'nullable|file|mimes:jpeg,png,jpg|max:10240', // Accept 'file' field
            ]);

            if ($validator->fails()) {
                \Log::warning('Validation failed during maintenance report creation:', [
                    'errors' => $validator->errors()->toArray()
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            \Log::info('Creating maintenance report', [
                'maintenance_id' => $request->maintenance_id,
                'has_maintenance_date' => $request->has('maintenance_date'),
                'maintenance_date_value' => $request->input('maintenance_date'),
                'description' => $request->description,
                'has_file' => $request->hasFile('file')
            ]);

            // Build multipart request for API
            $multipart = [];

            // Add file if present
            if ($request->hasFile('file')) {
                $fileData = $request->file('file');

                \Log::info('Processing file upload for maintenance report', [
                    'file_name' => $fileData->getClientOriginalName(),
                    'file_size' => $fileData->getSize(),
                    'mime_type' => $fileData->getMimeType()
                ]);

                $multipart[] = [
                    'name' => 'file', // Use 'file' for API based on screenshot
                    'contents' => fopen($fileData->getPathname(), 'r'),
                    'filename' => $fileData->getClientOriginalName()
                ];
            }

            // Add maintenance_id field
            $multipart[] = [
                'name' => 'maintenance_id',
                'contents' => $request->input('maintenance_id')
            ];

            // Add description field
            $multipart[] = [
                'name' => 'description',
                'contents' => $request->input('description')
            ];

            // Add maintenance_date field if provided, otherwise use current date
            $multipart[] = [
                'name' => 'maintenance_date',
                'contents' => $request->input('maintenance_date') ?: now()->format('Y-m-d')
            ];

            // Add reporter_number if needed
            if (!$request->has('reporter_number')) {
                $multipart[] = [
                    'name' => 'reporter_number',
                    'contents' => '1234'  // Default reporter number
                ];
            }

            // Log the multipart request structure
            \Log::info('Sending multipart request for maintenance report', [
                'multipart_fields' => array_map(function($item) {
                    return ['name' => $item['name'], 'value' => $item['name'] === 'file' ? 'FILE_DATA' : substr((string)$item['contents'], 0, 100)];
                }, $multipart)
            ]);

            // Call API with multipart request
            $result = $this->apiService->request('POST', '/maintenance-reports', [
                'multipart' => $multipart
            ]);

            // Log complete API response
            \Log::info('API full response for maintenance report creation:', [
                'response' => $result
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during maintenance report creation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], status: 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Failed to create maintenance report';

                \Log::warning('Error during maintenance report creation:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

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

                return redirect()->back()
                    ->with('error', $errorMessage);
            }

            // Return response in the expected format
            return response()->json([
                'success' =>  $result['success'] ?? true,
                'message' => $result['message'] ?? 'Maintenance report created successfully',
                'data' => $result['data'] ?? null
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during maintenance report creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['general' => 'Failed to create maintenance report: ' . $e->getMessage()]
            ], status: 500);
        }
    }
}
