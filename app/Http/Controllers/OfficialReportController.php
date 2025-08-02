<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use App\Helpers\DataFormatter;
use App\Http\Controllers\Traits\ApiResourceOperations;

class OfficialReportController extends Controller
{
    use ApiResourceOperations;

    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display a listing of official reports with pagination and filtering.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            // Get search and filter parameters
            $search = $request->input('search');
            $reportType = $request->input('report_type');
            $status = $request->input('status');
            $dateFrom = $request->input('release_date_from');
            $dateTo = $request->input('release_date_to');

            // Get direct sort parameters
            $sortBy = $request->input('sort_by');
            $sortOrder = $request->input('sort_order');

            // Build extra parameters for filtering
            $extraParams = [];

            if ($search) {
                $extraParams['search'] = $search;
            }

            if ($reportType) {
                $extraParams['report_type'] = $reportType;
            }

            if ($status) {
                $extraParams['status'] = $status;
            }

            if ($dateFrom) {
                $extraParams['release_date_from'] = $dateFrom;
            }

            if ($dateTo) {
                $extraParams['release_date_to'] = $dateTo;
            }

            // Add direct sort parameters if provided
            if ($sortBy) {
                $extraParams['sort_by'] = $sortBy;
            }

            if ($sortOrder) {
                $extraParams['sort_order'] = $sortOrder;
            }

            // Set sort mappings
            $sortMappings = [
                'newest' => ['sort_by' => 'created_at', 'sort_order' => 'desc'],
                'oldest' => ['sort_by' => 'created_at', 'sort_order' => 'asc'],
                'release_date_asc' => ['sort_by' => 'release_date', 'sort_order' => 'asc'],
                'release_date_desc' => ['sort_by' => 'release_date', 'sort_order' => 'desc'],
                'report_type_asc' => ['sort_by' => 'report_type', 'sort_order' => 'asc'],
                'report_type_desc' => ['sort_by' => 'report_type', 'sort_order' => 'desc'],
                'status_asc' => ['sort_by' => 'status', 'sort_order' => 'asc'],
                'status_desc' => ['sort_by' => 'status', 'sort_order' => 'desc'],
            ];

