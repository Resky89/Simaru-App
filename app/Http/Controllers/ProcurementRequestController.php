<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use App\Helpers\DataFormatter;
use App\Http\Controllers\Traits\ApiResourceOperations;

class ProcurementRequestController extends Controller
{
    use ApiResourceOperations;

    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display the procurements page with pagination.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            // Get pagination parameters with defaults
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            // Get search and filter parameters
            $search = $request->input('search');
            $status = $request->input('status');
            $sort = $request->input('sort');

            // Build query parameters
            $extraParams = [];

            // Add status filter if provided
            if ($status) {
                $extraParams['status'] = $status;
            }

            // Set sort mappings based on selection
            $sortMappings = [
                'newest' => ['sort_by' => 'created_at', 'sort_order' => 'desc'],
                'oldest' => ['sort_by' => 'created_at', 'sort_order' => 'asc'],
                'title_asc' => ['sort_by' => 'title', 'sort_order' => 'asc'],
                'title_desc' => ['sort_by' => 'title', 'sort_order' => 'desc'],
            ];

            return $this->getResourceList(
                $request,
                '/procurements',
                'procurements',
                'Procurement.Request.Request',
                'created_at',
                $extraParams,
                $sortMappings
            );
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Procurement.Request.Request', [
                'procurements' => [],
                'pagination' => null
            ]);
        }
    }

    /**
     * Create a new procurement request
     */
    public function store(Request $request)
    {
        try {
            // Validate request data
            $validated = $request->validate([
                'title' => 'required|string',
                'priority' => 'required|string|in:Low,Medium,High',
                'justification' => 'required|string',
                'details' => 'required|array',
                'details.*.asset_name' => 'required_without:details.*.asset_master_id|string',
                'details.*.asset_master_id' => 'required_without:details.*.asset_name|integer',
                'details.*.quantity' => 'required|integer',
                'details.*.estimated_unit_price' => 'required|numeric',
            ]);

            // Process details to ensure proper types
            if (isset($validated['details']) && is_array($validated['details'])) {
                // Ensure details is indexed numerically to maintain array structure
                $details = array_values($validated['details']);
                $validated['details'] = [];

                foreach ($details as $detail) {
                    $processedDetail = [];

                    // Process asset_name or asset_master_id
                    if (isset($detail['asset_name'])) {
                        $processedDetail['asset_name'] = $detail['asset_name'];
                    }

                    if (isset($detail['asset_master_id'])) {
                        $processedDetail['asset_master_id'] = (int) $detail['asset_master_id'];
                    }

                    // Process required fields
                    $processedDetail['quantity'] = (int) $detail['quantity'];
                    $processedDetail['estimated_unit_price'] = (float) $detail['estimated_unit_price'];

                    // Process optional fields
                    if (isset($detail['specifications'])) {
                        $processedDetail['specifications'] = $detail['specifications'];
                    }

                    if (isset($detail['notes'])) {
                        $processedDetail['notes'] = $detail['notes'];
                    }

                    // Add to details array
                    $validated['details'][] = $processedDetail;
                }
            }

            return $this->storeResource(
                $request,
                '/procurements',
                $validated,
                'Pengadaan berhasil dibuat',
                'procurement.request'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Gagal membuat pengadaan: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Update an existing procurement request
     */
    public function update(Request $request, $id)
    {
        try {
            // First, get the procurement details to check the status
            $procurementData = $this->apiService->request('GET', "/procurements/{$id}");

            if (!isset($procurementData['success']) || $procurementData['success'] !== true) {
                return response()->json([
                    'success' => false,
                    'errors' => $procurementData['errors'] ?? 'Gagal mengambil detail pengadaan',
                ], 400);
            }

            // Check if the procurement status allows updates
            $status = $procurementData['data']['status'] ?? null;
            if ($status !== 'Submitted') {
                return response()->json([
                    'success' => false,
                    'errors' => ['status' => ['Pengadaan dengan status lainnya dari "Submitted" tidak dapat diperbarui']],
                ], 403);
            }

            // Validate request data
            $validated = $request->validate([
                'title' => 'required|string',
                'priority' => 'required|string|in:Low,Medium,High',
                'justification' => 'required|string',
                'details' => 'required|array',
                'details.*.asset_name' => 'required_without:details.*.asset_master_id|string',
                'details.*.asset_master_id' => 'required_without:details.*.asset_name|integer',
                'details.*.quantity' => 'required|integer',
                'details.*.estimated_unit_price' => 'required|numeric',
                'details.*.specifications' => 'nullable|string',
                'details.*.notes' => 'nullable|string',
            ]);

            // Process details to ensure proper types
            if (isset($validated['details']) && is_array($validated['details'])) {
                // Ensure details is indexed numerically to maintain array structure
                $details = array_values($validated['details']);
                $validated['details'] = [];

                foreach ($details as $detail) {
                    $processedDetail = [];

                    // Process asset_name or asset_master_id
                    if (isset($detail['asset_name'])) {
                        $processedDetail['asset_name'] = $detail['asset_name'];
                    }

                    if (isset($detail['asset_master_id'])) {
                        $processedDetail['asset_master_id'] = (int) $detail['asset_master_id'];
                    }

                    // Process required fields
                    $processedDetail['quantity'] = (int) $detail['quantity'];
                    $processedDetail['estimated_unit_price'] = (float) $detail['estimated_unit_price'];

                    // Process optional fields
                    if (isset($detail['specifications'])) {
                        $processedDetail['specifications'] = $detail['specifications'];
                    }

                    if (isset($detail['notes'])) {
                        $processedDetail['notes'] = $detail['notes'];
                    }

                    // Add to details array
                    $validated['details'][] = $processedDetail;
                }
            }

            return $this->updateResource(
                $request,
                "/procurements/{$id}",
                $validated,
                'Pengadaan berhasil diperbarui',
                'procurement.request'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Gagal memperbarui pengadaan: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Get a specific procurement request by ID
     * @param Request $request
     * @param int $id Procurement ID
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function getOne(Request $request, $id)
    {
        try {
            $result = $this->apiService->request('GET', "/procurements/{$id}");

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil detail pengadaan';

                // Format error message for better display in toast notifications
                $formattedErrors = DataFormatter::formatErrorMessage($errorData);

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors,
                ], 404);
            }

            // Return successful response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Pengadaan berhasil ditemukan',
                'data' => $result['data'] ?? null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Gagal mengambil detail pengadaan: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Delete a procurement request
     */
    public function destroy(Request $request, $id)
    {
        try {
            // First, get the procurement details to check the status
            $procurementData = $this->apiService->request('GET', "/procurements/{$id}");

            if (!isset($procurementData['success']) || $procurementData['success'] !== true) {
                // Format error message for toast notifications
                $errorData = $procurementData['errors'] ?? 'Gagal mengambil detail pengadaan';
                $formattedErrors = DataFormatter::formatErrorMessage($errorData);

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors,
                ], 400);
            }

            // Check if the procurement status allows deletion
            $status = $procurementData['data']['status'] ?? null;
            if ($status !== 'Submitted') {
                return response()->json([
                    'success' => false,
                    'errors' => ['status' => ['Pengadaan dengan status lainnya dari "Submitted" tidak dapat dihapus']],
                ], 403);
            }

            return $this->deleteResource(
                $request,
                "/procurements/{$id}",
                'Pengadaan berhasil dihapus',
                'procurement.request'
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Gagal menghapus pengadaan: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Display the procurement detail page.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $id)
    {
        return $this->getResource(
            $request,
            "/procurements/{$id}",
            'procurement',
            'Procurement.Request.DetailRequest'
        );
    }

    /**
     * Process manager approval for a procurement request
     * @param Request $request
     * @param int $id Procurement ID
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function managerApproval(Request $request, $id)
    {
        try {
            // First, get the procurement details to check the grand total
            $procurementData = $this->apiService->request('GET', "/procurements/{$id}");

            if (!isset($procurementData['success']) || $procurementData['success'] !== true) {
                // Format error message for toast notifications
                $errorData = $procurementData['errors'] ?? 'Gagal mengambil detail pengadaan';
                $formattedErrors = DataFormatter::formatErrorMessage($errorData);

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors,
                ], 400);
            }

            // Manager approval
            $result = $this->apiService->request('POST', "/procurements/{$id}/manager-approval");

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menyetujui pengadaan';

                // Format error message for better display in toast notifications
                $formattedErrors = DataFormatter::formatErrorMessage($errorData);

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors,
                ], 400);
            }

            // Check if grand total is less than or equal to 50 million
            $grandTotal = $procurementData['data']['estimated_grand_total'] ?? 0;
            if ($grandTotal <= 50000000) {
                // Automatically approve as director as well
                $directorResult = $this->apiService->request('POST', "/procurements/{$id}/director-approval");

                if (isset($directorResult['success']) && $directorResult['success'] === true) {
                    return response()->json([
                        'success' => true,
                        'message' => $directorResult['message'] ?? 'Pengadaan berhasil disetujui oleh direktur',
                        'data' => $directorResult['data'] ?? null
                    ]);
                }

                // Even if director approval fails, manager approval succeeded
                return response()->json([
                    'success' => true,
                    'message' => $directorResult['message'] ?? 'Pengadaan berhasil disetujui oleh direktur',
                    'data' => $result['data'] ?? null
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Pengadaan berhasil disetujui oleh manajer',
                'data' => $result['data'] ?? null
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during procurement manager approval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'procurement_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Gagal menyetujui pengadaan: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Process director approval for a procurement request
     *
     * @param Request $request
     * @param int $id Procurement ID
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function directorApproval(Request $request, $id)
    {
        try {
            $result = $this->apiService->request('POST', "/procurements/{$id}/director-approval");

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menyetujui pengadaan';

                // Format error message for better display in toast notifications
                $formattedErrors = DataFormatter::formatErrorMessage($errorData);

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors,
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Pengadaan berhasil disetujui oleh direktur',
                'data' => $result['data'] ?? null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Gagal menyetujui pengadaan: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Process rejection of a procurement request
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id Procurement ID
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function rejectProcurement(Request $request, $id)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'rejected_reason' => 'required|string',
            ]);

            // Call the API to reject the procurement
            $result = $this->apiService->request('POST', "/procurements/{$id}/reject", [
                'json' => $validated
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menolak pengadaan';

                // Format error message for better display in toast notifications
                $formattedErrors = DataFormatter::formatErrorMessage($errorData);

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors,
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Pengadaan berhasil ditolak',
                'data' => $result['data'] ?? null
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Gagal menolak pengadaan: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Start procurement process.
     *
     * @param Request $request
     * @param int $id Procurement ID
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function startProcurement(Request $request, $id)
    {
        try {
            // Send request to API
            $result = $this->apiService->request('PATCH', "/procurements/{$id}/start");

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memulai proses pengadaan';

                // Format error message for better display in toast notifications
                $formattedErrors = DataFormatter::formatErrorMessage($errorData);

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors,
                ], 400);
            }

            // Return successful response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Pengadaan berhasil dimulai',
                'data' => $result['data'] ?? null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Gagal memulai proses pengadaan: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Search for procurement requests
     * @param int $id Procurement ID
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

            // Check if status parameter is provided
            if ($request->has('status')) {
                $queryParams['status'] = $request->input('status');
            }

            // Send request to API service
            $result = $this->apiService->request('GET', '/procurements', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mencari pengadaan';

                // Format error message for better display in toast notifications
                $formattedErrors = DataFormatter::formatErrorMessage($errorData);

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors,
                ], 400);
            }

            // Return successful response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Pengadaan berhasil ditemukan',
                'data' => $result['data'] ?? []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Gagal mencari pengadaan: ' . $e->getMessage()]],
            ], 500);
        }
    }
}
