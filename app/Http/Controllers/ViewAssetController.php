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
     * Display a listing of brands.
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            // Add debug logging
            \Log::info('Fetching brands with parameters:', [
                'page' => $page,
                'limit' => $limit,
                'request_url' => $request->fullUrl()
            ]);

            $result = $this->apiService->request('GET', '/brands', [
                'query' => [
                    'page' => $page,
                    'limit' => $limit,
                    'sort_by' => 'brand_id',
                    'sort_order' => 'asc'
                ]
            ]);

            // Log the API response for debugging
            \Log::info('API response for brands list:', [
                'api_response' => $result
            ]);

            // Check if we got an error response from the ApiService
            if (isset($result['error'])) {
                if (strpos($result['error'], 'login') !== false) {
                    return redirect()->route('login')->with('error', $result['error']);
                }

                throw new \Exception($result['error']);
            }

            // Return to the Asset.ViewAsset view with the brand data
            return view('Asset.ViewAsset', [
                'brands' => $result['data'] ?? [],
                'pagination' => $result['pagination'] ?? null
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch brands', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('Asset.ViewAsset', [
                'brands' => [],
                'error' => 'Failed to fetch brands: ' . $e->getMessage()
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
}
