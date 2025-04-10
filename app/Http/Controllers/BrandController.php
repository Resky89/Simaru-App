<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class BrandController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display a listing of all brands.
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');

            // Log request info
            \Log::info('Fetching brands with parameters:', [
                'page' => $page,
                'limit' => $limit,
                'search' => $search,
                'request_url' => $request->fullUrl()
            ]);

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
                'sort_by' => 'brand_id',
                'sort_order' => 'asc'
            ];

            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Fetch brands
            $brandsResult = $this->apiService->request('GET', '/brands', [
                'query' => $queryParams
            ]);

            // Log API responses for debugging
            \Log::info('API response for brands list:', [
                'brands_status' => $brandsResult['status'] ?? null,
                'brands_count' => isset($brandsResult['data']) ? count($brandsResult['data']) : 0
            ]);

            // Check for auth errors
            if (isset($brandsResult['error']) && in_array($brandsResult['error'], ['auth_failed', 'session_expired'])) {
                $errorMessage = $brandsResult['message'] ?? 'Authentication failed';
                return redirect()->route('login')->with('error', $errorMessage);
            }

            // Check for API errors based on status flag
            if (!isset($brandsResult['status']) || $brandsResult['status'] !== true) {
                $errorMessage = $brandsResult['message'] ?? 'Failed to fetch data';

                \Log::warning('Error during data retrieval:', [
                    'brands_status' => $brandsResult['status'] ?? false,
                    'message' => $errorMessage
                ]);

                return view('Brand.Brand', [
                    'brands' => [],
                    'brands_pagination' => null,
                    'error' => $errorMessage
                ]);
            }

            $brands = $brandsResult['data'] ?? [];

            // Format pagination for brands
            $brandsPagination = null;
            if (isset($brandsResult['pagination'])) {
                $pagination = $brandsResult['pagination'];
                $brandsPagination = [
                    'current_page' => $pagination['current_page'] ?? 1,
                    'last_page' => ceil(($pagination['total_items'] ?? 0) / ($pagination['limit'] ?? 10)),
                    'from' => (($pagination['current_page'] ?? 1) - 1) * ($pagination['limit'] ?? 10) + 1,
                    'to' => min(($pagination['current_page'] ?? 1) * ($pagination['limit'] ?? 10), $pagination['total_items'] ?? 0),
                    'total' => $pagination['total_items'] ?? 0,
                    'per_page' => $pagination['limit'] ?? 10,
                    'next_page_url' => ($pagination['has_next'] ?? false) ? url()->current() . '?page=' . ($pagination['current_page'] + 1) : null,
                    'prev_page_url' => ($pagination['has_prev'] ?? false) ? url()->current() . '?page=' . ($pagination['current_page'] - 1) : null,
                ];
            }

            return view('Brand.Brand', [
                'brands' => $brands,
                'brands_pagination' => $brandsPagination
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during data retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('Brand.Brand', [
                'brands' => [],
                'brands_pagination' => null,
                'error' => 'Failed to fetch data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created brand.
     */
    public function store(Request $request)
    {
        try {
            // Log the request data
            \Log::info('Attempting to create brand with data:', [
                'request_data' => $request->all()
            ]);

            $result = $this->apiService->request('POST', '/brands', [
                'json' => [
                    'brand_name' => $request->input('brand_name')
                ]
            ]);

            // Log the API response
            \Log::info('API response for brand creation:', [
                'api_response' => $result
            ]);

            // Check if we got an auth error response
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during brand creation:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (isset($result['error']) || (isset($result['success']) && $result['success'] === false)) {
                \Log::warning('Error during brand creation:', [
                    'error' => $result['error'] ?? null,
                    'success' => $result['success'] ?? null,
                    'message' => $result['message'] ?? 'Failed to create brand'
                ]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Failed to create brand');
            }

            // Successfully created
            \Log::info('Brand created successfully');
            return redirect()->route('brands')
                ->with('success', 'Brand created successfully');
        } catch (\Exception $e) {
            \Log::error('Exception during brand creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'brand_data' => $request->except('_token')
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create brand: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified brand.
     */
    public function update(Request $request, $id)
    {
        try {
            $result = $this->apiService->request('PUT', "/brands/{$id}", [
                'json' => [
                    'brand_id' => $id,
                    'brand_name' => $request->input('brand_name')
                ]
            ]);

            // Check if we got an auth error response
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (isset($result['error']) || (isset($result['success']) && $result['success'] === false)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Failed to update brand');
            }

            // Successfully updated
            return redirect()->route('brands')
                ->with('success', 'Brand updated successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to update brand', [
                'error' => $e->getMessage(),
                'brand_id' => $id,
                'brand_data' => $request->except(['_token', '_method'])
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update brand: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified brand.
     */
    public function destroy($id)
    {
        try {
            $result = $this->apiService->request('DELETE', "/brands/{$id}");

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (isset($result['error']) || (isset($result['success']) && $result['success'] === false)) {
                return redirect()->back()
                    ->with('error', $result['message'] ?? 'Failed to delete brand');
            }

            // Successfully deleted
            return redirect()->route('brands')
                ->with('success', 'Brand deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to delete brand', [
                'error' => $e->getMessage(),
                'brand_id' => $id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete brand: ' . $e->getMessage());
        }
    }

    /**
     * Get a single brand for editing.
     */
    public function getBrand($id)
    {
        try {
            // Fetch the brand with the given ID
            $result = $this->apiService->request('GET', "/brands/{$id}");

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                if (request()->ajax()) {
                    return response()->json(['error' => $result['message'] ?? 'Authentication failed'], 401);
                }
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors
            if (!isset($result['status']) || $result['status'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to retrieve brand';

                if (request()->ajax()) {
                    return response()->json(['error' => $errorMessage], 400);
                }
                return redirect()->back()->with('error', $errorMessage);
            }

            $brand = $result['data'] ?? null;

            if (!$brand) {
                $errorMessage = 'Brand not found or response data is invalid';

                if (request()->ajax()) {
                    return response()->json(['error' => $errorMessage], 404);
                }
                return redirect()->back()->with('error', $errorMessage);
            }

            if (request()->ajax()) {
                return response()->json(['brand' => $brand]);
            }

            return view('Brand.EditBrand', ['brand' => $brand]);
        } catch (\Exception $e) {
            $errorMessage = 'Failed to retrieve brand: ' . $e->getMessage();

            if (request()->ajax()) {
                return response()->json(['error' => $errorMessage], 500);
            }
            return redirect()->back()->with('error', $errorMessage);
        }
    }
}
