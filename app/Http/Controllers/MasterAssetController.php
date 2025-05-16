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
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');
            $assetType = $request->input('type', '');
            $sortOrder = $request->input('sort', 'newest');

            // Log request info
            \Log::info('Fetching master assets with parameters:', [
                'page' => $page,
                'limit' => $limit,
                'search' => $search,
                'asset_type' => $assetType,
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

            // Log API responses for debugging
            \Log::info('API response for master assets list:', [
                'assets_status' => $masterAssetsResult['status'] ?? null,
                'assets_count' => isset($masterAssetsResult['data']) ? count($masterAssetsResult['data']) : 0
            ]);

            // Check for auth errors
            if (isset($masterAssetsResult['errors']) && is_string($masterAssetsResult['errors']) &&
                in_array($masterAssetsResult['errors'], ['auth_failed', 'session_expired'])) {
                $errorMessage = $masterAssetsResult['errors'] ?? 'Authentication failed';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => is_string($errorMessage) ? $errorMessage : 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($errorMessage) ? $errorMessage : 'Authentication failed');
            }

            // Check for API errors based on status flag
            if (isset($masterAssetsResult['success']) && $masterAssetsResult['success'] !== true) {
                $errorData = $masterAssetsResult['errors'] ?? 'Failed to fetch data';

                \Log::warning('Error during data retrieval:', [
                    'assets_status' => $masterAssetsResult['success'] ?? false,
                    'errors' => $errorData
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => is_array($errorData) ? implode(', ', (array)$errorData) : $errorData
                    ], 400);
                }

                return view('Asset.MasterAsset', [
                    'masterAssets' => [],
                    'masterAssets_pagination' => null,
                    'error' => is_array($errorData) ? implode(', ', (array)$errorData) : $errorData
                ]);
            }

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

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'masterAssets' => $masterAssets,
                    'pagination' => $masterAssetsPagination
                ]);
            }

            // Return view for regular requests
            return view('Asset.MasterAsset', [
                'masterAssets' => $masterAssets,
                'masterAssets_pagination' => $masterAssetsPagination
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during data retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to fetch data: ' . $e->getMessage()
                ], 500);
            }

            return view('Asset.MasterAsset', [
                'masterAssets' => [],
                'masterAssets_pagination' => null,
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

            // Prepare master asset data - only include non-empty fields
            $masterAssetData = [];

            // Only add non-empty fields to request body
            if ($request->filled('asset_name')) {
                $masterAssetData['asset_name'] = $request->input('asset_name');
            }

            if ($request->filled('description')) {
                $masterAssetData['description'] = $request->input('description');
            }

            if ($request->filled('subcategory_id')) {
                $masterAssetData['subcategory_id'] = (int) $request->input('subcategory_id');
            }

            if ($request->filled('brand_id')) {
                $masterAssetData['brand_id'] = (int) $request->input('brand_id');
            }

            // Boolean fields - only include if they have values
            if ($request->has('is_depreciable')) {
                $masterAssetData['is_depreciable'] = $request->input('is_depreciable') === 'true' || $request->input('is_depreciable') === true;
            }

            if ($request->has('needs_calibration')) {
                $masterAssetData['needs_calibration'] = $request->input('needs_calibration') === 'true' || $request->input('needs_calibration') === true;
            }

            if ($request->filled('asset_type')) {
                $masterAssetData['asset_type'] = $request->input('asset_type');
            }

            // Log structured data yang akan dikirim ke API
            \Log::info('Sending to API:', [
                'master_asset_data' => $masterAssetData
            ]);

            // Handle image upload if present
            if ($request->hasFile('image_file')) {
                $multipartData = [];

                // Add asset data as form fields - only include fields that have values
                foreach ($masterAssetData as $key => $value) {
                    // Skip empty values except for boolean fields which might be false
                    if ($value === null || ($value === '' && !is_bool($value))) {
                        continue;
                    }

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
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during master asset creation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors'] ?? 'Authentication failed') ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                \Log::warning('Error during master asset creation:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $result['errors'] ?? 'Failed to create master asset'
                ]);

                // Format error message properly before passing to session
                $errorMessage = 'Failed to create master asset';

                if (isset($result['errors'])) {
                    if (is_array($result['errors'])) {
                        // Handle array of error messages
                        $errorMessage = '';
                        foreach ($result['errors'] as $key => $error) {
                            if (is_array($error) && isset($error['message'])) {
                                $errorMessage .= $error['message'] . '. ';
                            } else if (is_string($error)) {
                                $errorMessage .= $error . '. ';
                            }
                        }
                    } else {
                        // Handle string error message
                        $errorMessage = $result['errors'];
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

            // Prepare master asset data - only include non-empty fields
            $masterAssetData = [
                'asset_master_id' => $id // ID is always required for update
            ];

            // Only add non-empty fields to request body
            if ($request->filled('asset_name')) {
                $masterAssetData['asset_name'] = $request->input('asset_name');
            }

            if ($request->filled('description')) {
                $masterAssetData['description'] = $request->input('description');
            }

            if ($request->filled('subcategory_id')) {
                $masterAssetData['subcategory_id'] = (int) $request->input('subcategory_id');
            }

            if ($request->filled('brand_id')) {
                $masterAssetData['brand_id'] = (int) $request->input('brand_id');
            }

            // Boolean fields need special handling - we need to explicitly include them
            // because their absence means they should be false
            $masterAssetData['is_depreciable'] = $request->has('is_depreciable');
            $masterAssetData['needs_calibration'] = $request->has('needs_calibration');

            if ($request->filled('asset_type')) {
                $masterAssetData['asset_type'] = $request->input('asset_type');
            }

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
                    // Skip empty values except for booleans which might be false
                    // and the asset_master_id which is required for updates
                    if ($key !== 'asset_master_id' && $value === null ||
                       ($value === '' && !is_bool($value))) {
                        continue;
                    }

                    // Convert boolean values to string for multipart
                    if (is_bool($value)) {
                        // Explicitly convert to 'true'/'false' strings
                        $value = $value ? 'true' : 'false';
                    } elseif (is_array($value)) {
                        $value = json_encode($value);
                    } elseif ($value === null) {
                        continue; // Skip null values altogether
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
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during master asset update:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors'] ?? 'Authentication failed') ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                \Log::warning('Error during master asset update:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $result['errors'] ?? 'Failed to update master asset'
                ]);

                // Format error message properly before passing to session
                $errorMessage = 'Failed to update master asset';

                if (isset($result['errors'])) {
                    if (is_array($result['errors'])) {
                        // Handle array of error messages
                        $errorMessage = '';
                        foreach ($result['errors'] as $key => $error) {
                            if (is_array($error) && isset($error['message'])) {
                                $errorMessage .= $error['message'] . '. ';
                            } else if (is_string($error)) {
                                $errorMessage .= $error . '. ';
                            }
                        }
                    } else {
                        // Handle string error message
                        $errorMessage = $result['errors'];
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
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during master asset deletion:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors'] ?? 'Authentication failed') ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                \Log::warning('Error during master asset deletion:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $result['errors'] ?? 'Failed to delete master asset'
                ]);
                return redirect()->back()
                    ->with('error', is_array($result['errors'] ?? 'Failed to delete master asset') ? implode(', ', (array)$result['errors']) : ($result['errors'] ?? 'Failed to delete master asset'));
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
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during master asset retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors'] ?? 'Authentication failed') ? $result['errors'] : 'Authentication failed');
            }

            // Check for API errors based on status flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve master asset';

                \Log::warning('Error during master asset retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], 400);
                }

                return redirect()->back()->with('error', is_array($errorData) ? implode(', ', (array)$errorData) : $errorData);
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
     * Export master assets data to PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportMasterAssetPDF(Request $request)
    {
        try {
            // Get filter parameters
            $search = $request->input('search', '');
            $assetType = $request->input('type', '');
            $brandId = $request->input('brand', '');
            $subcategoryId = $request->input('category', '');
            $sortOrder = $request->input('sort', 'newest');

            // Log request info
            \Log::info('Exporting master assets to PDF with parameters:', [
                'search' => $search,
                'asset_type' => $assetType,
                'brand_id' => $brandId,
                'subcategory_id' => $subcategoryId,
                'sort' => $sortOrder,
                'request_url' => $request->fullUrl()
            ]);

            // Build query parameters
            $queryParams = [
                'page' => 1,
                'limit' => 1000  // Get a large number for export
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

            // Fetch master assets for PDF
            $masterAssetsResult = $this->apiService->request('GET', '/asset-masters', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($masterAssetsResult['errors']) && is_string($masterAssetsResult['errors']) &&
                in_array($masterAssetsResult['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during master assets export:', [
                    'errors' => $masterAssetsResult['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($masterAssetsResult['errors'] ?? 'Authentication failed') ? $masterAssetsResult['errors'] : 'Authentication failed');
            }

            // Check for API errors based on status flag
            if (!isset($masterAssetsResult['success']) || $masterAssetsResult['success'] !== true) {
                $errorData = $masterAssetsResult['errors'] ?? 'Failed to fetch master assets data';
                \Log::warning('Error during master assets export:', [
                    'errors' => $errorData
                ]);
                return redirect()->back()->with('error', is_array($errorData) ? implode(', ', (array)$errorData) : $errorData);
            }

            // Get master assets data
            $masterAssets = $masterAssetsResult['data'] ?? [];

            // Fetch brands and subcategories data for reference
            $brandsResult = $this->apiService->request('GET', '/brands', [
                'query' => [
                    'page' => 1,
                    'limit' => 1000
                ]
            ]);
            $brands = $brandsResult['data'] ?? [];

            $subcategoriesResult = $this->apiService->request('GET', '/asset-subcategories', [
                'query' => [
                    'page' => 1,
                    'limit' => 1000
                ]
            ]);
            $subcategories = $subcategoriesResult['data'] ?? [];

            // Create lookup arrays for brands and subcategories for easier reference
            $brandMap = [];
            foreach ($brands as $brand) {
                $brandMap[$brand['brand_id']] = $brand;
            }

            $subcategoryMap = [];
            foreach ($subcategories as $subcategory) {
                $subcategoryMap[$subcategory['subcategory_id']] = $subcategory;
            }

            // Generate PDF
            $pdf = Pdf::loadView('Asset.MasterAssetPDF', [
                'masterAssets' => $masterAssets,
                'brandMap' => $brandMap,
                'subcategoryMap' => $subcategoryMap,
                'search' => $search,
                'assetType' => $assetType,
                'brandId' => $brandId,
                'subcategoryId' => $subcategoryId,
                'sort' => $sortOrder,
                'date_generated' => now()->format('d M Y H:i:s')
            ]);

            // Log PDF generation
            \Log::info('Master assets PDF generated successfully', [
                'assets_count' => count($masterAssets)
            ]);

            // Stream the PDF to browser
            return $pdf->stream('master_assets_report_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Exception during master assets PDF export:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to export Master Assets as PDF: ' . $e->getMessage());
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
                'has_excel_data' => $request->has('excel_data'),
                'is_ajax' => $request->ajax()
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
                    if ($request->ajax()) {
                        return response()->json(['success' => false, 'errors' => ['import' => 'No valid data found for import']], 400);
                    }
                    return redirect()->back()->with('error', 'No valid data found for import');
                }

                // Decode the JSON data
                $parsedData = json_decode($excelData, true);

                if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsedData) || empty($parsedData)) {
                    if ($request->ajax()) {
                        return response()->json(['success' => false, 'errors' => ['import' => 'Invalid data format for import']], 400);
                    }
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
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'errors' => ['import' => 'No Excel file or data provided']], 400);
                }
                return redirect()->back()->with('error', 'No Excel file or data provided');
            }

            // Log the API response
            \Log::info('API response for master asset import:', [
                'api_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during master asset import:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], 401);
                }
                return redirect()->route('login')->with('error', is_string($result['errors'] ?? 'Authentication failed') ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Failed to import master assets';

                \Log::warning('Error during master asset import:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                // Format error message including detailed errors from the response
                if (is_array($errorData)) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'errors' => $errorData
                        ], 400);
                    }

                    // For non-AJAX, format as HTML
                    $errorMessage = 'Failed to import master assets: ';
                    $errorMessage .= "<ul class='list-disc pl-4 mt-2'>";
                    foreach ($errorData as $key => $detail) {
                        if (is_array($detail)) {
                            $errorMessage .= "<li>" . implode(', ', $detail) . "</li>";
                        } else {
                            $errorMessage .= "<li>{$detail}</li>";
                        }
                    }
                    $errorMessage .= "</ul>";
                } else {
                    $errorMessage = $errorData;

                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'errors' => ['general' => $errorMessage]
                        ], 400);
                    }
                }

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

            // Return response based on request type
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => [
                        'total' => $totalImported,
                        'success' => $successCount,
                        'failed' => $failedCount
                    ]
                ]);
            }

            // Redirect back with success message for non-AJAX requests
            return redirect()->route('asset-master')->with('success', $successMessage);
        } catch (\Exception $e) {
            \Log::error('Exception during master asset import:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Failed to import master assets: ' . $e->getMessage()]
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to import master assets: ' . $e->getMessage());
        }
    }
}
