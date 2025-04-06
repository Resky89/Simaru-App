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
                'api_response_status' => $result['status'] ?? null,
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

            // Check for API errors based on status flag
            if (!isset($result['status']) || $result['status'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to retrieve asset details';

                \Log::warning('Error during asset details retrieval:', [
                    'status' => $result['status'] ?? false,
                    'message' => $errorMessage
                ]);

                return redirect()->back()->with('error', $errorMessage);
            }

            $asset = $result['data'] ?? null;

            if (!$asset) {
                $errorMessage = 'Asset not found or response data is invalid';
                return redirect()->back()->with('error', $errorMessage);
            }

            // Fetch subcategories for the dropdown
            $subcategoriesResult = $this->apiService->request('GET', '/asset-subcategories');
            $subcategories = $subcategoriesResult['data'] ?? [];

            // Fetch rooms for the room dropdown
            $roomsResult = $this->apiService->request('GET', '/rooms');
            $rooms = $roomsResult['data'] ?? [];

            // Fetch brands for brand dropdown
            $brandsResult = $this->apiService->request('GET', '/brands');
            $brands = $brandsResult['data'] ?? [];

            // Return the view with asset details
            return view('Asset.AssetDetail', [
                'asset' => $asset,
                'subcategories' => $subcategories,
                'rooms' => $rooms,
                'brands' => $brands,
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
                    'status' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check for API errors
            if (!isset($result['status']) || $result['status'] !== true) {
                return response()->json([
                    'status' => false,
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
                'status' => false,
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

            // Prepare asset data
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

            // If asset is depreciable, add depreciation fields directly to root object
            if ($request->input('is_depreciable') === '1') {
                $assetData['depreciation_method'] = $request->input('depreciation_method');
                $assetData['acquisition_cost'] = (float)$request->input('acquisition_cost');
                $assetData['salvage_value'] = (float)$request->input('salvage_value');
                $assetData['asset_life_months'] = (int)$request->input('asset_life_months');
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
            return redirect()->route('asset.details', ['id' => $id])
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
}
