<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Barryvdh\DomPDF\Facade\Pdf;

class CalibrationController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Menampilkan daftar kalibrasi.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');
            $status = $request->input('status', '');
            $result = $request->input('result', '');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

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
                // Hanya meneruskan nilai status yang valid
                if (in_array($status, ['scheduled', 'in_progress', 'completed', 'overdue', 'cancelled'])) {
                    $queryParams['status_calibration'] = $status;
                }
            }

            // Menambahkan filter hasil jika disediakan
            if (!empty($result)) {
                // Hanya meneruskan nilai hasil yang valid
                if (in_array($result, ['pass', 'fail', 'unknown'])) {
                    $queryParams['calibration_result'] = $result;
                }
            }

            // Menambahkan parameter pengurutan
            if (!empty($sortBy)) {
                $queryParams['sort_by'] = $sortBy;
            }

            if (!empty($sortOrder)) {
                $queryParams['sort_order'] = $sortOrder;
            }

            // Mengambil data kalibrasi
            $result = $this->apiService->request('GET', '/calibrations', [
                'query' => $queryParams
            ]);

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

            // Memeriksa kesalahan API atau respons tidak berhasil
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil data kalibrasi';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], 400);
                }

                // Format pesan kesalahan untuk tampilan
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

                return view('Calibration.Calibration', [
                    'calibrations' => [],
                    'pagination' => null,
                    'search' => $search,
                    'status' => $status,
                    'result' => $result,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder,
                    'error' => $errorMessage
                ]);
            }

            // Parsing data untuk tampilan
            $calibrations = $result['data'] ?? [];
            $pagination = $result['pagination'] ?? null;

            // Untuk permintaan AJAX, kembalikan respons JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Data kalibrasi berhasil diambil',
                    'calibrations' => $calibrations,
                    'pagination' => $pagination
                ]);
            }

            // Kembalikan tampilan dengan data untuk permintaan reguler
            return view('Calibration.Calibration', [
                'calibrations' => $calibrations,
                'pagination' => $pagination,
                'search' => $search,
                'status' => $status,
                'result' => $result,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder
            ]);

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal mengambil data kalibrasi: ' . $e->getMessage()
                ], 500);
            }

            return view('Calibration.Calibration', [
                'calibrations' => [],
                'pagination' => null,
                'search' => $search,
                'status' => $status,
                'result' => $result,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'error' => 'Gagal mengambil data kalibrasi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mendapatkan semua data kalibrasi.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCalibrations(Request $request)
    {
        try {
            // Mendapatkan parameter paginasi
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');

            // Membangun parameter kueri
            $queryParams = [
                'page' => $page,
                'limit' => $limit
            ];

            // Menambahkan parameter pencarian jika disediakan
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Mengambil data kalibrasi
            $result = $this->apiService->request('GET', '/calibrations', [
                'query' => $queryParams
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Autentikasi gagal'
                ], 401);
            }

            // Memeriksa kesalahan API atau respons tidak berhasil
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil data kalibrasi';

                return response()->json([
                    'success' => false,
                    'errors' => $errorData
                ], 400);
            }

            // Mengembalikan respons
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Data kalibrasi berhasil diambil',
                'data' => $result['data'] ?? [],
                'pagination' => $result['pagination'] ?? [
                    'total_items' => 0,
                    'total_pages' => 0,
                    'current_page' => $page,
                    'limit' => $limit,
                    'has_next' => false,
                    'has_prev' => false
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal mengambil data kalibrasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Membuat kalibrasi secara massal.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function createBulkCalibrations(Request $request)
    {
        try {
            // Memvalidasi permintaan
            $request->validate([
                'asset_ids' => 'required|array',
                'planning_calibration_date' => 'required|date'
            ]);

            // Menghapus ID aset duplikat untuk mencegah duplikasi kalibrasi
            $assetIds = array_unique($request->input('asset_ids'));

            // Menyiapkan data permintaan dengan ID aset yang telah dideduplikasi
            $requestData = [
                'asset_ids' => $assetIds,
                'planning_calibration_date' => $request->input('planning_calibration_date')
            ];

            // Mengirim permintaan ke API
            $result = $this->apiService->request('POST', '/calibrations/bulk', [
                'json' => $requestData
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Autentikasi gagal'
                ], 401);
            }

            // Memeriksa kesalahan API atau respons tidak berhasil
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat kalibrasi';

                return response()->json([
                    'success' => false,
                    'errors' => $errorData
                ], 400);
            }

            // Untuk permintaan AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Kalibrasi berhasil dibuat',
                    'data' => $result['data'] ?? []
                ]);
            }

            // Untuk pengajuan formulir reguler, alihkan dengan flash sesi
            return redirect()->route('calibration')->with('success', 'Kalibrasi berhasil dibuat');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal membuat kalibrasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Melaporkan hasil kalibrasi.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function reportCalibration(Request $request, $id)
    {
        try {
            // Memvalidasi permintaan
            $validator = \Validator::make($request->all(), [
                'actual_calibration_date' => 'nullable|date',
                'next_calibration_date' => 'nullable|date',
                'status_calibration' => 'nullable|string|in:scheduled,in_progress,completed,overdue',
                'vendor_id' => 'nullable|integer',
                'certificate_number' => 'nullable|string',
                'calibration_result' => 'nullable|string',
                'calibration_price' => 'nullable|numeric',
                'notes' => 'nullable|string',
                'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $requestData = [];
            $fields = [
                'actual_calibration_date',
                'next_calibration_date',
                'status_calibration',
                'vendor_id',
                'certificate_number',
                'calibration_result',
                'calibration_price',
                'notes'
            ];

            foreach ($fields as $field) {
                if ($request->has($field) && $request->input($field) !== '') {
                    $requestData[$field] = $request->input($field);
                }
            }

            // Menangani unggahan file jika ada
            if ($request->hasFile('file')) {
                $file = $request->file('file');

                // Membuat unggahan multipart alih-alih pengkodean base64
                $multipart = [
                    [
                        'name' => 'file',
                        'contents' => fopen($file->getPathname(), 'r'),
                        'filename' => $file->getClientOriginalName()
                    ]
                ];

                // Menambahkan semua bidang lain ke permintaan multipart
                foreach ($requestData as $key => $value) {
                    $multipart[] = [
                        'name' => $key,
                        'contents' => $value
                    ];
                }

                // Mengirim permintaan ke API menggunakan data formulir multipart
                $result = $this->apiService->request('PUT', '/calibrations/report/' . $id, [
                    'multipart' => $multipart
                ]);
            } else {
                // Mengirim permintaan ke API tanpa file
                $result = $this->apiService->request('PUT', '/calibrations/report/' . $id, [
                    'json' => $requestData
                ]);
            }

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Autentikasi gagal'
                ], 401);
            }

            // Memeriksa kesalahan API atau respons tidak berhasil
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui kalibrasi';

                return response()->json([
                    'success' => false,
                    'errors' => $errorData
                ], 400);
            }

            // Untuk permintaan AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kalibrasi telah dilakukan',
                    'data' => $result['data'] ?? []
                ]);
            }

            // Untuk pengajuan formulir reguler, alihkan dengan flash sesi
            return redirect()->route('calibration')->with('success', 'Kalibrasi telah dilakukan');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal memperbarui kalibrasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus catatan kalibrasi tunggal atau beberapa catatan.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        try {
            // Mendapatkan ID dari permintaan - menangani berbagai format yang mungkin
            $ids = null;
            $requestData = $request->json()->all(); // Coba dapatkan data JSON terlebih dahulu

            // Coba beberapa cara untuk mendapatkan ID
            if (isset($requestData['ids']) && is_array($requestData['ids'])) {
                $ids = $requestData['ids'];
            } elseif ($request->has('ids')) {
                $ids = $request->input('ids');
            } elseif ($request->has('all') && isset($request->all()['all']['ids'])) {
                $ids = $request->all()['all']['ids'];
            }

            // Pastikan kita memiliki array ID yang valid
            if (empty($ids) || !is_array($ids)) {
                return response()->json([
                    'success' => false,
                    'errors' => 'ID kalibrasi valid diperlukan'
                ], 400);
            }

            // Konversi semua ID menjadi bilangan bulat untuk memastikan sesuai dengan format yang diharapkan
            $ids = array_map('intval', $ids);

            // Membangun payload untuk API
            $payload = [
                'json' => [
                    'ids' => $ids
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ];

            // Memanggil API untuk menghapus kalibrasi
            $result = $this->apiService->request('DELETE', '/calibrations/bulk', $payload);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Autentikasi gagal'
                ], 401);
            }

            // Memeriksa kesalahan API atau respons tidak berhasil
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menghapus kalibrasi';

                return response()->json([
                    'success' => false,
                    'errors' => $errorData
                ], 400);
            }

            // Untuk permintaan AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kalibrasi berhasil dihapus',
                    'data' => $result['data'] ?? []
                ]);
            }

            // Untuk pengajuan formulir reguler, alihkan dengan flash sesi
            return redirect()->route('calibration')->with('success', 'Kalibrasi berhasil dihapus');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal menghapus kalibrasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan kalibrasi tunggal berdasarkan ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCalibration($id)
    {
        try {
            // Ambil kalibrasi dari API
            $result = $this->apiService->request('GET', '/calibrations/' . $id);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Autentikasi gagal'
                ], 401);
            }

            // Memeriksa apakah kalibrasi ada
            if (!isset($result['data']) || empty($result['data'])) {
                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Kalibrasi tidak ditemukan'
                ], 404);
            }

            // Kembalikan respons API
            return response()->json([
                'success' => true,
                'message' => 'Data kalibrasi berhasil diambil',
                'data' => $result['data']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal mengambil data kalibrasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail kalibrasi.
     *
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function showCalibrationDetail($id)
    {
        try {
            // Ambil kalibrasi dari API
            $result = $this->apiService->request('GET', '/calibrations/' . $id);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa apakah kalibrasi ada
            if (!isset($result['data']) || empty($result['data'])) {
                return redirect()->route('calibration')->with('error', 'Kalibrasi tidak ditemukan');
            }

            // Dapatkan data kalibrasi
            $calibrationData = $result['data'];

            // Coba ambil riwayat jika tidak termasuk dalam respons utama
            if (!isset($calibrationData['history'])) {
                try {
                    $historyResult = $this->apiService->request('GET', '/calibrations/' . $id . '/history');
                    if (isset($historyResult['data']) && !empty($historyResult['data'])) {
                        $calibrationData['history'] = $historyResult['data'];
                    }
                } catch (\Exception $e) {
                    // Lanjutkan tanpa riwayat jika gagal
                }
            }

            // Kembalikan tampilan dengan data kalibrasi
            return view('Calibration.CalibrationDetail', [
                'calibration' => $calibrationData
            ]);

        } catch (\Exception $e) {
            return redirect()->route('calibration')->with('error', 'Gagal mengambil detail kalibrasi: ' . $e->getMessage());
        }
    }

    /**
     * Ekspor data kalibrasi ke PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportCalibrationPDF(Request $request)
    {
        try {
            // Mendapatkan parameter filter
            $search = $request->input('search', '');
            $status = $request->input('status', '');
            $result = $request->input('result', '');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            // Membangun parameter kueri
            $queryParams = [
                'page' => 1,
                'limit' => 1000  // Dapatkan jumlah besar untuk ekspor
            ];

            // Menambahkan parameter pencarian jika disediakan
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Menambahkan filter status jika disediakan
            if (!empty($status)) {
                // Hanya meneruskan nilai status yang valid
                if (in_array($status, ['scheduled', 'in_progress', 'completed', 'overdue', 'cancelled'])) {
                    $queryParams['status_calibration'] = $status;
                }
            }

            // Menambahkan filter hasil jika disediakan
            if (!empty($result)) {
                // Hanya meneruskan nilai hasil yang valid
                if (in_array($result, ['pass', 'fail', 'unknown'])) {
                    $queryParams['calibration_result'] = $result;
                }
            }

            // Menambahkan parameter pengurutan
            if (!empty($sortBy)) {
                $queryParams['sort_by'] = $sortBy;
            }

            if (!empty($sortOrder)) {
                $queryParams['sort_order'] = $sortOrder;
            }

            // Mengambil kalibrasi dari API
            $result = $this->apiService->request('GET', '/calibrations', [
                'query' => $queryParams
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Mendapatkan data kalibrasi
            $calibrations = $result['data'] ?? [];

            // Menghasilkan PDF
            $pdf = Pdf::loadView('Calibration.CalibrationPDF', [
                'calibrations' => $calibrations,
                'search' => $search,
                'status' => $status,
                'result' => $result,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'date_generated' => now()->format('d M Y H:i:s')
            ]);

            // Alirkan PDF ke peramban
            return $pdf->stream('laporan_kalibrasi_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor kalibrasi sebagai PDF: ' . $e->getMessage());
        }
    }

    /**
     * Ekspor detail kalibrasi tunggal ke PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportCalibrationDetailPDF($id)
    {
        try {
            // Mengambil kalibrasi dari API
            $result = $this->apiService->request('GET', '/calibrations/' . $id);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa apakah kalibrasi ada
            if (!isset($result['data']) || empty($result['data'])) {
                return redirect()->route('calibration')->with('error', 'Kalibrasi tidak ditemukan');
            }

            // Mendapatkan data kalibrasi
            $calibrationData = $result['data'];

            // Coba ambil riwayat jika tidak termasuk dalam respons utama
            if (!isset($calibrationData['history'])) {
                try {
                    $historyResult = $this->apiService->request('GET', '/calibrations/' . $id . '/history');
                    if (isset($historyResult['data']) && !empty($historyResult['data'])) {
                        $calibrationData['history'] = $historyResult['data'];
                    }
                } catch (\Exception $e) {
                    // Lanjutkan tanpa riwayat jika gagal
                }
            }

            // Konversi file sertifikat ke base64 jika ada dan merupakan gambar
            if (!empty($calibrationData['certificate_file_path'])) {
                try {
                    $fileName = basename($calibrationData['certificate_file_path']);
                    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                    $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']);

                    if ($isImage) {
                        // Construct proper path to the image
                        $backendUrl = config('app.backend_url', 'http://localhost:5000');

                        // Check if certificate_file_path already includes /public
                        if (strpos($calibrationData['certificate_file_path'], '/public') === 0) {
                            $imagePath = $backendUrl . $calibrationData['certificate_file_path'];
                        } else {
                            $imagePath = $backendUrl . '/public' . $calibrationData['certificate_file_path'];
                        }

                        // Alternative path in case the above doesn't work
                        $altImagePath = $backendUrl . '/public' . $fileName;

                        // Try to get image data from the main path
                        $imageData = @file_get_contents($imagePath);

                        // If main path failed, try alternative path
                        if ($imageData === false) {
                            $imageData = @file_get_contents($altImagePath);
                        }

                        if ($imageData !== false) {
                            $calibrationData['certificate_file_base64'] = base64_encode($imageData);
                        }
                    } else {
                        // For non-image documents, just store the filename
                        $calibrationData['certificate_file_url'] = $fileName;
                    }
                } catch (\Exception $e) {
                    // Continue without certificate file if failed
                    \Log::error('Failed to process certificate file: ' . $e->getMessage());
                }
            }

            // Menghasilkan PDF menggunakan tampilan yang sama dengan halaman detail
            $pdf = PDF::loadView('Calibration.CalibrationDetailPDF', [
                'calibration' => $calibrationData,
                'date_generated' => now()->format('d M Y H:i:s')
            ]);

            // Alirkan PDF ke peramban
            return $pdf->stream('detail_kalibrasi_' . $id . '_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor detail kalibrasi sebagai PDF: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui jadwal kalibrasi.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCalibrationSchedule(Request $request, $id)
    {
        try {
            // Memvalidasi permintaan
            $validator = \Validator::make($request->all(), [
                'planning_calibration_date' => 'required|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Menyiapkan data permintaan
            $requestData = [
                'planning_calibration_date' => $request->input('planning_calibration_date')
            ];

            // Mengirim permintaan ke API
            $result = $this->apiService->request('PUT', '/calibrations/schedule/' . $id, [
                'json' => $requestData
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Autentikasi gagal'
                ], 401);
            }

            // Memeriksa kesalahan API atau respons tidak berhasil
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui jadwal kalibrasi';

                return response()->json([
                    'success' => false,
                    'errors' => $errorData
                ], 400);
            }

            // Mengembalikan respons berhasil
            return response()->json([
                'success' => true,
                'message' => 'Jadwal kalibrasi berhasil diperbarui',
                'data' => $result['data'] ?? []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal memperbarui jadwal kalibrasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan daftar aset yang tersedia untuk kalibrasi.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssetsForCalibration(Request $request)
    {
        try {
            // Mendapatkan parameter paginasi
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');

            // Membangun parameter kueri
            $queryParams = [
                'page' => $page,
                'limit' => $limit
            ];

            // Menambahkan parameter pencarian jika disediakan
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Mengambil data aset yang tersedia untuk kalibrasi
            $result = $this->apiService->request('GET', '/calibrations/assets', [
                'query' => $queryParams
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Autentikasi gagal'
                ], 401);
            }

            // Memeriksa kesalahan API atau respons tidak berhasil
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil daftar aset untuk kalibrasi';

                return response()->json([
                    'success' => false,
                    'errors' => $errorData
                ], 400);
            }

            // Mengembalikan respons
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Daftar aset tersedia untuk kalibrasi berhasil diambil',
                'data' => $result['data'] ?? [],
                'pagination' => $result['pagination'] ?? [
                    'total_items' => 0,
                    'total_pages' => 0,
                    'current_page' => $page,
                    'limit' => $limit,
                    'has_next' => false,
                    'has_prev' => false
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal mengambil daftar aset untuk kalibrasi: ' . $e->getMessage()
            ], 500);
        }
    }
}
