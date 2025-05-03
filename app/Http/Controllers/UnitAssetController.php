<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;

class UnitAssetController extends Controller
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
            $statusFilter = $request->input('current_status', '');
            $typeFilter = $request->input('type', '');
            $sortOrder = $request->input('sort', '');

            // Log request info
            \Log::info('Fetching assets with parameters:', [
                'page' => $page,
                'limit' => $limit,
                'search' => $search,
                'current_status' => $statusFilter,
                'type' => $typeFilter,
                'sort' => $sortOrder,
                'request_url' => $request->fullUrl()
            ]);

            // Build query parameters
            $query = [
                'page' => $page,
                'limit' => $limit,
            ];

            // Set sort parameters based on user selection
            if (!empty($sortOrder)) {
                switch ($sortOrder) {
                    case 'newest':
                        $query['sort_by'] = 'created_at';
                        $query['sort_order'] = 'desc';
                        break;
                    case 'oldest':
                        $query['sort_by'] = 'created_at';
                        $query['sort_order'] = 'asc';
                        break;
                    case 'name_asc':
                        // Try using the frontend sort parameter directly
                        $query['sort'] = 'name_asc';

                        // Remove standard sort params that might interfere
                        unset($query['sort_by']);
                        unset($query['sort_order']);
                        break;
                    case 'name_desc':
                        // Try using the frontend sort parameter directly
                        $query['sort'] = 'name_desc';

                        // Remove standard sort params that might interfere
                        unset($query['sort_by']);
                        unset($query['sort_order']);
                        break;
                    default:
                        $query['sort_by'] = 'asset_id';
                        $query['sort_order'] = 'desc';
                }
            } else {
                $query['sort_by'] = 'asset_id';
                $query['sort_order'] = 'desc';
            }

            // Add search filter if provided
            if (!empty($search)) {
                $query['search'] = $search;
            }

            // Add status filter if provided
            if (!empty($statusFilter)) {
                $query['current_status'] = $statusFilter;
            }

            // Add asset type filter if provided
            if (!empty($typeFilter)) {
                $query['type'] = $typeFilter;
            }

            // Fetch assets
            \Log::debug('Sending API request to /assets with query parameters:', [
                'query' => $query,
                'sort_by' => $query['sort_by'] ?? 'none',
                'sort_order' => $query['sort_order'] ?? 'none'
            ]);

            $assetsResult = $this->apiService->request('GET', '/assets', [
                'query' => $query
            ]);

            // Comprehensive debug logging of the API response
            \Log::debug('Complete API response for assets', [
                'sortOrder' => $sortOrder,
                'query_params' => $query,
                'success' => $assetsResult['success'] ?? false,
                'error' => $assetsResult['error'] ?? null,
                'message' => $assetsResult['message'] ?? null,
                'data_count' => isset($assetsResult['data']) ? count($assetsResult['data']) : 0,
                'pagination' => $assetsResult['pagination'] ?? null,
                'request_url' => $request->fullUrl()
            ]);

            // If we have data, log a sample for debugging
            if (!empty($assetsResult['data'])) {
                \Log::debug('Sample data from response', [
                    'first_item' => reset($assetsResult['data'])
                ]);
            }

            // Fetch subcategories which contain asset type information
            $subcategoriesResult = $this->apiService->request('GET', '/asset-subcategories');

            // Fetch rooms for the room dropdown
            $roomsResult = $this->apiService->request('GET', '/rooms');

            // Fetch brands for brand dropdown with separate pagination
            $brandsResult = $this->apiService->request('GET', '/brands', [
                'query' => [
                    'page' => $request->input('brand_page', 1),
                    'limit' => $request->input('brand_limit', 1000),
                    'sort_by' => 'brand_id',
                    'sort_order' => 'asc'
                ]
            ]);

            // Fetch all users for the dropdown
            $usersResult = $this->apiService->request('GET', '/users', [
                'query' => [
                    'limit' => 1000, // Get only a minimal set of users for fallback, we now use lazy loading
                    'sort_by' => 'employee_number',
                    'sort_order' => 'asc'
                ]
            ]);

            // Log API responses for debugging
            \Log::info('API response for assets list:', [
                'assets_success' => $assetsResult['success'] ?? null,
                'assets_count' => isset($assetsResult['data']) ? count($assetsResult['data']) : 0,
                'brands_success' => $brandsResult['success'] ?? null,
                'brands_count' => isset($brandsResult['data']) ? count($brandsResult['data']) : 0
            ]);

            // Check for auth errors
            if (
                (isset($brandsResult['error']) && in_array($brandsResult['error'], ['auth_failed', 'session_expired'])) ||
                (isset($assetsResult['error']) && in_array($assetsResult['error'], ['auth_failed', 'session_expired']))
            ) {
                $errorMessage = $brandsResult['message'] ?? $assetsResult['message'] ?? 'Authentication failed';
                return redirect()->route('login')->with('error', $errorMessage);
            }

            // Check for API errors based on success flag
            if (
                (!isset($assetsResult['success']) || $assetsResult['success'] !== true) ||
                (!isset($brandsResult['success']) || $brandsResult['success'] !== true)
            ) {
                $errorMessage = $assetsResult['message'] ?? $brandsResult['message'] ?? 'Failed to fetch data';

                \Log::warning('Error during data retrieval:', [
                    'assets_success' => $assetsResult['success'] ?? false,
                    'brands_success' => $brandsResult['success'] ?? false,
                    'message' => $errorMessage
                ]);

                return view('Asset.UnitAsset', [
                    'assets' => [],
                    'brands' => [],
                    'users' => [],
                    'rooms' => [],
                    'assets_pagination' => null,
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
                if (isset($subcategory['asset_type']) && !in_array($subcategory['asset_type'], $assetTypes)) {
                    $assetTypes[] = $subcategory['asset_type'];
                }
            }

            // Parse other data
            $rooms = $roomsResult['data'] ?? [];
            $brands = $brandsResult['data'] ?? [];
            $assets = $assetsResult['data'] ?? [];
            $users = $usersResult['data'] ?? [];

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
                        if (isset($buildingResult['success']) && $buildingResult['success'] === true && isset($buildingResult['data']['building_name'])) {
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

            // Map subcategories by ID for quick lookup
            $subcategoryMap = [];
            foreach ($subcategories as $subcategory) {
                $subcategoryMap[$subcategory['subcategory_id']] = $subcategory;
            }

            // Add subcategory data to each asset
            foreach ($assets as &$asset) {
                if (isset($asset['subcategory_id']) && isset($subcategoryMap[$asset['subcategory_id']])) {
                    $asset['subcategory'] = $subcategoryMap[$asset['subcategory_id']];
                }

                // Ensure room data is properly structured
                if (!isset($asset['room'])) {
                    $asset['room'] = [];
                }

                if (!isset($asset['room']['building'])) {
                    $asset['room']['building'] = ['building_name' => '-'];
                }

                // If asset_name is not set but asset_master has a name, use it
                if ((!isset($asset['asset_name']) || empty($asset['asset_name'])) &&
                    isset($asset['asset_master']) && isset($asset['asset_master']['asset_master_name'])) {
                    $asset['asset_name'] = $asset['asset_master']['asset_master_name'];
                }

                // If description is not set but asset_master has a description, use it
                if ((!isset($asset['description']) || empty($asset['description'])) &&
                    isset($asset['asset_master']) && isset($asset['asset_master']['description'])) {
                    $asset['description'] = $asset['asset_master']['description'];
                }

                // For subcategory, we need to handle cases where it might be nested in asset_master
                if (!isset($asset['subcategory']) || empty($asset['subcategory'])) {
                    if (isset($asset['asset_master']) && isset($asset['asset_master']['subcategory'])) {
                        $asset['subcategory'] = $asset['asset_master']['subcategory'];
                    } elseif (isset($asset['asset_master']) && isset($asset['asset_master']['subcategory_id'])) {
                        $subcatId = $asset['asset_master']['subcategory_id'];
                        if (isset($subcategoryMap[$subcatId])) {
                            $asset['subcategory'] = $subcategoryMap[$subcatId];
                        }
                    }
                }
            }

            return view('Asset.UnitAsset', [
                'assets' => $assets,
                'brands' => $brands,
                'users' => $users,
                'rooms' => $transformedRooms,
                'assets_pagination' => $assetsPagination,
                'brands_pagination' => $brandsPagination,
                'subcategories' => $subcategories,
                'assetTypes' => $assetTypes
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during data retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('Asset.UnitAsset', [
                'assets' => [],
                'brands' => [],
                'users' => [],
                'rooms' => [],
                'assets_pagination' => null,
                'brands_pagination' => null,
                'subcategories' => [],
                'assetTypes' => [],
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

            // Prepare asset data with the new format that uses asset_master_id
            $assetData = [
                'asset_master_id' => (int) $request->input('asset_master_id'),
                'serial_number' => $request->input('serial_number'),
                'purchase_date' => $request->input('purchase_date'),
                'purchase_cost' => (float) $request->input('purchase_cost'),
                'warranty_end_date' => $request->input('warranty_end_date'),
                'user_id' => $request->input('user_id') ? (int) $request->input('user_id') : null,
                'current_status' => $request->input('current_status', 'available'),
                'condition' => $request->input('condition', 'good'),
                'room_id' => (int) $request->input('room_id')
            ];

            // Add depreciation data if present - now directly from the request without checking is_depreciable
            if ($request->has('depreciation_method')) {
                $assetData['depreciation_method'] = $request->input('depreciation_method');
                $assetData['acquisition_cost'] = (float) $request->input('acquisition_cost');
                $assetData['salvage_value'] = (float) $request->input('salvage_value');
                $assetData['asset_life_months'] = (int) $request->input('asset_life_months');
                $assetData['date_acquired'] = $request->input('date_acquired');
            }

            // Log structured data yang akan dikirim ke API
            \Log::info('Sending to API:', [
                'asset_data' => $assetData
            ]);

            // Handle image upload if present
            if ($request->hasFile('image_file')) {
                $multipartData = [];

                // Add asset data as form fields
                foreach ($assetData as $key => $value) {
                    // Convert values appropriately for multipart
                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    } elseif (is_array($value)) {
                        $value = json_encode($value);
                    } elseif ($value === null) {
                        $value = ''; // Convert null to empty string for multipart
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
            if (isset($result['error']) || (isset($result['success']) && $result['success'] === false)) {
                \Log::warning('Error during asset creation:', [
                    'error' => $result['error'] ?? null,
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
                'asset_id' => $result['data']['asset_id'] ?? 'unknown',
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

            // Prepare asset data with new format
            $assetData = [
                'asset_id' => $id,
                'serial_number' => $request->input('serial_number'),
                'purchase_date' => $request->input('purchase_date'),
                'purchase_cost' => (float) $request->input('purchase_cost'),
                'warranty_end_date' => $request->input('warranty_end_date'),
                'user_id' => $request->input('user_id') ? (int) $request->input('user_id') : null,
                'current_status' => $request->input('current_status', 'available'),
                'condition' => $request->input('condition'),
                'room_id' => (int) $request->input('room_id')
            ];

            // Add depreciation fields directly without checking is_depreciable
            if ($request->has('depreciation_method')) {
                $assetData['depreciation_method'] = $request->input('depreciation_method');
                $assetData['acquisition_cost'] = (float) $request->input('acquisition_cost');
                $assetData['salvage_value'] = (float) $request->input('salvage_value');
                $assetData['asset_life_months'] = (int) $request->input('asset_life_months');
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
                    // Convert values appropriately for multipart
                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    } elseif (is_array($value)) {
                        $value = json_encode($value);
                    } elseif ($value === null) {
                        $value = ''; // Convert null to empty string for multipart
                    }

                    $multipartData[] = [
                        'name' => $key,
                        'contents' => $value
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
                $options = ['json' => $assetData];
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

            // Check for other API errors or unsuccessful responses
            if (
                isset($result['error']) ||
                (isset($result['success']) && $result['success'] === false)
            ) {

                \Log::warning('Error during asset update:', [
                    'error' => $result['error'] ?? null,
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
            if (isset($result['error']) || (isset($result['success']) && $result['success'] === false)) {
                \Log::warning('Error during asset deletion:', [
                    'error' => $result['error'] ?? null,
                    'success' => $result['success'] ?? null,
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
            // Check if this is a user search request - this lets us use the same endpoint for user search without adding a new route
            if ($id === 'search' && request()->ajax() && request()->wantsJson()) {
                \Log::info('Processing users search request:', [
                    'search' => request()->input('search'),
                    'limit' => request()->input('limit', 10),
                    'offset' => request()->input('offset', 0)
                ]);

                // Build query parameters for users API
                $searchTerm = request()->input('search');
                $limit = request()->input('limit', 10);
                $offset = request()->input('offset', 0);

                $queryParams = [
                    'limit' => $limit,
                    'offset' => $offset,
                    'sort_by' => 'employee_number', // Sort by employee number for easier searching
                    'sort_order' => 'asc'
                ];

                if (!empty($searchTerm)) {
                    $queryParams['search'] = $searchTerm;
                }

                // Call the API to get users
                $usersResult = $this->apiService->request('GET', '/users', [
                    'query' => $queryParams
                ]);

                // Check for errors
                if (isset($usersResult['error']) || !isset($usersResult['success']) || $usersResult['success'] !== true) {
                    $errorMessage = $usersResult['message'] ?? 'Failed to fetch users';
                    \Log::warning('Error fetching users:', [
                        'error' => $usersResult['error'] ?? 'unknown',
                        'message' => $errorMessage
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 400);
                }

                // Return the users data
                return response()->json([
                    'success' => true,
                    'data' => $usersResult['data'] ?? [],
                    'pagination' => $usersResult['pagination'] ?? null
                ]);
            }

            // Regular asset fetch logic continues below
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
                'api_response_success' => $result['success'] ?? null,
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
                    return response()->json(['success' => false, 'message' => $result['message'] ?? 'Authentication failed'], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to retrieve asset';

                \Log::warning('Error during asset retrieval:', [
                    'success' => $result['success'] ?? false,
                    'message' => $errorMessage
                ]);

                if (request()->ajax()) {
                    return response()->json(['success' => false, 'message' => $errorMessage], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            $asset = $result['data'] ?? null;

            if (!$asset) {
                $errorMessage = 'Asset not found or response data is invalid';

                if (request()->ajax()) {
                    return response()->json(['success' => false, 'message' => $errorMessage], 404);
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

                // Fetch asset masters
                $assetMastersResult = $this->apiService->request('GET', '/asset-masters', [
                    'query' => [
                        'limit' => 100,
                        'sort_by' => 'asset_master_id',
                        'sort_order' => 'asc'
                    ]
                ]);
                $assetMasters = $assetMastersResult['data'] ?? [];

                // If we have a user_id, fetch user details to ensure we have complete information
                if (isset($asset['user_id']) && $asset['user_id']) {
                    try {
                        $userResult = $this->apiService->request('GET', "/users/{$asset['user_id']}");
                        if (isset($userResult['success']) && $userResult['success'] === true && isset($userResult['data'])) {
                            // Enhance the user object with complete details
                            $asset['user'] = $userResult['data'];
                            \Log::info('Enhanced user data for asset:', [
                                'asset_id' => $id,
                                'user_id' => $asset['user_id'],
                                'employee_number' => $asset['user']['employee_number'] ?? 'not available'
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::warning("Error fetching user details: {$e->getMessage()}");
                    }
                }

                // Return complete data set for the modal
                return response()->json([
                    'success' => true,
                    'data' => $asset,
                    'subcategories' => $subcategories,
                    'rooms' => $rooms,
                    'brands' => $brands,
                    'assetMasters' => $assetMasters
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
                return response()->json(['success' => false, 'message' => $errorMessage], 500);
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
            if (isset($result['error']) || (isset($result['success']) && $result['success'] === false)) {
                $errorMessage = $result['message'] ?? 'Failed to generate barcode';

                \Log::warning('Error during barcode generation:', [
                    'error' => $result['error'] ?? null,
                    'success' => $result['success'] ?? null,
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
                'success' => isset($result['success']) ? $result['success'] : 'not set',
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
            if (!isset($result['success']) || $result['success'] !== true) {
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
            \Log::info('Attempting to print QR codes as PDF', [
                'request_data' => $request->all()
            ]);

            // Clear any previous QR data from session to avoid using old data
            session()->forget('qr_data');

            // Always use the asset_ids from the current request
            if (!$request->has('asset_ids')) {
                return redirect()->back()->with('error', 'No assets selected for QR code printing');
            }

                // Parse asset IDs
                $assetIds = $request->input('asset_ids');
                if (is_string($assetIds)) {
                    // Make sure we're properly parsing the comma-separated list
                    $assetIds = array_map('trim', explode(',', $assetIds));
                // Remove any empty items and convert to integers
                $assetIds = array_map('intval', array_filter($assetIds));
            }

            if (empty($assetIds)) {
                return redirect()->back()->with('error', 'No valid asset IDs found for QR code printing');
                }

                // Ensure we've got an array of IDs (log this for debugging)
            \Log::info('Asset IDs for QR generation:', ['asset_ids' => $assetIds, 'count' => count($assetIds)]);

                $qrSize = $request->input('qr_size', 50);
                $quantity = $request->input('quantity', 1);

            // Use apiService to generate QR codes for the selected assets
                $result = $this->apiService->request('POST', "/assets/qr/generate-bulk", [
                    'json' => [
                        'asset_ids' => $assetIds,
                        'qr_size' => $qrSize,
                        'quantity' => $quantity,
                    ]
                ]);

            // Log the API request and response for debugging
                \Log::info('QR generation API request/response:', [
                    'request' => [
                        'asset_ids' => $assetIds,
                        'qr_size' => $qrSize,
                        'quantity' => $quantity
                    ],
                'response_success' => $result['success'] ?? false,
                'response_data_count' => isset($result['data']) ? count($result['data']) : 0
                ]);

                // Check for auth errors
                if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                    \Log::warning('Authentication error during QR generation:', [
                        'error' => $result['error'],
                        'message' => $result['message'] ?? 'Authentication failed'
                    ]);
                    return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
                }

                if (isset($result['success']) && $result['success'] === true && isset($result['data'])) {
                    \Log::info('Successfully generated QR codes from API', [
                        'count' => count($result['data']),
                        'asset_ids_in_response' => array_column($result['data'], 'asset_id')
                    ]);

                    $qrData = $result['data'];
                } else {
                    \Log::error('Failed to generate QR codes from API', [
                        'result' => $result
                    ]);
                    return redirect()->back()->with('error', $result['message'] ?? 'Failed to generate QR codes');
            }

            if (empty($qrData)) {
                return redirect()->back()->with('error', 'No QR code data returned from the API');
            }

            // Log what we have before processing
            \Log::info('Processing QR data for PDF:', [
                'qr_count' => count($qrData),
                'first_few_ids' => array_slice(array_column($qrData, 'asset_id'), 0, min(5, count($qrData)))
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

    /**
     * Get asset data for AJAX requests.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssetData(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');

            // Log request info
            \Log::info('Fetching assets with parameters for AJAX:', [
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

            // Log API responses for debugging
            \Log::info('API response for assets AJAX list:', [
                'assets_success' => $assetsResult['success'] ?? null,
                'assets_count' => isset($assetsResult['data']) ? count($assetsResult['data']) : 0
            ]);

            // Check for auth errors
            if (isset($assetsResult['error']) && in_array($assetsResult['error'], ['auth_failed', 'session_expired'])) {
                return response()->json([
                    'error' => $assetsResult['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Process assets and add subcategory info
            $assets = $assetsResult['data'] ?? [];
            $subcategories = $subcategoriesResult['data'] ?? [];

            // Map subcategories by ID for quick lookup
            $subcategoryMap = [];
            foreach ($subcategories as $subcategory) {
                $subcategoryMap[$subcategory['subcategory_id']] = $subcategory;
            }

            // Add subcategory data to each asset
            foreach ($assets as &$asset) {
                if (isset($asset['subcategory_id']) && isset($subcategoryMap[$asset['subcategory_id']])) {
                    $asset['subcategory'] = $subcategoryMap[$asset['subcategory_id']];
                }

                // Ensure room data is properly structured
                if (!isset($asset['room'])) {
                    $asset['room'] = [];
                }

                if (!isset($asset['room']['building'])) {
                    $asset['room']['building'] = ['building_name' => '-'];
                }

                // Log asset structure to debug
                \Log::debug('Asset structure:', [
                    'asset_id' => $asset['asset_id'] ?? 'No ID',
                    'asset_code' => $asset['asset_code'] ?? 'No Code',
                    'asset_name' => $asset['asset_name'] ?? 'No Name',
                    'asset_master' => $asset['asset_master'] ?? null,
                    'subcategory' => $asset['subcategory'] ?? null
                ]);

                // If asset_name is not set but asset_master has a name, use it
                if ((!isset($asset['asset_name']) || empty($asset['asset_name'])) &&
                    isset($asset['asset_master']) && isset($asset['asset_master']['asset_master_name'])) {
                    $asset['asset_name'] = $asset['asset_master']['asset_master_name'];
                }

                // If description is not set but asset_master has a description, use it
                if ((!isset($asset['description']) || empty($asset['description'])) &&
                    isset($asset['asset_master']) && isset($asset['asset_master']['description'])) {
                    $asset['description'] = $asset['asset_master']['description'];
                }

                // For subcategory, we need to handle cases where it might be nested in asset_master
                if (!isset($asset['subcategory']) || empty($asset['subcategory'])) {
                    if (isset($asset['asset_master']) && isset($asset['asset_master']['subcategory'])) {
                        $asset['subcategory'] = $asset['asset_master']['subcategory'];
                    } elseif (isset($asset['asset_master']) && isset($asset['asset_master']['subcategory_id'])) {
                        $subcatId = $asset['asset_master']['subcategory_id'];
                        if (isset($subcategoryMap[$subcatId])) {
                            $asset['subcategory'] = $subcategoryMap[$subcatId];
                        }
                    }
                }
            }

            // Format pagination
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

            // Return JSON response
            return response()->json([
                'assets' => $assets,
                'assets_pagination' => $assetsPagination
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during asset data retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to fetch assets: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import assets from Excel/CSV file
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importAssets(Request $request)
    {
        try {
            \Log::info('Attempting to import assets from Excel', [
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
                $result = $this->apiService->request('POST', '/assets/import', [
                    'multipart' => $multipartData
                ]);
            } else if ($request->has('excel_data')) {
                // Fallback to the previous method if no file but has parsed data
                // Get the JSON data from the form
                $excelData = $request->input('excel_data');

                if (empty($excelData)) {
                    if ($request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'No valid data found for import'], 400);
                    }
                    return redirect()->back()->with('error', 'No valid data found for import');
                }

                // Decode the JSON data
                $parsedData = json_decode($excelData, true);

                if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsedData) || empty($parsedData)) {
                    if ($request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'Invalid data format for import'], 400);
                    }
                    return redirect()->back()->with('error', 'Invalid data format for import');
                }

                \Log::info('Parsed Excel data for import', [
                    'record_count' => count($parsedData)
                ]);

                // Send data to API
                $result = $this->apiService->request('POST', '/assets/import', [
                    'json' => [
                        'data' => $parsedData
                    ]
                ]);
            } else {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'No Excel file or data provided'], 400);
                }
                return redirect()->back()->with('error', 'No Excel file or data provided');
            }

            // Log the API response
            \Log::info('API response for asset import:', [
                'api_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during asset import:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => $result['message'] ?? 'Authentication failed'], 401);
                }
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (
                isset($result['error']) ||
                (isset($result['success']) && $result['success'] === false)
            ) {
                \Log::warning('Error during asset import:', [
                    'error' => $result['error'] ?? null,
                    'success' => $result['success'] ?? null,
                    'message' => $result['message'] ?? 'Failed to import assets'
                ]);

                // Format error message including detailed errors from the response
                $errorMessage = $result['message'] ?? 'Failed to import assets';

                // Extract and format detailed error information if available
                if (isset($result['data']['errors']) && is_array($result['data']['errors']) && count($result['data']['errors']) > 0) {
                    $errorDetails = [];

                    foreach ($result['data']['errors'] as $error) {
                        if (isset($error['row']) && isset($error['reason'])) {
                            $errorDetailMsg = "Row {$error['row']}: ";

                            // Include asset_master_code if available
                            if (isset($error['asset_master_code'])) {
                                $errorDetailMsg .= "{$error['asset_master_code']} - ";
                            }
                            // Include asset_name if available
                            elseif (isset($error['asset_name'])) {
                                $errorDetailMsg .= "{$error['asset_name']} - ";
                            }

                            $errorDetailMsg .= $error['reason'];
                            $errorDetails[] = $errorDetailMsg;
                        } elseif (is_string($error)) {
                            $errorDetails[] = $error;
                        } elseif (is_array($error) && isset($error['message'])) {
                            $errorDetails[] = $error['message'];
                        }
                    }

                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage,
                            'errors' => $errorDetails
                        ], 400);
                    }

                    // For non-AJAX, format as HTML
                    $errorMessage .= "<ul class='list-disc pl-4 mt-2'>";
                    foreach ($errorDetails as $detail) {
                        $errorMessage .= "<li>{$detail}</li>";
                    }
                    $errorMessage .= "</ul>";
                } else {
                    if ($request->ajax()) {
                        return response()->json(['success' => false, 'message' => $errorMessage], 400);
                    }
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Extract import results
            $totalImported = $result['data']['total'] ?? 0;
            $successCount = $result['data']['success'] ?? 0;
            $failedCount = $result['data']['failed'] ?? 0;

            // Prepare success message
            $successMessage = "Successfully imported {$successCount} assets";
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
            return redirect()->route('assets')->with('success', $successMessage);
        } catch (\Exception $e) {
            \Log::error('Exception during asset import:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to import assets: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to import assets: ' . $e->getMessage());
        }
    }

    /**
     * Export unit assets data to PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportUnitAssetPDF(Request $request)
    {
        try {
            // Get filter parameters
            $search = $request->input('search', '');
            $typeFilter = $request->input('type', '');
            $statusFilter = $request->input('current_status', '');
            $sortOrder = $request->input('sort', 'newest');

            // Log request info
            \Log::info('Exporting unit assets to PDF with parameters:', [
                'search' => $search,
                'type' => $typeFilter,
                'current_status' => $statusFilter,
                'sort' => $sortOrder,
                'request_url' => $request->fullUrl()
            ]);

            // Build query parameters
            $query = [
                'page' => 1,
                'limit' => 1000  // Get a large number for export
            ];

            // Set sort parameters based on user selection
            if (!empty($sortOrder)) {
                switch ($sortOrder) {
                    case 'newest':
                        $query['sort_by'] = 'created_at';
                        $query['sort_order'] = 'desc';
                        break;
                    case 'oldest':
                        $query['sort_by'] = 'created_at';
                        $query['sort_order'] = 'asc';
                        break;
                    case 'name_asc':
                        $query['sort_by'] = 'asset_name';
                        $query['sort_order'] = 'asc';
                        break;
                    case 'name_desc':
                        $query['sort_by'] = 'asset_name';
                        $query['sort_order'] = 'desc';
                        break;
                    default:
                        $query['sort_by'] = 'asset_id';
                        $query['sort_order'] = 'desc';
                }
            } else {
                $query['sort_by'] = 'asset_id';
                $query['sort_order'] = 'desc';
            }

            // Add search filter if provided
            if (!empty($search)) {
                $query['search'] = $search;
            }

            // Add status filter if provided
            if (!empty($statusFilter)) {
                $query['current_status'] = $statusFilter;
            }

            // Add asset type filter if provided
            if (!empty($typeFilter)) {
                $query['type'] = $typeFilter;
            }

            // Fetch assets for PDF
            $assetsResult = $this->apiService->request('GET', '/assets', [
                'query' => $query
            ]);

            // Check for auth errors
            if (isset($assetsResult['error']) && in_array($assetsResult['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during unit assets export:', [
                    'error' => $assetsResult['error'],
                    'message' => $assetsResult['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $assetsResult['message'] ?? 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($assetsResult['success']) || $assetsResult['success'] !== true) {
                $errorMessage = $assetsResult['message'] ?? 'Failed to fetch unit assets data';
                \Log::warning('Error during unit assets export:', [
                    'error' => $errorMessage
                ]);
                return redirect()->back()->with('error', $errorMessage);
            }

            // Get assets data
            $assets = $assetsResult['data'] ?? [];

            // Generate PDF
            $pdf = Pdf::loadView('Asset.UnitAssetPDF', [
                'assets' => $assets,
                'search' => $search,
                'typeFilter' => $typeFilter,
                'statusFilter' => $statusFilter,
                'sortOrder' => $sortOrder,
                'date_generated' => now()->format('d M Y H:i:s')
            ]);

            // Log PDF generation
            \Log::info('Unit assets PDF generated successfully', [
                'assets_count' => count($assets)
            ]);

            // Stream the PDF to browser
            return $pdf->stream('unit_assets_report_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Exception during unit assets PDF export:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to export Unit Assets as PDF: ' . $e->getMessage());
        }
    }
}
