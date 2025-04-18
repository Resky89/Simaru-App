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

            // Prepare request data
            $requestData = [
                'asset_ids' => $request->input('asset_ids'),
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

            // Return the API response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Calibrations created successfully',
                'data' => $result['data'] ?? []
            ]);

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

            // Prepare request data
            $requestData = $request->except(['document_file']);

            // Handle file upload if present
            if ($request->hasFile('document_file')) {
                $file = $request->file('document_file');

                // You may need to adjust this based on your API's requirements
                // This assumes the API can accept base64 encoded files
                $requestData['document_file'] = base64_encode(file_get_contents($file->path()));
                $requestData['document_file_name'] = $file->getClientOriginalName();
                $requestData['document_file_type'] = $file->getMimeType();
            }

            // Send request to API
            $result = $this->apiService->request('PUT', '/calibrations/' . $id, [
                'json' => $requestData
            ]);

            // Log API response for debugging
            \Log::info('API response for calibration update:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null
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
                    'message' => $result['message'] ?? 'Failed to update calibration'
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to update calibration'
                ], 400);
            }

            // Return the API response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Calibration updated successfully',
                'data' => $result['data'] ?? []
            ]);

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
     * Delete a calibration record.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Log request info
            \Log::info('Deleting calibration with ID: ' . $id);

            // Send request to API
            $result = $this->apiService->request('DELETE', '/calibrations/' . $id);

            // Log API response for debugging
            \Log::info('API response for calibration deletion:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during calibration deletion:', [
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
                \Log::warning('Error during calibration deletion:', [
                    'message' => $result['message'] ?? 'Failed to delete calibration'
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to delete calibration'
                ], 400);
            }

            // Return the API response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Calibration deleted successfully'
            ]);

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
