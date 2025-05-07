<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class ProcurementRequestController extends Controller
{
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
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
            ];

            // Add search parameter if provided
            if ($search) {
                $queryParams['search'] = $search;
            }

            // Add status filter if provided
            if ($status) {
                $queryParams['status'] = $status;
            }

            // Set sort parameters based on selection
            if ($sort) {
                switch ($sort) {
                    case 'newest':
                        $queryParams['sort_by'] = 'created_at';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    case 'oldest':
                        $queryParams['sort_by'] = 'created_at';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'title_asc':
                        $queryParams['sort_by'] = 'title';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'title_desc':
                        $queryParams['sort_by'] = 'title';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    default:
                        $queryParams['sort_by'] = 'created_at';
                        $queryParams['sort_order'] = 'desc';
                }
            } else {
                // Default sorting if not specified
                $queryParams['sort_by'] = 'created_at';
                $queryParams['sort_order'] = 'desc';
            }

            $result = $this->apiService->request('GET', '/procurements', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during procurements index retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], status: 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve procurements';

                \Log::warning('Error during procurements index retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], status: 400);
                }

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

                // Return view with empty procurements data and error message
                return view('Procurement.Request.Request', [
                    'procurements' => [],
                    'pagination' => null,
                    'error' => $errorMessage
                ]);
            }

            // Make sure procurements is always defined
            $procurements = $result['data'] ?? [];
            $pagination = $result['pagination'] ?? null;

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Daftar pengadaan berhasil diambil',
                    'data' => $procurements,
                    'pagination' => $pagination
                ]);
            }

            return view('Procurement.Request.Request', [
                'procurements' => $procurements,
                'pagination' => $pagination
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during procurements index retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Failed to fetch procurements: ' . $e->getMessage()]
                ], status: 500);
            }

            // Always pass an empty array for procurements in case of error
            return view('Procurement.Request.Request', [
                'procurements' => [],
                'pagination' => null,
                'error' => 'Failed to fetch procurement data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get procurement data as JSON for API requests
     */
    public function getDataJson(Request $request)
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
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
            ];

            // Add search parameter if provided
            if ($search) {
                $queryParams['search'] = $search;
            }

            // Add status filter if provided
            if ($status) {
                $queryParams['status'] = $status;
            }

            // Set sort parameters based on selection
            if ($sort) {
                switch ($sort) {
                    case 'newest':
                        $queryParams['sort_by'] = 'created_at';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    case 'oldest':
                        $queryParams['sort_by'] = 'created_at';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'title_asc':
                        $queryParams['sort_by'] = 'title';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'title_desc':
                        $queryParams['sort_by'] = 'title';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    default:
                        $queryParams['sort_by'] = 'created_at';
                        $queryParams['sort_order'] = 'desc';
                }
            } else {
                // Default sorting if not specified
                $queryParams['sort_by'] = 'created_at';
                $queryParams['sort_order'] = 'desc';
            }

            $result = $this->apiService->request('GET', '/procurements', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during procurements retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], status: 401);
            }

            // Check if we got an error response from the ApiService
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve procurements';

                \Log::warning('Error during procurements retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                ], status: 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Daftar pengadaan berhasil diambil',
                'data' => $result['data'] ?? [],
                'pagination' => $result['pagination'] ?? [
                    'total_items' => 0,
                    'total_pages' => 0,
                    'current_page' => (int) $page,
                    'limit' => (int) $limit,
                    'has_next' => false,
                    'has_prev' => false
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during procurements retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => 'Failed to retrieve procurements: ' . $e->getMessage()]
            ], status: 500);
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
                'title' => 'required|string|max:255',
                'priority' => 'required|string|in:Low,Medium,High',
                'justification' => 'required|string',
                'details' => 'required|array|min:1',
                'details.*.asset_name' => 'required_without:details.*.asset_master_id|string|max:255',
                'details.*.asset_master_id' => 'required_without:details.*.asset_name|integer|exists:asset_masters,id',
                'details.*.quantity' => 'required|integer|min:1',
                'details.*.estimated_unit_price' => 'required|numeric|min:0',
                'details.*.specifications' => 'nullable|string',
                'details.*.notes' => 'nullable|string',
            ]);

            // Call the API to create the procurement
            $result = $this->apiService->request('POST', '/procurements', [
                'json' => $validated
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during procurement creation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], status: 401);
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to create procurement';

                \Log::warning('Error during procurement creation:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                ], status: 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengadaan berhasil dibuat',
                'data' => $result['data'] ?? []
            ], status: 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], status: 422);
        } catch (\Exception $e) {
            \Log::error('Exception during procurement creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => 'Failed to create procurement: ' . $e->getMessage()]
            ], status: 500);
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
                    'errors' => ['general' => 'Failed to fetch procurement details']
                ], 400);
            }

            // Check if the procurement status allows updates
            $status = $procurementData['data']['status'] ?? null;
            if ($status !== 'Submitted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Procurement with status other than "Submitted" cannot be updated',
                ], 403);
            }

            // Validate request data
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'priority' => 'required|string|in:Low,Medium,High',
                'justification' => 'required|string',
                'details' => 'required|array|min:1',
                'details.*.asset_name' => 'required_without:details.*.asset_master_id|string|max:255',
                'details.*.asset_master_id' => 'required_without:details.*.asset_name|integer|exists:asset_masters,id',
                'details.*.quantity' => 'required|integer|min:1',
                'details.*.estimated_unit_price' => 'required|numeric|min:0',
                'details.*.specifications' => 'nullable|string',
                'details.*.notes' => 'nullable|string',
            ]);

            // Call the API to update the procurement
            $result = $this->apiService->request('PUT', "/procurements/{$id}", [
                'json' => $validated
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during procurement update:', [
                    'errors' => $result['errors'] ?? 'Authentication failed',
                    'procurement_id' => $id
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], status: 401);
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to update procurement';

                \Log::warning('Error during procurement update:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'procurement_id' => $id
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                ], status: 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengadaan berhasil diperbarui',
                'data' => $result['data'] ?? []
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], status: 422);
        } catch (\Exception $e) {
            \Log::error('Exception during procurement update:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'procurement_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => 'Failed to update procurement: ' . $e->getMessage()]
            ], status: 500);
        }
    }

    /**
     * Get a specific procurement request by ID
     */
    public function getOne($id)
    {
        try {
            $result = $this->apiService->request('GET', "/procurements/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during procurement retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed',
                    'procurement_id' => $id
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], status: 401);
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve procurement';

                \Log::warning('Error during procurement retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'procurement_id' => $id
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                ], status: 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengadaan berhasil ditemukan',
                'data' => $result['data'] ?? null
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during procurement retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'procurement_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => 'Failed to retrieve procurement: ' . $e->getMessage()]
            ], status: 500);
        }
    }

    /**
     * Delete a procurement request
     */
    public function destroy($id)
    {
        try {
            // First, get the procurement details to check the status
            $procurementData = $this->apiService->request('GET', "/procurements/{$id}");

            if (!isset($procurementData['success']) || $procurementData['success'] !== true) {
                return response()->json([
                    'success' => false,
                    'errors' => ['general' => 'Failed to fetch procurement details']
                ], 400);
            }

            // Check if the procurement status allows deletion
            $status = $procurementData['data']['status'] ?? null;
            if ($status !== 'Submitted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Procurement with status other than "Submitted" cannot be deleted',
                ], 403);
            }

            $result = $this->apiService->request('DELETE', "/procurements/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during procurement deletion:', [
                    'errors' => $result['errors'] ?? 'Authentication failed',
                    'procurement_id' => $id
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], status: 401);
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to delete procurement';

                \Log::warning('Error during procurement deletion:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'procurement_id' => $id
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                ], status: 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengadaan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during procurement deletion:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'procurement_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => 'Failed to delete procurement: ' . $e->getMessage()]
            ], status: 500);
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
        try {
            \Log::info('Fetching procurement details for ID: ' . $id);

            $result = $this->apiService->request('GET', "/procurements/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during procurement detail retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed',
                    'procurement_id' => $id
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], status: 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve procurement details';

                \Log::warning('Error during procurement detail retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'procurement_id' => $id
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], status: 400);
                }

                return redirect()->route('procurement.request')
                    ->with('error', is_string($errorData) ? $errorData : 'Failed to retrieve procurement details');
            }

            // Make sure procurement data exists
            if (!isset($result['data'])) {
                $errorMessage = 'Procurement data not found';

                \Log::warning('Empty data returned for procurement detail:', [
                    'procurement_id' => $id,
                    'result_keys' => array_keys($result)
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['general' => $errorMessage]
                    ], status: 404);
                }

                return redirect()->route('procurement.request')
                    ->with('error', $errorMessage);
            }

            // Load view with procurement data
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pengadaan berhasil ditemukan',
                    'data' => $result['data']
                ]);
            }

            return view('Procurement.Request.DetailRequest', [
                'procurement' => $result['data']
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during procurement detail retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'procurement_id' => $id
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Failed to fetch procurement details: ' . $e->getMessage()]
                ], status: 500);
            }

            return redirect()->route('procurement.request')
                ->with('error', 'Failed to fetch procurement details: ' . $e->getMessage());
        }
    }

    /**
     * Process manager approval for a procurement request
     *
     * @param int $id Procurement ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function managerApproval($id)
    {
        try {
            // First, get the procurement details to check the grand total
            $procurementData = $this->apiService->request('GET', "/procurements/{$id}");

            if (!isset($procurementData['success']) || $procurementData['success'] !== true) {
                return response()->json([
                    'success' => false,
                    'errors' => ['general' => 'Failed to fetch procurement details']
                ], 400);
            }

            // Manager approval
            $result = $this->apiService->request('POST', "/procurements/{$id}/manager-approval");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during procurement manager approval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed',
                    'procurement_id' => $id
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], 401);
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to approve procurement';

                \Log::warning('Error during procurement manager approval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'procurement_id' => $id
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
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
                        'message' => 'Procurement has been fully approved (manager + auto-director approval for amount ≤ 50M)',
                        'data' => $directorResult['data'] ?? null
                    ]);
                }

                // Even if director approval fails, manager approval succeeded
                return response()->json([
                    'success' => true,
                    'message' => 'Procurement has been approved by manager (auto-director approval failed)',
                    'data' => $result['data'] ?? null
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengadaan berhasil disetujui oleh manajer',
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
                'errors' => ['exception' => 'Failed to approve procurement: ' . $e->getMessage()]
            ], 500);
        }
    }

    /**
     * Process director approval for a procurement request
     *
     * @param int $id Procurement ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function directorApproval($id)
    {
        try {
            $result = $this->apiService->request('POST', "/procurements/{$id}/director-approval");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during procurement director approval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed',
                    'procurement_id' => $id
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], 401);
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to approve procurement';

                \Log::warning('Error during procurement director approval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'procurement_id' => $id
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengadaan berhasil disetujui oleh direktur',
                'data' => $result['data'] ?? null
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during procurement director approval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'procurement_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => 'Failed to approve procurement: ' . $e->getMessage()]
            ], 500);
        }
    }

    /**
     * Process rejection of a procurement request
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id Procurement ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectProcurement(Request $request, $id)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'rejected_reason' => 'required|string|max:500',
            ]);

            // Call the API to reject the procurement
            $result = $this->apiService->request('POST', "/procurements/{$id}/reject", [
                'json' => $validated
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during procurement rejection:', [
                    'errors' => $result['errors'] ?? 'Authentication failed',
                    'procurement_id' => $id
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], 401);
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to reject procurement';

                \Log::warning('Error during procurement rejection:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'procurement_id' => $id
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengadaan berhasil ditolak',
                'data' => $result['data'] ?? null
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Exception during procurement rejection:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'procurement_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => 'Failed to reject procurement: ' . $e->getMessage()]
            ], 500);
        }
    }
}
