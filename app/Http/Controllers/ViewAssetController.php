<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class ViewAssetController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display a listing of assets.
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');

            // Log request info
            \Log::info('Fetching assets with parameters:', [
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
            \Log::info('API response for assets list:', [
                'assets_status' => $assetsResult['status'] ?? null,
                'assets_count' => isset($assetsResult['data']) ? count($assetsResult['data']) : 0,
                'brands_status' => $brandsResult['status'] ?? null,
                'brands_count' => isset($brandsResult['data']) ? count($brandsResult['data']) : 0
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
                // Buat salinan room data untuk menghindari referensi
                $transformedRoom = $room;

                // Cek struktur data building dengan lebih detil
                if (isset($room['building']) && is_array($room['building'])) {
                    $buildingName = $room['building']['building_name'] ?? 'Unknown Building';
                } elseif (isset($room['building_id'])) {
                    // Jika building_id ada tapi tidak ada nested building object,
                    // coba cari building dari daftar buildings
                    $buildingId = $room['building_id'];
                    $buildingName = 'Building ID: ' . $buildingId;

                    // Ambil data building dari API jika perlu
                    try {
                        $buildingResult = $this->apiService->request('GET', "/buildings/{$buildingId}");
                        if (isset($buildingResult['status']) && $buildingResult['status'] === true && isset($buildingResult['data']['building_name'])) {
                            $buildingName = $buildingResult['data']['building_name'];
                        }
                    } catch (\Exception $e) {
                        // Abaikan error saat fetch building
                    }
                } else {
                    $buildingName = 'No Building';
                }

                // Tambahkan building_name langsung ke level root
                $transformedRoom['building_name'] = $buildingName;

                $transformedRooms[] = $transformedRoom;
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
                'is_depreciable' => $request->input('is_depreciable') === '1' // Simpan sebagai true/false, bukan boolean string
            ];

            // Add depreciation data if asset is depreciable - use flat structure like updateAsset
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

                // Add asset data as form fields - perbaiki konversi tipe data
                foreach ($assetData as $key => $value) {
                    // Perbaiki konversi boolean and other types
                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false'; // Konversi boolean ke string 'true'/'false'
                    } elseif (is_array($value)) {
                        $value = json_encode($value); // Konversi array ke JSON string
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
     * Generate barcode for a single asset
     */
    public function generateBarcode($id)
    {
        try {
            \Log::info('Attempting to generate barcode for single asset:', [
                'asset_id' => $id,
                'url' => request()->url()
            ]);

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

    /**
     * Generate QR codes for multiple assets in bulk
     */
    public function generateBulkQR(Request $request)
    {
        try {
            \Log::info('Attempting to generate bulk QR codes:', [
                'request_data' => $request->all()
            ]);

            // Get asset IDs from request
            $assetIds = $request->input('asset_ids', []);

            // Ensure asset_ids is an array
            if (!is_array($assetIds)) {
                if (is_string($assetIds) && !empty($assetIds)) {
                    $assetIds = explode(',', $assetIds);
                    $assetIds = array_map('intval', array_filter($assetIds));
                } else {
                    $assetIds = [];
                }
            }

            if (empty($assetIds)) {
                \Log::warning('No assets selected for QR code generation');
                return redirect()->back()->with('error', 'No assets selected for QR code generation');
            }

            \Log::info('Calling API to generate QR codes', [
                'asset_ids' => $assetIds
            ]);

            // Call API to generate QR codes with simplified request format
            $result = $this->apiService->request('POST', "/assets/qr/generate-bulk", [
                'json' => [
                    'asset_ids' => $assetIds
                ]
            ]);

            \Log::info('API response for bulk QR generation:', [
                'status' => isset($result['status']) ? $result['status'] : 'not set',
                'message' => isset($result['message']) ? $result['message'] : 'no message',
                'data_count' => isset($result['data']) ? count($result['data']) : 0
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during QR generation:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors
            if (!isset($result['status']) || $result['status'] !== true) {
                \Log::error('API error in bulk QR generation:', [
                    'result' => $result
                ]);
                return redirect()->back()->with('error', $result['message'] ?? 'Failed to generate QR codes');
            }

            // Store QR data in session for PDF generation
            session(['qr_data' => $result['data'] ?? []]);

            \Log::info('QR codes generated successfully, redirecting to assets page');

            // Redirect back with success message
            return redirect()->route('assets')->with('success', 'QR codes generated successfully. Ready for printing.');
        } catch (\Exception $e) {
            $errorMessage = 'Failed to generate QR codes: ' . $e->getMessage();

            \Log::error('Exception during bulk QR generation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Print QR codes as PDF
     */
    public function printQRCodesPDF(Request $request)
    {
        try {
            \Log::info('Attempting to print QR codes as PDF');

            // Get QR data from session or generate new data if asset_ids are provided
            $qrData = session('qr_data', []);

            // If QR data is not in session, check if we have asset_ids in the request
            if (empty($qrData) && $request->has('asset_ids')) {
                \Log::info('No QR data in session, generating from asset_ids');

                // Parse asset IDs
                $assetIds = $request->input('asset_ids');
                if (is_string($assetIds)) {
                    // Make sure we're properly parsing the comma-separated list
                    $assetIds = array_map('trim', explode(',', $assetIds));
                    $assetIds = array_filter($assetIds); // Remove any empty items
                }

                // Ensure we've got an array of IDs (log this for debugging)
                \Log::info('Asset IDs for QR generation:', ['asset_ids' => $assetIds]);

                $qrSize = $request->input('qr_size', 50);
                $quantity = $request->input('quantity', 1);

                // Use apiService instead of direct Http facade to properly handle authentication
                $result = $this->apiService->request('POST', "/assets/qr/generate-bulk", [
                    'json' => [
                        'asset_ids' => $assetIds,
                        'qr_size' => $qrSize,
                        'quantity' => $quantity,
                    ]
                ]);

                // Log the complete API request and response for debugging
                \Log::info('QR generation API request/response:', [
                    'request' => [
                        'asset_ids' => $assetIds,
                        'qr_size' => $qrSize,
                        'quantity' => $quantity
                    ],
                    'response' => $result
                ]);

                // Check for auth errors
                if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                    \Log::warning('Authentication error during QR generation:', [
                        'error' => $result['error'],
                        'message' => $result['message'] ?? 'Authentication failed'
                    ]);
                    return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
                }

                if (isset($result['status']) && $result['status'] === true && isset($result['data'])) {
                    \Log::info('Successfully generated QR codes from API', [
                        'count' => count($result['data']),
                        'asset_ids_in_response' => array_column($result['data'], 'asset_id')
                    ]);

                    $qrData = $result['data'];
                    session(['qr_data' => $qrData]);

                    \Log::info('QR data being stored in session:', [
                        'qr_data_count' => count($result['data']),
                        'qr_data_sample' => array_slice($result['data'], 0, min(5, count($result['data'])))
                    ]);
                } else {
                    \Log::error('Failed to generate QR codes from API', [
                        'result' => $result
                    ]);
                    return redirect()->back()->with('error', $result['message'] ?? 'Failed to generate QR codes');
                }
            }

            if (empty($qrData)) {
                return redirect()->back()->with('error', 'No QR code data found for printing');
            }

            // Log what we have before processing
            \Log::info('Processing QR data for PDF:', [
                'qr_count' => count($qrData),
                'first_few_ids' => array_slice(array_column($qrData, 'asset_id'), 0, 5)
            ]);

            // Fetch and embed QR images as base64
            foreach ($qrData as $key => $asset) {
                if (isset($asset['qr_url'])) {
                    try {
                        // Get proper API URL from backend configuration
                        $backendUrl = rtrim(config('app.backend_url'), '/');
                        $imageUrl = $backendUrl . "/public" . $asset['qr_url'];

                        \Log::info("Fetching QR image for asset ID: {$asset['asset_id']}", [
                            'url' => $imageUrl
                        ]);

                        // Try to get the image content through file_get_contents first
                        $imageData = @file_get_contents($imageUrl);
                        if ($imageData !== false) {
                            $base64Image = base64_encode($imageData);
                            $qrData[$key]['qr_base64'] = 'data:image/png;base64,' . $base64Image;
                            \Log::info("Successfully fetched QR image for asset ID: {$asset['asset_id']}");
                        } else {
                            \Log::warning("Failed to download QR image: {$imageUrl}");
                            $qrData[$key]['qr_base64'] = null;
                        }
                    } catch (\Exception $e) {
                        \Log::error("Error fetching QR image: {$e->getMessage()}");
                        $qrData[$key]['qr_base64'] = null;
                    }
                }
            }

            // Generate PDF
            $pdf = Pdf::loadView('Asset.qrcode_pdf', [
                'qrData' => $qrData
            ]);

            // Set paper size and orientation
            $pdf->setPaper('a4', 'portrait');

            // Stream the PDF directly to the browser
            return $pdf->stream('asset_qrcodes.pdf');
        } catch (\Exception $e) {
            $errorMessage = 'Failed to print QR codes: ' . $e->getMessage();

            \Log::error('Exception during QR PDF printing:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', $errorMessage);
        }
    }
}
