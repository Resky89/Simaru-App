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

            // Check if we got an error response from the ApiService
            if (isset($result['error'])) {
                if (strpos($result['error'], 'login') !== false) {
                    return redirect()->route('login')->with('error', $result['error']);
                }
                throw new \Exception($result['error']);
            }

            // Make sure procurements is always defined
            $procurements = $result['data'] ?? [];

            return view('Procurement.Request.Request', [
                'procurements' => $procurements,
                'pagination' => $result['pagination'] ?? null
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch procurements', [
                'error' => $e->getMessage()
            ]);

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

            // Check if we got an error response from the ApiService
            if (isset($result['error'])) {
                throw new \Exception($result['error']);
            }

            return response()->json([
                'status' => true,
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
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve procurements: ' . $e->getMessage()
            ], 500);
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

            // Check if we got an error response
            if (isset($result['error'])) {
                throw new \Exception($result['error']);
            }

            return response()->json([
                'status' => true,
                'message' => 'Procurement created successfully',
                'data' => $result['data'] ?? []
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create procurement: ' . $e->getMessage()
            ], 500);
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

            // Check if we got an error response
            if (isset($result['error'])) {
                return response()->json([
                    'status' => false,
                    'message' => $result['error']
                ], 400);
            }

            return response()->json([
                'status' => true,
                'message' => 'Procurement updated successfully',
                'data' => $result['data'] ?? []
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Failed to update procurement', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to update procurement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific procurement request by ID
     */
    public function getOne($id)
    {
        try {
            $result = $this->apiService->request('GET', "/procurements/{$id}");

            // Check if we got an error response from the ApiService
            if (isset($result['error'])) {
                throw new \Exception($result['error']);
            }

            return response()->json([
                'status' => true,
                'message' => 'Procurement retrieved successfully',
                'data' => $result['data'] ?? null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve procurement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a procurement request
     */
    public function destroy($id)
    {
        try {
            $result = $this->apiService->request('DELETE', "/procurements/{$id}");

            // Check if we got an error response from the ApiService
            if (isset($result['error'])) {
                throw new \Exception($result['error']);
            }

            return response()->json([
                'status' => true,
                'message' => 'Procurement deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete procurement: ' . $e->getMessage()
            ], 500);
        }
    }
}
