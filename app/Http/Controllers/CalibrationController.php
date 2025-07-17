<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DataFormatter;
use App\Http\Controllers\Traits\ApiResourceOperations;
use App\Http\Controllers\Traits\ExportableToPdf;

class CalibrationController extends Controller
{
    use ApiResourceOperations, ExportableToPdf;

    /**
     * Display a listing of calibrations.
     */
    public function index(Request $request)
    {
        $extraParams = [];

        // Add status filter if provided
        if ($request->filled('status')) {
            // Only pass valid status values
            if (in_array($request->input('status'), ['scheduled', 'in_progress', 'completed', 'overdue'])) {
                $extraParams['status_calibration'] = $request->input('status');
            }
        }

        // Add result filter if provided
        if ($request->filled('result')) {
            // Only pass valid result values
            if (in_array($request->input('result'), ['pass', 'fail', 'unknown'])) {
                $extraParams['calibration_result'] = $request->input('result');
            }
        }

        // Custom sort mappings
        $sortMappings = [
            'newest' => ['sort_by' => 'calibration_id', 'sort_order' => 'desc'],
            'oldest' => ['sort_by' => 'calibration_id', 'sort_order' => 'asc'],
            'asset_asc' => ['sort_by' => 'asset_name', 'sort_order' => 'asc'],
            'asset_desc' => ['sort_by' => 'asset_name', 'sort_order' => 'desc'],
        ];

        return $this->getResourceList(
            $request,
            '/calibrations',
            'calibrations',
            'Calibration.Calibration',
            'calibration_id',
            $extraParams,
            $sortMappings
        );
    }

