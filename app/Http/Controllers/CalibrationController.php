<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Barryvdh\DomPDF\Facade\Pdf;

class CalibrationController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display a listing of calibrations.
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
            $result = $request->input('result', '');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            // Log request info with detailed search info
            \Log::info('Fetching calibrations for view with parameters:', [
                'page' => $page,
                'limit' => $limit,
                'search' => $search,
                'status' => $status,
                'result' => $result,
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

            // Add search parameter if provided - this should search across task_code, asset_name, and asset_code
            if (!empty($search)) {
                $queryParams['search'] = $search;
                \Log::debug('Search query added:', ['search' => $search]);
            }

            // Add status filter if provided
            if (!empty($status)) {
                // Only pass valid status values
                if (in_array($status, ['scheduled', 'in_progress', 'completed', 'overdue', 'cancelled'])) {
                    $queryParams['status_calibration'] = $status; // Pass status_calibration to match API field name
                }
            }

            // Add result filter if provided
            if (!empty($result)) {
                // Only pass valid result values
                if (in_array($result, ['pass', 'fail', 'unknown'])) {
                    $queryParams['calibration_result'] = $result;
                }
            }

            // Add sorting parameters
            if (!empty($sortBy)) {
                $queryParams['sort_by'] = $sortBy;
            }

            if (!empty($sortOrder)) {
                $queryParams['sort_order'] = $sortOrder;
            }

            // Fetch calibrations with detailed logging
            \Log::debug('Sending API request with query params:', $queryParams);
            $result = $this->apiService->request('GET', '/calibrations', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during calibrations retrieval:', [
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
                $errorData = $result['errors'] ?? 'Failed to retrieve calibrations';

                \Log::warning('Error during calibrations retrieval:', [
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

                return view('Calibration.Calibration', [
                    'calibrations' => [],
                    'pagination' => null,
                    'search' => $search,
                    'status' => $status,
                    'result' => $result,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder,
                    'error' => $errorMessage
                ]);
            }

            // Log API response for debugging
            \Log::info('API response for calibrations view:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'data_count' => isset($result['data']) ? count($result['data']) : 0
            ]);

            // Parse data for view
            $calibrations = $result['data'] ?? [];
            $pagination = $result['pagination'] ?? null;

            // For AJAX requests, return JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'calibrations' => $calibrations,
                    'pagination' => $pagination
                ]);
            }

            // Return the view with data for regular requests
            return view('Calibration.Calibration', [
                'calibrations' => $calibrations,
                'pagination' => $pagination,
                'search' => $search,
                'status' => $status,
                'result' => $result,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during calibrations view retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retrieve calibrations: ' . $e->getMessage()
                ], 500);
            }

            return view('Calibration.Calibration', [
                'calibrations' => [],
                'pagination' => null,
                'search' => $search,
                'status' => $status,
                'result' => $result,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'error' => 'Failed to retrieve calibrations: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get all calibrations.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCalibrations(Request $request)
    {
        try {
            // Get pagination parameters
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');

            // Log request info
            \Log::info('Fetching all calibrations with parameters:', [
                'page' => $page,
                'limit' => $limit,
                'search' => $search,
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

            // Fetch calibrations
            $result = $this->apiService->request('GET', '/calibrations', [
                'query' => $queryParams
            ]);

            // Log API response for debugging
            \Log::info('API response for calibrations:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during calibrations retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], status: 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve calibrations';

                \Log::warning('Error during calibrations retrieval:', [
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
                'message' => 'Calibrations retrieved successfully',
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
            \Log::error('Exception during calibrations retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve calibrations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create calibrations in bulk.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function createBulkCalibrations(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'asset_ids' => 'required|array',
                'planning_calibration_date' => 'required|date'
            ]);

            // Log request info
            \Log::info('Creating bulk calibrations with parameters:', [
                'asset_ids' => $request->input('asset_ids'),
                'planning_date' => $request->input('planning_calibration_date'),
                'request_url' => $request->fullUrl()
            ]);

            // Remove any duplicate asset IDs to prevent duplicate calibrations
            $assetIds = array_unique($request->input('asset_ids'));

            // Prepare request data with deduplicated asset IDs
            $requestData = [
                'asset_ids' => $assetIds,
                'planning_calibration_date' => $request->input('planning_calibration_date')
            ];

            // Send request to API
            $result = $this->apiService->request('POST', '/calibrations/bulk', [
                'json' => $requestData
            ]);

            // Log API response for debugging
            \Log::info('API response for bulk calibration creation:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during bulk calibration creation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], status: 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to create calibrations';

                \Log::warning('Error during bulk calibration creation:', [
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
                    'message' => $result['message'] ?? 'Calibrations created successfully',
                    'data' => $result['data'] ?? []
                ]);
            }

            // For regular form submissions, redirect with session flash
            return redirect()->route('calibration')->with('success', 'Calibrations created successfully');

        } catch (\Exception $e) {
            \Log::error('Exception during bulk calibration creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => 'Failed to create calibrations: ' . $e->getMessage()]
            ], status: 500);
        }
    }

    /**
     * Update a calibration record.
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
                'actual_calibration_date' => 'nullable|date',
                'next_calibration_date' => 'nullable|date',
                'status_calibration' => 'nullable|string|in:scheduled,in_progress,completed,overdue',
                'vendor_id' => 'nullable|integer',
                'certificate_number' => 'nullable|string|max:255',
                'calibration_result' => 'nullable|string|max:255',
                'calibration_price' => 'nullable|numeric',
                'notes' => 'nullable|string',
                'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
            ]);

            if ($validator->fails()) {
                \Log::warning('Validation error in calibration update:', [
                    'errors' => $validator->errors()->toArray()
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], status: 422);
            }

            // Log request info
            \Log::info('Updating calibration with ID: ' . $id, [
                'request_data' => $request->except(['file']),
                'request_url' => $request->fullUrl()
            ]);

            // Verify if file exists in request
            if ($request->hasFile('file')) {
                \Log::info('File detected in calibration update request', [
                    'file_name' => $request->file('file')->getClientOriginalName(),
                    'file_size' => $request->file('file')->getSize(),
                    'file_type' => $request->file('file')->getMimeType()
                ]);
            } else {
                \Log::info('No file detected in calibration update request');
            }

            $requestData = [];
            $fields = [
                'actual_calibration_date',
                'next_calibration_date',
                'status_calibration',
                'vendor_id',
                'certificate_number',
                'calibration_result',
                'calibration_price',
                'notes'
            ];

            foreach ($fields as $field) {
                if ($request->has($field) && $request->input($field) !== '') {
                    $requestData[$field] = $request->input($field);
                }
            }

            // Handle file upload if present
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                \Log::info('Processing file for calibration update:', [
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_mime' => $file->getMimeType()
                ]);

                // Create a multipart upload instead of base64 encoding
                $multipart = [
                    [
                        'name' => 'file',
                        'contents' => fopen($file->getPathname(), 'r'),
                        'filename' => $file->getClientOriginalName()
                    ]
                ];

                // Add all other fields to the multipart request
                foreach ($requestData as $key => $value) {
                    $multipart[] = [
                        'name' => $key,
                        'contents' => $value
                    ];
                }

                \Log::info('Sending multipart request with file to API', [
                    'multipart_fields' => array_map(function($item) {
                        return $item['name'];
                    }, $multipart)
                ]);

                // Send request to API using multipart form data
                $result = $this->apiService->request('PUT', '/calibrations/' . $id, [
                    'multipart' => $multipart
                ]);
            } else {
                \Log::info('Sending calibration update request without file', [
                    'request_data_keys' => array_keys($requestData)
                ]);

                // Send request to API without file
                $result = $this->apiService->request('PUT', '/calibrations/' . $id, [
                    'json' => $requestData
                ]);
            }

            // Log API response for debugging
            \Log::info('API response for calibration update:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'api_response_data' => $result
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during calibration update:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], status: 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to update calibration';

                \Log::warning('Error during calibration update:', [
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
                    'message' => 'Calibration telah dilakukan',
                    'data' => $result['data'] ?? []
                ]);
            }

            // For regular form submissions, redirect with session flash
            return redirect()->route('calibration')->with('success', 'Calibration telah dilakukan');

        } catch (\Exception $e) {
            \Log::error('Exception during calibration update:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update calibration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a calibration record or multiple records.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        try {
            // Debug log to see what's in the request
            \Log::info('Calibration destroy method called with request data:', [
                'all' => $request->all(),
                'has_ids' => $request->has('ids'),
                'ids_value' => $request->input('ids'),
                'content_type' => $request->header('Content-Type')
            ]);

            // Get IDs from the request - handle different possible formats
            $ids = null;
            $requestData = $request->json()->all(); // Try to get JSON data first

            // Try multiple ways to get the IDs
            if (isset($requestData['ids']) && is_array($requestData['ids'])) {
                $ids = $requestData['ids'];
                \Log::info('IDs found in JSON body', ['ids' => $ids]);
            } elseif ($request->has('ids')) {
                $ids = $request->input('ids');
                \Log::info('IDs found in request input', ['ids' => $ids]);
            } elseif ($request->has('all') && isset($request->all()['all']['ids'])) {
                $ids = $request->all()['all']['ids'];
                \Log::info('IDs found in all.ids', ['ids' => $ids]);
            }

            // Make sure we have a valid array of IDs
            if (empty($ids) || !is_array($ids)) {
                \Log::warning('No valid IDs array found in request');
                return response()->json([
                    'success' => false,
                    'message' => 'Valid calibration IDs are required'
                ], status: 400);
            }

            // Convert all IDs to integers to ensure they match the expected format
            $ids = array_map('intval', $ids);

            \Log::info('Deleting calibrations with IDs: ' . implode(', ', $ids));

            // Build the payload for the API
            $payload = [
                'json' => [
                    'ids' => $ids
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ];

            \Log::info('Sending API request with payload:', $payload);

            // Call the API to delete the calibrations
            $result = $this->apiService->request('DELETE', '/calibrations/bulk', $payload);

            // Log the full API response for debugging
            \Log::info('API response:', [
                'full_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during calibration deletion:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], status: 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to delete calibrations';

                \Log::warning('Error during calibration deletion:', [
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
                    'message' => 'Calibrations deleted successfully',
                    'data' => $result['data'] ?? []
                ]);
            }

            // For regular form submissions, redirect with session flash
            return redirect()->route('calibration')->with('success', 'Calibrations deleted successfully');

        } catch (\Exception $e) {
            \Log::error('Exception during calibration deletion:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete calibration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single calibration by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCalibration($id)
    {
        try {
            // Log request info
            \Log::info('Fetching calibration with ID: ' . $id);

            // Fetch calibration from API
            $result = $this->apiService->request('GET', '/calibrations/' . $id);

            // Log API response for debugging
            \Log::info('API response for calibration retrieval:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during calibration retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], status: 401);
            }

            // Check if the calibration exists
            if (!isset($result['data']) || empty($result['data'])) {
                \Log::warning('Calibration not found:', [
                    'id' => $id,
                    'errors' => $result['errors'] ?? 'Calibration not found'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? ['not_found' => 'Calibration not found']
                ], status: 404);
            }

            // Return the API response
            return response()->json([
                'success' => true,
                'message' => 'Calibration retrieved successfully',
                'data' => $result['data']
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during calibration retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => 'Failed to retrieve calibration: ' . $e->getMessage()]
            ], 500);
        }
    }

    /**
     * Display calibration details.
     *
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function showCalibrationDetail($id)
    {
        try {
            // Log request info
            \Log::info('Fetching calibration details for ID: ' . $id);

            // Fetch calibration from API
            $result = $this->apiService->request('GET', '/calibrations/' . $id);

            // Log API response for debugging
            \Log::info('API response for calibration details:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during calibration details retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check if the calibration exists
            if (!isset($result['data']) || empty($result['data'])) {
                \Log::warning('Calibration not found:', [
                    'id' => $id,
                    'message' => $result['message'] ?? 'Calibration not found'
                ]);

                return redirect()->route('calibration')->with('error', 'Calibration not found');
            }

            // Get calibration history if available
            $calibrationData = $result['data'];

            // Try to fetch history if it's not included in the main response
            if (!isset($calibrationData['history'])) {
                try {
                    $historyResult = $this->apiService->request('GET', '/calibrations/' . $id . '/history');
                    if (isset($historyResult['data']) && !empty($historyResult['data'])) {
                        $calibrationData['history'] = $historyResult['data'];
                    }
                } catch (\Exception $e) {
                    \Log::warning('Error fetching calibration history:', [
                        'error' => $e->getMessage()
                    ]);
                    // Continue without history if it fails
                }
            }

            // Return the view with calibration data
            return view('Calibration.CalibrationDetail', [
                'calibration' => $calibrationData
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during calibration details retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('calibration')->with('error', 'Failed to retrieve calibration details: ' . $e->getMessage());
        }
    }

    /**
     * Export calibrations data to PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportCalibrationPDF(Request $request)
    {
        try {
            // Get filter parameters
            $search = $request->input('search', '');
            $status = $request->input('status', '');
            $result = $request->input('result', '');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            // Log request info
            \Log::info('Exporting calibrations to PDF with parameters:', [
                'search' => $search,
                'status' => $status,
                'result' => $result,
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
                // Only pass valid status values
                if (in_array($status, ['scheduled', 'in_progress', 'completed', 'overdue', 'cancelled'])) {
                    $queryParams['status_calibration'] = $status;
                }
            }

            // Add result filter if provided
            if (!empty($result)) {
                // Only pass valid result values
                if (in_array($result, ['pass', 'fail', 'unknown'])) {
                    $queryParams['calibration_result'] = $result;
                }
            }

            // Add sorting parameters
            if (!empty($sortBy)) {
                $queryParams['sort_by'] = $sortBy;
            }

            if (!empty($sortOrder)) {
                $queryParams['sort_order'] = $sortOrder;
            }

            // Fetch calibrations from API
            $result = $this->apiService->request('GET', '/calibrations', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during calibrations PDF export:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Get calibrations data
            $calibrations = $result['data'] ?? [];

            // Generate PDF
            $pdf = Pdf::loadView('Calibration.CalibrationPDF', [
                'calibrations' => $calibrations,
                'search' => $search,
                'status' => $status,
                'result' => $result,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'date_generated' => now()->format('d M Y H:i:s')
            ]);

            // Log PDF generation
            \Log::info('Calibrations PDF generated successfully', [
                'calibrations_count' => count($calibrations)
            ]);

            // Stream the PDF to browser
            return $pdf->stream('calibration_report_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Exception during calibrations PDF export:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to export Calibrations as PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export a single calibration detail to PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportCalibrationDetailPDF($id)
    {
        try {
            // Log request info
            \Log::info('Exporting calibration detail to PDF for ID: ' . $id);

            // Fetch calibration from API
            $result = $this->apiService->request('GET', '/calibrations/' . $id);

            // Log API response for debugging
            \Log::info('API response for calibration detail PDF export:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during calibration detail PDF export:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check if the calibration exists
            if (!isset($result['data']) || empty($result['data'])) {
                \Log::warning('Calibration not found for PDF export:', [
                    'id' => $id,
                    'message' => $result['message'] ?? 'Calibration not found'
                ]);

                return redirect()->route('calibration')->with('error', 'Calibration not found');
            }

            // Get calibration data
            $calibrationData = $result['data'];

            // Try to fetch history if it's not included in the main response
            if (!isset($calibrationData['history'])) {
                try {
                    $historyResult = $this->apiService->request('GET', '/calibrations/' . $id . '/history');
                    if (isset($historyResult['data']) && !empty($historyResult['data'])) {
                        $calibrationData['history'] = $historyResult['data'];
                    }
                } catch (\Exception $e) {
                    \Log::warning('Error fetching calibration history for PDF:', [
                        'error' => $e->getMessage()
                    ]);
                    // Continue without history if it fails
                }
            }

            // Convert certificate file to base64 if it exists and is an image
            if (!empty($calibrationData['certificate_file_path'])) {
                try {
                    $fileName = basename($calibrationData['certificate_file_path']);
                    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                    $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']);

                    if ($isImage) {
                        $imagePath = 'http://localhost:5000/public/images/' . $fileName;
                        $imageData = file_get_contents($imagePath);
                        if ($imageData !== false) {
                            $calibrationData['certificate_file_base64'] = base64_encode($imageData);
                        }
                    } else {
                        // For documents, store the URL
                        $calibrationData['certificate_file_url'] = 'http://localhost:5000/public/documents/' . $fileName;
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to process certificate file for PDF:', [
                        'error' => $e->getMessage(),
                        'calibration_id' => $id,
                        'file_path' => $calibrationData['certificate_file_path'] ?? 'N/A'
                    ]);
                }
            }

            // Generate PDF using the same view as the detail page
            $pdf = PDF::loadView('Calibration.CalibrationDetailPDF', [
                'calibration' => $calibrationData,
                'date_generated' => now()->format('d M Y H:i:s')
            ]);

            // Log PDF generation
            \Log::info('Calibration detail PDF generated successfully', [
                'calibration_id' => $id
            ]);

            // Stream the PDF to browser
            return $pdf->stream('calibration_detail_' . $id . '_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Exception during calibration detail PDF export:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to export Calibration detail as PDF: ' . $e->getMessage());
        }
    }
}
