<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class DashboardController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Menampilkan halaman dashboard.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            // Mengambil ringkasan dashboard dari API
            $result = $this->apiService->request('GET', '/dashboard/summary');

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return redirect()->route('login')
                    ->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal. Silakan login kembali.');
            }

            // Mengambil data depresiasi dari API
            $depreciationResult = $this->getDepreciationData();

            // Menyiapkan data dashboard
            $dashboardData = [
                'total_users' => 0,
                'total_assets' => 0,
                'total_acquisition_cost' => 0,
                'total_book_value' => 0,
                'total_depreciation' => 0,
                'as_of_date' => date('Y-m-d'),
                'assets_by_type' => [
                    'medical' => 0,
                    'non_medical' => 0
                ],
                'assets_by_status' => [
                    'available' => 0,
                    'check out' => 0,
                    'dispose' => 0,
                    'lost' => 0,
                    'under repair' => 0
                ],
                'assets_by_condition' => [
                    'good' => 0,
                    'slighly damage' => 0,
                    'high damage' => 0
                ],
                'assets_by_subcategory' => [],
                'assets_by_location' => [],
                'upcoming_calibrations' => [
                    'medical' => [],
                    'non_medical' => [],
                    'all' => []
                ]
            ];

            // Memperbarui dengan data API jika tersedia
            if (isset($result['data']) && is_array($result['data'])) {
                $dashboardData = array_merge($dashboardData, $result['data']);
            }

            // Menggabungkan data depresiasi jika tersedia
            if (isset($depreciationResult['success']) && $depreciationResult['success'] === true && isset($depreciationResult['data'])) {
                $dashboardData['total_acquisition_cost'] = $depreciationResult['data']['total_acquisition_cost'] ?? 0;
                $dashboardData['total_book_value'] = $depreciationResult['data']['total_book_value'] ?? 0;
                $dashboardData['total_depreciation'] = $depreciationResult['data']['total_depreciation'] ?? 0;
                $dashboardData['as_of_date'] = $depreciationResult['data']['as_of_date'] ?? date('Y-m-d');
            }

            return view('Dashboard', ['dashboardData' => $dashboardData]);

        } catch (\Exception $e) {
            // Kembalikan tampilan dengan data kosong default dan pesan kesalahan
            return view('Dashboard', [
                'dashboardData' => [
                    'total_users' => 0,
                    'total_assets' => 0,
                    'total_acquisition_cost' => 0,
                    'total_book_value' => 0,
                    'total_depreciation' => 0,
                    'as_of_date' => date('Y-m-d'),
                    'assets_by_type' => [
                        'medical' => 0,
                        'non_medical' => 0
                    ],
                    'assets_by_status' => [
                        'available' => 0,
                        'check out' => 0,
                        'dispose' => 0,
                        'lost' => 0,
                        'under repair' => 0
                    ],
                    'assets_by_condition' => [
                        'good' => 0,
                        'slighly damage' => 0,
                        'high damage' => 0
                    ],
                    'assets_by_subcategory' => [],
                    'assets_by_location' => [],
                    'upcoming_calibrations' => [
                        'medical' => [],
                        'non_medical' => [],
                        'all' => []
                    ]
                ],
                'error' => 'Gagal memuat data dashboard: ' . $e->getMessage()
            ])->with('error', 'Gagal memuat data dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Mendapatkan data total depresiasi
     *
     * @return array
     */
    private function getDepreciationData()
    {
        try {
            // Mengambil data depresiasi dari API
            $result = $this->apiService->request('GET', '/depreciations/total-value');

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return [
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Autentikasi gagal'
                ];
            }

            // Memeriksa kesalahan API
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil data depresiasi';

                return [
                    'success' => false,
                    'errors' => $errorData
                ];
            }

            return $result;

        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => 'Gagal mengambil data depresiasi: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Endpoint API untuk data ringkasan dashboard (untuk permintaan AJAX)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSummary()
    {
        try {
            // Mengambil ringkasan dashboard dari API
            $result = $this->apiService->request('GET', '/dashboard/summary');

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
                $errorData = $result['errors'] ?? 'Gagal mengambil ringkasan dashboard';

                return response()->json([
                    'success' => false,
                    'errors' => $errorData
                ], 400);
            }

            // Mengambil data depresiasi dari API
            $depreciationResult = $this->getDepreciationData();

            // Menggabungkan data depresiasi dengan data ringkasan jika tersedia
            if (isset($depreciationResult['success']) && $depreciationResult['success'] === true && isset($depreciationResult['data'])) {
                $result['data']['total_acquisition_cost'] = $depreciationResult['data']['total_acquisition_cost'] ?? 0;
                $result['data']['total_book_value'] = $depreciationResult['data']['total_book_value'] ?? 0;
                $result['data']['total_depreciation'] = $depreciationResult['data']['total_depreciation'] ?? 0;
                $result['data']['as_of_date'] = $depreciationResult['data']['as_of_date'] ?? date('Y-m-d');
            }

            // Mengembalikan data ringkasan
            return response()->json([
                'success' => true,
                'message' => 'Data ringkasan dashboard berhasil diambil',
                'data' => $result['data'] ?? [
                    'total_users' => 0,
                    'total_assets' => 0,
                    'total_acquisition_cost' => 0,
                    'total_book_value' => 0,
                    'total_depreciation' => 0,
                    'as_of_date' => date('Y-m-d'),
                    'assets_by_type' => [
                        'medical' => 0,
                        'non_medical' => 0
                    ],
                    'assets_by_status' => [
                        'available' => 0,
                        'check out' => 0,
                        'dispose' => 0,
                        'lost' => 0,
                        'under repair' => 0
                    ],
                    'assets_by_condition' => [
                        'good' => 0,
                        'slighly damage' => 0,
                        'high damage' => 0
                    ],
                    'assets_by_subcategory' => [],
                    'assets_by_location' => [],
                    'upcoming_calibrations' => [
                        'medical' => [],
                        'non_medical' => [],
                        'all' => []
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal mengambil ringkasan dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan data acara kalender untuk tahun dan bulan tertentu
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCalendarEvents(Request $request)
    {
        try {
            // Mendapatkan tahun dan bulan dari permintaan (default ke tahun dan bulan saat ini jika tidak disediakan)
            $year = $request->query('year', date('Y'));
            $month = $request->query('month', date('m'));

            // Mengambil data kalender dari API
            $result = $this->apiService->request('GET', "/dashboard/calendar?year={$year}&month={$month}");

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
                $errorData = $result['errors'] ?? 'Gagal mengambil data kalender';

                return response()->json([
                    'success' => false,
                    'errors' => $errorData
                ], 400);
            }

            // Mendapatkan data API
            $calendarData = $result['data'] ?? [];

            // Jika tidak ada data acara, inisialisasi dengan array kosong
            if (!isset($calendarData['events'])) {
                $calendarData['events'] = [];
            }

            // Menyiapkan metadata jika tidak disediakan oleh API
            if (!isset($calendarData['meta'])) {
                $events = $calendarData['events'] ?? [];
                $calendarData['meta'] = [
                    'total_events' => count($events),
                    'total_by_type' => [
                        'calibration' => count(array_filter($events, function($e) { return $e['type'] === 'calibration'; })),
                        'maintenance' => count(array_filter($events, function($e) { return $e['type'] === 'maintenance'; })),
                        'warranty' => count(array_filter($events, function($e) { return $e['type'] === 'warranty'; }))
                    ],
                    'period' => [
                        'year' => $year,
                        'month' => $month,
                        'month_name' => $this->getIndonesianMonthName($month)
                    ]
                ];
            }

            // Mengembalikan data kalender dengan pesan sukses
            return response()->json([
                'success' => true,
                'message' => 'Daftar acara kalender berhasil diambil',
                'data' => $calendarData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal mengambil data kalender: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan nama bulan dalam Bahasa Indonesia dari nomor bulan
     *
     * @param  int|string  $month
     * @return string
     */
    private function getIndonesianMonthName($month)
    {
        $monthNumber = (int) $month;
        $indonesianMonths = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        return $indonesianMonths[$monthNumber] ?? 'Tidak Diketahui';
    }

    /**
     * Mendapatkan riwayat aktivitas aset
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssetActivities(Request $request)
    {
        try {
            // Mendapatkan parameter paginasi dari permintaan
            $page = $request->query('page', 1);
            $limit = $request->query('limit', 10);

            // Mengambil aktivitas aset dari API
            $result = $this->apiService->request('GET', "/asset-histories/activities/all?page={$page}&limit={$limit}");

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
                $errorData = $result['errors'] ?? 'Gagal mengambil aktivitas aset';

                return response()->json([
                    'success' => false,
                    'errors' => $errorData
                ], 400);
            }

            // Mengembalikan data aktivitas dengan pesan sukses
            return response()->json([
                'success' => true,
                'message' => 'Aktivitas aset berhasil diambil',
                'data' => $result['data'] ?? [
                    'histories' => [],
                    'pagination' => [
                        'total_items' => 0,
                        'total_pages' => 0,
                        'current_page' => (int)$page,
                        'limit' => (int)$limit,
                        'has_next' => false,
                        'has_prev' => false
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal mengambil aktivitas aset: ' . $e->getMessage()
            ], 500);
        }
    }
}
