<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Barryvdh\DomPDF\Facade\Pdf;

class ViewMasterAssetController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Get a master asset by its ID with linked assets information.
     *
     * @param int $id The master asset ID
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function getMasterAssetById($id)
    {
        try {
            // Log request info
            \Log::info('Fetching master asset with linked assets, ID:', [
                'asset_master_id' => $id,
                'request_url' => request()->fullUrl()
            ]);

            // Fetch the master asset with the given ID
            $result = $this->apiService->request('GET', "/asset-masters/{$id}");

            // Log API response for debugging
            \Log::info('API response for master asset with linked assets:', [
                'api_response_status' => $result['success'] ?? false,
                'api_response_message' => $result['message'] ?? null,
                'asset_master_id' => $id
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during master asset retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message'] ?? 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to retrieve master asset';

                \Log::warning('Error during master asset retrieval:', [
                    'success' => $result['success'] ?? false,
                    'message' => $errorMessage
                ]);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 404);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            $masterAsset = $result['data'] ?? null;

            if (!$masterAsset) {
                $errorMessage = 'Master asset not found or response data is invalid';

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 404);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Tambahkan ambil brands & subcategories
            $brandsResult = $this->apiService->request('GET', '/brands', [
                'query' => [
                    'limit' => 1000,
                    'sort_by' => 'brand_name',
                    'sort_order' => 'asc'
                ]
            ]);
            $subcategoriesResult = $this->apiService->request('GET', '/asset-subcategories', [
                'query' => [
                    'limit' => 1000,
                    'sort_by' => 'subcategory_name',
                    'sort_order' => 'asc'
                ]
            ]);
            $brands = $brandsResult['data'] ?? [];
            $subcategories = $subcategoriesResult['data'] ?? [];

            // Return view with all data
            return view('Asset.ViewMasterAsset', [
                'masterAsset' => $masterAsset,
                'brands' => $brands,
                'subcategories' => $subcategories
            ]);
        } catch (\Exception $e) {
            $errorMessage = 'Failed to retrieve master asset: ' . $e->getMessage();

            \Log::error('Exception during master asset retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_master_id' => $id
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Get master asset for editing.
     */
    public function editMasterAsset($id)
    {
        try {
            // Log request info
            \Log::info('Fetching master asset for editing:', [
                'asset_master_id' => $id,
                'request_url' => request()->fullUrl(),
                'is_ajax' => request()->ajax() ? 'Yes' : 'No'
            ]);

            // Only continue if this is an AJAX request
            if (!request()->ajax()) {
                return redirect()->route('asset-master.view', ['id' => $id]);
            }

            // Fetch the master asset data
            $result = $this->apiService->request('GET', "/asset-masters/{$id}");

            // Check for errors
            if (!isset($result['success']) || $result['success'] !== true) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to retrieve master asset'
                ], 400);
            }

            // Prepare response data
            $responseData = [
                'masterAsset' => $result['data']
            ];

            // In the same request, also fetch brands and subcategories
            // This is more efficient than separate requests
            $brandsResult = $this->apiService->request('GET', '/brands', [
                'query' => [
                    'limit' => 1000,
                    'sort_by' => 'brand_name',
                    'sort_order' => 'asc'
                ]
            ]);

            $subcategoriesResult = $this->apiService->request('GET', '/asset-subcategories', [
                'query' => [
                    'limit' => 1000,
                    'sort_by' => 'subcategory_name',
                    'sort_order' => 'asc'
                ]
            ]);

            // Add brands and subcategories to response if available
            if (isset($brandsResult['success']) && $brandsResult['success'] === true) {
                $responseData['brands'] = $brandsResult['data'] ?? [];
            }

            if (isset($subcategoriesResult['success']) && $subcategoriesResult['success'] === true) {
                $responseData['subcategories'] = $subcategoriesResult['data'] ?? [];
            }

            // Return JSON response
            return response()->json($responseData);
        } catch (\Exception $e) {
            \Log::error('Exception during master asset edit retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load master asset: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified master asset.
     */
    public function updateMasterAsset(Request $request, $id)
    {
        try {
            // Log request data
            \Log::info('Updating master asset:', [
                'asset_master_id' => $id,
                'request_data' => $request->except(['image_file'])
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

            // Handle image upload if present
            if ($request->hasFile('image_file')) {
                $multipartData = [];

                // Convert each field to multipart form data
                foreach ($masterAssetData as $key => $value) {
                    // Convert boolean values to string
                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    } elseif (is_array($value)) {
                        $value = json_encode($value);
                    } elseif ($value === null) {
                        $value = '';
                    }

                    $multipartData[] = [
                        'name' => $key,
                        'contents' => (string)$value
                    ];
                }

                // Add file to multipart data
                $multipartData[] = [
                    'name' => 'image_file',
                    'contents' => fopen($request->file('image_file')->getPathname(), 'r'),
                    'filename' => $request->file('image_file')->getClientOriginalName()
                ];

                $options = ['multipart' => $multipartData];
                $result = $this->apiService->request('PUT', "/asset-masters/{$id}", $options);
            } else {
                // Standard JSON request if no file
                $options = ['json' => $masterAssetData];
                $result = $this->apiService->request('PUT', "/asset-masters/{$id}", $options);
            }

            // Handle API response
            if (!isset($result['success']) || $result['success'] !== true) {
                \Log::warning('Error updating master asset:', [
                    'asset_master_id' => $id,
                    'api_response' => $result
                ]);

                $errorMessage = $result['message'] ?? 'Failed to update master asset';
                return redirect()->back()->with('error', $errorMessage);
            }

            // Success
            \Log::info('Master asset updated successfully:', [
                'asset_master_id' => $id
            ]);

            return redirect()->route('asset-master.view', ['id' => $id])
                ->with('success', 'Master asset updated successfully');
        } catch (\Exception $e) {
            \Log::error('Exception during master asset update:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update master asset: ' . $e->getMessage());
        }
    }

    /**
     * Export master asset detail to PDF
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportMasterAssetPDF($id, Request $request)
    {
        try {
            // Fetch master asset details
            $result = $this->apiService->request('GET', "/asset-masters/{$id}");

            if (!isset($result['data'])) {
                return redirect()->route('asset-master')->with('error', 'Master asset not found');
            }

            $masterAsset = $result['data'];

            // Convert asset image to base64
            if (!empty($masterAsset['reference_image_path'])) {
                try {
                    $backendUrl = config('app.backend_url', 'http://localhost:5000');
                    $imageUrl = $backendUrl . '/public' . $masterAsset['reference_image_path'];
                    $imageData = @file_get_contents($imageUrl);

                    if ($imageData !== false) {
                        $masterAsset['reference_image_base64'] = base64_encode($imageData);
                        \Log::info('Successfully encoded image to base64', ['size' => strlen($masterAsset['reference_image_base64'])]);
                    } else {
                        \Log::warning('Failed to get image data', ['url' => $imageUrl]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error loading image: ' . $e->getMessage());
                }
            }

            // Generate PDF
            $pdf = Pdf::loadView('Asset.ViewMasterAssetPDF', [
                'masterAsset' => $masterAsset,
                'date_generated' => now()->format('d M Y H:i:s')
            ]);

            // Stream the PDF to browser
            return $pdf->stream('master_asset_detail_' . $id . '_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Exception during master asset detail PDF export:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_master_id' => $id
            ]);

            return redirect()->back()->with('error', 'Failed to export master asset detail as PDF: ' . $e->getMessage());
        }
    }
}
