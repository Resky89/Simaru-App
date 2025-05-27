<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Barryvdh\DomPDF\Facade\Pdf;

class ComplainRepairController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mendapatkan semua keluhan.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function getAllComplaints(Request $request)
    {
        try {
            // Mendapatkan parameter paginasi
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');
            $sort = $request->input('sort', 'newest');
            $status = $request->input('status', '');

            // Membangun parameter kueri
            $queryParams = [
                'page' => $page,
                'limit' => $limit
            ];

            // Menambahkan parameter pencarian jika disediakan
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Menambahkan filter status jika disediakan
            if (!empty($status)) {
                $queryParams['status'] = $status;
            }

            // Menangani pengurutan
            switch ($sort) {
                case 'newest':
                    $queryParams['sort_by'] = 'created_at';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'oldest':
                    $queryParams['sort_by'] = 'created_at';
                    $queryParams['sort_order'] = 'asc';
                    break;
                default:
                    // Pengurutan default (terbaru lebih dulu)
                    $queryParams['sort_by'] = 'created_at';
                    $queryParams['sort_order'] = 'desc';
            }

            // Mengambil keluhan dari API
            $result = $this->apiService->request('GET', '/complaints', [
                'query' => $queryParams
            ]);

            // Mengambil aset untuk dropdown (jumlah terbatas untuk pemuatan awal)
            try {
            $assetsResult = $this->apiService->request('GET', '/assets', [
                    'query' => [
                        'limit' => 1000
                    ]
                ]);

                // Jika tidak ada aset yang ditemukan, coba endpoint alternatif sebagai fallback
                if (empty($assetsResult['data'] ?? [])) {
                    $assetsResult = $this->apiService->request('GET', '/asset', [
                'query' => [
                    'limit' => 1000,
                    'sort_by' => 'asset_master_name',
                    'sort_order' => 'asc'
                ]
                    ]);
                }
            } catch (\Exception $e) {
                $assetsResult = ['data' => []];
            }

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil data keluhan';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], 400);
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

                return view('ComplainRepair.ComplainRepair', [
                    'complaints' => [],
                    'pagination' => null,
                    'search' => $search,
                    'sort' => $sort,
                    'status' => $status,
                    'assets' => [],
                    'error' => $errorMessage
                ]);
            }

            // Mendapatkan data keluhan dan paginasi
            $complaints = $result['data'] ?? [];
            $pagination = $result['pagination'] ?? null;

            // Mendapatkan data aset dan memetakan ke format yang dibutuhkan untuk dropdown
            $rawAssets = $assetsResult['data'] ?? [];
            $assets = [];

            // Memeriksa kesalahan autentikasi dalam respons API aset
            if (isset($assetsResult['errors']) && is_string($assetsResult['errors']) &&
                in_array($assetsResult['errors'], ['auth_failed', 'session_expired'])) {
                // Lanjutkan dengan array aset kosong karena tidak kritis
            }
            // Memeriksa apakah API mengembalikan status kesalahan
            elseif (isset($assetsResult['success']) && $assetsResult['success'] !== true) {
                // Lanjutkan dengan kesalahan API aset tetapi tidak kritis
            }

            foreach ($rawAssets as $asset) {
                // Membuat data aset dengan struktur baru yang sesuai dengan respons API
                $assetData = [
                    'asset_id' => $asset['asset_id'],
                    'asset_code' => $asset['asset_code'],
                    'asset_name' => isset($asset['asset_master']) && isset($asset['asset_master']['asset_name'])
                        ? $asset['asset_master']['asset_name']
                        : ($asset['asset_master_name'] ?? 'Aset Tidak Dikenal'),
                    'asset_master_name' => $asset['asset_master_name'] ?? 'Aset Tidak Dikenal',
                    'serial_number' => $asset['serial_number'] ?? null,
                    'description' => isset($asset['asset_master']) && isset($asset['asset_master']['description'])
                        ? $asset['asset_master']['description']
                        : null,
                    'room_name' => $asset['room_name'] ?? null
                ];

                $assets[] = $assetData;
            }

            // Untuk permintaan AJAX atau JSON, kembalikan respons JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data keluhan berhasil diambil',
                    'data' => $complaints,
                    'pagination' => $pagination
                ]);
            }

            // Untuk permintaan biasa, kembalikan tampilan
            return view('ComplainRepair.ComplainRepair', [
                'complaints' => $complaints,
                'pagination' => $pagination,
                'search' => $search,
                'sort' => $sort,
                'status' => $status,
                'assets' => $assets
            ]);

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal mengambil data keluhan: ' . $e->getMessage()
                ], 500);
            }

            return view('ComplainRepair.ComplainRepair', [
                'complaints' => [],
                'pagination' => null,
                'search' => $search,
                'sort' => $sort,
                'status' => $status,
                'assets' => [],
                'error' => 'Gagal mengambil data keluhan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mendapatkan detail keluhan berdasarkan ID.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showComplaintDetail($id, Request $request)
    {
        try {
            // Mengambil detail keluhan dari API
            $result = $this->apiService->request('GET', "/complaints/{$id}");

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa apakah keluhan ada
            if (!isset($result['data'])) {
                $errorMessage = 'Keluhan tidak ditemukan';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 404);
                }

                return redirect()->route('complaint.index')->with('error', $errorMessage);
            }

            // Mendapatkan data keluhan
            $complaint = $result['data'];

            // Untuk permintaan AJAX atau JSON, kembalikan respons JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data keluhan berhasil diambil',
                    'data' => $complaint
                ]);
            }

            // Untuk permintaan biasa, kembalikan tampilan
            return view('ComplainRepair.ComplainRepairDetail', [
                'complaint' => $complaint
            ]);

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal mengambil detail keluhan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('complaint-repair.index')->with('error', 'Gagal mengambil detail keluhan: ' . $e->getMessage());
        }
    }

    /**
     * Mengekspor data keluhan ke PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportComplaintPDF(Request $request)
    {
        try {
            // Mendapatkan parameter filter
            $search = $request->input('search', '');
            $sort = $request->input('sort', 'newest');
            $status = $request->input('status', '');

            // Membangun parameter kueri
            $queryParams = [
                'page' => 1,
                'limit' => 1000  // Mendapatkan jumlah besar untuk ekspor
            ];

            // Menambahkan parameter pencarian jika disediakan
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Menambahkan filter status jika disediakan
            if (!empty($status)) {
                $queryParams['status'] = $status;
            }

            // Menangani pengurutan
            switch ($sort) {
                case 'newest':
                    $queryParams['sort_by'] = 'created_at';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'oldest':
                    $queryParams['sort_by'] = 'created_at';
                    $queryParams['sort_order'] = 'asc';
                    break;
                default:
                    // Pengurutan default (terbaru lebih dulu)
                    $queryParams['sort_by'] = 'created_at';
                    $queryParams['sort_order'] = 'desc';
            }

            // Mengambil keluhan dari API
            $result = $this->apiService->request('GET', '/complaints', [
                'query' => $queryParams
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Mendapatkan data keluhan
            $complaints = $result['data'] ?? [];

            // Menghasilkan PDF
            $pdf = Pdf::loadView('ComplainRepair.ComplainRepairPDF', [
                'complaints' => $complaints,
                'search' => $search,
                'sort' => $sort,
                'status' => $status,
                'date_generated' => now()->format('d M Y H:i:s')
            ]);

            // Alirkan PDF ke browser
            return $pdf->stream('laporan_keluhan_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor data keluhan sebagai PDF: ' . $e->getMessage());
        }
    }

    /**
     * Membuat keluhan baru
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function createComplaint(Request $request)
    {
        try {
            // Memeriksa apakah permintaan adalah AJAX
            $isAjax = $request->ajax() || $request->wantsJson();

            $validator = \Validator::make($request->all(), [
                'asset_id' => 'required|integer',
                'description' => 'required|string',
                'image_file' => 'required|image',
            ]);

            if ($validator->fails()) {
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->withErrors($validator)
                    ->with('error', 'Silakan periksa formulir untuk kesalahan.');
            }

            // Menyiapkan data permintaan multipart
            $multipart = [
                [
                    'name' => 'asset_id',
                    'contents' => $request->input('asset_id')
                ],
                [
                    'name' => 'description',
                    'contents' => $request->input('description')
                ]
            ];

            // Menangani file gambar
            if ($request->hasFile('image_file') && $request->file('image_file')->isValid()) {
                $multipart[] = [
                    'name' => 'image_file',
                    'contents' => fopen($request->file('image_file')->getPathname(), 'r'),
                    'filename' => $request->file('image_file')->getClientOriginalName()
                ];
            }

            // Mengirim permintaan ke API
            $result = $this->apiService->request('POST', '/complaints', [
                'multipart' => $multipart
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')
                    ->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat keluhan';

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

                // Mengembalikan respons yang sesuai berdasarkan jenis permintaan
                if ($isAjax) {
                    // Untuk permintaan AJAX, kembalikan JSON
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], 422);
                } else {
                    // Untuk permintaan biasa, redirect kembali dengan kesalahan
                    return redirect()->back()
                        ->withInput()
                        ->with('error', $errorMessage);
                }
            }

            // Respons sukses
            if ($isAjax) {
                // Untuk permintaan AJAX, kembalikan JSON sukses
                return response()->json([
                    'success' => true,
                    'message' => 'Keluhan berhasil dibuat',
                    'data' => $result['data'] ?? null
                ]);
            } else {
                // Untuk permintaan biasa, redirect dengan pesan sukses
                return redirect()->route('complaint.index')
                    ->with('success', 'Keluhan berhasil dibuat');
            }

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal membuat keluhan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat keluhan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus keluhan
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroyComplaint($id, Request $request)
    {
        try {
            // Memeriksa apakah permintaan adalah AJAX
            $isAjax = $request->ajax() || $request->wantsJson();

            // Mengirim permintaan hapus ke API
            $result = $this->apiService->request('DELETE', "/complaints/{$id}");

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')
                    ->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menghapus keluhan';

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

                // Mengembalikan respons yang sesuai berdasarkan jenis permintaan
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], 400);
                } else {
                    return redirect()->back()
                        ->with('error', $errorMessage);
                }
            }

            // Respons sukses
            if ($isAjax) {
                // Untuk permintaan AJAX, kembalikan JSON sukses
                return response()->json([
                    'success' => true,
                    'message' => 'Keluhan berhasil dihapus',
                    'data' => $result['data'] ?? null
                ]);
            } else {
                // Untuk permintaan biasa, redirect dengan pesan sukses
                return redirect()->route('complaint.index')
                    ->with('success', 'Keluhan berhasil dihapus');
            }

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal menghapus keluhan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus keluhan: ' . $e->getMessage());
        }
    }

    /**
     * Membuat catatan perbaikan baru untuk keluhan
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function createRepair(Request $request)
    {
        try {
            // Memeriksa apakah permintaan adalah AJAX
            $isAjax = $request->ajax() || $request->wantsJson();

            // Memvalidasi permintaan
            $validator = \Validator::make($request->all(), [
                'complaint_id' => 'required|integer',
                'repair_description' => 'required|string',
                'final_result' => 'required|string|in:Good,Slightly Damage,Heavy Damage,Waiting for Part',
                'repair_cost' => 'required|numeric',
                'parts_replaced' => 'required|string',
                'file' => 'required|image', // maks 5MB
            ]);

            if ($validator->fails()) {
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->withErrors($validator)
                    ->with('error', 'Silakan periksa formulir untuk kesalahan.');
            }

            // Menyiapkan data permintaan multipart
            $multipart = [
                [
                    'name' => 'complaint_id',
                    'contents' => $request->input('complaint_id')
                ],
                [
                    'name' => 'repair_description',
                    'contents' => $request->input('repair_description')
                ],
                [
                    'name' => 'final_result',
                    'contents' => $request->input('final_result')
                ],
                [
                    'name' => 'repair_cost',
                    'contents' => $request->input('repair_cost')
                ],
                [
                    'name' => 'parts_replaced',
                    'contents' => $request->input('parts_replaced')
                ]
            ];

            // Menangani file gambar
            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                $multipart[] = [
                    'name' => 'file',
                    'contents' => fopen($request->file('file')->getPathname(), 'r'),
                    'filename' => $request->file('file')->getClientOriginalName()
                ];
            }

            // Mengirim permintaan ke API
            $result = $this->apiService->request('POST', '/repairs', [
                'multipart' => $multipart
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')
                    ->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat perbaikan';

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

                // Mengembalikan respons yang sesuai berdasarkan jenis permintaan
                if ($isAjax) {
                    // Untuk permintaan AJAX, kembalikan JSON
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], 422);
                } else {
                    // Untuk permintaan biasa, redirect kembali dengan kesalahan
                    return redirect()->back()
                        ->withInput()
                        ->with('error', $errorMessage);
                }
            }

            // Respons sukses
            if ($isAjax) {
                // Untuk permintaan AJAX, kembalikan JSON sukses
                return response()->json([
                    'success' => true,
                    'message' => 'Perbaikan berhasil dibuat',
                    'data' => $result['data'] ?? null
                ]);
            } else {
                // Untuk permintaan biasa, redirect dengan pesan sukses
                return redirect()->route('complaint.detail', ['id' => $request->input('complaint_id')])
                    ->with('success', 'Perbaikan berhasil dibuat');
            }

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal membuat perbaikan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat perbaikan: ' . $e->getMessage());
        }
    }

    /**
     * Mengekspor detail keluhan ke PDF
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportComplaintDetailPDF($id, Request $request)
    {
        try {
            // Mengambil detail keluhan dari API
            $result = $this->apiService->request('GET', "/complaints/{$id}");

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa apakah keluhan ada
            if (!isset($result['data'])) {
                return redirect()->route('complaint.index')->with('error', 'Keluhan tidak ditemukan');
            }

            // Mendapatkan data keluhan
            $complaint = $result['data'];

            // Mengkonversi gambar ke base64
            if (!empty($complaint['complaint_picture_path'])) {
                try {
                    $imagePath = 'http://localhost:5000/public/images/' . basename($complaint['complaint_picture_path']);
                    $imageData = file_get_contents($imagePath);
                    if ($imageData !== false) {
                        $complaint['complaint_picture_base64'] = base64_encode($imageData);
                    }
                } catch (\Exception $e) {
                    // Lanjutkan tanpa gambar keluhan jika gagal
                }
            }

            // Mengkonversi gambar perbaikan ke base64 jika ada
            if (!empty($complaint['repair']) && !empty($complaint['repair']['repair_picture_path'])) {
                try {
                    $repairImagePath = 'http://localhost:5000/public/images/' . basename($complaint['repair']['repair_picture_path']);
                    $repairImageData = file_get_contents($repairImagePath);
                    if ($repairImageData !== false) {
                        $complaint['repair']['repair_picture_base64'] = base64_encode($repairImageData);
                    }
                } catch (\Exception $e) {
                    // Lanjutkan tanpa gambar perbaikan jika gagal
                }
            }

            // Menghasilkan PDF
            $pdf = Pdf::loadView('ComplainRepair.ComplainRepairDetailPDF', [
                'complaint' => $complaint
            ]);

            // Alirkan PDF ke browser
            return $pdf->stream('detail_keluhan_' . $id . '_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor detail keluhan sebagai PDF: ' . $e->getMessage());
        }
    }
}
