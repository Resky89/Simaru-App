<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DataFormatter;
use App\Http\Controllers\Traits\ApiResourceOperations;
use App\Http\Controllers\Traits\ExportableToPdf;
use App\Services\ApiService;

class ViewMasterAssetController extends Controller
{
    use ApiResourceOperations, ExportableToPdf;

    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Get a master asset by its ID with linked assets information.
     *
     * @param int $id The master asset ID
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function getMasterAssetById($id)
    {
        try {
            // Fetch the master asset with the given ID
            $result = $this->apiService->request('GET', "/asset-masters/{$id}");

            // Check for auth errors
            $authError = $this->handleAuthError($result, request());
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            $apiError = $this->handleApiError($result, request(), 'Asset.ViewMasterAsset', 'Gagal mengambil data aset master');
            if ($apiError) {
                return $apiError;
            }

            $masterAsset = $result['data'] ?? null;

            if (!$masterAsset) {
                $errorMessage = 'Aset master tidak ditemukan atau data respons tidak valid';

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 404);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Return JSON for AJAX requests (for modal edit)
            if (request()->ajax() || request()->wantsJson() || request()->has('json')) {
                return response()->json([
                    'success' => true,
                    'masterAsset' => $masterAsset
                ]);
            }

            // Return view with master asset data for normal requests
            return view('Asset.ViewMasterAsset', [
                'masterAsset' => $masterAsset
            ]);
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson() || request()->has('json')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memuat data aset master: ' . $e->getMessage()
                ], 500);
            }

            return $this->handleException($e, request(), 'Asset.ViewMasterAsset');
        }
    }

    /**
     * Update the specified master asset.
     */
    public function updateMasterAsset(Request $request, $id)
    {
        try {
            // Define field definitions for DataFormatter
            $fields = [
                'asset_name' => ['type' => 'string', 'required' => true],
                'description' => 'string',
                'subcategory_id' => 'integer',
                'brand_id' => 'integer',
                'is_depreciable' => 'boolean',
                'needs_calibration' => 'boolean',
                'asset_type' => 'string'
            ];

            // Format request data using DataFormatter
            $data = DataFormatter::formatRequestData($request, $fields);

            // Add the ID to the data
            $data['asset_master_id'] = $id;

            // Check if the image should be removed
            if ($request->has('remove_image')) {
                $data['remove_image'] = true;
            }

            if ($request->hasFile('image_file')) {
                // For file uploads, we can't use updateResource directly
                // because it doesn't handle multipart form data
                $multipartData = [];

                // Add asset data as form fields
                foreach ($data as $key => $value) {
                    // Convert boolean values to string
                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    } elseif (is_array($value)) {
                        $value = json_encode($value);
                    } elseif ($value === null) {
                        $value = '';
                    }

                    $multipartData[] = ['name' => $key, 'contents' => (string)$value];
                }

                // Add the image file
                $multipartData[] = [
                    'name' => 'image_file',
                    'contents' => fopen($request->file('image_file')->getPathname(), 'r'),
                    'filename' => $request->file('image_file')->getClientOriginalName()
                ];

                $result = $this->apiService->request('PUT', "/asset-masters/{$id}", [
                    'multipart' => $multipartData
                ]);

                // Handle auth errors
                $authError = $this->handleAuthError($result, $request);
                if ($authError) {
                    return $authError;
                }

                // Check for API errors
                if (!isset($result['success']) || $result['success'] !== true) {
                    $errorData = $result['errors'] ?? 'Gagal memperbarui aset master';
                    $errorMessage = DataFormatter::formatErrorMessage($errorData);

                    // Handle AJAX request
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage,
                            'errors' => $result['errors'] ?? ['general' => 'Gagal memperbarui aset master']
                        ]);
                    }

                    return redirect()->back()
                        ->withInput()
                        ->with('error', $errorMessage);
                }

                // Handle AJAX request on success
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => $result['message'] ?? 'Aset master berhasil diperbarui',
                        'data' => $result['data'] ?? null
                    ]);
                }

                return redirect()->route('view-asset-master', ['id' => $id])
                    ->with('success', $result['message'] ?? 'Aset master berhasil diperbarui');

            } else {
                // For regular updates without file uploads, we can use updateResource
                return $this->updateResource(
                    $request,
                    "/asset-masters/{$id}",
                    $data,
                    'Aset master berhasil diperbarui',
                    'view-asset-master'
                );
            }
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui aset master: ' . $e->getMessage(),
                    'errors' => ['exception' => $e->getMessage()]
                ], 500);
            }

            return $this->handleException($e, $request, 'Asset.ViewMasterAsset');
        }
    }

    /**
     * Export master asset detail to PDF
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function exportViewMasterAssetPDF($id, Request $request)
    {
        try {
            // Fetch master asset details
            $result = $this->apiService->request('GET', "/asset-masters/{$id}");

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['data'])) {
                $errorData = $result['errors'] ?? 'Aset master tidak ditemukan';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);
                return redirect()->route('asset-master')->with('error', $errorMessage);
            }

            $masterAsset = $result['data'];

            // Convert asset image to base64
            if (!empty($masterAsset['reference_image_path'])) {
                try {
                    $backendUrl = api_url();
                    $imageUrl = $backendUrl . '/public' . $masterAsset['reference_image_path'];
                    $imageData = @file_get_contents($imageUrl);

                    if ($imageData !== false) {
                        $masterAsset['reference_image_base64'] = base64_encode($imageData);
                    }
                } catch (\Exception $e) {
                    // Image loading failed, continue without image
                }
            }

            return $this->generatePdf(
                'Asset.ViewMasterAssetPDF',
                [
                'masterAsset' => $masterAsset,
                'date_generated' => now()->format('d M Y H:i:s')
                ],
                'master_asset_detail_' . $id . '_' . now()->format('YmdHis') . '.pdf'
            );
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Asset.ViewMasterAsset');
        }
    }
}
