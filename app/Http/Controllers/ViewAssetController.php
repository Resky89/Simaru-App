<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class ViewAssetController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display a listing of both assets and brands.
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');

            // Log request info
            \Log::info('Fetching assets and brands with parameters:', [
                'page' => $page,
                'limit' => $limit,
                'search' => $search,
                'request_url' => $request->fullUrl()
            ]);

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
                'sort_by' => 'asset_id',
                'sort_order' => 'asc'
            ];

            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Fetch assets
            $assetsResult = $this->apiService->request('GET', '/assets', [
                'query' => $queryParams
            ]);

            // Fetch subcategories which contain asset type information
            $subcategoriesResult = $this->apiService->request('GET', '/asset-subcategories');

            // Fetch rooms for the room dropdown
            $roomsResult = $this->apiService->request('GET', '/rooms');

            // Fetch brands for brand dropdown with separate pagination
            $brandsResult = $this->apiService->request('GET', '/brands', [
                'query' => [
                    'page' => $request->input('brand_page', 1),
                    'limit' => $request->input('brand_limit', 10),
                    'sort_by' => 'brand_id',
                    'sort_order' => 'asc'
                ]
            ]);

            // Log API responses for debugging
            \Log::info('API response for assets and brands list:', [
                'brands_status' => $brandsResult['status'] ?? null,
                'brands_count' => isset($brandsResult['data']) ? count($brandsResult['data']) : 0,
                'assets_status' => $assetsResult['status'] ?? null,
                'assets_count' => isset($assetsResult['data']) ? count($assetsResult['data']) : 0
            ]);

            // Check for auth errors
            if ((isset($brandsResult['error']) && in_array($brandsResult['error'], ['auth_failed', 'session_expired'])) ||
                (isset($assetsResult['error']) && in_array($assetsResult['error'], ['auth_failed', 'session_expired']))) {
                $errorMessage = $brandsResult['message'] ?? $assetsResult['message'] ?? 'Authentication failed';
                return redirect()->route('login')->with('error', $errorMessage);
            }

            // Check for API errors based on status flag
            if ((!isset($assetsResult['status']) || $assetsResult['status'] !== true) ||
                (!isset($brandsResult['status']) || $brandsResult['status'] !== true)) {
                $errorMessage = $assetsResult['message'] ?? $brandsResult['message'] ?? 'Failed to fetch data';

                \Log::warning('Error during data retrieval:', [
                    'assets_status' => $assetsResult['status'] ?? false,
                    'brands_status' => $brandsResult['status'] ?? false,
                    'message' => $errorMessage
                ]);

                return view('Asset.ViewAsset', [
                    'assets' => [],
                    'brands' => [],
                    'assets_pagination' => null,
                    'brands_pagination' => null,
                    'subcategories' => [],
                    'assetTypes' => [],
                    'rooms' => [],
                    'error' => $errorMessage
                ]);
            }

            // Parse subcategories data
            $subcategories = $subcategoriesResult['data'] ?? [];

            // Extract asset types from subcategories
            $assetTypes = [];
            foreach ($subcategories as $subcategory) {
                if (isset($subcategory['asset_type']) && !in_array($subcategory['asset_type'], $assetTypes)) {
                    $assetTypes[] = $subcategory['asset_type'];
                }
            }

            // Parse other data
            $rooms = $roomsResult['data'] ?? [];
            $brands = $brandsResult['data'] ?? [];
            $assets = $assetsResult['data'] ?? [];

            // Use either pagination structure, prioritizing the assets one
            $pagination = $assetsResult['pagination'] ?? $brandsResult['pagination'] ?? null;

            // Setelah mendapatkan data assets dari API
            foreach ($assets as &$asset) {
                // Pastikan semua properti yang diperlukan ada
                if (!isset($asset['room'])) {
                    $asset['room'] = [];
                }

                if (!isset($asset['room']['building'])) {
                    $asset['room']['building'] = ['building_name' => '-'];
                }

                // Tambahkan default values untuk properti lain yang mungkin missing
                if (!isset($asset['room']['room_name'])) {
                    $asset['room']['room_name'] = '-';
                }
            }

            // Transform the rooms data to include building_name at root level for compatibility
            $transformedRooms = [];
            foreach ($rooms as $room) {
                $room['building_name'] = $room['building']['building_name'] ?? '-';
                $transformedRooms[] = $room;
            }

            // Format pagination for assets
            $assetsPagination = null;
            if (isset($assetsResult['pagination'])) {
                $pagination = $assetsResult['pagination'];
                $assetsPagination = [
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
                    'next_page_url' => ($pagination['has_next'] ?? false) ? url()->current() . '?brand_page=' . ($pagination['current_page'] + 1) : null,
                    'prev_page_url' => ($pagination['has_prev'] ?? false) ? url()->current() . '?brand_page=' . ($pagination['current_page'] - 1) : null,
                ];
            }

            return view('Asset.ViewAsset', [
                'assets' => $assets,
                'brands' => $brands,
                'assets_pagination' => $assetsPagination,
                'brands_pagination' => $brandsPagination,
                'subcategories' => $subcategories,
                'assetTypes' => $assetTypes,
                'rooms' => $transformedRooms
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during data retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('Asset.ViewAsset', [
                'assets' => [],
                'brands' => [],
                'assets_pagination' => null,
                'brands_pagination' => null,
                'subcategories' => [],
                'assetTypes' => [],
                'rooms' => [],
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
            return redirect()->route('assets')
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
            return redirect()->route('assets')
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
            return redirect()->route('assets')
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
     * Store a newly created asset.
     */
    public function storeAsset(Request $request)
    {
        try {
            // Log the request data
            \Log::info('Attempting to create asset with data:', [
                'request_data' => $request->all()
            ]);

            // Prepare asset data
            $assetData = [
                'asset_name' => $request->input('asset_name'),
                'description' => $request->input('description'),
                'subcategory_id' => (int)$request->input('subcategory_id'),
                'model_number' => $request->input('model_number'),
                'serial_number' => $request->input('serial_number'),
                'purchase_date' => $request->input('purchase_date'),
                'purchase_cost' => (float)$request->input('purchase_cost'),
                'warranty_end_date' => $request->input('warranty_end_date'),
                'current_status' => $request->input('current_status', 'available'),
                'condition' => $request->input('condition', 'good'),
                'room_id' => (int)$request->input('room_id'),
                'brand_id' => (int)$request->input('brand_id'),
                'is_depreciable' => (bool) $request->input('is_depreciable')
            ];

            // Add depreciation data if asset is depreciable
            if ($request->has('is_depreciable')) {
                $assetData['depreciation'] = [
                    'depreciation_method' => $request->input('depreciation_method'),
                    'acquisition_cost' => (float)$request->input('acquisition_cost'),
                    'salvage_value' => (float)$request->input('salvage_value'),
                    'asset_life_months' => (int)$request->input('asset_life_months'),
                    'date_acquired' => $request->input('date_acquired')
                ];
            }

            // Handle image upload if present
            if ($request->hasFile('image_file')) {
                $multipartData = [];

                // Add asset data as form fields
                foreach ($assetData as $key => $value) {
                    if ($key === 'depreciation' && is_array($value)) {
                        foreach ($value as $depKey => $depValue) {
                            $multipartData[] = [
                                'name' => "depreciation[{$depKey}]",
                                'contents' => $depValue
                            ];
                        }
                    } else {
                        $multipartData[] = ['name' => $key, 'contents' => $value];
                    }
                }

                // Add the image file
                $multipartData[] = [
                    'name' => 'image_file',
                    'contents' => fopen($request->file('image_file')->getPathname(), 'r'),
                    'filename' => $request->file('image_file')->getClientOriginalName()
                ];

                $options = ['multipart' => $multipartData];
                $result = $this->apiService->request('POST', '/assets', $options);
            } else {
                // Standard JSON request if no file is uploaded
                $options = ['json' => $assetData];
                $result = $this->apiService->request('POST', '/assets', $options);
            }

            // Log the API response
            \Log::info('API response for asset creation:', [
                'api_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during asset creation:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (isset($result['error']) || (isset($result['status']) && $result['status'] === false) || (isset($result['success']) && $result['success'] === false)) {
                \Log::warning('Error during asset creation:', [
                    'error' => $result['error'] ?? null,
                    'status' => $result['status'] ?? null,
                    'success' => $result['success'] ?? null,
                    'message' => $result['message'] ?? $result['errors'] ?? 'Failed to create asset'
                ]);

                // Format error message properly before passing to session
                $errorMessage = 'Failed to create asset';

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

            // Successfully created
            \Log::info('Asset created successfully', [
                'asset_code' => $result['data']['asset_code'] ?? 'unknown'
            ]);
            return redirect()->route('assets')
                ->with('success', 'Asset created successfully');
        } catch (\Exception $e) {
            \Log::error('Exception during asset creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create asset: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified asset.
     */
    public function updateAsset(Request $request, $id)
    {
        try {
            // Log request data untuk debugging
            \Log::info('Attempting to update asset with data:', [
                'asset_id' => $id,
                'request_data' => $request->all()
            ]);

            // Prepare asset data - struktur yang lebih sederhana
            $assetData = [
                'asset_id' => $id,
                'asset_name' => $request->input('asset_name'),
                'description' => $request->input('description'),
                'subcategory_id' => (int)$request->input('subcategory_id'),
                'model_number' => $request->input('model_number'),
                'serial_number' => $request->input('serial_number'),
                'purchase_date' => $request->input('purchase_date'),
                'purchase_cost' => (float)$request->input('purchase_cost'),
                'warranty_end_date' => $request->input('warranty_end_date'),
                'current_status' => $request->input('current_status', 'available'),
                'condition' => $request->input('condition'),
                'room_id' => (int)$request->input('room_id'),
                'brand_id' => (int)$request->input('brand_id'),
                'is_depreciable' => (bool)($request->input('is_depreciable') === '1')
            ];

            // Jika asset depreciable, tambahkan field depreciation langsung ke root object
            // ini berbeda dari struktur sebelumnya yang nested
            if ($request->input('is_depreciable') === '1') {
                $assetData['depreciation_method'] = $request->input('depreciation_method');
                $assetData['acquisition_cost'] = (float)$request->input('acquisition_cost');
                $assetData['salvage_value'] = (float)$request->input('salvage_value');
                $assetData['asset_life_months'] = (int)$request->input('asset_life_months');
                $assetData['date_acquired'] = $request->input('date_acquired');
            }

            // Log structured data yang akan dikirim ke API
            \Log::info('Sending to API:', [
                'asset_data' => $assetData
            ]);

            // Handle image upload if present
            if ($request->hasFile('image_file')) {
                $multipartData = [];

                // Kirim setiap field asset data secara individual dalam multipart
                foreach ($assetData as $key => $value) {
                    $multipartData[] = [
                        'name' => $key,
                        'contents' => is_array($value) ? json_encode($value) : $value
                    ];
                }

                // Add file upload
                $multipartData[] = [
                    'name' => 'image_file',
                    'contents' => fopen($request->file('image_file')->getPathname(), 'r'),
                    'filename' => $request->file('image_file')->getClientOriginalName()
                ];

                $options = ['multipart' => $multipartData];
                $result = $this->apiService->request('PUT', "/assets/{$id}", $options);
            } else {
                // No file upload, just send JSON data
                $options = ['json' => $assetData];  // Kirim data langsung tanpa asset_data wrapper
                $result = $this->apiService->request('PUT', "/assets/{$id}", $options);
            }

            // Log the API response
            \Log::info('API response for asset update:', [
                'asset_id' => $id,
                'api_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during asset update:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // PERBAIKAN: Check for other API errors or unsuccessful responses
            if (isset($result['error']) ||
                (isset($result['status']) && $result['status'] === false) ||
                (isset($result['success']) && $result['success'] === false)) {

                \Log::warning('Error during asset update:', [
                    'error' => $result['error'] ?? null,
                    'status' => $result['status'] ?? null,
                    'success' => $result['success'] ?? null,
                    'message' => $result['message'] ?? 'Failed to update asset',
                    'errors' => $result['errors'] ?? []
                ]);

                // Format error message properly before passing to session
                $errorMessage = 'Failed to update asset';

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
            \Log::info('Asset updated successfully', ['asset_id' => $id]);
            return redirect()->route('assets')
                ->with('success', 'Asset updated successfully');
        } catch (\Exception $e) {
            \Log::error('Exception during asset update:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $id
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update asset: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified asset.
     */
    public function destroyAsset($id)
    {
        try {
            \Log::info('Attempting to delete asset:', [
                'asset_id' => $id,
                'url' => request()->url()
            ]);

            $result = $this->apiService->request('DELETE', "/assets/{$id}");

            // Log the API response
            \Log::info('API response for asset deletion:', [
                'asset_id' => $id,
                'api_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during asset deletion:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (isset($result['error']) || (isset($result['status']) && $result['status'] === false)) {
                \Log::warning('Error during asset deletion:', [
                    'error' => $result['error'] ?? null,
                    'status' => $result['status'] ?? null,
                    'message' => $result['message'] ?? 'Failed to delete asset'
                ]);
                return redirect()->back()
                    ->with('error', $result['message'] ?? 'Failed to delete asset');
            }

            // Successfully deleted
            \Log::info('Asset deleted successfully', ['asset_id' => $id]);
            return redirect()->route('assets')
                ->with('success', 'Asset deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Exception during asset deletion:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete asset: ' . $e->getMessage());
        }
    }

    /**
     * Get a single asset for editing.
     */
    public function getAsset($id)
    {
        try {
            // Log request info
            \Log::info('Fetching single asset with ID:', [
                'asset_id' => $id,
                'request_url' => request()->fullUrl(),
                'ajax' => request()->ajax() ? 'Yes' : 'No'
            ]);

            // Fetch the asset with the given ID
            $result = $this->apiService->request('GET', "/assets/{$id}");

            // Log API response for debugging
            \Log::info('API response for single asset:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'asset_id' => $id
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during asset retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                if (request()->ajax()) {
                    return response()->json(['error' => $result['message'] ?? 'Authentication failed'], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors based on status flag
            if (!isset($result['status']) || $result['status'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to retrieve asset';

                \Log::warning('Error during asset retrieval:', [
                    'status' => $result['status'] ?? false,
                    'message' => $errorMessage
                ]);

                if (request()->ajax()) {
                    return response()->json(['error' => $errorMessage], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            $asset = $result['data'] ?? null;

            if (!$asset) {
                $errorMessage = 'Asset not found or response data is invalid';

                if (request()->ajax()) {
                    return response()->json(['error' => $errorMessage], 404);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // For AJAX requests, also fetch subcategories, rooms, and brands
            if (request()->ajax()) {
                // Fetch subcategories
                $subcategoriesResult = $this->apiService->request('GET', '/asset-subcategories');
                $subcategories = $subcategoriesResult['data'] ?? [];

                // Fetch rooms
                $roomsResult = $this->apiService->request('GET', '/rooms');
                $rooms = $roomsResult['data'] ?? [];

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
                    'asset' => $asset,
                    'subcategories' => $subcategories,
                    'rooms' => $rooms,
                    'brands' => $brands
                ]);
            }

            // Return full view with asset data for non-AJAX requests
            return view('Asset.EditAsset', ['asset' => $asset]);
        } catch (\Exception $e) {
            $errorMessage = 'Failed to retrieve asset: ' . $e->getMessage();

            \Log::error('Exception during asset retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $id
            ]);

            if (request()->ajax()) {
                return response()->json(['error' => $errorMessage], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Generate barcode for the specified asset(s).
     *
     * @param int|string $id The asset ID or 'batch' for multiple assets
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function generateBarcode($id)
    {
        try {
            \Log::info('Attempting to generate barcode for asset:', [
                'asset_id' => $id,
                'url' => request()->url()
            ]);

            // Handle batch request
            if ($id === 'batch' && request()->has('ids')) {
                $assetIds = request('ids');
                $batchResults = [];

                foreach ($assetIds as $assetId) {
                    $result = $this->apiService->request('GET', "/assets/barcode/generate/{$assetId}");
                    if (isset($result['status']) && $result['status'] === true) {
                        $batchResults[] = $result['data'] ?? [];
                    }
                }

                if (request()->ajax()) {
                    return response()->json([
                        'status' => true,
                        'message' => count($batchResults) . ' barcodes generated successfully',
                        'data' => $batchResults
                    ]);
                }

                return redirect()->back()->with('success', count($batchResults) . ' barcodes generated successfully');
            }

            // Single asset request
            $result = $this->apiService->request('GET', "/assets/barcode/generate/{$id}");

            // Log the API response
            \Log::info('API response for barcode generation:', [
                'asset_id' => $id,
                'api_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during barcode generation:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                if (request()->ajax()) {
                    return response()->json([
                        'status' => false,
                        'message' => $result['message'] ?? 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors
            if (isset($result['error']) || (isset($result['status']) && $result['status'] === false)) {
                $errorMessage = $result['message'] ?? 'Failed to generate barcode';

                \Log::warning('Error during barcode generation:', [
                    'error' => $result['error'] ?? null,
                    'status' => $result['status'] ?? null,
                    'message' => $errorMessage
                ]);

                if (request()->ajax()) {
                    return response()->json([
                        'status' => false,
                        'message' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Successfully generated barcode
            \Log::info('Barcode generated successfully', ['asset_id' => $id]);

            if (request()->ajax()) {
                return response()->json($result);
            }

            return redirect()->back()->with('success', 'Barcode generated successfully');
        } catch (\Exception $e) {
            $errorMessage = 'Failed to generate barcode: ' . $e->getMessage();

            \Log::error('Exception during barcode generation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $id
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }
}
