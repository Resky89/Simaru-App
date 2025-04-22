<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class ComplainRepairController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Get all complaints.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function getAllComplaints(Request $request)
    {
        try {
            // Get pagination parameters
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');
            $sort = $request->input('sort', 'newest');
            $status = $request->input('status', '');

            // Log request info
            \Log::info('Fetching all complaints with parameters:', [
                'page' => $page,
                'limit' => $limit,
                'search' => $search,
                'sort' => $sort,
                'status' => $status,
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
            }

            // Add status filter if provided
            if (!empty($status)) {
                $queryParams['status'] = $status;
            }

            // Handle sorting
            switch ($sort) {
                case 'newest':
                    $queryParams['sort_by'] = 'created_at';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'oldest':
                    $queryParams['sort_by'] = 'created_at';
                    $queryParams['sort_order'] = 'asc';
                    break;
                default:
                    // Default sort (newest first)
                    $queryParams['sort_by'] = 'created_at';
                    $queryParams['sort_order'] = 'desc';
            }

            // Fetch complaints from API
            $result = $this->apiService->request('GET', '/complaints', [
                'query' => $queryParams
            ]);

            // Log API response for debugging
            \Log::info('API response for complaints:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'data_count' => isset($result['data']) ? count($result['data']) : 0
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during complaints retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message'] ?? 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Get complaints and pagination data
            $complaints = $result['data'] ?? [];
            $pagination = $result['pagination'] ?? null;

            // For AJAX or JSON requests, return JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Complaints retrieved successfully',
                    'data' => $complaints,
                    'pagination' => $pagination
                ]);
            }

            // For regular requests, return view
            return view('ComplainRepair.ComplainRepair', [
                'complaints' => $complaints,
                'pagination' => $pagination,
                'search' => $search,
                'sort' => $sort,
                'status' => $status
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during complaints retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to retrieve complaints: ' . $e->getMessage()
                ], 500);
            }

            return view('ComplainRepair.ComplainRepair', [
                'complaints' => [],
                'pagination' => null,
                'search' => $search,
                'sort' => $sort,
                'status' => $status,
                'error' => 'Failed to retrieve complaints: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get complaint detail by ID.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showComplaintDetail($id, Request $request)
    {
        try {
            // Log request info
            \Log::info('Fetching complaint detail:', [
                'id' => $id,
                'request_url' => $request->fullUrl(),
                'ajax' => $request->ajax()
            ]);

            // Fetch complaint details from API
            $result = $this->apiService->request('GET', "/complaints/{$id}");

            // Log API response for debugging
            \Log::info('API response for complaint detail:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'has_data' => isset($result['data'])
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during complaint detail retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed',
                    'complaint_id' => $id
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message'] ?? 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check if complaint exists
            if (!isset($result['data'])) {
                $errorMessage = 'Complaint not found';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => false,
                        'message' => $errorMessage
                    ], 404);
                }

                return redirect()->route('complaint.index')->with('error', $errorMessage);
            }

            // Get complaint data
            $complaint = $result['data'];

            // For AJAX or JSON requests, return JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Complaint retrieved successfully',
                    'data' => $complaint
                ]);
            }

            // For regular requests, return view
            return view('ComplainRepair.ComplaintDetail', [
                'complaint' => $complaint
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during complaint detail retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'complaint_id' => $id
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to retrieve complaint detail: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('complaint.index')->with('error', 'Failed to retrieve complaint detail: ' . $e->getMessage());
        }
    }
}
