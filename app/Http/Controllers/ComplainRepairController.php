<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Barryvdh\DomPDF\Facade\Pdf;

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

            // Fetch subcategories for dropdown
            $subcategoriesResult = $this->apiService->request('GET', '/asset-subcategories');

            // Fetch assets for dropdown (limited number for initial load)
            $assetsResult = $this->apiService->request('GET', '/assets', [
                'query' => [
                    'limit' => 100,
                    'sort_by' => 'asset_name',
                    'sort_order' => 'asc'
                ]
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

            // Parse subcategories and group by asset type
            $subcategories = $subcategoriesResult['data'] ?? [];
            $medicalSubcategories = [];
            $nonMedicalSubcategories = [];

            foreach ($subcategories as $subcategory) {
                if (isset($subcategory['asset_type']) && $subcategory['asset_type'] === 'medical') {
                    $medicalSubcategories[] = $subcategory;
                } elseif (isset($subcategory['asset_type']) && $subcategory['asset_type'] === 'non_medical') {
                    $nonMedicalSubcategories[] = $subcategory;
                }
            }

            // Get assets data
            $assets = $assetsResult['data'] ?? [];

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
                'status' => $status,
                'medicalSubcategories' => $medicalSubcategories,
                'nonMedicalSubcategories' => $nonMedicalSubcategories,
                'assets' => $assets
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
                'medicalSubcategories' => [],
                'nonMedicalSubcategories' => [],
                'assets' => [],
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
            return view('ComplainRepair.ComplainRepairDetail', [
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

    /**
     * Export complaints data to PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportComplaintPDF(Request $request)
    {
        try {
            // Get filter parameters
            $search = $request->input('search', '');
            $sort = $request->input('sort', 'newest');
            $status = $request->input('status', '');

            // Log request info
            \Log::info('Exporting complaints to PDF with parameters:', [
                'search' => $search,
                'sort' => $sort,
                'status' => $status,
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

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during complaints export:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Get complaints data
            $complaints = $result['data'] ?? [];

            // Generate PDF
            $pdf = Pdf::loadView('ComplainRepair.ComplainRepairPDF', [
                'complaints' => $complaints,
                'search' => $search,
                'sort' => $sort,
                'status' => $status,
                'date_generated' => now()->format('d M Y H:i:s')
            ]);

            // Log PDF generation
            \Log::info('Complaints PDF generated successfully', [
                'complaints_count' => count($complaints)
            ]);

            // Stream the PDF to browser
            return $pdf->stream('complaint_report_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Exception during complaints PDF export:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to export Complain and Repair as PDF: ' . $e->getMessage());
        }
    }

    /**
     * Create a new complaint
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createComplaint(Request $request)
    {
        try {
            // Validate request based on the image showing only 3 required fields
            $validator = \Validator::make($request->all(), [
                'asset_id' => 'required|integer',
                'description' => 'required|string',
                'image_file' => 'required|image|max:5120', // max 5MB
            ]);

            if ($validator->fails()) {
                \Log::warning('Complaint creation validation failed:', [
                    'errors' => $validator->errors()->toArray()
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Log request info
            \Log::info('Creating new complaint:', [
                'asset_id' => $request->input('asset_id'),
                'has_image' => $request->hasFile('image_file')
            ]);

            // Prepare multipart request data
            $multipart = [
                [
                    'name' => 'asset_id',
                    'contents' => $request->input('asset_id')
                ],
                [
                    'name' => 'description',
                    'contents' => $request->input('description')
                ]
            ];

            // Handle image file
            if ($request->hasFile('image_file') && $request->file('image_file')->isValid()) {
                $multipart[] = [
                    'name' => 'image_file',
                    'contents' => fopen($request->file('image_file')->getPathname(), 'r'),
                    'filename' => $request->file('image_file')->getClientOriginalName()
                ];
            }

            // Send request to API
            $result = $this->apiService->request('POST', '/complaints', [
                'multipart' => $multipart
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during complaint creation:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'status' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check for API errors
            if (!isset($result['status']) || $result['status'] !== true) {
                \Log::error('API error during complaint creation:', [
                    'api_response' => $result
                ]);

                return response()->json([
                    'status' => false,
                    'message' => $result['message'] ?? 'Failed to create complaint'
                ], 500);
            }

            // Success response
            \Log::info('Complaint created successfully', [
                'complaint_id' => $result['data']['id'] ?? null
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Complaint created successfully',
                'data' => $result['data']
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during complaint creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to create complaint: ' . $e->getMessage()
            ], 500);
        }
    }
}
