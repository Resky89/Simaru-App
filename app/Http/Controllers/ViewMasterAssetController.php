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
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during master asset retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve master asset';

                \Log::warning('Error during master asset retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], 404);
                }

                // Format error message for redirect
                $errorMessage = '';
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $errorMessage .= implode(', ', $messages) . '; ';
                        } else {
                            $errorMessage .= $messages . '; ';
                        }
                    }
                } else {
                    $errorMessage = $errorData;
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

            // Return view with master asset data only
            return view('Asset.ViewMasterAsset', [
                'masterAsset' => $masterAsset
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
                return redirect()->route('view-asset-master', ['id' => $id]);
            }

            // Fetch the master asset data
            $result = $this->apiService->request('GET', "/asset-masters/{$id}");

            // Check for errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], 401);
            }

            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve master asset';

                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                ], 400);
            }

            // Return JSON response with just the master asset data
            return response()->json([
                'masterAsset' => $result['data']
            ]);
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
                $errorData = $result['errors'] ?? 'Failed to update master asset';

                \Log::warning('Error updating master asset:', [
                    'asset_master_id' => $id,
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                // Format error message for redirect
                $errorMessage = '';
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $errorMessage .= implode(', ', $messages) . '; ';
                        } else {
                            $errorMessage .= $messages . '; ';
                        }
                    }
                } else {
                    $errorMessage = $errorData;
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Success
            \Log::info('Master asset updated successfully:', [
                'asset_master_id' => $id
            ]);

            return redirect()->route('asset-master', ['id' => $id])
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
    public function exportViewMasterAssetPDF($id, Request $request)
    {
        try {
            // Fetch master asset details
            $result = $this->apiService->request('GET', "/asset-masters/{$id}");

            if (!isset($result['data'])) {
                $errorData = $result['errors'] ?? 'Master asset not found';

                \Log::warning('Error retrieving master asset for PDF export:', [
                    'asset_master_id' => $id,
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                // Format error message
                $errorMessage = '';
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $errorMessage .= implode(', ', $messages) . '; ';
                        } else {
                            $errorMessage .= $messages . '; ';
                        }
                    }
                } else {
                    $errorMessage = $errorData;
                }

                return redirect()->route('asset-master')->with('error', $errorMessage);
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
