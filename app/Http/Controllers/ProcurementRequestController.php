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

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil daftar pengadaan';

                if ($request->ajax() || $request->wantsJson()) {
                    // Format error message for better display in toast notifications
                    $formattedErrors = [];
                    if (is_array($errorData)) {
                        foreach ($errorData as $field => $messages) {
                            if (is_array($messages)) {
                                $formattedErrors[$field] = $messages;
                            } else {
                                $formattedErrors[$field] = [$messages];
                            }
                        }
                    } else {
                        $formattedErrors['general'] = [$errorData];
                    }

                    return response()->json([
                        'success' => false,
                        'errors' => $formattedErrors,
                    ], 400);
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
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => ['Gagal mengambil daftar pengadaan: ' . $e->getMessage()]],
                ], 500);
            }

            // Always pass an empty array for procurements in case of error
            return view('Procurement.Request.Request', [
                'procurements' => [],
                'pagination' => null,
                'error' => 'Gagal mengambil data pengadaan: ' . $e->getMessage()
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
                        $processedDetail['asset_master_id'] = (int)$detail['asset_master_id'];
                    }

                    // Process required fields
                    $processedDetail['quantity'] = (int)$detail['quantity'];
                    $processedDetail['estimated_unit_price'] = (float)$detail['estimated_unit_price'];

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

            // Call the API to create the procurement
            $result = $this->apiService->request('POST', '/procurements', [
                'json' => $validated
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {


                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat pengadaan';

                // Format error message for better display in toast notifications
                $formattedErrors = [];
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $formattedErrors[$field] = $messages;
                        } else {
                            $formattedErrors[$field] = [$messages];
                        }
                    }
                } else {
                    $formattedErrors['general'] = [$errorData];
                }

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors,
                ], 400);
            }

            // Return successful response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Pengadaan berhasil dibuat',
                'data' => $result['data'] ?? null,
                'redirect_url' => route('procurement.request')
            ], 201);
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
                        $processedDetail['asset_master_id'] = (int)$detail['asset_master_id'];
                    }

                    // Process required fields
                    $processedDetail['quantity'] = (int)$detail['quantity'];
                    $processedDetail['estimated_unit_price'] = (float)$detail['estimated_unit_price'];

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

            // Call the API to update the procurement
            $result = $this->apiService->request('PUT', "/procurements/{$id}", [
                'json' => $validated
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui pengadaan';

                // Format error message for better display in toast notifications
                $formattedErrors = [];
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $formattedErrors[$field] = $messages;
                        } else {
                            $formattedErrors[$field] = [$messages];
                        }
                    }
                } else {
                    $formattedErrors['general'] = [$errorData];
                }

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors,
                ], 400);
            }

            // Return successful response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Pengadaan berhasil diperbarui',
                'data' => $result['data'] ?? null,
                'redirect_url' => route('procurement.request')
            ]);
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
    public function getOne( Request $request, $id)
    {
        try {
            $result = $this->apiService->request('GET', "/procurements/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil detail pengadaan';

                // Format error message for better display in toast notifications
                $formattedErrors = [];
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $formattedErrors[$field] = $messages;
                        } else {
                            $formattedErrors[$field] = [$messages];
                        }
                    }
                } else {
                    $formattedErrors['general'] = [$errorData];
                }

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
                $formattedErrors = [];

                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $formattedErrors[$field] = $messages;
                        } else {
                            $formattedErrors[$field] = [$messages];
                        }
                    }
                } else {
                    $formattedErrors['general'] = [$errorData];
                }

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

            $result = $this->apiService->request('DELETE', "/procurements/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menghapus pengadaan';

                // Format error message for better display in toast notifications
                $formattedErrors = [];
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $formattedErrors[$field] = $messages;
                        } else {
                            $formattedErrors[$field] = [$messages];
                        }
                    }
                } else {
                    $formattedErrors['general'] = [$errorData];
                }

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors,
                ], 400);
            }

            // Return successful response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Pengadaan berhasil dihapus'
            ]);
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
        try {
            $result = $this->apiService->request('GET', "/procurements/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {


                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil detail pengadaan';

                if ($request->ajax() || $request->wantsJson()) {
                    // Format error message for better display in toast notifications
                    $formattedErrors = [];
                    if (is_array($errorData)) {
                        foreach ($errorData as $field => $messages) {
                            if (is_array($messages)) {
                                $formattedErrors[$field] = $messages;
                            } else {
                                $formattedErrors[$field] = [$messages];
                            }
                        }
                    } else {
                        $formattedErrors['general'] = [$errorData];
                    }

                    return response()->json([
                        'success' => false,
                        'errors' => $formattedErrors,
                    ], 400);
                }

                return redirect()->route('procurement.request')
                    ->with('error', is_string($errorData) ? $errorData : 'Gagal mengambil detail pengadaan');
            }

            // Make sure procurement data exists
            if (!isset($result['data'])) {
                $errorMessage = 'Procurement data not found';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['general' => [$errorMessage]],
                    ], 404);
                }

                return redirect()->route('procurement.request')
                    ->with('error', $errorMessage);
            }

            // Load view with procurement data
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Pengadaan berhasil ditemukan',
                    'data' => $result['data']
                ]);
            }

            return view('Procurement.Request.DetailRequest', [
                'procurement' => $result['data']
            ]);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => ['Gagal mengambil detail pengadaan: ' . $e->getMessage()]],
                ], 500);
            }

            return redirect()->route('procurement.request')
                ->with('error', 'Gagal mengambil detail pengadaan: ' . $e->getMessage());
        }
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
                $formattedErrors = [];

                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $formattedErrors[$field] = $messages;
                        } else {
                            $formattedErrors[$field] = [$messages];
                        }
                    }
                } else {
                    $formattedErrors['general'] = [$errorData];
                }

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors,
                ], 400);
            }

            // Manager approval
            $result = $this->apiService->request('POST', "/procurements/{$id}/manager-approval");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menyetujui pengadaan';

                // Format error message for better display in toast notifications
                $formattedErrors = [];
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $formattedErrors[$field] = $messages;
                        } else {
                            $formattedErrors[$field] = [$messages];
                        }
                    }
                } else {
                    $formattedErrors['general'] = [$errorData];
                }

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
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menyetujui pengadaan';

                // Format error message for better display in toast notifications
                $formattedErrors = [];
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $formattedErrors[$field] = $messages;
                        } else {
                            $formattedErrors[$field] = [$messages];
                        }
                    }
                } else {
                    $formattedErrors['general'] = [$errorData];
                }

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
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check if we got an error response
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menolak pengadaan';

                // Format error message for better display in toast notifications
                $formattedErrors = [];
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $formattedErrors[$field] = $messages;
                        } else {
                            $formattedErrors[$field] = [$messages];
                        }
                    }
                } else {
                    $formattedErrors['general'] = [$errorData];
                }

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
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {


                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mencari pengadaan';

                // Format error message for better display in toast notifications
                $formattedErrors = [];
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $formattedErrors[$field] = $messages;
                        } else {
                            $formattedErrors[$field] = [$messages];
                        }
                    }
                } else {
                    $formattedErrors['general'] = [$errorData];
                }

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