            return $this->getResourceList(
                $request,
                '/official-reports',
                'official_reports',
                'OfficialReport.OfficialReport',
                'created_at',
                $extraParams,
                $sortMappings
            );
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'OfficialReport.OfficialReport', [
                'official_reports' => [],
                'official_reports_pagination' => null
            ]);
        }
    }



    /**
     * Display the specified official report.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $id)
    {
        try {
            // Get data from API
            $result = $this->apiService->request('GET', "/official-reports/{$id}");

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil detail berita acara';
                $formattedErrors = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $formattedErrors,
                    ], 404);
                }

                return view('OfficialReport.OfficialReportDetail', [
                    'error' => $formattedErrors
                ]);
            }

            // Get the data
            $officialReport = $result['data'] ?? [];

            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Berita acara berhasil ditemukan',
                    'data' => $officialReport
                ]);
            }

            // Return view for regular requests
            return view('OfficialReport.OfficialReportDetail', [
                'official_report' => $officialReport
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => ['Gagal mengambil detail berita acara: ' . $e->getMessage()]],
                ], 500);
            }

            return view('OfficialReport.OfficialReportDetail', [
                'error' => 'Gagal mengambil detail berita acara: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created official report.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Validate request data
            $validated = $request->validate([
                'report_type' => 'required|string|in:DISPOSAL,LOSS,FOUND',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.asset_id' => 'required|integer',
                'items.*.item_notes' => 'nullable|string',
            ]);

            // Format the data using DataFormatter
            $data = DataFormatter::formatRequestData($request, [
                'report_type' => 'string',
                'notes' => 'string',
                'items' => 'array',
            ]);

            // Process items array to ensure correct structure
            if (isset($validated['items']) && is_array($validated['items'])) {
                $data['items'] = [];
                foreach ($validated['items'] as $item) {
                    $itemData = [
                        'asset_id' => (int) $item['asset_id'],
                    ];

                    // Only include item_notes if it has a non-empty value
                    if (isset($item['item_notes']) && !empty(trim($item['item_notes']))) {
                        $itemData['item_notes'] = trim($item['item_notes']);
                    }

                    $data['items'][] = $itemData;
                }
            }

            return $this->storeResource(
                $request,
                '/official-reports',
                $data,
                'Berita acara berhasil dibuat',
                'official-report.index'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                // Format validation errors for AJAX requests
                $formattedErrors = [];
                foreach ($e->errors() as $field => $messages) {
                    $formattedErrors[] = [
                        'path' => $field,
                        'message' => is_array($messages) ? $messages[0] : $messages
                    ];
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan validasi',
                    'errors' => $formattedErrors,
                ], 422);
            }

            return redirect()->back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses permintaan',
                    'errors' => [
                        [
                            'path' => 'exception',
                            'message' => 'Gagal membuat berita acara: ' . $e->getMessage()
                        ]
                    ],
                ], 500);
            }

            return redirect()->back()->withInput()->withErrors('Gagal membuat berita acara: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified official report.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // First, get the official report details to check if it can be updated
            $reportData = $this->apiService->request('GET', "/official-reports/{$id}");

            if (!isset($reportData['success']) || $reportData['success'] !== true) {
                $errorData = $reportData['errors'] ?? 'Gagal mengambil detail berita acara';
                $formattedErrors = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $formattedErrors,
                    ], 400);
                }

                return redirect()->back()->withErrors($formattedErrors);
            }

            // Check if the report can be updated (e.g., not already approved)
            $status = $reportData['data']['status'] ?? null;
            if (in_array($status, ['APPROVED', 'REJECTED'])) {
                $errorMessage = 'Berita acara yang sudah disetujui atau ditolak tidak dapat diperbarui';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['status' => [$errorMessage]],
                    ], 403);
                }

                return redirect()->back()->withErrors($errorMessage);
            }

            // Validate request data
            $validated = $request->validate([
                'report_type' => 'required|string|in:DISPOSAL,LOSS,FOUND',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.asset_id' => 'required|integer',
                'items.*.item_notes' => 'nullable|string',
            ]);

            // Format the data using DataFormatter
            $data = DataFormatter::formatRequestData($request, [
                'report_type' => 'string',
                'notes' => 'string',
                'items' => 'array',
            ]);

            // Process items array to ensure correct structure
            if (isset($validated['items']) && is_array($validated['items'])) {
                $data['items'] = [];
                foreach ($validated['items'] as $item) {
                    $itemData = [
                        'asset_id' => (int) $item['asset_id'],
                    ];

                    // Only include item_notes if it has a non-empty value
                    if (isset($item['item_notes']) && !empty(trim($item['item_notes']))) {
                        $itemData['item_notes'] = trim($item['item_notes']);
                    }

                    $data['items'][] = $itemData;
                }
            }

            return $this->updateResource(
                $request,
                "/official-reports/{$id}",
                $data,
                'Berita acara berhasil diperbarui',
                'official-report.index'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                // Format validation errors for AJAX requests
                $formattedErrors = [];
                foreach ($e->errors() as $field => $messages) {
                    $formattedErrors[] = [
                        'path' => $field,
                        'message' => is_array($messages) ? $messages[0] : $messages
                    ];
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan validasi',
                    'errors' => $formattedErrors,
                ], 422);
            }

            return redirect()->back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses permintaan',
                    'errors' => [
                        [
                            'path' => 'exception',
                            'message' => 'Gagal memperbarui berita acara: ' . $e->getMessage()
                        ]
                    ],
                ], 500);
            }

            return redirect()->back()->withInput()->withErrors('Gagal memperbarui berita acara: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified official report.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        try {
            // First, get the official report details to check if it can be deleted
            $reportData = $this->apiService->request('GET', "/official-reports/{$id}");

            if (!isset($reportData['success']) || $reportData['success'] !== true) {
                $errorData = $reportData['errors'] ?? 'Gagal mengambil detail berita acara';
                $formattedErrors = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Berita acara tidak ditemukan',
                        'errors' => is_array($formattedErrors) ? $formattedErrors : [
                            [
                                'path' => 'not_found',
                                'message' => $formattedErrors
                            ]
                        ],
                    ], 404);
                }

                return redirect()->back()->withErrors($formattedErrors);
            }

            // Check if the report can be deleted (e.g., not already approved)
            $status = $reportData['data']['status'] ?? null;
            $approval1Status = $reportData['data']['approval_1_status'] ?? null;
            $approval2Status = $reportData['data']['approval_2_status'] ?? null;

            if ($status === 'APPROVED' || $approval1Status === 'APPROVED' || $approval2Status === 'APPROVED') {
                $errorMessage = 'Berita acara yang sudah disetujui tidak dapat dihapus';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => [
                            [
                                'path' => 'status',
                                'message' => $errorMessage
                            ]
                        ],
                    ], 403);
                }

                return redirect()->back()->withErrors($errorMessage);
            }

            // Call the API to delete the official report
            $result = $this->apiService->request('DELETE', "/official-reports/{$id}");

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menghapus berita acara';
                $formattedErrors = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal menghapus berita acara',
                        'errors' => is_array($formattedErrors) ? $formattedErrors : [
                            [
                                'path' => 'delete',
                                'message' => $formattedErrors
                            ]
                        ],
                    ], 400);
                }

                return redirect()->back()->withErrors($formattedErrors);
            }

            // Success response
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Berita acara berhasil dihapus',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('official-report.index')->with('success', $result['message'] ?? 'Berita acara berhasil dihapus');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses permintaan',
                    'errors' => [
                        [
                            'path' => 'exception',
                            'message' => 'Gagal menghapus berita acara: ' . $e->getMessage()
                        ]
                    ],
                ], 500);
            }

            return redirect()->back()->withErrors('Gagal menghapus berita acara: ' . $e->getMessage());
        }
    }



    /**
     * Approve official report.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, $id)
    {
        try {
            $result = $this->apiService->request('POST', "/official-reports/{$id}/approve");

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menyetujui berita acara';
                $formattedErrors = DataFormatter::formatErrorMessage($errorData);

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyetujui berita acara',
                    'errors' => is_array($formattedErrors) ? $formattedErrors : [
                        [
                            'path' => 'approval',
                            'message' => $formattedErrors
                        ]
                    ],
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Berita acara berhasil disetujui',
                'data' => $result['data'] ?? null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses permintaan',
                'errors' => [
                    [
                        'path' => 'exception',
                        'message' => 'Gagal menyetujui berita acara: ' . $e->getMessage()
                    ]
                ],
            ], 500);
        }
    }

    /**
     * Reject official report.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, $id)
    {
        try {
            // Call the API to reject the official report
            $result = $this->apiService->request('POST', "/official-reports/{$id}/reject");

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menolak berita acara';
                $formattedErrors = DataFormatter::formatErrorMessage($errorData);

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menolak berita acara',
                    'errors' => is_array($formattedErrors) ? $formattedErrors : [
                        [
                            'path' => 'rejection',
                            'message' => $formattedErrors
                        ]
                    ],
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Berita acara berhasil ditolak',
                'data' => $result['data'] ?? null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses permintaan',
                'errors' => [
                    [
                        'path' => 'exception',
                        'message' => 'Gagal menolak berita acara: ' . $e->getMessage()
                    ]
                ],
            ], 500);
        }
    }

    /**
     * Search for official reports.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function search(Request $request)
    {
        try {
            // Get search parameter
            $search = $request->input('search', '');

            // Build query parameters
            $queryParams = [
                'search' => $search,
                'limit' => 10,  // Return a reasonable number of results
            ];

            // Check if additional filters are provided
            if ($request->has('report_type')) {
                $queryParams['report_type'] = $request->input('report_type');
            }

            if ($request->has('status')) {
                $queryParams['status'] = $request->input('status');
            }

            // Send request to API service
            $result = $this->apiService->request('GET', '/official-reports', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mencari berita acara';
                $formattedErrors = DataFormatter::formatErrorMessage($errorData);

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors,
                ], 400);
            }

            // Return successful response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Berita acara berhasil ditemukan',
                'data' => $result['data'] ?? []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Gagal mencari berita acara: ' . $e->getMessage()]],
            ], 500);
        }
    }
}
