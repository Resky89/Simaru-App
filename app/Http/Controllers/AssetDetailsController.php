<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Barryvdh\DomPDF\Facade\Pdf;

class AssetDetailsController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Menampilkan detail aset tertentu.
     *
     * @param int $id ID aset
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            // Mengambil aset dengan ID yang diberikan
            $result = $this->apiService->request('GET', "/assets/{$id}");

            // Log API response for debugging
            \Log::info('API response for asset details:', [
                'api_response_status' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'asset_id' => $id
            ]);

            // Check for auth errors
            if (isset($result['errors']) && (is_array($result['errors']) &&
                (isset($result['errors']['auth_failed']) || isset($result['errors']['session_expired'])) ||
                in_array($result['errors'], ['auth_failed', 'session_expired']))) {
                \Log::warning('Authentication error during asset details retrieval:', [
                    'errors' => $result['errors']
                ]);

                return redirect()->route('login')->with('error', 'Authentication failed');
            }

            // Memeriksa kesalahan API berdasarkan flag sukses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil detail aset';

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
                return redirect()->back()->with('error', $errorMessage);
            }

            // Membuat kode QR untuk aset menggunakan endpoint API bulk
            if (isset($asset['asset_id'])) {
                try {
                    // Buat array dengan ID aset tunggal
                    $assetIds = [$asset['asset_id']];

                    // Memanggil API pembuatan kode QR bulk
                    $qrResult = $this->apiService->request('POST', "/assets/qr/generate-bulk", [
                        'json' => [
                            'asset_ids' => $assetIds
                        ]
                    ]);

                    // Jika respons berhasil dengan data, dapatkan kode QR untuk aset
                    if (isset($qrResult['success']) && $qrResult['success'] === true &&
                        isset($qrResult['data']) && is_array($qrResult['data']) && count($qrResult['data']) > 0) {

                        // Temukan data QR untuk aset ini
                        foreach ($qrResult['data'] as $qrData) {
                            if (isset($qrData['asset_id']) && $qrData['asset_id'] == $asset['asset_id']) {
                                // Jika ada kode QR base64 dalam respons
                                if (isset($qrData['qr_base64'])) {
                                    $asset['qr_base64'] = $qrData['qr_base64'];
                                    break;
                                }
                                // Atau jika ada URL QR yang perlu dikonversi ke base64
                                else if (isset($qrData['qr_url'])) {
                                    try {
                                        // Dapatkan URL API yang benar dari konfigurasi backend
                                        $backendUrl = rtrim(config('app.backend_url'), '/');
                                        $imageUrl = $backendUrl . "/public" . $qrData['qr_url'];

                                        // Coba mendapatkan konten gambar
                                        $imageData = @file_get_contents($imageUrl);
                                        if ($imageData !== false) {
                                            $asset['qr_base64'] = 'data:image/png;base64,' . base64_encode($imageData);
                                        }
                                    } catch (\Exception $qrImageEx) {
                                        // Lanjutkan tanpa kode QR
                                    }
                                    break;
                                }
                            }
                        }
                    } else if (isset($asset['qr_code']) && !empty($asset['qr_code'])) {
                        // Jika kode QR sudah disediakan dalam data aset
                        try {
                            $backendUrl = rtrim(config('app.backend_url'), '/');
                            $imageUrl = $backendUrl . "/public" . $asset['qr_code'];

                            $imageData = @file_get_contents($imageUrl);
                            if ($imageData !== false) {
                                $asset['qr_base64'] = 'data:image/png;base64,' . base64_encode($imageData);
                            }
                        } catch (\Exception $qrImageEx) {
                            // Lanjutkan tanpa kode QR
                        }
                    }
                } catch (\Exception $qrEx) {
                    // Lanjutkan tanpa kode QR jika gagal
                }
            }

            // Ambil master aset untuk dropdown master aset
            $assetMastersResult = $this->apiService->request('GET', '/asset-masters', [
                'query' => [
                    'limit' => 1000,
                    'sort_by' => 'asset_master_id',
                    'sort_order' => 'asc'
                ]
            ]);
            $assetMasters = $assetMastersResult['data'] ?? [];

            // Konversi gambar aset ke base64
            if (!empty($asset['image_path'])) {
                try {
                    $backendUrl = config('app.backend_url', 'https://web-magangunbin2025.rsummi.co.id/api');
                    $imageUrl = $backendUrl . '/public/images/' . basename($asset['image_path']);
                    $imageData = file_get_contents($imageUrl);

                    if ($imageData !== false) {
                        $asset['image_base64'] = base64_encode($imageData);
                    }
                } catch (\Exception $e) {
                    // Lanjutkan tanpa gambar jika gagal
                }
            }

            // Konversi kode QR ke base64 jika diperlukan
            if (!empty($asset['qr_code_path'])) {
                try {
                    $backendUrl = config('app.backend_url', 'https://web-magangunbin2025.rsummi.co.id/api');
                    $qrPath = $backendUrl . '/public/qrcodes/' . basename($asset['qr_code_path']);
                    $qrData = file_get_contents($qrPath);
                    if ($qrData !== false) {
                        $asset['qr_base64'] = base64_encode($qrData);
                    }
                } catch (\Exception $e) {
                    // Lanjutkan tanpa kode QR jika gagal
                }
            }

            // Kembalikan tampilan dengan detail aset
            return view('Asset.AssetDetail', [
                'asset' => $asset,
                'assetMasters' => $assetMasters,
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengambil detail aset: ' . $e->getMessage());
        }
    }

    /**
     * Mendapatkan detail aset sebagai JSON (untuk permintaan API)
     *
     * @param int $id ID aset
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssetJson($id)
    {
        try {
            // Mengambil aset dengan ID yang diberikan
            $result = $this->apiService->request('GET', "/assets/{$id}");

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Autentikasi gagal'
                ], 401);
            }

            // Memeriksa kesalahan API
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil detail aset';

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

                return response()->json([
                    'success' => false,
                    'errors' => $errorMessage
                ], 400);
            }

            // Mengembalikan data aset sebagai JSON
            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal mengambil detail aset: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memperbarui aset yang ditentukan.
     *
     * @param Request $request
     * @param int $id ID aset
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAsset(Request $request, $id)
    {
        try {
            // Menyiapkan data aset dengan format baru (struktur datar)
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

            // Tambahkan bidang depresiasi langsung tanpa memeriksa is_depreciable
            if ($request->has('depreciation_method')) {
                $assetData['depreciation_method'] = $request->input('depreciation_method');
                $assetData['acquisition_cost'] = (float) $request->input('acquisition_cost');
                $assetData['salvage_value'] = (float) $request->input('salvage_value');
                $assetData['asset_life_months'] = (int) $request->input('asset_life_months');
                $assetData['date_acquired'] = $request->input('date_acquired');
            }

            // Tangani unggahan gambar jika ada
            if ($request->hasFile('image_file')) {
                $multipartData = [];

                // Kirim setiap bidang data aset secara individual dalam multipart
                foreach ($assetData as $key => $value) {
                    // Konversi nilai dengan tepat untuk multipart
                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    } elseif (is_array($value)) {
                        $value = json_encode($value);
                    } elseif ($value === null) {
                        $value = ''; // Konversi null ke string kosong untuk multipart
                    }

                        $multipartData[] = [
                            'name' => $key,
                        'contents' => $value
                        ];
                }

                // Tambahkan unggahan file
                $multipartData[] = [
                    'name' => 'image_file',
                    'contents' => fopen($request->file('image_file')->getPathname(), 'r'),
                    'filename' => $request->file('image_file')->getClientOriginalName()
                ];

                $options = ['multipart' => $multipartData];
                $result = $this->apiService->request('PUT', "/assets/{$id}", $options);
            } else {
                // Tidak ada unggahan file, kirim saja data JSON
                $options = ['json' => $assetData];
                $result = $this->apiService->request('PUT', "/assets/{$id}", $options);
            }

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API atau respons tidak berhasil
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui aset';

                // Format pesan kesalahan
                        $errorMessage = '';
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            foreach ($messages as $message) {
                                $errorMessage .= $message . '. ';
                        }
                    } else {
                            $errorMessage .= $messages . '. ';
                        }
                    }
                } else {
                    $errorMessage = $errorData;
                }

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Berhasil diperbarui
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Aset berhasil diperbarui',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('asset.details', ['id' => $id])
                ->with('success', 'Aset berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal memperbarui aset: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui aset: ' . $e->getMessage());
        }
    }

    /**
     * Checkout aset.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function checkoutAsset(Request $request)
    {
        try {
            // Data dasar checkout hanya dengan asset_id
            $checkoutData = [
                'asset_id' => (int)$request->input('asset_id')
            ];

            // Tambahkan catatan hanya jika tidak kosong
            if ($request->filled('checkout_notes')) {
                $checkoutData['checkout_notes'] = $request->input('checkout_notes');
            }

            // Tentukan jenis checkout berdasarkan checkout_to_type
            if ($request->input('checkout_to_type') === 'location') {
                // Checkout lokasi - sertakan room_id
                $roomId = (int)($request->input('room_id') ?? $request->input('location_id') ?? 0);
                if ($roomId > 0) {
                    $checkoutData['room_id'] = $roomId;
                } else {
                    return redirect()->back()->with('error', 'ID ruangan harus berupa angka positif');
                }
            } else {
                // Checkout karyawan - sertakan assigned_to
                $userId = (int)$request->input('assigned_to');
                if ($userId > 0) {
                    $checkoutData['assigned_to'] = $userId;
                } else {
                    return redirect()->back()->with('error', 'ID karyawan harus berupa angka positif');
                }
            }

            // Kirim permintaan ke API
            $options = ['json' => $checkoutData];
            $result = $this->apiService->request('POST', '/asset-transfers/checkout', $options);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API lainnya
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal melakukan checkout aset';

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

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Kembalikan respons berhasil
            $successMessage = $result['message'] ?? 'Aset berhasil di-checkout';

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->back()->with('success', $successMessage);

        } catch (\Exception $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal melakukan checkout aset: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal melakukan checkout aset: ' . $e->getMessage());
        }
    }

    /**
     * Check in (kembalikan) aset.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function checkinAsset(Request $request)
    {
        try {
            // Persiapkan data check-in dengan bidang yang diperlukan
            $checkinData = [
                'asset_id' => (int)$request->input('asset_id')
            ];

            // Sertakan catatan hanya jika disediakan
            if ($request->filled('return_notes')) {
                $checkinData['return_notes'] = $request->input('return_notes');
            }

            // Tambahkan kondisi jika disediakan
            if ($request->filled('condition')) {
                $checkinData['condition'] = $request->input('condition');
            }

            // Kirim permintaan ke API
            $options = ['json' => $checkinData];
            $result = $this->apiService->request('POST', '/asset-transfers/return', $options);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API lainnya
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal mengembalikan aset';

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

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Kembalikan respons berhasil
            $successMessage = $result['message'] ?? 'Aset berhasil dikembalikan';

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->back()->with('success', $successMessage);

        } catch (\Exception $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal mengembalikan aset: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal mengembalikan aset: ' . $e->getMessage());
        }
    }

    /**
     * Melaporkan aset sebagai hilang.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reportAssetLost(Request $request)
    {
        try {
            // Persiapkan data laporan hilang dengan bidang yang diperlukan
            $lostData = [
                'asset_id' => (int)$request->input('asset_id')
            ];

            // Sertakan alasan hanya jika disediakan
            if ($request->filled('loss_reason')) {
                $lostData['loss_reason'] = $request->input('loss_reason');
            }

            // Kirim permintaan ke API
            $options = ['json' => $lostData];
            $result = $this->apiService->request('POST', '/asset-transfers/loss', $options);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API lainnya
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal melaporkan aset hilang';

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

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Kembalikan respons berhasil
            $successMessage = $result['message'] ?? 'Aset berhasil dilaporkan hilang';

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->back()->with('success', $successMessage);

        } catch (\Exception $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal melaporkan aset hilang: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal melaporkan aset hilang: ' . $e->getMessage());
        }
    }

    /**
     * Melaporkan aset sebagai ditemukan.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reportAssetFound(Request $request)
    {
        try {
            // Persiapkan data ditemukan dengan bidang yang diperlukan
            $foundData = [
                'asset_id' => (int)$request->input('asset_id')
            ];

            // Sertakan catatan hanya jika disediakan
            if ($request->filled('found_notes')) {
                $foundData['found_notes'] = $request->input('found_notes');
            }

            // Kirim permintaan ke API
            $options = ['json' => $foundData];
            $result = $this->apiService->request('POST', '/asset-transfers/found', $options);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API lainnya
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal melaporkan aset ditemukan';

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

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Kembalikan respons berhasil
            $successMessage = $result['message'] ?? 'Aset berhasil dilaporkan ditemukan';

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->back()->with('success', $successMessage);

        } catch (\Exception $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal melaporkan aset ditemukan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal melaporkan aset ditemukan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus aset.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function disposeAsset(Request $request)
    {
        try {
            // Persiapkan data penghapusan dengan bidang yang diperlukan
            $disposeData = [
                'asset_id' => (int)$request->input('asset_id'),
                'disposal_reason' => $request->input('disposal_reason'),
            ];

            if ($request->filled('disposal_method')) {
                $disposeData['disposal_method'] = $request->input('disposal_method');
            }

            if ($request->filled('disposal_notes')) {
                $disposeData['disposal_notes'] = $request->input('disposal_notes');
            }

            // Kirim permintaan ke API
            $options = ['json' => $disposeData];
            $result = $this->apiService->request('POST', '/asset-transfers/dispose', $options);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API lainnya
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

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Kembalikan respons berhasil
            $successMessage = $result['message'] ?? 'Aset berhasil dihapus';

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->back()->with('success', $successMessage);

        } catch (\Exception $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal menghapus aset: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal menghapus aset: ' . $e->getMessage());
        }
    }

    /**
     * Ekspor detail aset ke PDF
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportAssetDetailPDF($id, Request $request)
    {
        try {
            // Ambil detail aset
            $result = $this->apiService->request('GET', "/assets/{$id}");

            if (!isset($result['data'])) {
                return redirect()->route('assets.index')->with('error', 'Aset tidak ditemukan');
            }

            $asset = $result['data'];

            // Konversi gambar aset ke base64
            if (!empty($asset['asset_master']['reference_image_path'])) {
                try {
                    $imagePath = 'https://web-magangunbin2025.rsummi.co.id/api/public' . $asset['asset_master']['reference_image_path'];
                    $imageData = file_get_contents($imagePath);
                    if ($imageData !== false) {
                        $asset['image_base64'] = base64_encode($imageData);
                    }
                } catch (\Exception $e) {
                    // Lanjutkan tanpa gambar
                }
            }

            // Konversi kode QR ke base64
            if (!empty($asset['qr_code'])) {
                try {
                    $qrPath = 'https://web-magangunbin2025.rsummi.co.id/api/public' . $asset['qr_code'];
                    $qrData = file_get_contents($qrPath);
                    if ($qrData !== false) {
                        $asset['qr_base64'] = base64_encode($qrData);
                    }
                } catch (\Exception $e) {
                    // Lanjutkan tanpa kode QR
                }
            }

            // Ambil data depresiasi
            $depreciationData = null;
            try {
                $depreciationResult = $this->apiService->request('GET', "/depreciations/calculate/asset/{$id}");
                if (isset($depreciationResult['success']) && $depreciationResult['success'] === true) {
                    $depreciationData = $depreciationResult['data']['depreciation'] ?? $depreciationResult['depreciation'] ?? null;
                }
            } catch (\Exception $e) {
                // Lanjutkan tanpa data depresiasi
            }

            // Ambil data transaksi keuangan
            $financeData = null;
            try {
                $financeResult = $this->apiService->request('GET', "/asset-transactions/asset/{$id}", [
                    'query' => [
                        'limit' => 100,
                        'sort_by' => 'transaction_date',
                        'sort_order' => 'desc'
                    ]
                ]);

                if (isset($financeResult['success']) && $financeResult['success'] === true) {
                    $financeData = [
                        'transactions' => $financeResult['data']['transactions'] ?? [],
                        'summary' => $financeResult['data']['summary'] ?? null
                    ];
                }
            } catch (\Exception $e) {
                // Lanjutkan tanpa data keuangan
            }

            // Buat PDF
            $pdf = Pdf::loadView('Asset.AssetDetailPDF', [
                'asset' => $asset,
                'date_generated' => now()->format('d M Y H:i:s'),
                'depreciation' => $depreciationData,
                'finance' => $financeData
            ]);

            // Alirkan PDF ke browser
            return $pdf->stream('detail_aset_' . $id . '_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor detail aset sebagai PDF: ' . $e->getMessage());
        }
    }
}
