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

            // Check for auth errors
            if (isset($masterAssetsResult['errors']) && is_string($masterAssetsResult['errors']) &&
                in_array($masterAssetsResult['errors'], ['auth_failed', 'session_expired'])) {
                $errorMessage = $masterAssetsResult['errors'] ?? 'Autentikasi gagal';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => is_string($errorMessage) ? $errorMessage : 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($errorMessage) ? $errorMessage : 'Autentikasi gagal');
            }

            // Check for API errors based on status flag
            if (isset($masterAssetsResult['success']) && $masterAssetsResult['success'] !== true) {
                $errorData = $masterAssetsResult['errors'] ?? 'Gagal mengambil data';

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
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Gagal mengambil data: ' . $e->getMessage()
                ], 500);
            }

            return view('Asset.MasterAsset', [
                'masterAssets' => [],
                'masterAssets_pagination' => null,
                'error' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created master asset.
     */
    public function storeMasterAsset(Request $request)
    {
        try {
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

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', is_string($result['errors'] ?? 'Autentikasi gagal') ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                // Format error message properly before passing to session
                $errorMessage = 'Gagal membuat aset master';

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

            return redirect()->route('asset-master')
                ->with('success', 'Aset master berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat aset master: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified master asset.
     */
    public function updateMasterAsset(Request $request, $id)
    {
        try {
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

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', is_string($result['errors'] ?? 'Autentikasi gagal') ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                // Format error message properly before passing to session
                $errorMessage = 'Gagal memperbarui aset master';

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

            return redirect()->route('asset-master')
                ->with('success', 'Aset master berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui aset master: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified master asset.
     */
    public function destroyMasterAsset($id)
    {
        try {
            $result = $this->apiService->request('DELETE', "/asset-masters/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', is_string($result['errors'] ?? 'Autentikasi gagal') ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                return redirect()->back()
                    ->with('error', is_array($result['errors'] ?? 'Gagal menghapus aset master') ? implode(', ', (array)$result['errors']) : ($result['errors'] ?? 'Gagal menghapus aset master'));
            }

            return redirect()->route('asset-master')
                ->with('success', 'Aset master berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus aset master: ' . $e->getMessage());
        }
    }

    /**
     * Get a single master asset for editing.
     */
    public function getMasterAsset($id)
    {
        try {
            // Fetch the master asset with the given ID
            $result = $this->apiService->request('GET', "/asset-masters/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Autentikasi gagal']
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors'] ?? 'Autentikasi gagal') ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors based on status flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil data aset master';

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
                $errorMessage = 'Aset master tidak ditemukan atau data respons tidak valid';

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
            $errorMessage = 'Gagal mengambil data aset master: ' . $e->getMessage();

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
            $sortOrder = $request->input('sort', 'newest');

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

            // Fetch master assets for PDF
            $masterAssetsResult = $this->apiService->request('GET', '/asset-masters', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($masterAssetsResult['errors']) && is_string($masterAssetsResult['errors']) &&
                in_array($masterAssetsResult['errors'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', is_string($masterAssetsResult['errors'] ?? 'Autentikasi gagal') ? $masterAssetsResult['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors based on status flag
            if (!isset($masterAssetsResult['success']) || $masterAssetsResult['success'] !== true) {
                $errorData = $masterAssetsResult['errors'] ?? 'Gagal mengambil data aset master';
                return redirect()->back()->with('error', is_array($errorData) ? implode(', ', (array)$errorData) : $errorData);
            }

            // Get master assets data
            $masterAssets = $masterAssetsResult['data'] ?? [];

            // Generate PDF
            $pdf = Pdf::loadView('Asset.MasterAssetPDF', [
                'masterAssets' => $masterAssets,
                'search' => $search,
                'assetType' => $assetType,
                'sort' => $sortOrder,
                'date_generated' => now()->format('d M Y H:i:s')
            ]);

            // Stream the PDF to browser
            return $pdf->stream('laporan_aset_master_' . now()->format('YmdHis') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor Aset Master sebagai PDF: ' . $e->getMessage());
        }
    }

    /**
     * Import master assets from Excel data.
     */
    public function importMasterAsset(Request $request)
    {
        try {
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
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'errors' => ['import' => 'Tidak ada data valid untuk diimpor']
                        ], 400);
                    }
                    return redirect()->back()->with('error', 'Tidak ada data valid untuk diimpor');
                }

                // Decode the JSON data
                $parsedData = json_decode($excelData, true);

                if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsedData) || empty($parsedData)) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'errors' => ['import' => 'Format data tidak valid untuk diimpor']
                        ], 400);
                    }
                    return redirect()->back()->with('error', 'Format data tidak valid untuk diimpor');
                }

                // Send data to API
                $result = $this->apiService->request('POST', '/asset-masters/import', [
                    'json' => [
                        'data' => $parsedData
                    ]
                ]);
            } else {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['import' => 'Tidak ada file Excel atau data yang disediakan']
                    ], 400);
                }
                return redirect()->back()->with('error', 'Tidak ada file Excel atau data yang disediakan');
            }

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }
                return redirect()->route('login')->with('error', is_string($result['errors'] ?? 'Autentikasi gagal') ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal mengimpor aset master';

                // Handle JSON response for API requests
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData,
                        'data' => $result['data'] ?? null
                    ], 400);
                }

                // Format error message for detailed errors
                $errorMessage = 'Gagal mengimpor aset master: ';

                if (is_array($errorData)) {
                    $errorDetails = [];

                    foreach ($errorData as $key => $error) {
                        if (is_array($error)) {
                            if (isset($error['message'])) {
                                $errorDetails[] = $error['message'];
                            } else if (isset($error['asset_name'], $error['reason'])) {
                                $errorDetails[] = "\"{$error['asset_name']}\" - {$error['reason']}";
                            } else if (isset($error['row'], $error['reason'])) {
                                $errorDetails[] = "Baris {$error['row']}: {$error['reason']}";
                            } else {
                                $errorDetails[] = implode(', ', $error);
                            }
                        } else if (is_string($error)) {
                            $errorDetails[] = $error;
                        }
                    }

                    // Format as HTML list for non-AJAX response
                    if (!empty($errorDetails)) {
                        $errorMessage .= "<ul class='list-disc pl-4 mt-2'>";
                        foreach ($errorDetails as $detail) {
                            $errorMessage .= "<li>{$detail}</li>";
                        }
                        $errorMessage .= "</ul>";
                    }
                } else if (is_string($errorData)) {
                    $errorMessage = $errorData;
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Extract import results
            $totalImported = $result['data']['total'] ?? 0;
            $successCount = $result['data']['success'] ?? 0;
            $failedCount = $result['data']['failed'] ?? 0;

            // Prepare success message
            $successMessage = "Berhasil mengimpor {$successCount} aset master";
            if ($failedCount > 0) {
                $successMessage .= " ({$failedCount} gagal)";
            }

            // Return response based on request type
            if ($request->expectsJson()) {
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
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengimpor aset master: ' . $e->getMessage()]
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal mengimpor aset master: ' . $e->getMessage());
        }
    }
}
