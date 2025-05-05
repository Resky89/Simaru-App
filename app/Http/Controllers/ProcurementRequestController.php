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

            $result = $this->apiService->request('GET', '/procurements', [
                'query' => [
                    'page' => $page,
                    'limit' => $limit,
                    'sort_by' => 'created_at',
                    'sort_order' => 'desc'
                ]
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
                    'message' => 'Procurements retrieved successfully',
                    'procurements' => $procurements,
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

            $result = $this->apiService->request('GET', '/procurements', [
                'query' => [
                    'page' => $page,
                    'limit' => $limit,
                    'sort_by' => 'created_at',
                    'sort_order' => 'desc'
                ]
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
                'message' => 'Procurements retrieved successfully',
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
                'details.*.asset_name' => 'required|string|max:255',
                'details.*.quantity' => 'required|integer|min:1',
                'details.*.estimated_unit_price' => 'required|numeric|min:0',
                'details.*.specifications' => 'nullable|string',
                'details.*.justification' => 'nullable|string',
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
                'message' => 'Procurement created successfully',
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
            // Validate request data
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'priority' => 'required|string|in:Low,Medium,High',
                'justification' => 'required|string',
                'details' => 'required|array|min:1',
                'details.*.asset_name' => 'required|string|max:255',
                'details.*.quantity' => 'required|integer|min:1',
                'details.*.estimated_unit_price' => 'required|numeric|min:0',
                'details.*.specifications' => 'nullable|string',
                'details.*.justification' => 'nullable|string',
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
                'message' => 'Procurement updated successfully',
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
                'message' => 'Procurement retrieved successfully',
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
                'message' => 'Procurement deleted successfully'
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
                    'data' => $result['data'],
                    'message' => 'Procurement details retrieved successfully'
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
}
