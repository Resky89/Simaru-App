<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DataFormatter;
use App\Http\Controllers\Traits\ApiResourceOperations;
use App\Http\Controllers\Traits\ExportableToPdf;

class MaintenanceController extends Controller
{
    use ApiResourceOperations, ExportableToPdf;

    /**
     * Display a listing of maintenance schedules.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $extraParams = [];

        // Add status filter if provided
        if ($request->filled('status')) {
            $extraParams['status'] = $request->input('status');
        }

        // Custom sort mappings
        $sortMappings = [
            'newest' => ['sort_by' => 'created_at', 'sort_order' => 'desc'],
            'oldest' => ['sort_by' => 'created_at', 'sort_order' => 'asc'],
        ];

        return $this->getResourceList(
            $request,
            '/maintenance',
            'maintenances',
            'Maintenance.Maintenance',
            'created_at',
            $extraParams,
            $sortMappings
        );
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
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
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
            return $this->handleException($e, $request, 'Maintenance.Maintenance');
        }
    }

    /**
     * Get maintenance task reminders.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTaskReminders(Request $request)
    {
        try {
            // Get the asset_type parameter if provided
            $assetType = $request->input('asset_type', '');

            // Build query parameters
            $queryParams = [];

            // Add asset_type filter if provided
            if (!empty($assetType)) {
                $queryParams['asset_type'] = $assetType;
            }

            // Fetch maintenance task reminders from API
            $result = $this->apiService->request('GET', '/maintenance/task-reminders', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil pengingat tugas pemeliharaan';
                return response()->json([
                    'success' => false,
                    'errors' => $errorData
                ], 400);
            }

            // Return the response as is from the API
            return response()->json([
                'success' => true,
                'message' => 'Daftar pengingat tugas pemeliharaan berhasil diambil',
                'data' => $result['data'] ?? []
            ]);

        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Maintenance.Maintenance');
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
            $authError = $this->handleAuthError($result, request());
            if ($authError) {
                return $authError;
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
            return $this->handleException($e, request(), 'Maintenance.Maintenance');
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
            $authError = $this->handleAuthError($result, request());
            if ($authError) {
                return $authError;
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
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
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

            return $this->storeResource(
                $request,
                '/maintenance/bulk',
                $requestData,
                'Jadwal pemeliharaan berhasil dibuat',
                'maintenance'
            );
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Maintenance.Maintenance');
        }
    }

    /**
     * Create maintenance schedules.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
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
                    foreach ((array) $messages as $message) {
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
            return $this->storeResource(
                $request,
                '/maintenance',
                $requestData,
                'Jadwal pemeliharaan berhasil dibuat'
            );

        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Maintenance.Maintenance');
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
                'status' => 'nullable|string|in:new,in progress,finished',
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
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check if the request was successful
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui jadwal pemeliharaan';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                return response()->json([
                    'success' => false,
                    'errors' => $errorMessage
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
            return $this->handleException($e, $request, 'Maintenance.Maintenance');
        }
    }

    /**
     * Delete a maintenance record by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Contracts\View\View
     */
    public function destroy ($id)
    {
        return $this->deleteResource(
            request(),
            '/maintenance/' . $id,
            'Jadwal pemeliharaan berhasil dihapus',
            'maintenance'
        );
    }

    /**
     * Start maintenance process.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function startMaintenance($id)
    {
        try {
            // Prepare the request data - changing status to in_progress
            $requestData = [
                'status' => 'in progress'
            ];

            // Send request to API
            $result = $this->apiService->request('PATCH', '/maintenance/' . $id . '/start', [
                'json' => $requestData
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, request());
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Gagal memulai proses pemeliharaan'
                ], 400);
            }

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Maintenance berhasil dimulai',
                'data' => $result['data'] ?? []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal memulai proses pemeliharaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export maintenance records to PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
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
                'limit' => 1000,  // Get a large number for export
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder
            ];

            // Add search parameter if provided
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Add status filter if provided
            if (!empty($status)) {
                $queryParams['status'] = $status;
            }

            // Fetch maintenance records from API
            $result = $this->apiService->request('GET', '/maintenance', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Handle API errors
            $apiError = $this->handleApiError($result, $request, 'Maintenance.Maintenance', 'Gagal mengambil data untuk ekspor');
            if ($apiError) {
                return $apiError;
            }

            // Get maintenance data
            $maintenances = $result['data'] ?? [];

            // Generate timestamp for filename
            $timestamp = now()->format('YmdHis');
            $filename = "maintenance_report_{$timestamp}.pdf";

            // Generate PDF using the trait method
            return $this->streamPdf(
                'Maintenance.MaintenancePDF',
                [
                    'maintenances' => $maintenances,
                    'search' => $search,
                    'status' => $status,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder,
                    'date_generated' => now()->format('d M Y H:i:s')
                ],
                $filename
            );

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
            $authError = $this->handleAuthError($result, request());
            if ($authError) {
                return $authError;
            }

            // Check if the maintenance exists
            if (!isset($result['data']) || empty($result['data'])) {
                return redirect()->route('maintenance')->with('error', 'Data pemeliharaan tidak ditemukan');
            }

            // Get maintenance data
            $maintenance = $result['data'];

            // Generate filename
            $timestamp = now()->format('YmdHis');
            $filename = "maintenance_detail_{$id}_{$timestamp}.pdf";

            // Generate PDF using the trait method
            return $this->streamPdf(
                'Maintenance.MaintenanceDetailPDF',
                [
                    'maintenance' => $maintenance,
                    'date_generated' => now()->format('d M Y H:i:s')
                ],
                $filename
            );

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
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal membuat laporan pemeliharaan';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                return response()->json([
                    'success' => false,
                    'errors' => $errorMessage
                ], 400);
            }

            // Return response in the expected format
            return response()->json([
                'success' => $result['success'] ?? true,
                'message' => $result['message'] ?? 'Laporan pemeliharaan berhasil dibuat',
                'data' => $result['data'] ?? null
            ]);

        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Maintenance.Maintenance');
        }
    }
}
