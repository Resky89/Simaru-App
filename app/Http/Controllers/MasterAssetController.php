<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DataFormatter;
use App\Http\Controllers\Traits\ApiResourceOperations;
use App\Http\Controllers\Traits\ExportableToPdf;

class MasterAssetController extends Controller
{
    use ApiResourceOperations, ExportableToPdf;

    /**
     * Display a listing of master assets.
     */
    public function index(Request $request)
    {
        $extraParams = [];

        // Asset type filter
        if ($request->filled('type')) {
            $extraParams['asset_type'] = $request->input('type');
        }

        // Brand filter
        if ($request->filled('brand_id')) {
            $extraParams['brand_id'] = (int) $request->input('brand_id');
        }

        // Subcategory filter
        if ($request->filled('subcategory_id')) {
            $extraParams['subcategory_id'] = (int) $request->input('subcategory_id');
        }

        // Custom sort mappings
        $sortMappings = [
            'oldest' => ['sort_by' => 'asset_master_id', 'sort_order' => 'asc'],
            'name_asc' => ['sort_by' => 'asset_name', 'sort_order' => 'asc'],
            'name_desc' => ['sort_by' => 'asset_name', 'sort_order' => 'desc'],
            'code_asc' => ['sort_by' => 'asset_master_code', 'sort_order' => 'asc'],
            'code_desc' => ['sort_by' => 'asset_master_code', 'sort_order' => 'desc'],
        ];

        return $this->getResourceList(
            $request,
            '/asset-masters',
            'masterAssets',
            'Asset.MasterAsset',
            'asset_master_id',
            $extraParams,
            $sortMappings
        );
    }

    /**
     * Store a newly created master asset.
     */
    public function storeMasterAsset(Request $request)
    {
        try {
            $fields = [
                'asset_name' => ['type' => 'string', 'required' => true],
                'description' => 'string',
                'subcategory_id' => 'integer',
                'brand_id' => 'integer',
                'is_depreciable' => 'boolean',
                'needs_calibration' => 'boolean',
                'asset_type' => 'string',
                'calibration_period' => 'integer',
                'maintenance_period' => 'integer',
                'warranty_period' => 'integer',
                'estimated_useful_life' => 'integer',
                'depreciation_method' => 'string',
                'salvage_value_percentage' => 'float'
            ];

            $data = DataFormatter::formatRequestData($request, $fields);

            // Handle image upload if present
            if ($request->hasFile('image_file')) {
                $multipartData = [];

                // Add asset data as form fields
                foreach ($data as $key => $value) {
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

                $result = $this->apiService->request('POST', '/asset-masters', [
                    'multipart' => $multipartData
                ]);
            } else {
                $result = $this->apiService->request('POST', '/asset-masters', [
                    'json' => $data
                ]);
            }

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat aset master';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            return redirect()->route('asset-master')
                ->with('success', $result['message'] ?? 'Aset master berhasil dibuat');
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Asset.MasterAsset');
        }
    }

    /**
     * Update the specified master asset.
     */
    public function updateMasterAsset(Request $request, $id)
    {
        try {
            $fields = [
                'asset_name' => ['type' => 'string', 'required' => true],
                'description' => 'string',
                'subcategory_id' => 'integer',
                'brand_id' => 'integer',
                'is_depreciable' => 'boolean',
                'needs_calibration' => 'boolean',
                'asset_type' => 'string',
                'calibration_period' => 'integer',
                'maintenance_period' => 'integer',
                'warranty_period' => 'integer',
                'estimated_useful_life' => 'integer',
                'depreciation_method' => 'string',
                'salvage_value_percentage' => 'float'
            ];

            $data = DataFormatter::formatRequestData($request, $fields);

            // ID is always required for update
            $data['asset_master_id'] = $id;

            // Check if the image should be removed
            if ($request->has('remove_image')) {
                $data['remove_image'] = true;
            }

            // Handle image upload if present
            if ($request->hasFile('image_file')) {
                $multipartData = [];

                // Add asset data as form fields
                foreach ($data as $key => $value) {
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

                $result = $this->apiService->request('PUT', "/asset-masters/{$id}", [
                    'multipart' => $multipartData
                ]);
            } else {
                $result = $this->apiService->request('PUT', "/asset-masters/{$id}", [
                    'json' => $data
                ]);
            }

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui aset master';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            return redirect()->route('asset-master')
                ->with('success', $result['message'] ?? 'Aset master berhasil diperbarui');
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Asset.MasterAsset');
        }
    }

    /**
     * Remove the specified master asset.
     */
    public function destroyMasterAsset($id)
    {
        return $this->deleteResource(
            request(),
            "/asset-masters/{$id}",
            'Aset master berhasil dihapus',
            'asset-master'
        );
    }

    /**
     * Get a single master asset for editing.
     */
    public function getMasterAsset($id)
    {
        try {
            $result = $this->getResource(
                request(),
                "/asset-masters/{$id}",
                'masterAsset',
                'Asset.EditMasterAsset'
            );

            if (request()->ajax() && $result instanceof \Illuminate\Http\JsonResponse) {
                $responseData = json_decode($result->getContent(), true);

                if (isset($responseData['masterAsset'])) {

                    // Return complete data set for the modal
                    return response()->json([
                        'masterAsset' => $responseData['masterAsset'],
                    ]);
                }
            }

            return $result;
        } catch (\Exception $e) {
            return $this->handleException($e, request(), 'Asset.MasterAsset');
        }
    }

    /**
     * Export master assets to PDF.
     */
    public function exportMasterAssetPDF(Request $request)
    {
        try {
            // Ambil parameter filter yang sama dengan index
            $queryParams = [];

            if ($request->filled('search')) {
                $queryParams['search'] = $request->input('search');
            }

            if ($request->filled('type')) {
                $queryParams['asset_type'] = $request->input('type');
            }

            // Tidak menggunakan pagination untuk export
            $queryParams['pagination'] = 'false';
            $queryParams['limit'] = 1000;

            // Mendapatkan sorting
            $sortOrder = $request->input('sort', 'newest');
            $sortMappings = [
                'oldest' => ['sort_by' => 'asset_master_id', 'sort_order' => 'asc'],
                'name_asc' => ['sort_by' => 'asset_name', 'sort_order' => 'asc'],
                'name_desc' => ['sort_by' => 'asset_name', 'sort_order' => 'desc'],
                'code_asc' => ['sort_by' => 'asset_master_code', 'sort_order' => 'asc'],
                'code_desc' => ['sort_by' => 'asset_master_code', 'sort_order' => 'desc'],
            ];

            // Menerapkan sorting
            if (!empty($sortOrder) && isset($sortMappings[$sortOrder])) {
                $queryParams = array_merge($queryParams, $sortMappings[$sortOrder]);
            } else {
                $queryParams['sort_by'] = 'asset_master_id';
                $queryParams['sort_order'] = 'desc';
            }

            // Get data for export
            $masterAssetsResult = $this->apiService->request('GET', '/asset-masters', [
                'query' => $queryParams
            ]);

            // Handle errors
            $authError = $this->handleAuthError($masterAssetsResult, $request);
            if ($authError) {
                return $authError;
            }

            $apiError = $this->handleApiError($masterAssetsResult, $request, 'Asset.MasterAsset', 'Gagal mengambil data untuk ekspor');
            if ($apiError) {
                return $apiError;
            }

            $masterAssets = $masterAssetsResult['data'] ?? [];

            // Generate filename
            $timestamp = date('YmdHis');
            $filename = "laporan_aset_master_{$timestamp}.pdf";

            // Generate PDF
            return $this->generatePdf(
                'Asset.MasterAssetPDF',
                [
                    'masterAssets' => $masterAssets,
                    'search' => $request->input('search', ''),
                    'assetType' => $request->input('type', ''),
                    'sort' => $sortOrder,
                    'date_generated' => date('d M Y H:i:s')
                ],
                $filename,
                'landscape'
            );
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Asset.MasterAsset');
        }
    }

    /**
     * Import master assets from Excel/CSV.
     */
    public function importMasterAsset(Request $request)
    {
        try {
            if (!$request->hasFile('excel_file_upload') && !$request->has('excel_data')) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Tidak ada file atau data yang diunggah'
                ], 400);
            }

            if ($request->hasFile('excel_file_upload')) {
                $file = $request->file('excel_file_upload');

                // Validasi file
                if (!in_array($file->getClientOriginalExtension(), ['csv', 'xlsx', 'xls'])) {
                    return response()->json([
                        'success' => false,
                        'errors' => 'Format file tidak didukung. Gunakan CSV, XLSX, atau XLS.'
                    ], 400);
                }

                // Kirim file ke API
                $result = $this->apiService->request('POST', '/asset-masters/import', [
                    'multipart' => [
                        [
                            'name' => 'excel_file',
                            'contents' => fopen($file->getPathname(), 'r'),
                            'filename' => $file->getClientOriginalName()
                        ]
                    ]
                ]);
            } else {
                // Menggunakan data Excel dalam bentuk JSON
                $excelData = $request->input('excel_data');

                if (empty($excelData)) {
                    return response()->json([
                        'success' => false,
                        'errors' => 'Tidak ada data valid untuk diimpor'
                    ], 400);
                }

                // Mendekode data JSON
                $parsedData = json_decode($excelData, true);

                if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsedData) || empty($parsedData)) {
                    return response()->json([
                        'success' => false,
                        'errors' => 'Format data tidak valid untuk diimpor'
                    ], 400);
                }

