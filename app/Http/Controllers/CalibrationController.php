<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

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
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');
            $status = $request->input('status', '');

            // Log request info
            \Log::info('Fetching calibrations for view with parameters:', [
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

            // Fetch calibrations
            $result = $this->apiService->request('GET', '/calibrations', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during calibrations retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
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

            // Return the view with data
            return view('Calibration', [
                'calibrations' => $calibrations,
                'pagination' => $pagination,
                'search' => $search,
                'status' => $status
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during calibrations view retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('Calibration', [
                'calibrations' => [],
                'pagination' => null,
                'search' => $search,
                'status' => $status,
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
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during calibrations retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
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
 * @return \Illuminate\Http\JsonResponse
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
        if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
            \Log::warning('Authentication error during bulk calibration creation:', [
                'error' => $result['error'],
                'message' => $result['message'] ?? 'Authentication failed'
            ]);

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Authentication failed'
            ], 401);
        }

        // Check if the request was successful
        if (!isset($result['success']) || $result['success'] !== true) {
            \Log::warning('Error during bulk calibration creation:', [
                'message' => $result['message'] ?? 'Failed to create calibrations'
            ]);

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to create calibrations'
            ], 400);
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
            return redirect()->route('calibrations.index')
                ->with('success', $result['message'] ?? 'Calibrations created successfully');

        } catch (\Exception $e) {
            \Log::error('Exception during bulk calibration creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to create calibrations: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Update a calibration record.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate the request
            $request->validate([
                'actual_calibration_date' => 'nullable|date',
                'next_calibration_date' => 'nullable|date',
                'status_calibration' => 'nullable|string|in:scheduled,in_progress,completed,overdue',
                'vendor_id' => 'nullable|integer',
                'certificate_number' => 'nullable|string|max:255',
                'calibration_result' => 'nullable|string|max:255',
                'calibration_price' => 'nullable|numeric',
                'notes' => 'nullable|string',
                'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240'
            ]);

            // Log request info
            \Log::info('Updating calibration with ID: ' . $id, [
                'request_data' => $request->except(['document_file']),
                'request_url' => $request->fullUrl()
            ]);

            // Prepare request data - ONLY include the fields that were submitted
            // Don't use except() as it will include empty fields
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
            if ($request->hasFile('document_file')) {
                $file = $request->file('document_file');

                // Create a multipart upload instead of base64 encoding
                $multipart = [
                    [
                        'name' => 'document_file',
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

                // Send request to API using multipart form data
                $result = $this->apiService->request('PUT', '/calibrations/' . $id, [
                    'multipart' => $multipart
                ]);
            } else {
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
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during calibration update:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check if the request was successful
            if (!isset($result['success']) || $result['success'] !== true) {
                \Log::warning('Error during calibration update:', [
                    'message' => $result['message'] ?? 'Failed to update calibration',
                    'response' => $result
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to update calibration'
                ], 400);
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
            return redirect()->route('calibrations.index')
                ->with('success', 'Calibration telah dilakukan');

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
     * @return \Illuminate\Http\JsonResponse
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
                ], 400);
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

            // Check if the request was successful
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to delete calibrations';
                $errorDetails = $result['errors'] ?? null;

                \Log::warning('Error during calibration deletion:', [
                    'message' => $errorMessage,
                    'errors' => $errorDetails
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => $errorDetails
                ], 400);
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
            return redirect()->route('calibrations.index')
                ->with('success', $result['message'] ?? 'Calibrations deleted successfully');

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
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during calibration retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check if the calibration exists
            if (!isset($result['data']) || empty($result['data'])) {
                \Log::warning('Calibration not found:', [
                    'id' => $id,
                    'message' => $result['message'] ?? 'Calibration not found'
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Calibration not found'
                ], 404);
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
                'message' => 'Failed to retrieve calibration: ' . $e->getMessage()
            ], 500);
        }
    }
}
