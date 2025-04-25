<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Barryvdh\DomPDF\Facade\Pdf;

class MasterAssetController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display a listing of master assets.
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 100);
            $search = $request->input('search', '');
            $assetType = $request->input('type', '');
            $brandId = $request->input('brand', '');
            $subcategoryId = $request->input('category', '');
            $sortOrder = $request->input('sort', 'newest');

            // Log request info
            \Log::info('Fetching master assets with parameters:', [
                'page' => $page,
                'limit' => $limit,
                'search' => $search,
                'asset_type' => $assetType,
                'brand_id' => $brandId,
                'subcategory_id' => $subcategoryId,
                'sort' => $sortOrder,
                'request_url' => $request->fullUrl()
            ]);

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit
            ];

            // Set sort parameters based on sortOrder
            switch ($sortOrder) {
                case 'oldest':
                    $queryParams['sort_by'] = 'asset_master_id';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'name_asc':
                    $queryParams['sort_by'] = 'asset_name';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'name_desc':
                    $queryParams['sort_by'] = 'asset_name';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'newest':
                default:
                    $queryParams['sort_by'] = 'asset_master_id';
                    $queryParams['sort_order'] = 'desc';
                    break;
            }

            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            if (!empty($assetType)) {
                $queryParams['asset_type'] = $assetType;
            }

            if (!empty($brandId)) {
                $queryParams['brand_id'] = $brandId;
            }

            if (!empty($subcategoryId)) {
                $queryParams['subcategory_id'] = $subcategoryId;
            }

            // Fetch master assets
            $masterAssetsResult = $this->apiService->request('GET', '/asset-masters', [
                'query' => $queryParams
            ]);

            // Fetch subcategories for dropdown
            $subcategoriesResult = $this->apiService->request('GET', '/asset-subcategories', [
                'query' => [
                    'page' => 1,
                    'limit' => 1000, // High limit to load all for client-side lazy loading
                    'sort_by' => 'subcategory_id',
                    'sort_order' => 'asc'
                ]
            ]);

            // Fetch brands for dropdown with pagination
            $brandsResult = $this->apiService->request('GET', '/brands', [
                'query' => [
                    'page' => 1, // Always get first page for complete set
                    'limit' => 1000, // High limit to load all for client-side lazy loading
                    'sort_by' => 'brand_id',
                    'sort_order' => 'asc'
                ]
            ]);

            // Log API responses for debugging
            \Log::info('API response for master assets list:', [
                'assets_status' => $masterAssetsResult['status'] ?? null,
                'assets_count' => isset($masterAssetsResult['data']) ? count($masterAssetsResult['data']) : 0,
                'brands_status' => $brandsResult['status'] ?? null,
                'brands_count' => isset($brandsResult['data']) ? count($brandsResult['data']) : 0
            ]);

            // Check for auth errors
            if (
                (isset($brandsResult['error']) && in_array($brandsResult['error'], ['auth_failed', 'session_expired'])) ||
                (isset($masterAssetsResult['error']) && in_array($masterAssetsResult['error'], ['auth_failed', 'session_expired']))
            ) {
                $errorMessage = $brandsResult['message'] ?? $masterAssetsResult['message'] ?? 'Authentication failed';
                return redirect()->route('login')->with('error', $errorMessage);
            }

            // Check for API errors based on status flag
            if (
                (!isset($masterAssetsResult['status']) && !isset($masterAssetsResult['success'])) ||
                (isset($masterAssetsResult['status']) && $masterAssetsResult['status'] !== true) ||
                (isset($masterAssetsResult['success']) && $masterAssetsResult['success'] !== true) ||
                (!isset($brandsResult['status']) && !isset($brandsResult['success'])) ||
                (isset($brandsResult['status']) && $brandsResult['status'] !== true) ||
                (isset($brandsResult['success']) && $brandsResult['success'] !== true)
            ) {
                $errorMessage = $masterAssetsResult['message'] ?? $brandsResult['message'] ?? 'Failed to fetch data';

                \Log::warning('Error during data retrieval:', [
                    'assets_status' => $masterAssetsResult['status'] ?? $masterAssetsResult['success'] ?? false,
                    'brands_status' => $brandsResult['status'] ?? $brandsResult['success'] ?? false,
                    'message' => $errorMessage
                ]);

                return view('Asset.MasterAsset', [
                    'masterAssets' => [],
                    'brands' => [],
                    'masterAssets_pagination' => null,
                    'brands_pagination' => null,
                    'subcategories' => [],
                    'assetTypes' => [],
                    'error' => $errorMessage
                ]);
            }

            // Parse subcategories data
            $subcategories = $subcategoriesResult['data'] ?? [];

            // Extract asset types from subcategories
            $assetTypes = [];
            foreach ($subcategories as $subcategory) {
                if (isset($subcategory['asset_type']) && !empty($subcategory['asset_type'])) {
                    // Normalize the asset_type value
                    $type = strtolower(trim($subcategory['asset_type']));
                    if (!in_array($type, $assetTypes)) {
                        $assetTypes[] = $type;
                    }
                }
            }

            // Parse other data
            $brands = $brandsResult['data'] ?? [];
            $masterAssets = $masterAssetsResult['data'] ?? [];

            // Format pagination for masterAssets
            $masterAssetsPagination = null;
            if (isset($masterAssetsResult['pagination'])) {
                $pagination = $masterAssetsResult['pagination'];
                $masterAssetsPagination = [
                    'current_page' => $pagination['current_page'] ?? 1,
                    'last_page' => $pagination['total_pages'] ?? ceil(($pagination['total_items'] ?? 0) / ($pagination['limit'] ?? 10)),
                    'from' => (($pagination['current_page'] ?? 1) - 1) * ($pagination['limit'] ?? 10) + 1,
                    'to' => min(($pagination['current_page'] ?? 1) * ($pagination['limit'] ?? 10), $pagination['total_items'] ?? 0),
                    'total' => $pagination['total_items'] ?? 0,
                    'per_page' => $pagination['limit'] ?? 10,
                    'next_page_url' => ($pagination['has_next'] ?? false) ? url()->current() . '?page=' . ($pagination['current_page'] + 1) : null,
                    'prev_page_url' => ($pagination['has_prev'] ?? false) ? url()->current() . '?page=' . ($pagination['current_page'] - 1) : null,
                ];
            }

            // Format pagination for brands
            $brandsPagination = null;
            if (isset($brandsResult['pagination'])) {
                $pagination = $brandsResult['pagination'];
                $brandsPagination = [
                    'current_page' => $pagination['current_page'] ?? 1,
                    'last_page' => $pagination['total_pages'] ?? ceil(($pagination['total_items'] ?? 0) / ($pagination['limit'] ?? 10)),
                    'from' => (($pagination['current_page'] ?? 1) - 1) * ($pagination['limit'] ?? 10) + 1,
                    'to' => min(($pagination['current_page'] ?? 1) * ($pagination['limit'] ?? 10), $pagination['total_items'] ?? 0),
                    'total' => $pagination['total_items'] ?? 0,
                    'per_page' => $pagination['limit'] ?? 10,
                    'next_page_url' => ($pagination['has_next'] ?? false) ? url()->current() . '?brand_page=' . ($pagination['current_page'] + 1) : null,
                    'prev_page_url' => ($pagination['has_prev'] ?? false) ? url()->current() . '?brand_page=' . ($pagination['current_page'] - 1) : null,
                ];
            }

            return view('Asset.MasterAsset', [
                'masterAssets' => $masterAssets,
                'brands' => $brands,
                'masterAssets_pagination' => $masterAssetsPagination,
                'brands_pagination' => $brandsPagination,
                'subcategories' => $subcategories,
                'assetTypes' => $assetTypes
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during data retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('Asset.MasterAsset', [
                'masterAssets' => [],
                'brands' => [],
                'masterAssets_pagination' => null,
                'brands_pagination' => null,
                'subcategories' => [],
                'assetTypes' => [],
                'error' => 'Failed to fetch data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created master asset.
     */
    public function storeMasterAsset(Request $request)
    {
        try {
            // Log the request data
            \Log::info('Attempting to create master asset with data:', [
                'request_data' => $request->all()
            ]);

            // Prepare master asset data
            $masterAssetData = [
                'asset_name' => $request->input('asset_name'),
                'description' => $request->input('description'),
                'subcategory_id' => (int) $request->input('subcategory_id'),
                'brand_id' => (int) $request->input('brand_id'),
                'is_depreciable' => $request->input('is_depreciable') === 'true' || $request->input('is_depreciable') === true,
                'needs_calibration' => $request->input('needs_calibration') === 'true' || $request->input('needs_calibration') === true,
                'asset_type' => $request->input('asset_type')
            ];

            // Log structured data yang akan dikirim ke API
            \Log::info('Sending to API:', [
                'master_asset_data' => $masterAssetData
            ]);

            // Handle image upload if present
            if ($request->hasFile('image_file')) {
                $multipartData = [];

                // Add asset data as form fields
                foreach ($masterAssetData as $key => $value) {
                    // Convert boolean values to string
                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    } elseif (is_array($value)) {
                        $value = json_encode($value);
                    }

                    $multipartData[] = ['name' => $key, 'contents' => $value];
                }

                // Add the image file
                $multipartData[] = [
                    'name' => 'image_file',
                    'contents' => fopen($request->file('image_file')->getPathname(), 'r'),
                    'filename' => $request->file('image_file')->getClientOriginalName()
                ];

                $options = ['multipart' => $multipartData];
                $result = $this->apiService->request('POST', '/asset-masters', $options);
            } else {
                // Standard JSON request if no file is uploaded
                $options = ['json' => $masterAssetData];
                $result = $this->apiService->request('POST', '/asset-masters', $options);
            }

            // Log the API response
            \Log::info('API response for master asset creation:', [
                'api_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during master asset creation:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (
                isset($result['error']) ||
                (isset($result['status']) && $result['status'] === false) ||
                (isset($result['success']) && $result['success'] === false)
            ) {
                \Log::warning('Error during master asset creation:', [
                    'error' => $result['error'] ?? null,
                    'status' => $result['status'] ?? null,
                    'success' => $result['success'] ?? null,
                    'message' => $result['message'] ?? $result['errors'] ?? 'Failed to create master asset'
                ]);

                // Format error message properly before passing to session
                $errorMessage = 'Failed to create master asset';

                if (isset($result['message'])) {
                    if (is_array($result['message'])) {
                        // Handle array of error messages
                        $errorMessage = '';
                        foreach ($result['message'] as $error) {
                            if (is_array($error) && isset($error['message'])) {
                                $errorMessage .= $error['message'] . '. ';
                            } else if (is_string($error)) {
                                $errorMessage .= $error . '. ';
                            }
                        }
                    } else {
                        // Handle string error message
                        $errorMessage = $result['message'];
                    }
                } else if (isset($result['errors']) && is_array($result['errors'])) {
                    // Handle errors array
                    $errorMessage = '';
                    foreach ($result['errors'] as $error) {
                        if (is_array($error) && isset($error['message'])) {
                            $errorMessage .= $error['message'] . '. ';
                        } else if (is_string($error)) {
                            $errorMessage .= $error . '. ';
                        }
                    }
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Successfully created - use the proper response format based on the example
            \Log::info('Master asset created successfully', [
                'asset_master_code' => $result['data']['asset_master_code'] ?? 'unknown',
                'asset_master_id' => $result['data']['asset_master_id'] ?? 'unknown'
            ]);

            return redirect()->route('asset-master')
                ->with('success', 'Master asset created successfully');
        } catch (\Exception $e) {
            \Log::error('Exception during master asset creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create master asset: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified master asset.
     */
    public function updateMasterAsset(Request $request, $id)
    {
        try {
            // Log request data for debugging
            \Log::info('Attempting to update master asset with data:', [
                'asset_master_id' => $id,
                'request_data' => $request->all()
            ]);

            // Prepare master asset data
            $masterAssetData = [
                'asset_master_id' => $id,
                'asset_name' => $request->input('asset_name'),
                'description' => $request->input('description'),
                'subcategory_id' => (int) $request->input('subcategory_id'),
                'brand_id' => (int) $request->input('brand_id'),
                'is_depreciable' => $request->has('is_depreciable'),
                'needs_calibration' => $request->has('needs_calibration'),
                'asset_type' => $request->input('asset_type')
            ];

            // Check if the image should be removed
            if ($request->has('remove_image')) {
                $masterAssetData['remove_image'] = true;
            }

            // Log structured data yang akan dikirim ke API
            \Log::info('Sending to API:', [
                'master_asset_data' => $masterAssetData
            ]);

            // Handle image upload if present
            if ($request->hasFile('image_file')) {
                $multipartData = [];

                // Send each field asset data individually in multipart
                foreach ($masterAssetData as $key => $value) {
                    // Convert boolean values to string for multipart
                    if (is_bool($value)) {
                        // Explicitly convert to 'true'/'false' strings
                        $value = $value ? 'true' : 'false';
                    } elseif (is_array($value)) {
                        $value = json_encode($value);
                    } elseif ($value === null) {
                        $value = ''; // Convert null to empty string
                    }

                    $multipartData[] = [
                        'name' => $key,
                        'contents' => (string)$value // Ensure all values are strings
                    ];
                }

                // Add file upload
                $multipartData[] = [
                    'name' => 'image_file',
                    'contents' => fopen($request->file('image_file')->getPathname(), 'r'),
                    'filename' => $request->file('image_file')->getClientOriginalName()
                ];

                $options = ['multipart' => $multipartData];
                $result = $this->apiService->request('PUT', "/asset-masters/{$id}", $options);
            } else {
                // No file upload, just send JSON data
                $options = ['json' => $masterAssetData];
                $result = $this->apiService->request('PUT', "/asset-masters/{$id}", $options);
            }

            // Log the API response
            \Log::info('API response for master asset update:', [
                'asset_master_id' => $id,
                'api_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during master asset update:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (
                isset($result['error']) ||
                (isset($result['status']) && $result['status'] === false) ||
                (isset($result['success']) && $result['success'] === false)
            ) {
                \Log::warning('Error during master asset update:', [
                    'error' => $result['error'] ?? null,
                    'status' => $result['status'] ?? null,
                    'success' => $result['success'] ?? null,
                    'message' => $result['message'] ?? 'Failed to update master asset',
                    'errors' => $result['errors'] ?? []
                ]);

                // Format error message properly before passing to session
                $errorMessage = 'Failed to update master asset';

                if (isset($result['message'])) {
                    if (is_array($result['message'])) {
                        // Handle array of error messages
                        $errorMessage = '';
                        foreach ($result['message'] as $error) {
                            if (is_array($error) && isset($error['message'])) {
                                $errorMessage .= $error['message'] . '. ';
                            } else if (is_string($error)) {
                                $errorMessage .= $error . '. ';
                            }
                        }
                    } else {
                        // Handle string error message
                        $errorMessage = $result['message'];
                    }
                } else if (isset($result['errors']) && is_array($result['errors'])) {
                    // Handle errors array
                    $errorMessage = '';
                    foreach ($result['errors'] as $error) {
                        if (is_array($error) && isset($error['message'])) {
                            $errorMessage .= $error['message'] . '. ';
                        } else if (is_string($error)) {
                            $errorMessage .= $error . '. ';
                        }
                    }
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Successfully updated
            \Log::info('Master asset updated successfully', ['asset_master_id' => $id]);
            return redirect()->route('asset-master')
                ->with('success', 'Master asset updated successfully');
        } catch (\Exception $e) {
            \Log::error('Exception during master asset update:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_master_id' => $id
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update master asset: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified master asset.
     */
    public function destroyMasterAsset($id)
    {
        try {
            \Log::info('Attempting to delete master asset:', [
                'asset_master_id' => $id,
                'url' => request()->url()
            ]);

            $result = $this->apiService->request('DELETE', "/asset-masters/{$id}");

            // Log the API response
            \Log::info('API response for master asset deletion:', [
                'asset_master_id' => $id,
                'api_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during master asset deletion:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (
                isset($result['error']) ||
                (isset($result['status']) && $result['status'] === false) ||
                (isset($result['success']) && $result['success'] === false)
            ) {
                \Log::warning('Error during master asset deletion:', [
                    'error' => $result['error'] ?? null,
                    'status' => $result['status'] ?? null,
                    'success' => $result['success'] ?? null,
                    'message' => $result['message'] ?? 'Failed to delete master asset'
                ]);
                return redirect()->back()
                    ->with('error', $result['message'] ?? 'Failed to delete master asset');
            }

            // Successfully deleted
            \Log::info('Master asset deleted successfully', ['asset_master_id' => $id]);
            return redirect()->route('asset-master')
                ->with('success', 'Master asset deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Exception during master asset deletion:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_master_id' => $id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete master asset: ' . $e->getMessage());
        }
    }

    /**
     * Get a single master asset for editing.
     */
    public function getMasterAsset($id)
    {
        try {
            // Log request info
            \Log::info('Fetching single master asset with ID:', [
                'asset_master_id' => $id,
                'request_url' => request()->fullUrl(),
                'ajax' => request()->ajax() ? 'Yes' : 'No'
            ]);

            // Fetch the master asset with the given ID
            $result = $this->apiService->request('GET', "/asset-masters/{$id}");

            // Log API response for debugging
            \Log::info('API response for single master asset:', [
                'api_response_status' => $result['status'] ?? $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'asset_master_id' => $id
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during master asset retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                if (request()->ajax()) {
                    return response()->json(['error' => $result['message'] ?? 'Authentication failed'], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors based on status flag
            if (
                (!isset($result['status']) && !isset($result['success'])) ||
                (isset($result['status']) && $result['status'] !== true) ||
                (isset($result['success']) && $result['success'] !== true)
            ) {
                $errorMessage = $result['message'] ?? 'Failed to retrieve master asset';

                \Log::warning('Error during master asset retrieval:', [
                    'status' => $result['status'] ?? $result['success'] ?? false,
                    'message' => $errorMessage
                ]);

                if (request()->ajax()) {
                    return response()->json(['error' => $errorMessage], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            $masterAsset = $result['data'] ?? null;

            if (!$masterAsset) {
                $errorMessage = 'Master asset not found or response data is invalid';

                if (request()->ajax()) {
                    return response()->json(['error' => $errorMessage], 404);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // For AJAX requests, also fetch subcategories and brands
            if (request()->ajax()) {
                // Fetch subcategories
                $subcategoriesResult = $this->apiService->request('GET', '/asset-subcategories');
                $subcategories = $subcategoriesResult['data'] ?? [];

                // Fetch brands
                $brandsResult = $this->apiService->request('GET', '/brands', [
                    'query' => [
                        'sort_by' => 'brand_id',
                        'sort_order' => 'asc'
                    ]
                ]);
                $brands = $brandsResult['data'] ?? [];

                // Return complete data set for the modal
                return response()->json([
                    'masterAsset' => $masterAsset,
                    'subcategories' => $subcategories,
                    'brands' => $brands
                ]);
            }

            // Return full view with master asset data for non-AJAX requests
            return view('Asset.EditMasterAsset', ['masterAsset' => $masterAsset]);
        } catch (\Exception $e) {
            $errorMessage = 'Failed to retrieve master asset: ' . $e->getMessage();

            \Log::error('Exception during master asset retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_master_id' => $id
            ]);

            if (request()->ajax()) {
                return response()->json(['error' => $errorMessage], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Get master asset data for AJAX requests.
     */
    public function getMasterAssetData(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');

            // Log request info
            \Log::info('Fetching master assets with parameters for AJAX:', [
                'page' => $page,
                'limit' => $limit,
                'search' => $search,
                'request_url' => $request->fullUrl()
            ]);

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
                'sort_by' => 'asset_master_id',
                'sort_order' => 'desc'
            ];

            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Fetch master assets
            $masterAssetsResult = $this->apiService->request('GET', '/asset-masters', [
                'query' => $queryParams
            ]);

            // Fetch subcategories for additional info
            $subcategoriesResult = $this->apiService->request('GET', '/asset-subcategories');

            // Log API responses for debugging
            \Log::info('API response for master assets AJAX list:', [
                'assets_status' => $masterAssetsResult['status'] ?? $masterAssetsResult['success'] ?? null,
                'assets_count' => isset($masterAssetsResult['data']) ? count($masterAssetsResult['data']) : 0
            ]);

            // Check for auth errors
            if (isset($masterAssetsResult['error']) && in_array($masterAssetsResult['error'], ['auth_failed', 'session_expired'])) {
                return response()->json([
                    'error' => $masterAssetsResult['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Process master assets and add subcategory info
            $masterAssets = $masterAssetsResult['data'] ?? [];
            $subcategories = $subcategoriesResult['data'] ?? [];

            // Map subcategories by ID for quick lookup
            $subcategoryMap = [];
            foreach ($subcategories as $subcategory) {
                $subcategoryMap[$subcategory['subcategory_id']] = $subcategory;
            }

            // Add subcategory data to each master asset if needed
            foreach ($masterAssets as &$masterAsset) {
                if (isset($masterAsset['subcategory_id']) && !isset($masterAsset['subcategory_name']) && isset($subcategoryMap[$masterAsset['subcategory_id']])) {
                    $masterAsset['subcategory_name'] = $subcategoryMap[$masterAsset['subcategory_id']]['subcategory_name'];
                    $masterAsset['asset_type'] = $subcategoryMap[$masterAsset['subcategory_id']]['asset_type'];
                }
            }

            // Format pagination
            $masterAssetsPagination = null;
            if (isset($masterAssetsResult['pagination'])) {
                $pagination = $masterAssetsResult['pagination'];
                $masterAssetsPagination = [
                    'current_page' => $pagination['current_page'] ?? 1,
                    'last_page' => $pagination['total_pages'] ?? ceil(($pagination['total_items'] ?? 0) / ($pagination['limit'] ?? 10)),
                    'from' => (($pagination['current_page'] ?? 1) - 1) * ($pagination['limit'] ?? 10) + 1,
                    'to' => min(($pagination['current_page'] ?? 1) * ($pagination['limit'] ?? 10), $pagination['total_items'] ?? 0),
                    'total' => $pagination['total_items'] ?? 0,
                    'per_page' => $pagination['limit'] ?? 10,
                    'next_page_url' => ($pagination['has_next'] ?? false) ? url()->current() . '?page=' . ($pagination['current_page'] + 1) : null,
                    'prev_page_url' => ($pagination['has_prev'] ?? false) ? url()->current() . '?page=' . ($pagination['current_page'] - 1) : null,
                ];
            }

            // Return JSON response
            return response()->json([
                'masterAssets' => $masterAssets,
                'masterAssets_pagination' => $masterAssetsPagination
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during master asset data retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to fetch master assets: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import master assets from Excel data.
     */
    public function importMasterAsset(Request $request)
    {
        try {
            \Log::info('Attempting to import master assets from Excel', [
                'has_file' => $request->hasFile('excel_file_upload'),
                'has_excel_data' => $request->has('excel_data')
            ]);

            if ($request->hasFile('excel_file_upload')) {
                // Use multipart form data to send the actual file
                $multipartData = [];

                // Add the Excel file
                $multipartData[] = [
                    'name' => 'excel_file',
                    'contents' => fopen($request->file('excel_file_upload')->getPathname(), 'r'),
                    'filename' => $request->file('excel_file_upload')->getClientOriginalName()
                ];

                // Send the actual file to API
                $result = $this->apiService->request('POST', '/asset-masters/import', [
                    'multipart' => $multipartData
                ]);
            } else if ($request->has('excel_data')) {
                // Fallback to the previous method if no file but has parsed data
                // Get the JSON data from the form
                $excelData = $request->input('excel_data');

                if (empty($excelData)) {
                    return redirect()->back()->with('error', 'No valid data found for import');
                }

                // Decode the JSON data
                $parsedData = json_decode($excelData, true);

                if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsedData) || empty($parsedData)) {
                    return redirect()->back()->with('error', 'Invalid data format for import');
                }

                \Log::info('Parsed Excel data for import', [
                    'record_count' => count($parsedData)
                ]);

                // Send data to API
                $result = $this->apiService->request('POST', '/asset-masters/import', [
                    'json' => [
                        'data' => $parsedData
                    ]
                ]);
            } else {
                return redirect()->back()->with('error', 'No Excel file or data provided');
            }

            // Log the API response
            \Log::info('API response for master asset import:', [
                'api_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during master asset import:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (
                isset($result['error']) ||
                (isset($result['success']) && $result['success'] === false)
            ) {
                \Log::warning('Error during master asset import:', [
                    'error' => $result['error'] ?? null,
                    'success' => $result['success'] ?? null,
                    'message' => $result['message'] ?? 'Failed to import master assets'
                ]);

                // Format error message if available
                $errorMessage = $result['message'] ?? 'Failed to import master assets';

                return redirect()->back()->with('error', $errorMessage);
            }

            // Extract import results
            $totalImported = $result['data']['total'] ?? 0;
            $successCount = $result['data']['success'] ?? 0;
            $failedCount = $result['data']['failed'] ?? 0;

            // Prepare success message
            $successMessage = "Successfully imported {$successCount} master assets";
            if ($failedCount > 0) {
                $successMessage .= " ({$failedCount} failed)";
            }

            // Redirect back with success message
            return redirect()->route('asset-master')->with('success', $successMessage);
        } catch (\Exception $e) {
            \Log::error('Exception during master asset import:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to import master assets: ' . $e->getMessage());
        }
    }
}
