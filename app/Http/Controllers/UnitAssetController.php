<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Barryvdh\DomPDF\Facade\Pdf;


class UnitAssetController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Menampilkan halaman daftar aset.
     */
    public function index(Request $request)
    {
        try {
            // Mendapatkan parameter kueri
            $page = $request->query('page', 1);
            $limit = $request->query('limit', 10);
            $search = $request->query('search', '');
            $statusFilter = $request->query('current_status', '');
            $typeFilter = $request->query('asset_type', '');
            $sortOrder = $request->query('sort', '');
            $needsCalibration = $request->query('needs_calibration', null);

            // Membangun parameter kueri
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
            ];

            // Pengaturan pengurutan
            if (!empty($sortOrder)) {
                switch ($sortOrder) {
                    case 'newest':
                        $queryParams['sort_by'] = 'created_at';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    case 'oldest':
                        $queryParams['sort_by'] = 'created_at';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'name_asc':
                        $queryParams['sort'] = 'name_asc';
                        unset($queryParams['sort_by']);
                        unset($queryParams['sort_order']);
                        break;
                    case 'name_desc':
                        $queryParams['sort'] = 'name_desc';
                        unset($queryParams['sort_by']);
                        unset($queryParams['sort_order']);
                        break;
                    default:
                        $queryParams['sort_by'] = 'asset_id';
                        $queryParams['sort_order'] = 'desc';
                }
            } else {
                $queryParams['sort_by'] = 'asset_id';
                $queryParams['sort_order'] = 'desc';
            }

            // Menambahkan parameter pencarian jika disediakan
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Menambahkan filter status jika disediakan
            if (!empty($statusFilter)) {
                $queryParams['current_status'] = $statusFilter;
            }

            // Menambahkan filter tipe aset jika disediakan
            if (!empty($typeFilter)) {
                $queryParams['asset_type'] = $typeFilter;
            }

            // Menambahkan filter needs_calibration jika disediakan
            if ($needsCalibration !== null) {
                $queryParams['needs_calibration'] = $needsCalibration;
            }

            // Mengambil aset dari API
            $assetsResult = $this->apiService->request('GET', '/assets', [
                'query' => $queryParams
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($assetsResult['errors']) && is_string($assetsResult['errors']) &&
                in_array($assetsResult['errors'], ['auth_failed', 'session_expired'])) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $assetsResult['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }
                return redirect()->route('login')->with('error', is_string($assetsResult['errors']) ? $assetsResult['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API berdasarkan flag sukses
            if (!isset($assetsResult['success']) || $assetsResult['success'] !== true) {
                $errorData = $assetsResult['errors'] ?? 'Gagal mengambil data aset';

                // Format pesan kesalahan
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

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return view('Asset.UnitAsset', [
                    'assets' => [],
                    'assets_pagination' => null,
                    'error' => $errorMessage
                ]);
            }

            // Memproses data aset
            $assets = $assetsResult['data'] ?? [];

            // Format pagination untuk aset
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

            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'assets' => $assets,
                    'assets_pagination' => $assetsPagination
                ]);
            }

            return view('Asset.UnitAsset', [
                'assets' => $assets,
                'assets_pagination' => $assetsPagination
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal mengambil data aset: ' . $e->getMessage()
                ], 500);
            }

            return view('Asset.UnitAsset', [
                'assets' => [],
                'assets_pagination' => null,
                'error' => 'Gagal mengambil data aset: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Menyimpan aset baru.
     */
    public function storeAsset(Request $request)
    {
        try {
            // Menyiapkan data aset
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

            // Menambahkan data depresiasi jika ada
            if ($request->has('depreciation_method')) {
                $assetData['depreciation_method'] = $request->input('depreciation_method');
                $assetData['acquisition_cost'] = (float) $request->input('acquisition_cost');
                $assetData['salvage_value'] = (float) $request->input('salvage_value');
                $assetData['asset_life_months'] = (int) $request->input('asset_life_months');
                $assetData['date_acquired'] = $request->input('date_acquired');
            }

            // Menangani unggahan gambar jika ada
            if ($request->hasFile('image_file')) {
                $multipartData = [];

                // Menambahkan data aset sebagai field form
                foreach ($assetData as $key => $value) {
                    // Mengkonversi nilai dengan tepat untuk multipart
                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    } elseif (is_array($value)) {
                        $value = json_encode($value);
                    } elseif ($value === null) {
                        $value = ''; // Mengkonversi null ke string kosong untuk multipart
                    }

                    $multipartData[] = ['name' => $key, 'contents' => $value];
                }

                // Menambahkan file gambar
                $multipartData[] = [
                    'name' => 'image_file',
                    'contents' => fopen($request->file('image_file')->getPathname(), 'r'),
                    'filename' => $request->file('image_file')->getClientOriginalName()
                ];

                $options = ['multipart' => $multipartData];
                $result = $this->apiService->request('POST', '/assets', $options);
            } else {
                // Permintaan JSON standar jika tidak ada file yang diunggah
                $options = ['json' => $assetData];
                $result = $this->apiService->request('POST', '/assets', $options);
            }

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API atau respons tidak berhasil
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal membuat aset';

                // Format pesan kesalahan
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

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Berhasil dibuat
            return redirect()->route('assets')
                ->with('success', 'Aset berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat aset: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui aset yang ditentukan.
     */
    public function updateAsset(Request $request, $id)
    {
        try {
            // Menyiapkan data aset
            $assetData = [
                'asset_id' => $id,
                'asset_master_id' => (int) $request->input('asset_master_id'),
                'serial_number' => $request->input('serial_number'),
                'purchase_date' => $request->input('purchase_date'),
                'purchase_cost' => (float) $request->input('purchase_cost'),
                'warranty_end_date' => $request->input('warranty_end_date'),
                'user_id' => $request->input('user_id') ? (int) $request->input('user_id') : null,
                'current_status' => $request->input('current_status', 'available'),
                'condition' => $request->input('condition'),
                'room_id' => (int) $request->input('room_id')
            ];

            // Menambahkan data depresiasi jika ada
            if ($request->has('depreciation_method')) {
                $assetData['depreciation_method'] = $request->input('depreciation_method');
                $assetData['acquisition_cost'] = (float) $request->input('acquisition_cost');
                $assetData['salvage_value'] = (float) $request->input('salvage_value');
                $assetData['asset_life_months'] = (int) $request->input('asset_life_months');
                $assetData['date_acquired'] = $request->input('date_acquired');
            }

            // Menangani unggahan gambar jika ada
            if ($request->hasFile('image_file')) {
                $multipartData = [];

                // Menambahkan data aset sebagai field form
                foreach ($assetData as $key => $value) {
                    // Mengkonversi nilai dengan tepat untuk multipart
                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    } elseif (is_array($value)) {
                        $value = json_encode($value);
                    } elseif ($value === null) {
                        $value = ''; // Mengkonversi null ke string kosong untuk multipart
                    }

                    $multipartData[] = [
                        'name' => $key,
                        'contents' => $value
                    ];
                }

                // Menambahkan file gambar
                $multipartData[] = [
                    'name' => 'image_file',
                    'contents' => fopen($request->file('image_file')->getPathname(), 'r'),
                    'filename' => $request->file('image_file')->getClientOriginalName()
                ];

                $options = ['multipart' => $multipartData];
                $result = $this->apiService->request('PUT', "/assets/{$id}", $options);
            } else {
                // Permintaan JSON standar jika tidak ada file yang diunggah
                $options = ['json' => $assetData];
                $result = $this->apiService->request('PUT', "/assets/{$id}", $options);
            }

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API atau respons tidak berhasil
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui aset';

                // Format pesan kesalahan
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

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Berhasil diperbarui
            return redirect()->route('assets')
                ->with('success', 'Aset berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui aset: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus aset yang ditentukan.
     */
    public function destroyAsset($id)
    {
        try {
            $result = $this->apiService->request('DELETE', "/assets/{$id}");

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API atau respons tidak berhasil
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal menghapus aset';

                // Format pesan kesalahan
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

                return redirect()->back()
                    ->with('error', $errorMessage);
            }

            // Berhasil dihapus
            return redirect()->route('assets')
                ->with('success', 'Aset berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus aset: ' . $e->getMessage());
        }
    }

    /**
     * Mendapatkan data aset untuk diedit.
     */
    public function getAsset($id)
    {
        try {
            // Memeriksa apakah ini adalah permintaan pencarian pengguna
            if ($id === 'search' && request()->ajax() && request()->wantsJson()) {
                // Membangun parameter kueri untuk API pengguna
                $searchTerm = request()->input('search');
                $limit = request()->input('limit', 10);
                $offset = request()->input('offset', 0);

                $queryParams = [
                    'limit' => $limit,
                    'offset' => $offset,
                    'sort_by' => 'employee_number',
                    'sort_order' => 'asc'
                ];

                if (!empty($searchTerm)) {
                    $queryParams['search'] = $searchTerm;
                }

                // Memanggil API untuk mendapatkan pengguna
                $usersResult = $this->apiService->request('GET', '/users', [
                    'query' => $queryParams
                ]);

                // Memeriksa kesalahan
                if (isset($usersResult['errors']) || !isset($usersResult['success']) || $usersResult['success'] !== true) {
                    $errorMessage = $usersResult['errors'] ?? 'Gagal mendapatkan data pengguna';

                    return response()->json([
                        'success' => false,
                        'errors' => ['auth' => $errorMessage]
                    ], 401);
                }

                // Mengembalikan data pengguna
                return response()->json([
                    'success' => true,
                    'data' => $usersResult['data'] ?? [],
                    'pagination' => $usersResult['pagination'] ?? null
                ]);
            }

            // Mengambil aset dengan ID yang diberikan
            $result = $this->apiService->request('GET', "/assets/{$id}");

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Autentikasi gagal']
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API berdasarkan flag sukses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mendapatkan data aset';

                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], 500);
                }

                // Format pesan kesalahan
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

            $asset = $result['data'] ?? null;

            if (!$asset) {
                $errorMessage = 'Aset tidak ditemukan atau data respons tidak valid';

                if (request()->ajax()) {
                    return response()->json(['success' => false, 'errors' => ['general' => $errorMessage]], 404);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Untuk permintaan AJAX, kembalikan hanya data aset
            if (request()->ajax()) {
                // Jika kita memiliki user_id, ambil detail pengguna
                if (isset($asset['user_id']) && $asset['user_id']) {
                    try {
                        $userResult = $this->apiService->request('GET', "/users/{$asset['user_id']}");
                        if (isset($userResult['success']) && $userResult['success'] === true && isset($userResult['data'])) {
                            $asset['user'] = $userResult['data'];
                        }
                    } catch (\Exception $e) {
                        // Lanjutkan meskipun gagal mengambil detail pengguna
                    }
                }

                // Mengembalikan hanya data aset
                return response()->json([
                    'success' => true,
                    'data' => $asset
                ]);
            }

            // Mengembalikan tampilan lengkap dengan data aset untuk permintaan non-AJAX
            return view('Asset.AssetDetail', ['asset' => $asset]);
        } catch (\Exception $e) {
            $errorMessage = 'Gagal mendapatkan data aset: ' . $e->getMessage();

            if (request()->ajax()) {
                return response()->json(['success' => false, 'errors' => ['exception' => $errorMessage]], 500);
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
            // Single asset request
            $result = $this->apiService->request('GET', "/assets/barcode/generate/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during barcode generation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Failed to generate barcode';

                \Log::warning('Error during barcode generation:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], 400);
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
                    'errors' => ['exception' => $errorMessage]
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
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during QR generation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to generate QR codes';

                \Log::error('API error in bulk QR generation:', [
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
            // Use default value for quantity
            $quantity = 1;

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
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during QR generation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            if (isset($result['success']) && $result['success'] === true && isset($result['data'])) {
                \Log::info('Successfully generated QR codes from API', [
                    'count' => count($result['data']),
                    'asset_ids_in_response' => array_column($result['data'], 'asset_id')
                ]);

                $qrData = $result['data'];
            } else {
                $errorData = $result['errors'] ?? 'Failed to generate QR codes';

                \Log::error('Failed to generate QR codes from API', [
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
     * Print QR codes directly without PDF
     */
    public function printQRCodesDirect(Request $request)
    {
        try {
            \Log::info('Attempting to print QR codes directly', [
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

            $qrSize = $request->input('qr_size', 80);
            // Use default value for quantity
            $quantity = 1;

            // Determine container width based on qr_size
            $containerWidth = 80;
            if ($qrSize == 100) {
                $containerWidth = 100;
            } elseif ($qrSize == 80) {
                $containerWidth = 80;
            } elseif ($qrSize == 60) {
                $containerWidth = 60;
            }

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
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during QR generation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            if (isset($result['success']) && $result['success'] === true && isset($result['data'])) {
                \Log::info('Successfully generated QR codes from API', [
                    'count' => count($result['data']),
                    'asset_ids_in_response' => array_column($result['data'], 'asset_id')
                ]);

                $qrData = $result['data'];
            } else {
                $errorData = $result['errors'] ?? 'Failed to generate QR codes';

                \Log::error('Failed to generate QR codes from API', [
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

            if (empty($qrData)) {
                return redirect()->back()->with('error', 'No QR code data returned from the API');
            }

            // Log what we have before processing
            \Log::info('Processing QR data for direct printing:', [
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

            // Always use the label printing view
            return view('Asset.qrcode_print_label', [
                'qrData' => $qrData,
                'qrSize' => $qrSize,
                'containerWidth' => $containerWidth
            ]);
        } catch (\Exception $e) {
            $errorMessage = 'Failed to print QR codes: ' . $e->getMessage();

            \Log::error('Exception during QR direct printing:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Mengimpor aset dari file Excel/CSV.
     */
    public function importAssets(Request $request)
    {
        try {
            if ($request->hasFile('excel_file_upload')) {
                // Menggunakan data formulir multipart untuk mengirim file
                $multipartData = [];

                // Menambahkan file Excel
                $multipartData[] = [
                    'name' => 'excel_file',
                    'contents' => fopen($request->file('excel_file_upload')->getPathname(), 'r'),
                    'filename' => $request->file('excel_file_upload')->getClientOriginalName()
                ];

                // Mengirim file ke API
                $result = $this->apiService->request('POST', '/assets/import', [
                    'multipart' => $multipartData
                ]);
            } else if ($request->has('excel_data')) {
                // Data Excel dalam bentuk JSON
                $excelData = $request->input('excel_data');

                if (empty($excelData)) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Tidak ada data valid untuk diimpor',
                            'errors' => ['import' => 'Tidak ada data valid untuk diimpor']
                        ], 400);
                    }
                    return redirect()->back()->with('error', 'Tidak ada data valid untuk diimpor');
                }

                // Mendekode data JSON
                $parsedData = json_decode($excelData, true);

                if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsedData) || empty($parsedData)) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Format data tidak valid untuk diimpor',
                            'errors' => ['import' => 'Format data tidak valid untuk diimpor']
                        ], 400);
                    }
                    return redirect()->back()->with('error', 'Format data tidak valid untuk diimpor');
                }

                // Mengirim data ke API
                $result = $this->apiService->request('POST', '/assets/import', [
                    'json' => [
                        'data' => $parsedData
                    ]
                ]);
            } else {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada file Excel atau data yang disediakan',
                        'errors' => ['import' => 'Tidak ada file Excel atau data yang disediakan']
                    ], 400);
                }
                return redirect()->back()->with('error', 'Tidak ada file Excel atau data yang disediakan');
            }

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['errors'] ?? 'Autentikasi gagal',
                        'errors' => ['auth' => $result['errors'] ?? 'Autentikasi gagal']
                    ], 401);
                }
                return redirect()->route('login')->with('error', $result['errors'] ?? 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API atau respons tidak berhasil
            if (
                isset($result['errors']) ||
                (isset($result['success']) && $result['success'] === false)
            ) {
                // Siapkan pesan error dasar
                $errorMessage = 'Gagal mengimpor aset';
                $errorDetails = [];

                // Ekstrak pesan error utama jika ada
                if (isset($result['errors']) && !empty($result['errors'])) {
                    if (is_string($result['errors'])) {
                        $errorMessage = $result['errors'];
                    } elseif (is_array($result['errors'])) {
                        // Jika errors berupa array, ambil pesan-pesan error
                        foreach ($result['errors'] as $key => $error) {
                            if (is_string($error)) {
                                $errorDetails[] = $error;
                            } elseif (is_array($error) && isset($error['message'])) {
                                $errorDetails[] = $error['message'];
                            }
                        }

                        if (!empty($errorDetails)) {
                            $errorMessage = implode('; ', $errorDetails);
                        }
                    }
                }

                // Periksa untuk error lebih detail di data
                if (isset($result['data']['errors']) && is_array($result['data']['errors']) && !empty($result['data']['errors'])) {
                    $errorDetails = [];

                    foreach ($result['data']['errors'] as $error) {
                        if (is_string($error)) {
                            $errorDetails[] = $error;
                        } elseif (is_array($error)) {
                            if (isset($error['row']) && isset($error['reason'])) {
                                $errorDetailMsg = "Baris {$error['row']}: ";

                                // Tambahkan asset_code jika tersedia
                                if (isset($error['asset_code'])) {
                                    $errorDetailMsg .= "{$error['asset_code']} - ";
                                }
                                // Tambahkan asset_name jika tersedia
                                elseif (isset($error['asset_name'])) {
                                    $errorDetailMsg .= "{$error['asset_name']} - ";
                                }
                                // Tambahkan serial_number jika tersedia
                                elseif (isset($error['serial_number'])) {
                                    $errorDetailMsg .= "SN: {$error['serial_number']} - ";
                                }
                                // Tambahkan asset_master_code jika tersedia
                                elseif (isset($error['asset_master_code'])) {
                                    $errorDetailMsg .= "{$error['asset_master_code']} - ";
                                }

                                $errorDetailMsg .= $error['reason'];
                                $errorDetails[] = $errorDetailMsg;
                            } elseif (isset($error['message'])) {
                                $errorDetails[] = $error['message'];
                            } elseif (isset($error['field'], $error['reason'])) {
                                $errorDetails[] = "Field {$error['field']}: {$error['reason']}";
                            }
                        }
                    }

                    // Untuk respons AJAX, kirim array error details
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage,
                            'errors' => ['import' => $errorMessage],
                            'data' => [
                                'errors' => $errorDetails,
                                'total' => $result['data']['total'] ?? 0,
                                'success' => $result['data']['success'] ?? 0,
                                'failed' => $result['data']['failed'] ?? $result['data']['total'] ?? 0
                            ]
                        ], 400);
                    }

                    // Untuk respons non-AJAX, format sebagai HTML
                    if (!empty($errorDetails)) {
                        $errorMessage .= "<ul class='list-disc pl-4 mt-2'>";
                        foreach ($errorDetails as $detail) {
                            $errorMessage .= "<li>{$detail}</li>";
                        }
                        $errorMessage .= "</ul>";
                    }
                } else {
                    // Jika tidak ada detail error, kirim respons standar
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage,
                            'errors' => ['import' => $errorMessage]
                        ], 400);
                    }
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Ekstrak hasil impor
            $totalImported = $result['data']['total'] ?? 0;
            $successCount = $result['data']['success'] ?? 0;
            $failedCount = $result['data']['failed'] ?? 0;

            // Menyiapkan pesan sukses
            $successMessage = "Berhasil mengimpor {$successCount} aset";
            if ($failedCount > 0) {
                $successMessage .= " ({$failedCount} gagal)";
            }

            // Mengembalikan respons berdasarkan jenis permintaan
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

            // Mengalihkan kembali dengan pesan sukses untuk permintaan non-AJAX
            return redirect()->route('assets')->with('success', $successMessage);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengimpor aset: ' . $e->getMessage(),
                    'errors' => ['exception' => 'Gagal mengimpor aset: ' . $e->getMessage()]
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal mengimpor aset: ' . $e->getMessage());
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
            $typeFilter = $request->input('asset_type', '');
            $statusFilter = $request->input('current_status', '');
            $sortOrder = $request->input('sort', 'newest');

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
                $query['asset_type'] = $typeFilter;
            }

            // Fetch assets for PDF
            $assetsResult = $this->apiService->request('GET', '/assets', [
                'query' => $query
            ]);

            // Check for auth errors
            if (isset($assetsResult['errors']) && is_string($assetsResult['errors']) &&
                in_array($assetsResult['errors'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', is_string($assetsResult['errors']) ? $assetsResult['errors'] : 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($assetsResult['success']) || $assetsResult['success'] !== true) {
                $errorData = $assetsResult['errors'] ?? 'Failed to fetch unit assets data';

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

            // Stream the PDF to browser
            return $pdf->stream('unit_assets_report_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to export Unit Assets as PDF: ' . $e->getMessage());
        }
    }
}
