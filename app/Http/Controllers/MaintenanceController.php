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

            // Add sorting parameters
            if (!empty($sortBy)) {
                $queryParams['sort_by'] = $sortBy;
            }

            if (!empty($sortOrder)) {
                $queryParams['sort_order'] = $sortOrder;
            }

            // Fetch maintenance schedules
            $result = $this->apiService->request('GET', '/maintenance', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => $result['errors'] ?? 'Autentikasi gagal']
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil jadwal pemeliharaan';

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

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], 400);
                }

                return view('Maintenance.Maintenance', [
                    'maintenances' => [],
                    'pagination' => null,
                    'search' => $search,
                    'status' => $status,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder,
                    'error' => $errorMessage
                ]);
            }

            // Parse data for view
            $maintenances = $result['data'] ?? [];
            $pagination = $result['pagination'] ?? null;

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
                'sort_order' => $sortOrder
            ]);

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['general' => 'Gagal mengambil jadwal pemeliharaan: ' . $e->getMessage()]
                ], 500);
            }

            return view('Maintenance.Maintenance', [
                'maintenances' => [],
                'pagination' => null,
                'search' => $search,
                'status' => $status,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'error' => 'Gagal mengambil jadwal pemeliharaan: ' . $e->getMessage()
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

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => $result['errors'] ?? 'Autentikasi gagal']
                ], 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil jadwal pemeliharaan';

                return response()->json([
                    'success' => false,
                    'errors' => $errorData
                ], 400);
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
            return response()->json([
                'success' => false,
                'errors' => ['general' => 'Gagal mengambil jadwal pemeliharaan: ' . $e->getMessage()]
            ], 500);
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
            // Fetch maintenance from API
            $result = $this->apiService->request('GET', '/maintenance/' . $id);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => $result['errors'] ?? 'Autentikasi gagal']
                ], 401);
            }

            // Check if the maintenance exists
            if (!isset($result['data']) || empty($result['data'])) {
                return response()->json([
                    'success' => false,
                    'errors' => ['maintenance' => 'Data pemeliharaan tidak ditemukan']
                ], 404);
            }

            // Return the API response
            return response()->json([
                'success' => true,
                'message' => 'Data pemeliharaan berhasil diambil',
                'data' => $result['data']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['general' => 'Gagal mengambil data pemeliharaan: ' . $e->getMessage()]
            ], 500);
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
            // Fetch maintenance from API
            $result = $this->apiService->request('GET', '/maintenance/' . $id);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check if the maintenance exists
            if (!isset($result['data']) || empty($result['data'])) {
                return redirect()->route('maintenance')->with('error', 'Data pemeliharaan tidak ditemukan');
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
                    // Ensure history is an empty array
                    $maintenanceData['history'] = [];
                }
            }

            // Return the view with maintenance data
            return view('Maintenance.MaintenanceDetail', [
                'maintenance' => $maintenanceData
            ]);

        } catch (\Exception $e) {
            return redirect()->route('maintenance')->with('error', 'Gagal mengambil detail pemeliharaan: ' . $e->getMessage());
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

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => $result['errors'] ?? 'Autentikasi gagal']
                ], 401);
            }

            // Check if the request was successful
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat jadwal pemeliharaan';

                return response()->json([
                    'success' => false,
                    'errors' => $errorData
                ], 400);
            }

            // For AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Jadwal pemeliharaan berhasil dibuat',
                    'data' => $result['data'] ?? []
                ]);
            }

            // For regular form submissions, redirect with session flash
            return redirect()->route('maintenance')->with('success', 'Jadwal pemeliharaan berhasil dibuat');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['general' => 'Gagal membuat jadwal pemeliharaan: ' . $e->getMessage()]
            ], 500);
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
            // Get the interval to check validation rules
            $interval = $request->input('interval');

            // Validate the request with different rules based on interval
            $validationRules = [
                'asset_ids' => 'required|array',
                'asset_ids.*' => 'required|integer',
                'start_date' => 'required|date',
                'interval' => 'required|string',
                'assigned_to' => 'required|integer',
                'vendor_id' => 'nullable|integer'
            ];

            // Only require end_date for intervals other than ONCE and DAILY
            if (!in_array($interval, ['ONCE', 'DAILY'])) {
                $validationRules['end_date'] = 'required|date|after_or_equal:start_date';
            }

            $validator = \Validator::make($request->all(), $validationRules);

            if ($validator->fails()) {
                // Format error for consistent array format with path and message
                $formattedErrors = [];
                foreach ($validator->errors()->toArray() as $field => $messages) {
                    foreach ((array)$messages as $message) {
                        $formattedErrors[] = [
                            'path' => $field,
                            'message' => $message
                        ];
                    }
                }

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors
                ], 422);
            }

            // Get asset IDs and make sure they're all integers
            $assetIds = $request->input('asset_ids', []);
            $assetIds = array_map('intval', $assetIds);

            // Prepare request data
            $requestData = [
                'asset_ids' => $assetIds,
                'start_date' => $request->input('start_date'),
                'interval' => $request->input('interval'),
                'assigned_to' => (int) $request->input('assigned_to')
            ];

            // Only add end_date for non-ONCE and non-DAILY intervals
            // Or if it was explicitly provided
            if (!in_array($interval, ['ONCE', 'DAILY']) || $request->has('end_date')) {
                $requestData['end_date'] = $request->input('end_date') ?? $request->input('start_date');
            }

            // Only add vendor_id if it's present and not empty
            if ($request->has('vendor_id') && $request->input('vendor_id') !== null && $request->input('vendor_id') !== '') {
                $requestData['vendor_id'] = (int) $request->input('vendor_id');
            }

            // Send request to API
            $result = $this->apiService->request('POST', '/maintenance', [
                'json' => $requestData
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => $result['errors'] ?? 'Autentikasi gagal']
                ], 401);
            }

            // Return the response from API
            return response()->json([
                'success' => isset($result['success']) && $result['success'] === true,
                'message' => $result['message'] ?? 'Jadwal pemeliharaan berhasil dibuat',
                'data' => $result['data'] ?? [],
                'errors' => isset($result['success']) && $result['success'] !== true ? ($result['errors'] ?? ['general' => 'Gagal membuat jadwal pemeliharaan']) : null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['general' => 'Gagal membuat jadwal pemeliharaan: ' . $e->getMessage()]
            ], 500);
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
            // Get the interval to determine validation rules
            $interval = $request->input('interval', '');

            // Define basic validation rules
            $validationRules = [
                'start_date' => 'nullable|date',
                'interval' => 'nullable|string|in:ONCE,DAILY,WEEKLY,2 WEEKS,MONTHLY,2 MONTHS,3 MONTHS,4 MONTHS,6 MONTHS,YEARLY',
                'assigned_to' => 'nullable|integer',
                'vendor_id' => 'nullable|integer',
                'status' => 'nullable|string|in:new,in_progress,finished',
                'notes' => 'nullable|string',
                'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png'
            ];

            // Add end_date validation based on interval
            if ($interval && !in_array($interval, ['ONCE', 'DAILY'])) {
                $validationRules['end_date'] = 'nullable|date|after_or_equal:start_date';
            } else {
                $validationRules['end_date'] = 'nullable|date';
            }

            $validator = \Validator::make($request->all(), $validationRules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if this is a regular JSON request or a multipart request with file
            if ($request->hasFile('file')) {
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

                // Send request to API without file
                $result = $this->apiService->request('PUT', '/maintenance/' . $id, [
                    'json' => $requestData
                ]);
            }

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => $result['errors'] ?? 'Autentikasi gagal']
                ], 401);
            }

            // Check if the request was successful
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui jadwal pemeliharaan';

                return response()->json([
                    'success' => false,
                    'errors' => $errorData
                ], 400);
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
            return response()->json([
                'success' => false,
                'errors' => ['general' => 'Gagal memperbarui jadwal pemeliharaan: ' . $e->getMessage()]
            ], 500);
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
            // Call the API to delete the maintenance record
            $result = $this->apiService->request('DELETE', '/maintenance/' . $id);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => $result['errors'] ?? 'Autentikasi gagal']
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check if the request was successful
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menghapus jadwal pemeliharaan';

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

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], 400);
                }

                return redirect()->route('maintenance')
                    ->with('error', $errorMessage);
            }

            // For AJAX requests
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jadwal pemeliharaan berhasil dihapus'
                ]);
            }

            // For regular form submissions, redirect with session flash
            return redirect()->route('maintenance')->with('success', 'Jadwal pemeliharaan berhasil dihapus');

        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['general' => 'Gagal menghapus jadwal pemeliharaan: ' . $e->getMessage()]
                ], 500);
            }

            return redirect()->route('maintenance')->with('error', 'Gagal menghapus jadwal pemeliharaan: ' . $e->getMessage());
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

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
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

            // Stream the PDF to browser
            return $pdf->stream('maintenance_report_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor jadwal pemeliharaan ke PDF: ' . $e->getMessage());
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
            // Fetch maintenance from API
            $result = $this->apiService->request('GET', '/maintenance/' . $id);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check if the maintenance exists
            if (!isset($result['data']) || empty($result['data'])) {
                return redirect()->route('maintenance')->with('error', 'Data pemeliharaan tidak ditemukan');
            }

            // Get maintenance data
            $maintenance = $result['data'];

            // Convert maintenance report attachment to base64 if exists
            if (isset($maintenance['maintenance_report']) && !empty($maintenance['maintenance_report']['attachment_path'])) {
                try {
                    // Use config service instead of directly accessing protected property
                    $baseUrl = rtrim(config('services.api.base_url', 'https://web-magangunbin2025.rsummi.co.id/api'), '/');
                    $imagePath = $baseUrl . '/public/images/' . basename($maintenance['maintenance_report']['attachment_path']);

                    $imageData = file_get_contents($imagePath);
                    if ($imageData !== false) {
                        $maintenance['maintenance_report']['attachment_picture_base64'] = base64_encode($imageData);
                    }
                } catch (\Exception $e) {
                    // Silently continue if image conversion fails
                }
            }

            // Generate PDF
            $pdf = Pdf::loadView('Maintenance.MaintenanceDetailPDF', [
                'maintenance' => $maintenance,
                'date_generated' => now()->format('d M Y H:i:s')
            ]);

            // Stream the PDF to browser
            return $pdf->stream('maintenance_report_' . $id . '_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor jadwal pemeliharaan ke PDF: ' . $e->getMessage());
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
            // Validate the request
            $validator = \Validator::make($request->all(), [
                'maintenance_id' => 'required|integer',
                'description' => 'required|string',
                'maintenance_date' => 'nullable|date',
                'file' => 'nullable|file|mimes:jpeg,png,jpg', // Accept 'file' field
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Build multipart request for API
            $multipart = [];

            // Add file if present
            if ($request->hasFile('file')) {
                $fileData = $request->file('file');
                $multipart[] = [
                    'name' => 'file', // Use 'file' for API
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

            // Call API with multipart request
            $result = $this->apiService->request('POST', '/maintenance-reports', [
                'multipart' => $multipart
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => $result['errors'] ?? 'Autentikasi gagal']
                ], 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal membuat laporan pemeliharaan';

                // Format error data for JSON response
                $formattedErrors = [];

                // Handle different error formats
                if (is_string($errorData)) {
                    // Case: errors is a string
                    $formattedErrors[] = [
                        'path' => 'general',
                        'message' => $errorData
                    ];
                } elseif (is_array($errorData)) {
                    // Case: errors is already an array of objects with path and message
                    if (isset($errorData[0]) && is_array($errorData[0]) && isset($errorData[0]['path'])) {
                        $formattedErrors = $errorData;
                    }
                    // Case: errors is a key-value pair of field and message
                    else {
                        foreach ($errorData as $field => $messages) {
                            if (is_array($messages)) {
                                foreach ($messages as $message) {
                                    $formattedErrors[] = [
                                        'path' => $field,
                                        'message' => $message
                                    ];
                                }
                            } else {
                                $formattedErrors[] = [
                                    'path' => $field,
                                    'message' => $messages
                                ];
                            }
                        }
                    }
                }

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors
                ], 400);
            }

            // Return response in the expected format
            return response()->json([
                'success' =>  $result['success'] ?? true,
                'message' => $result['message'] ?? 'Laporan pemeliharaan berhasil dibuat',
                'data' => $result['data'] ?? null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['general' => 'Gagal membuat laporan pemeliharaan: ' . $e->getMessage()]
            ], 500);
        }
    }
}