                // Mengirim data ke API
                $result = $this->apiService->request('POST', '/asset-masters/import', [
                    'json' => [
                        'data' => $parsedData
                    ]
                ]);
            }

            // Handle auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengimpor data';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                // Handle detailed error data
                if (isset($result['data']['errors']) && is_array($result['data']['errors'])) {
                    $errorDetails = [];

                    foreach ($result['data']['errors'] as $error) {
                        if (is_array($error)) {
                            if (isset($error['row'], $error['reason'])) {
                                $errorDetail = "Baris {$error['row']}: ";

                                if (isset($error['asset_name'])) {
                                    $errorDetail .= "{$error['asset_name']} - ";
                                }

                                $errorDetail .= $error['reason'];
                                $errorDetails[] = $errorDetail;
                            } elseif (isset($error['message'])) {
                                $errorDetails[] = $error['message'];
                            }
                        } elseif (is_string($error)) {
                            $errorDetails[] = $error;
                        }
                    }

                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $errorDetails,
                        'data' => [
                            'total' => $result['data']['total'] ?? 0,
                            'success' => $result['data']['success'] ?? 0,
                            'failed' => $result['data']['failed'] ?? $result['data']['total'] ?? 0
                        ]
                    ], 400);
                }

                return response()->json([
                    'success' => false,
                    'errors' => $errorMessage
                ], 400);
            }

            // Success response
            $importedCount = $result['data']['total'] ?? 0;
            $successCount = $result['data']['success'] ?? 0;
            $failedCount = $result['data']['failed'] ?? 0;

            $successMessage = "Berhasil mengimpor {$successCount} aset master";
            if ($failedCount > 0) {
                $successMessage .= " ({$failedCount} gagal)";
            }

            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'data' => [
                    'total' => $importedCount,
                    'success' => $successCount,
                    'failed' => $failedCount
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal memproses permintaan: ' . $e->getMessage()
            ], 500);
        }
    }
}
