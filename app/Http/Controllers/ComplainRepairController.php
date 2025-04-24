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
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function createComplaint(Request $request)
    {
        try {
            // Check if the request is AJAX
            $isAjax = $request->ajax() || $request->wantsJson();

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

                if ($isAjax) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->withErrors($validator)
                    ->with('error', 'Please check the form for errors.');
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

                if ($isAjax) {
                    return response()->json([
                        'status' => false,
                        'message' => $result['message'] ?? 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')
                    ->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors
            if (!isset($result['status']) || $result['status'] !== true) {
                \Log::error('API error during complaint creation:', [
                    'api_response' => $result
                ]);

                // Return appropriate response based on request type
                if ($isAjax) {
                    // For AJAX requests, return JSON
                    if (isset($result['errors'])) {
                        return response()->json([
                            'status' => false,
                            'message' => $result['message'] ?? 'Failed to create complaint',
                            'errors' => $result['errors']
                        ], 422);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => $result['message'] ?? 'Failed to create complaint'
                        ], 500);
                    }
                } else {
                    // For regular requests, redirect back with error
                    return redirect()->back()
                        ->withInput()
                        ->with('error', $result['message'] ?? 'Failed to create complaint');
                }
            }

            // Success response
            \Log::info('Complaint created successfully', [
                'complaint_id' => $result['data']['id'] ?? null
            ]);

            if ($isAjax) {
                // For AJAX requests, return JSON success
                return response()->json([
                    'status' => true,
                    'message' => 'Complaint created successfully',
                    'data' => $result['data'] ?? null
                ]);
            } else {
                // For regular requests, redirect with success message
                return redirect()->route('complaint.index')
                    ->with('success', 'Complaint created successfully');
            }

        } catch (\Exception $e) {
            \Log::error('Exception during complaint creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to create complaint: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create complaint: ' . $e->getMessage());
        }
    }

    /**
     * Delete a complaint
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroyComplaint($id, Request $request)
    {
        try {
            // Check if the request is AJAX
            $isAjax = $request->ajax() || $request->wantsJson();

            // Log request info
            \Log::info('Attempting to delete complaint:', [
                'complaint_id' => $id,
                'request_url' => $request->fullUrl(),
                'is_ajax' => $isAjax
            ]);

            // Send delete request to API
            $result = $this->apiService->request('DELETE', "/complaints/{$id}");

            // Log API response
            \Log::info('API response for complaint deletion:', [
                'complaint_id' => $id,
                'api_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during complaint deletion:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed',
                    'complaint_id' => $id
                ]);

                if ($isAjax) {
                    return response()->json([
                        'status' => false,
                        'message' => $result['message'] ?? 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')
                    ->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors
            if (!isset($result['status']) || $result['status'] !== true) {
                \Log::error('API error during complaint deletion:', [
                    'complaint_id' => $id,
                    'api_response' => $result
                ]);

                // Return appropriate response based on request type
                if ($isAjax) {
                    return response()->json([
                        'status' => false,
                        'message' => $result['message'] ?? 'Failed to delete complaint'
                    ], 500);
                } else {
                    return redirect()->back()
                        ->with('error', $result['message'] ?? 'Failed to delete complaint');
                }
            }

            // Success response
            \Log::info('Complaint deleted successfully', [
                'complaint_id' => $id
            ]);

            if ($isAjax) {
                // For AJAX requests, return JSON success
                return response()->json([
                    'status' => true,
                    'message' => 'Complaint deleted successfully',
                    'data' => $result['data'] ?? null
                ]);
            } else {
                // For regular requests, redirect with success message
                return redirect()->route('complaint.index')
                    ->with('success', 'Complaint deleted successfully');
            }

        } catch (\Exception $e) {
            \Log::error('Exception during complaint deletion:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'complaint_id' => $id
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to delete complaint: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to delete complaint: ' . $e->getMessage());
        }
    }

    /**
     * Create a new repair record for a complaint
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function createRepair(Request $request)
    {
        try {
            // Check if the request is AJAX
            $isAjax = $request->ajax() || $request->wantsJson();

            // Validate request
            $validator = \Validator::make($request->all(), [
                'complaint_id' => 'required|integer',
                'repair_description' => 'required|string',
                'final_result' => 'required|string|in:Good,Slightly Damage,Heavy Damage,Waiting for Part',
                'repair_cost' => 'required|numeric',
                'parts_replaced' => 'required|string',
                'file' => 'required|image|max:5120', // max 5MB
            ]);

            if ($validator->fails()) {
                \Log::warning('Repair creation validation failed:', [
                    'errors' => $validator->errors()->toArray()
                ]);

                if ($isAjax) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->withErrors($validator)
                    ->with('error', 'Please check the form for errors.');
            }

            // Log request info
            \Log::info('Creating new repair:', [
                'complaint_id' => $request->input('complaint_id'),
                'has_image' => $request->hasFile('file')
            ]);

            // Prepare multipart request data
            $multipart = [
                [
                    'name' => 'complaint_id',
                    'contents' => $request->input('complaint_id')
                ],
                [
                    'name' => 'repair_description',
                    'contents' => $request->input('repair_description')
                ],
                [
                    'name' => 'final_result',
                    'contents' => $request->input('final_result')
                ],
                [
                    'name' => 'repair_cost',
                    'contents' => $request->input('repair_cost')
                ],
                [
                    'name' => 'parts_replaced',
                    'contents' => $request->input('parts_replaced')
                ]
            ];

            // Handle image file
            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                $multipart[] = [
                    'name' => 'file',
                    'contents' => fopen($request->file('file')->getPathname(), 'r'),
                    'filename' => $request->file('file')->getClientOriginalName()
                ];
            }

            // Send request to API
            $result = $this->apiService->request('POST', '/repairs', [
                'multipart' => $multipart
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during repair creation:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                if ($isAjax) {
                    return response()->json([
                        'status' => false,
                        'message' => $result['message'] ?? 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')
                    ->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors
            if (!isset($result['status']) || $result['status'] !== true) {
                \Log::error('API error during repair creation:', [
                    'api_response' => $result
                ]);

                // Return appropriate response based on request type
                if ($isAjax) {
                    // For AJAX requests, return JSON
                    if (isset($result['errors'])) {
                        return response()->json([
                            'status' => false,
                            'message' => $result['message'] ?? 'Failed to create repair',
                            'errors' => $result['errors']
                        ], 422);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => $result['message'] ?? 'Failed to create repair'
                        ], 500);
                    }
                } else {
                    // For regular requests, redirect back with error
                    return redirect()->back()
                        ->withInput()
                        ->with('error', $result['message'] ?? 'Failed to create repair');
                }
            }

            // Success response
            \Log::info('Repair created successfully', [
                'repair_id' => $result['data']['id'] ?? null
            ]);

            if ($isAjax) {
                // For AJAX requests, return JSON success
                return response()->json([
                    'status' => true,
                    'message' => 'Repair created successfully',
                    'data' => $result['data'] ?? null
                ]);
            } else {
                // For regular requests, redirect with success message
                return redirect()->route('complaint.detail', ['id' => $request->input('complaint_id')])
                    ->with('success', 'Repair created successfully');
            }

        } catch (\Exception $e) {
            \Log::error('Exception during repair creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to create repair: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create repair: ' . $e->getMessage());
        }
    }

    /**
     * Export complaint detail to PDF
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportComplaintDetailPDF($id, Request $request)
    {
        try {
            // Log request info
            \Log::info('Exporting complaint detail to PDF:', [
                'complaint_id' => $id,
                'request_url' => $request->fullUrl()
            ]);

            // Fetch complaint details from API
            $result = $this->apiService->request('GET', "/complaints/{$id}");

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during complaint detail PDF export:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed',
                    'complaint_id' => $id
                ]);

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check if complaint exists
            if (!isset($result['data'])) {
                \Log::warning('Complaint not found during PDF export:', [
                    'complaint_id' => $id
                ]);

                return redirect()->route('complaint.index')->with('error', 'Complaint not found');
            }

            // Get complaint data
            $complaint = $result['data'];

            // Convert images to base64
            if (!empty($complaint['complaint_picture_path'])) {
                try {
                    $imagePath = 'http://localhost:5000/public/images/' . basename($complaint['complaint_picture_path']);
                    $imageData = file_get_contents($imagePath);
                    if ($imageData !== false) {
                        $complaint['complaint_picture_base64'] = base64_encode($imageData);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to get complaint image for PDF:', [
                        'error' => $e->getMessage(),
                        'complaint_id' => $id,
                        'image_path' => $complaint['complaint_picture_path'] ?? 'N/A'
                    ]);
                }
            }

            // Convert repair image to base64 if exists
            if (!empty($complaint['repair']) && !empty($complaint['repair']['repair_picture_path'])) {
                try {
                    $repairImagePath = 'http://localhost:5000/public/images/' . basename($complaint['repair']['repair_picture_path']);
                    $repairImageData = file_get_contents($repairImagePath);
                    if ($repairImageData !== false) {
                        $complaint['repair']['repair_picture_base64'] = base64_encode($repairImageData);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to get repair image for PDF:', [
                        'error' => $e->getMessage(),
                        'complaint_id' => $id,
                        'repair_id' => $complaint['repair']['id'] ?? 'N/A',
                        'image_path' => $complaint['repair']['repair_picture_path'] ?? 'N/A'
                    ]);
                }
            }

            // Generate PDF
            $pdf = Pdf::loadView('ComplainRepair.ComplainRepairDetailPDF', [
                'complaint' => $complaint
            ]);

            // Log PDF generation
            \Log::info('Complaint detail PDF generated successfully', [
                'complaint_id' => $id
            ]);

            // Stream the PDF to browser
            return $pdf->stream('complaint_detail_' . $id . '_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Exception during complaint detail PDF export:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'complaint_id' => $id
            ]);

            return redirect()->back()->with('error', 'Failed to export complaint detail as PDF: ' . $e->getMessage());
        }
    }
}
