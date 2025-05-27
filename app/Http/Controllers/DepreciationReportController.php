<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Barryvdh\DomPDF\Facade\Pdf;

class DepreciationReportController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mendapatkan semua data laporan depresiasi.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function getDepreciationReport(Request $request)
    {
        try {
            // Mendapatkan parameter paginasi
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            // Mendapatkan parameter filter
            $assetMasterName = $request->input('asset_master_name', '');
            $buildingId = $request->input('building_id', '');
            $roomId = $request->input('room_id', '');
            $assetType = $request->input('asset_type', '');
            $subcategoryId = $request->input('subcategory_id', '');
            $yearMonth = $request->input('year_month', '');
            $bookValueEnd = $request->input('book_value_end', 'true');

            $sort = $request->input('sort', 'newest');

            // Untuk kompatibilitas mundur
            if (empty($assetMasterName)) {
                $assetMasterName = $request->input('search', '');
            }

            if (empty($yearMonth)) {
                $yearMonth = $request->input('as_of_date', now()->format('Y-m'));
            }

            // Membangun parameter kueri
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
                'year_month' => $yearMonth,
                'book_value_end' => $bookValueEnd
            ];

            // Menambahkan parameter filter jika disediakan
            if (!empty($assetMasterName)) {
                $queryParams['asset_master_name'] = $assetMasterName;
            }

            if (!empty($buildingId)) {
                $queryParams['building_id'] = $buildingId;
            }

            if (!empty($roomId)) {
                $queryParams['room_id'] = $roomId;
            }

            if (!empty($assetType)) {
                $queryParams['asset_type'] = $assetType;
            }

            if (!empty($subcategoryId)) {
                $queryParams['subcategory_id'] = $subcategoryId;
            }

            // Menangani pengurutan
            switch ($sort) {
                case 'asset_name_asc':
                    $queryParams['sort_by'] = 'asset_name';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'asset_name_desc':
                    $queryParams['sort_by'] = 'asset_name';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'date_acquired_asc':
                    $queryParams['sort_by'] = 'date_acquired';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'date_acquired_desc':
                    $queryParams['sort_by'] = 'date_acquired';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'book_value_asc':
                    $queryParams['sort_by'] = 'book_value_at_month_end';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'book_value_desc':
                    $queryParams['sort_by'] = 'book_value_at_month_end';
                    $queryParams['sort_order'] = 'desc';
                    break;
                default:
                    // Pengurutan default (berdasarkan nama aset menaik)
                    $queryParams['sort_by'] = 'asset_name';
                    $queryParams['sort_order'] = 'asc';
            }

            // Mengambil data laporan depresiasi dari API
            $result = $this->apiService->request('GET', '/depreciations/report', [
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
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal mengambil laporan depresiasi';

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

                return view('Report.DepreciationReport.DepreciationReport', [
                    'items' => [],
                    'pagination' => null,
                    'summary' => null,
                    'search' => $assetMasterName,
                    'sort' => $sort,
                    'as_of_date' => $yearMonth,
                    'asset_type' => $assetType,
                    'error' => $errorMessage
                ]);
            }

            // Mendapatkan data laporan dan menyiapkan untuk tampilan
            $items = $result['data']['items'] ?? [];
            $pagination = $result['pagination'] ?? null;

            // Data ringkasan
            $summary = [
                'total_items' => $result['data']['total_items'] ?? 0,
                'total_acquisition_cost' => $result['data']['total_acquisition_cost'] ?? 0,
                'total_book_value' => $result['data']['total_book_value'] ?? 0,
                'total_depreciation' => $result['data']['total_depreciation'] ?? 0,
                'as_of_date' => $result['data']['as_of_date'] ?? $yearMonth
            ];

            // Untuk permintaan AJAX, kembalikan respons JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data laporan depresiasi berhasil diambil',
                    'data' => [
                        'items' => $items,
                        'summary' => $summary
                    ],
                    'pagination' => $pagination
                ]);
            }

            // Untuk permintaan reguler, kembalikan tampilan
            return view('Report.DepreciationReport.DepreciationReport', [
                'items' => $items,
                'pagination' => $pagination,
                'summary' => $summary,
                'search' => $assetMasterName,
                'sort' => $sort,
                'as_of_date' => $yearMonth,
                'asset_type' => $assetType
            ]);

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal mengambil laporan depresiasi: ' . $e->getMessage()
                ], 500);
            }

            return view('Report.DepreciationReport.DepreciationReport', [
                'items' => [],
                'pagination' => null,
                'summary' => null,
                'search' => $assetMasterName,
                'sort' => $sort,
                'as_of_date' => $yearMonth ?? now()->format('Y-m'),
                'asset_type' => $assetType,
                'error' => 'Gagal mengambil laporan depresiasi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mengekspor laporan depresiasi sebagai PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportDepreciationReportPDF(Request $request)
    {
        try {
            // Mendapatkan parameter filter
            $assetMasterName = $request->input('asset_master_name', '');
            $buildingId = $request->input('building_id', '');
            $roomId = $request->input('room_id', '');
            $assetType = $request->input('asset_type', '');
            $subcategoryId = $request->input('subcategory_id', '');
            $yearMonth = $request->input('year_month', '');
            $bookValueEnd = $request->input('book_value_end', 'true');

            $sort = $request->input('sort', 'asset_name_asc');

            // Untuk kompatibilitas mundur
            if (empty($assetMasterName)) {
                $assetMasterName = $request->input('search', '');
            }

            if (empty($yearMonth)) {
                $yearMonth = $request->input('as_of_date', now()->format('Y-m'));
            }

            // Membangun parameter kueri - menggunakan batas besar untuk mendapatkan semua data
            $queryParams = [
                'page' => 1,
                'limit' => 1000, // Batas besar untuk mendapatkan lebih banyak data untuk PDF
                'year_month' => $yearMonth,
                'book_value_end' => $bookValueEnd
            ];

            // Menambahkan parameter filter jika disediakan
            if (!empty($assetMasterName)) {
                $queryParams['asset_master_name'] = $assetMasterName;
            }

            if (!empty($buildingId)) {
                $queryParams['building_id'] = $buildingId;
            }

            if (!empty($roomId)) {
                $queryParams['room_id'] = $roomId;
            }

            if (!empty($assetType)) {
                $queryParams['asset_type'] = $assetType;
            }

            if (!empty($subcategoryId)) {
                $queryParams['subcategory_id'] = $subcategoryId;
            }

            // Menangani pengurutan
            switch ($sort) {
                case 'asset_name_asc':
                    $queryParams['sort_by'] = 'asset_name';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'asset_name_desc':
                    $queryParams['sort_by'] = 'asset_name';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'date_acquired_asc':
                    $queryParams['sort_by'] = 'date_acquired';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'date_acquired_desc':
                    $queryParams['sort_by'] = 'date_acquired';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'book_value_asc':
                    $queryParams['sort_by'] = 'book_value_at_month_end';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'book_value_desc':
                    $queryParams['sort_by'] = 'book_value_at_month_end';
                    $queryParams['sort_order'] = 'desc';
                    break;
                default:
                    // Pengurutan default (berdasarkan nama aset menaik)
                    $queryParams['sort_by'] = 'asset_name';
                    $queryParams['sort_order'] = 'asc';
            }

            // Mengambil data laporan depresiasi dari API
            $result = $this->apiService->request('GET', '/depreciations/report', [
                'query' => $queryParams
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API atau respons tidak berhasil
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal mengambil data laporan depresiasi';

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

            // Mendapatkan item depresiasi dan data ringkasan
            $items = $result['data']['items'] ?? [];

            // Data ringkasan
            $summary = [
                'total_items' => $result['data']['total_items'] ?? 0,
                'total_acquisition_cost' => $result['data']['total_acquisition_cost'] ?? 0,
                'total_book_value' => $result['data']['total_book_value'] ?? 0,
                'total_depreciation' => $result['data']['total_depreciation'] ?? 0,
                'as_of_date' => $result['data']['as_of_date'] ?? $yearMonth
            ];

            // Membuat PDF dengan data
            $pdf = Pdf::loadView('Report.DepreciationReport.DepreciationReportPDF', [
                'items' => $items,
                'summary' => $summary,
                'asset_master_name' => $assetMasterName,
                'building_id' => $buildingId,
                'room_id' => $roomId,
                'sort' => $sort,
                'year_month' => $yearMonth,
                'asset_type' => $assetType,
                'subcategory_id' => $subcategoryId
            ]);

            // Menetapkan ukuran kertas dan orientasi
            $pdf->setPaper('a4', 'landscape');

            return $pdf->stream("laporan_depresiasi.pdf");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor laporan depresiasi sebagai PDF: ' . $e->getMessage());
        }
    }
}
