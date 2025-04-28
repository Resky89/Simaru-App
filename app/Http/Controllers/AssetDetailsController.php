<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use App\Http\Controllers\AssetDocumentController;

class AssetDetailsController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display the details of a specific asset.
     *
     * @param int $id The asset ID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            // Log request info
            \Log::info('Fetching asset details with ID:', [
                'asset_id' => $id,
                'request_url' => request()->fullUrl()
            ]);

            // Fetch the asset with the given ID
            $result = $this->apiService->request('GET', "/assets/{$id}");

            // Log API response for debugging
            \Log::info('API response for asset details:', [
                'api_response_status' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'asset_id' => $id
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during asset details retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to retrieve asset details';

                \Log::warning('Error during asset details retrieval:', [
                    'success' => $result['success'] ?? false,
                    'message' => $errorMessage
                ]);

                return redirect()->back()->with('error', $errorMessage);
            }

            $asset = $result['data'] ?? null;

            if (!$asset) {
                $errorMessage = 'Asset not found or response data is invalid';
                return redirect()->back()->with('error', $errorMessage);
            }

            // Log the complete asset structure for debugging
            \Log::info('Complete asset data structure:', [
                'asset' => $asset
            ]);

            // Generate QR code for the asset using the bulk API endpoint
            if (isset($asset['asset_id'])) {
                try {
                    // Create an array with the single asset ID
                    $assetIds = [$asset['asset_id']];

                    // Call the bulk QR code generation API
                    $qrResult = $this->apiService->request('POST', "/assets/qr/generate-bulk", [
                        'json' => [
                            'asset_ids' => $assetIds
                        ]
                    ]);

                    \Log::info('QR code generation API response:', [
                        'status' => $qrResult['success'] ?? null,
                        'message' => $qrResult['message'] ?? null,
                        'data_count' => isset($qrResult['data']) ? count($qrResult['data']) : 0
                    ]);

                    // If successful response with data, get the QR code for the asset
                    if (isset($qrResult['success']) && $qrResult['success'] === true &&
                        isset($qrResult['data']) && is_array($qrResult['data']) && count($qrResult['data']) > 0) {

                        // Find the QR data for this asset
                        foreach ($qrResult['data'] as $qrData) {
                            if (isset($qrData['asset_id']) && $qrData['asset_id'] == $asset['asset_id']) {
                                // If there's a base64 QR code in the response
                                if (isset($qrData['qr_base64'])) {
                                    $asset['qr_base64'] = $qrData['qr_base64'];
                                    break;
                                }
                                // Or if there's a QR URL that needs to be converted to base64
                                else if (isset($qrData['qr_url'])) {
                                    try {
                                        // Get proper API URL from backend configuration
                                        $backendUrl = rtrim(config('app.backend_url'), '/');
                                        $imageUrl = $backendUrl . "/public" . $qrData['qr_url'];

                                        // Try to get the image content
                                        $imageData = @file_get_contents($imageUrl);
                                        if ($imageData !== false) {
                                            $asset['qr_base64'] = 'data:image/png;base64,' . base64_encode($imageData);
                                        }
                                    } catch (\Exception $qrImageEx) {
                                        \Log::error('Failed to load QR image: ' . $qrImageEx->getMessage());
                                    }
                                    break;
                                }
                            }
                        }
                    } else if (isset($asset['qr_code']) && !empty($asset['qr_code'])) {
                        // If QR code is already provided in the asset data
                        try {
                            $backendUrl = rtrim(config('app.backend_url'), '/');
                            $imageUrl = $backendUrl . "/public" . $asset['qr_code'];

                            $imageData = @file_get_contents($imageUrl);
                            if ($imageData !== false) {
                                $asset['qr_base64'] = 'data:image/png;base64,' . base64_encode($imageData);
                            }
                        } catch (\Exception $qrImageEx) {
                            \Log::error('Failed to load QR image from asset data: ' . $qrImageEx->getMessage());
                        }
                    }
                } catch (\Exception $qrEx) {
                    \Log::error('Exception during QR code generation:', [
                        'error' => $qrEx->getMessage(),
                        'trace' => $qrEx->getTraceAsString()
                    ]);
                    // Continue without QR code if failed
                }
            }

            // Fetch subcategories for the dropdown
            $subcategoriesResult = $this->apiService->request('GET', '/asset-subcategories');
            $subcategories = $subcategoriesResult['data'] ?? [];

            // Fetch buildings directly from buildings endpoint
            $buildingsResult = $this->apiService->request('GET', '/buildings');
            $buildings = $buildingsResult['data'] ?? [];

            // Fetch rooms from rooms endpoint
            $roomsResult = $this->apiService->request('GET', '/rooms');
            $rooms = $roomsResult['data'] ?? [];

            // Fetch brands for brand dropdown
            $brandsResult = $this->apiService->request('GET', '/brands');
            $brands = $brandsResult['data'] ?? [];

            // Fetch users/karyawan for responsible employee dropdown
            $usersResult = $this->apiService->request('GET', '/users', [
                'query' => [
                    'limit' => 1000, // Get a larger set of users
                    'sort_by' => 'employee_number',
                    'sort_order' => 'asc'
                ]
            ]);
            $users = $usersResult['data'] ?? [];

            // If there's a user_id in the asset data, fetch the complete user details
            if (isset($asset['user_id']) && $asset['user_id']) {
                try {
                    $userResult = $this->apiService->request('GET', "/users/{$asset['user_id']}");
                    if (isset($userResult['success']) && $userResult['success'] === true && isset($userResult['data'])) {
                        // Add complete user details to the asset
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

            // Fetch asset masters for the asset master dropdown
            $assetMastersResult = $this->apiService->request('GET', '/asset-masters', [
                'query' => [
                    'limit' => 1000,
                    'sort_by' => 'asset_master_id',
                    'sort_order' => 'asc'
                ]
            ]);
            $assetMasters = $assetMastersResult['data'] ?? [];

            // Return the view with asset details
            return view('Asset.AssetDetail', [
                'asset' => $asset,
                'subcategories' => $subcategories,
                'rooms' => $rooms,
                'buildings' => $buildings,
                'brands' => $brands,
                'users' => $users,
                'assetMasters' => $assetMasters,
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during asset details retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $id
            ]);

            return redirect()->back()->with('error', 'Failed to retrieve asset details: ' . $e->getMessage());
        }
    }

    /**
     * Get asset details as JSON (for API requests)
     *
     * @param int $id The asset ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssetJson($id)
    {
        try {
            // Fetch the asset with the given ID
            $result = $this->apiService->request('GET', "/assets/{$id}");

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to retrieve asset details'
                ], 400);
            }

            // Return the asset data as JSON
            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Exception during JSON asset details retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve asset details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified asset.
     *
     * @param Request $request
     * @param int $id The asset ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAsset(Request $request, $id)
    {
        try {
            // Log request data for debugging
            \Log::info('Attempting to update asset with data:', [
                'asset_id' => $id,
                'request_data' => $request->all()
            ]);

            // Prepare asset data with new format (flat structure)
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

            // Log structured data that will be sent to API
            \Log::info('Sending to API:', [
                'asset_data' => $assetData
            ]);

            // Handle image upload if present
            if ($request->hasFile('image_file')) {
                $multipartData = [];

                // Send each asset data field individually in multipart
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

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message'] ?? 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (isset($result['error']) ||
                (isset($result['success']) && $result['success'] === false)) {

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

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 400);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Successfully updated
            \Log::info('Asset updated successfully', ['asset_id' => $id]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Asset updated successfully',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('asset.details', ['id' => $id])
                ->with('success', 'Asset updated successfully');
        } catch (\Exception $e) {
            \Log::error('Exception during asset update:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $id
            ]);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update asset: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update asset: ' . $e->getMessage());
        }
    }

    public function checkoutAsset(Request $request)
    {
        try {
            // Log request data for debugging
            \Log::info('Attempting to checkout asset with data:', [
                'request_data' => $request->all()
            ]);

            // Base checkout data that's always required
            $checkoutData = [
                'asset_id' => (int)$request->input('asset_id'),
                'checkout_notes' => $request->input('checkout_notes')
            ];

            // Check if it's a location checkout or employee checkout
            if ($request->has('room_id') || ($request->has('location_id') && $request->input('checkout_to_type') === 'location')) {
                // Location checkout - use room_id and do NOT include assigned_to
                $checkoutData['room_id'] = (int)($request->input('room_id') ?? $request->input('location_id'));
            } else {
                // Employee checkout - use assigned_to
                $checkoutData['assigned_to'] = (int)$request->input('assigned_to');
            }

            // Send request to API
            $options = ['json' => $checkoutData];
            $result = $this->apiService->request('POST', '/asset-transfers/checkout', $options);

            // Log the API response
            \Log::info('API response for asset checkout:', [
                'asset_id' => $request->input('asset_id'),
                'api_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check for other API errors
            if (isset($result['error']) ||
                (isset($result['success']) && $result['success'] === false)) {

                $errorMessage = $result['message'] ?? 'Failed to checkout asset';

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }

            // Return successful response
            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Exception during asset checkout:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to checkout asset: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check in (return) an asset.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkinAsset(Request $request)
    {
        try {
            // Log request data for debugging
            \Log::info('Attempting to check in asset with data:', [
                'request_data' => $request->all()
            ]);

            // Prepare check-in data
            $checkinData = [
                'asset_id' => (int)$request->input('asset_id'),
                'return_notes' => $request->input('return_notes'),
                'condition' => $request->input('condition')
            ];

            // Send request to API
            $options = ['json' => $checkinData];
            $result = $this->apiService->request('POST', '/asset-transfers/return', $options);

            // Log the API response
            \Log::info('API response for asset check-in:', [
                'asset_id' => $request->input('asset_id'),
                'api_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check for other API errors
            if (isset($result['error']) ||
                (isset($result['success']) && $result['success'] === false)) {

                $errorMessage = $result['message'] ?? 'Failed to check in asset';

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }

            // Return successful response
            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Exception during asset check-in:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check in asset: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Report an asset as lost.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reportAssetLost(Request $request)
    {
        try {
            // Log request data for debugging
            \Log::info('Attempting to report asset as lost with data:', [
                'request_data' => $request->all()
            ]);

            // Prepare lost report data
            $lostData = [
                'asset_id' => (int)$request->input('asset_id'),
                'loss_reason' => $request->input('loss_reason')
            ];

            // Send request to API
            $options = ['json' => $lostData];
            $result = $this->apiService->request('POST', '/asset-transfers/loss', $options);

            // Log the API response
            \Log::info('API response for reporting asset as lost:', [
                'asset_id' => $request->input('asset_id'),
                'api_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check for other API errors
            if (isset($result['error']) ||
                (isset($result['success']) && $result['success'] === false)) {

                $errorMessage = $result['message'] ?? 'Failed to report asset as lost';

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }

            // Return successful response
            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Exception during reporting asset as lost:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to report asset as lost: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Report an asset as found.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reportAssetFound(Request $request)
    {
        try {
            // Log request data for debugging
            \Log::info('Attempting to report asset as found with data:', [
                'request_data' => $request->all()
            ]);

            // Prepare found data
            $foundData = [
                'asset_id' => (int)$request->input('asset_id'),
                'found_notes' => $request->input('found_notes')
            ];

            // Send request to API
            $options = ['json' => $foundData];
            $result = $this->apiService->request('POST', '/asset-transfers/found', $options);

            // Log the API response
            \Log::info('API response for reporting asset as found:', [
                'asset_id' => $request->input('asset_id'),
                'api_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check for other API errors
            if (isset($result['error']) ||
                (isset($result['success']) && $result['success'] === false)) {

                $errorMessage = $result['message'] ?? 'Failed to report asset as found';

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }

            // Return successful response
            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Exception during reporting asset as found:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to report asset as found: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dispose an asset.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function disposeAsset(Request $request)
    {
        try {
            // Log request data for debugging
            \Log::info('Attempting to dispose asset with data:', [
                'request_data' => $request->all()
            ]);

            // Prepare disposal data
            $disposeData = [
                'asset_id' => (int)$request->input('asset_id'),
                'disposal_reason' => $request->input('disposal_reason'),
                'disposal_method' => $request->input('disposal_method'),
                'disposal_notes' => $request->input('disposal_notes')
            ];

            // Send request to API
            $options = ['json' => $disposeData];
            $result = $this->apiService->request('POST', '/asset-transfers/dispose', $options);

            // Log the API response
            \Log::info('API response for asset disposal:', [
                'asset_id' => $request->input('asset_id'),
                'api_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check for other API errors
            if (isset($result['error']) ||
                (isset($result['success']) && $result['success'] === false)) {

                $errorMessage = $result['message'] ?? 'Failed to dispose asset';

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }

            // Return successful response
            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Exception during asset disposal:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to dispose asset: ' . $e->getMessage()
            ], 500);
        }
    }
}