    /**
     * Get all calibrations data.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCalibrations(Request $request)
    {
        try {
            $queryParams = $this->buildQueryParams($request, ['search']);

            $result = $this->apiService->request('GET', '/calibrations', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Gagal mengambil data kalibrasi'
                ], 400);
            }

            // Return the response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Data kalibrasi berhasil diambil',
                'data' => $result['data'] ?? [],
                'pagination' => $result['pagination'] ?? $this->getDefaultPagination($request)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal mengambil data kalibrasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create bulk calibrations.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function createBulkCalibrations(Request $request)
    {
        try {
            $request->validate([
                'asset_ids' => 'required|array',
                'planning_calibration_date' => 'required|date'
            ]);

            // Remove duplicate asset IDs to prevent calibration duplication
            $assetIds = array_unique($request->input('asset_ids'));

            $requestData = [
                'asset_ids' => $assetIds,
                'planning_calibration_date' => $request->input('planning_calibration_date')
            ];

            $result = $this->apiService->request('POST', '/calibrations/bulk', [
                'json' => $requestData
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Gagal membuat kalibrasi'
                ], 400);
            }

            // For AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Kalibrasi berhasil dibuat',
                    'data' => $result['data'] ?? []
                ]);
            }

            // For regular form submissions, redirect with flash session
            return redirect()->route('calibration')->with('success', 'Kalibrasi berhasil dibuat');

        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Calibration.Calibration');
        }
    }

    /**
     * Report calibration results.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function reportCalibration(Request $request, $id)
    {
        try {
            $fields = [
                'actual_calibration_date' => 'string',
                'next_calibration_date' => 'string',
                'status_calibration' => 'string',
                'vendor_id' => 'integer',
                'certificate_number' => 'string',
                'calibration_result' => 'string',
                'calibration_price' => 'float',
                'notes' => 'string'
            ];

            $requestData = DataFormatter::formatRequestData($request, $fields);

            // Handle file upload if present
            if ($request->hasFile('file')) {
                $file = $request->file('file');

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

                $result = $this->apiService->request('PUT', '/calibrations/report/' . $id, [
                    'multipart' => $multipart
                ]);
            } else {
                $result = $this->apiService->request('PUT', '/calibrations/report/' . $id, [
                    'json' => $requestData
                ]);
            }

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Gagal memperbarui kalibrasi'
                ], 400);
            }

            // For AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kalibrasi telah dilakukan',
                    'data' => $result['data'] ?? []
                ]);
            }

            // For regular form submissions
            return redirect()->route('calibration')->with('success', 'Kalibrasi telah dilakukan');

        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Calibration.Calibration');
        }
    }

    /**
     * Remove the specified calibration record(s).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        try {
            // Get IDs from the request - handle various possible formats
            $ids = null;
            $requestData = $request->json()->all();

            // Try several ways to get the IDs
            if (isset($requestData['ids']) && is_array($requestData['ids'])) {
                $ids = $requestData['ids'];
            } elseif ($request->has('ids')) {
                $ids = $request->input('ids');
            } elseif ($request->has('all') && isset($request->all()['all']['ids'])) {
                $ids = $request->all()['all']['ids'];
            }

            // Ensure we have a valid array of IDs
            if (empty($ids) || !is_array($ids)) {
                return response()->json([
                    'success' => false,
                    'errors' => 'ID kalibrasi valid diperlukan'
                ], 400);
            }

            // Convert all IDs to integers to ensure they match the expected format
            $ids = array_map('intval', $ids);

            // Build payload for API
            $payload = [
                'json' => [
                    'ids' => $ids
                ]
            ];

            // Call API to delete calibrations
            $result = $this->apiService->request('DELETE', '/calibrations/bulk', $payload);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Gagal menghapus kalibrasi'
                ], 400);
            }

            // For AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kalibrasi berhasil dihapus',
                    'data' => $result['data'] ?? []
                ]);
            }

            // For regular form submissions
            return redirect()->route('calibration')->with('success', 'Kalibrasi berhasil dihapus');

        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Calibration.Calibration');
        }
    }

    /**
     * Get a single calibration.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCalibration($id)
    {
        try {
            $result = $this->apiService->request('GET', '/calibrations/' . $id);

            // Check for auth errors
            $authError = $this->handleAuthError($result, request());
            if ($authError) {
                return $authError;
            }

            // Check if calibration exists
            if (!isset($result['data']) || empty($result['data'])) {
                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Kalibrasi tidak ditemukan'
                ], 404);
            }

            // Return API response
            return response()->json([
                'success' => true,
                'message' => 'Data kalibrasi berhasil diambil',
                'data' => $result['data']
            ]);

        } catch (\Exception $e) {
            return $this->handleException($e, request(), 'Calibration.Calibration');
        }
    }

    /**
     * Display calibration details.
     *
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function showCalibrationDetail($id)
    {
        try {
            $result = $this->apiService->request('GET', '/calibrations/' . $id);

            // Check for auth errors
            $authError = $this->handleAuthError($result, request());
            if ($authError instanceof \Illuminate\Http\RedirectResponse) {
                return $authError;
            }

            // Check if calibration exists
            if (!isset($result['data']) || empty($result['data'])) {
                return redirect()->route('calibration')->with('error', 'Kalibrasi tidak ditemukan');
            }

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data kalibrasi berhasil diambil',
                    'calibration' => $result['data']
                ]);
            }

            // Return view with calibration data
            return view('Calibration.CalibrationDetail', [
                'calibration' => $result['data']
            ]);

        } catch (\Exception $e) {
            return redirect()->route('calibration')->with('error', 'Gagal mengambil detail kalibrasi: ' . $e->getMessage());
        }
    }

    /**
     * Export calibrations to PDF.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportCalibrationPDF(Request $request)
    {
        try {
            $queryParams = [];

            // Add search parameter if provided
            if ($request->filled('search')) {
                $queryParams['search'] = $request->input('search');
            }

            // Add status filter if provided
            if ($request->filled('status')) {
                // Only pass valid status values
                if (in_array($request->input('status'), ['scheduled', 'in_progress', 'completed', 'overdue', 'cancelled'])) {
                    $queryParams['status_calibration'] = $request->input('status');
                }
            }

            // Add result filter if provided
            if ($request->filled('result')) {
                // Only pass valid result values
                if (in_array($request->input('result'), ['pass', 'fail', 'unknown'])) {
                    $queryParams['calibration_result'] = $request->input('result');
                }
            }

            // Add sorting parameter
            $sortOrder = $request->input('sort', 'newest');
            $sortMappings = [
                'oldest' => ['sort_by' => 'calibration_id', 'sort_order' => 'asc'],
                'asset_asc' => ['sort_by' => 'asset_name', 'sort_order' => 'asc'],
                'asset_desc' => ['sort_by' => 'asset_name', 'sort_order' => 'desc'],
            ];

            // Apply sorting
            if (!empty($sortOrder) && isset($sortMappings[$sortOrder])) {
                $queryParams = array_merge($queryParams, $sortMappings[$sortOrder]);
            } else {
                $queryParams['sort_by'] = 'calibration_id';
                $queryParams['sort_order'] = 'desc';
            }

            // Set pagination parameters for export (get all records)
            $queryParams['pagination'] = 'false';
            $queryParams['limit'] = 1000;

            // Get calibrations from API
            $result = $this->apiService->request('GET', '/calibrations', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError instanceof \Illuminate\Http\RedirectResponse) {
                return $authError;
            }

            // Handle API errors
            $apiError = $this->handleApiError($result, $request, 'Calibration.Calibration', 'Gagal mengambil data untuk ekspor');
            if ($apiError) {
                return $apiError;
            }

            // Get calibration data
            $calibrations = $result['data'] ?? [];

            // Generate timestamp for filename
            $timestamp = now()->format('YmdHis');
            $filename = "laporan_kalibrasi_{$timestamp}.pdf";

            // Generate PDF using the trait method
            return $this->generatePdf(
                'Calibration.CalibrationPDF',
                [
                    'calibrations' => $calibrations,
                    'search' => $request->input('search', ''),
                    'status' => $request->input('status', ''),
                    'result' => $request->input('result', ''),
                    'sort' => $sortOrder,
                    'date_generated' => now()->format('d M Y H:i:s')
                ],
                $filename
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor kalibrasi sebagai PDF: ' . $e->getMessage());
        }
    }

    /**
     * Export calibration detail to PDF.
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportCalibrationDetailPDF($id)
    {
        try {
            // Get calibration from API
            $result = $this->apiService->request('GET', '/calibrations/' . $id);

            // Check for auth errors
            $authError = $this->handleAuthError($result, request());
            if ($authError instanceof \Illuminate\Http\RedirectResponse) {
                return $authError;
            }

            // Handle API errors
            $apiError = $this->handleApiError($result, request(), 'Calibration.Calibration', 'Gagal mengambil data untuk ekspor');
            if ($apiError) {
                return $apiError;
            }

            // Get calibration data
            $calibrationData = $result['data'];

            // Try to get history if not included in main response
            if (!isset($calibrationData['history'])) {
                try {
                    $historyResult = $this->apiService->request('GET', '/calibrations/' . $id . '/history');
                    if (isset($historyResult['data']) && !empty($historyResult['data'])) {
                        $calibrationData['history'] = $historyResult['data'];
                    }
                } catch (\Exception $e) {
                    // Continue without history if failed
                }
            }

            // Handle certificate file if present and is an image
            if (!empty($calibrationData['certificate_file_path'])) {
                try {
                    $fileName = basename($calibrationData['certificate_file_path']);
                    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                    $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']);

                    if ($isImage) {
                        // Construct proper path to the image
                        $backendUrl = config('app.backend_url', 'https://web-magangunbin2025.rsummi.co.id/api');

                        // Check if certificate_file_path already includes /public
                        if (strpos($calibrationData['certificate_file_path'], '/public') === 0) {
                            $imagePath = $backendUrl . $calibrationData['certificate_file_path'];
                        } else {
                            $imagePath = $backendUrl . '/public' . $calibrationData['certificate_file_path'];
                        }

                        // Alternative path in case the above doesn't work
                        $altImagePath = $backendUrl . '/public' . $fileName;

                        // Try to get image data from the main path
                        $imageData = @file_get_contents($imagePath);

                        // If main path failed, try alternative path
                        if ($imageData === false) {
                            $imageData = @file_get_contents($altImagePath);
                        }

                        if ($imageData !== false) {
                            $calibrationData['certificate_file_base64'] = base64_encode($imageData);
                        }
                    } else {
                        // For non-image documents, just store the filename
                        $calibrationData['certificate_file_url'] = $fileName;
                    }
                } catch (\Exception $e) {
                    // Continue without certificate file if failed
                    \Log::error('Failed to process certificate file: ' . $e->getMessage());
                }
            }

            // Generate timestamp for filename
            $timestamp = now()->format('YmdHis');
            $filename = "detail_kalibrasi_{$id}_{$timestamp}.pdf";

            // Generate PDF using the trait method
            return $this->generatePdf(
                'Calibration.CalibrationDetailPDF',
                [
                    'calibration' => $calibrationData,
                    'date_generated' => now()->format('d M Y H:i:s')
                ],
                $filename
            );

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor detail kalibrasi sebagai PDF: ' . $e->getMessage());
        }
    }

    /**
     * Update calibration schedule.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCalibrationSchedule(Request $request, $id)
    {
        try {
            $fields = [
                'planning_calibration_date' => ['type' => 'string', 'required' => true]
            ];

            $requestData = DataFormatter::formatRequestData($request, $fields);

            $result = $this->apiService->request('PUT', '/calibrations/schedule/' . $id, [
                'json' => $requestData
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Gagal memperbarui jadwal kalibrasi'
                ], 400);
            }

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Jadwal kalibrasi berhasil diperbarui',
                'data' => $result['data'] ?? []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal memperbarui jadwal kalibrasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get assets available for calibration.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssetsForCalibration(Request $request)
    {
        try {
            $queryParams = $this->buildQueryParams($request, ['search']);

            $result = $this->apiService->request('GET', '/calibrations/assets', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Gagal mengambil daftar aset untuk kalibrasi'
                ], 400);
            }

            // Return the response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Daftar aset tersedia untuk kalibrasi berhasil diambil',
                'data' => $result['data'] ?? [],
                'pagination' => $result['pagination'] ?? $this->getDefaultPagination($request)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal mengambil daftar aset untuk kalibrasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start calibration process.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function startCalibration($id)
    {
        try {
            // Prepare the request data - we're changing status to in_progress
            $requestData = [
                'status_calibration' => 'in_progress'
            ];

            // Send request to API
            $result = $this->apiService->request('PATCH', '/calibrations/' . $id . '/start', [
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
                    'errors' => $result['errors'] ?? 'Gagal memulai proses kalibrasi'
                ], 400);
            }

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Kalibrasi berhasil dimulai',
                'data' => $result['data'] ?? []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal memulai proses kalibrasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build query parameters from request.
     *
     * @param Request $request
     * @param array $additionalParams
     * @return array
     */
    private function buildQueryParams(Request $request, array $additionalParams = [])
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);

        $queryParams = [
            'page' => $page,
            'limit' => $limit
        ];

        foreach ($additionalParams as $param) {
            if ($request->filled($param)) {
                $queryParams[$param] = $request->input($param);
            }
        }

        return $queryParams;
    }

    /**
     * Get default pagination structure.
     *
     * @param Request $request
     * @return array
     */
    private function getDefaultPagination(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);

        return [
            'total_items' => 0,
            'total_pages' => 0,
            'current_page' => $page,
            'limit' => $limit,
            'has_next' => false,
            'has_prev' => false
        ];
    }

    /**
     * Get calibration task reminders.
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

            // Fetch calibration task reminders from API
            $result = $this->apiService->request('GET', '/calibrations/task-reminders', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil pengingat tugas kalibrasi';
                return response()->json([
                    'success' => false,
                    'errors' => $errorData
                ], 400);
            }

            // Return the response as is from the API
            return response()->json([
                'success' => true,
                'message' => 'Daftar pengingat tugas kalibrasi berhasil diambil',
                'data' => $result['data'] ?? []
            ]);

        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Calibration.Calibration');
        }
    }
}
