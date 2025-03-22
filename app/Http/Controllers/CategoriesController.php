<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;

class CategoriesController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display a listing of the asset subcategories.
     */
    public function index(Request $request)
    {
        try {
            // Get query parameters
            $page = $request->query('page', 1);
            $limit = $request->query('limit', 10);
            $assetType = $request->query('asset_type', '');

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
                'sort_by' => 'subcategory_id',
                'sort_order' => 'asc'
            ];

            if (!empty($assetType)) {
                $queryParams['asset_type'] = $assetType;
            }

            // Get subcategories from API
            $result = $this->apiService->request('GET', '/asset-subcategories', ['query' => $queryParams]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                Log::warning('Authentication error while fetching asset subcategories:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Get asset types for the dropdown
            $assetTypesResult = $this->apiService->request('GET', '/asset-types');
            $assetTypes = $assetTypesResult['data'] ?? [];

            // Format data for the view
            $subcategories = $result['data'] ?? [];
            $pagination = $result['pagination'] ?? [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => $limit,
                'total' => count($subcategories)
            ];

            return view('Asset.AssetCategories', compact('subcategories', 'assetTypes', 'pagination'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch asset subcategories:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('Asset.AssetCategories', [
                'subcategories' => [],
                'assetTypes' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $limit,
                    'total' => 0
                ],
                'error' => 'Failed to load asset subcategories: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created subcategory.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'asset_type' => 'required|string',
                'subcategory_name' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);

            // Log the request data
            Log::info('Attempting to create subcategory with data:', [
                'request_data' => $validated
            ]);

            $result = $this->apiService->request('POST', '/asset-subcategories', ['json' => $validated]);

            // Log the API response
            Log::info('API response for subcategory creation:', [
                'api_response' => $result
            ]);

            // Check if we got an auth error response
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                Log::warning('Authentication error during subcategory creation:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (isset($result['error']) || (isset($result['success']) && $result['success'] === false)) {
                Log::warning('Error during subcategory creation:', [
                    'error' => $result['error'] ?? null,
                    'success' => $result['success'] ?? null,
                    'message' => $result['message'] ?? 'Failed to create subcategory'
                ]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Failed to create subcategory');
            }

            // Successfully created
            Log::info('Subcategory created successfully');
            return redirect()->route('categories')
                ->with('success', 'Subcategory added successfully');
        } catch (\Exception $e) {
            Log::error('Exception during subcategory creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'subcategory_data' => $request->except('_token')
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add subcategory: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified subcategory.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'asset_type' => 'required|string',
                'subcategory_name' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);

            $result = $this->apiService->request('PUT', "/asset-subcategories/{$id}", [
                'json' => array_merge(['subcategory_id' => $id], $validated)
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors
            if (isset($result['error']) || (isset($result['success']) && $result['success'] === false)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Failed to update subcategory');
            }

            return redirect()->route('categories')
                ->with('success', 'Subcategory updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update subcategory', [
                'error' => $e->getMessage(),
                'subcategory_id' => $id,
                'subcategory_data' => $request->except(['_token', '_method'])
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update subcategory: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified subcategory.
     */
    public function destroy($id)
    {
        try {
            $result = $this->apiService->request('DELETE', "/asset-subcategories/{$id}");

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (isset($result['error']) || (isset($result['success']) && $result['success'] === false)) {
                return redirect()->back()
                    ->with('error', $result['message'] ?? 'Failed to delete subcategory');
            }

            // Successfully deleted
            return redirect()->route('categories')
                ->with('success', 'Subcategory deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete subcategory', [
                'error' => $e->getMessage(),
                'subcategory_id' => $id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete subcategory: ' . $e->getMessage());
        }
    }

    /**
     * Get asset subcategories by asset type.
     */
    public function getByAssetType(Request $request)
    {
        try {
            $assetType = $request->query('asset_type');

            if (empty($assetType)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Asset type is required'
                ], 400);
            }

            $result = $this->apiService->request('GET', 'asset-subcategories', ['query' => [
                'asset_type' => $assetType
            ]]);

            // Check for auth errors in JSON context
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return response()->json([
                    'status' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error fetching subcategories by asset type:', [
                'error' => $e->getMessage(),
                'asset_type' => $assetType
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching subcategories: ' . $e->getMessage()
            ], 500);
        }
    }
}
