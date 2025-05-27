<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Barryvdh\DomPDF\Facade\Pdf;

class FinanceReportController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mendapatkan semua transaksi aset.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function getAllTransactions(Request $request)
    {
        try {
            // Mendapatkan parameter paginasi
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');
            $sort = $request->input('sort', 'newest');
            $filter = $request->input('filter', 'all');

            // Membangun parameter kueri
            $queryParams = [
                'page' => $page,
                'limit' => $limit
            ];

            // Menambahkan parameter pencarian jika disediakan
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Menambahkan parameter filter jika bukan 'all'
            if ($filter !== 'all') {
                // Hanya meneruskan nilai filter yang valid (pendapatan atau pengeluaran)
                if (in_array($filter, ['income', 'expense'])) {
                    $queryParams['type'] = $filter; // Menggunakan parameter 'type' untuk API
                }
            }

            // Menangani pengurutan
            switch ($sort) {
                case 'newest':
                    $queryParams['sort_by'] = 'transaction_date';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'oldest':
                    $queryParams['sort_by'] = 'transaction_date';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'amount-high':
                    $queryParams['sort_by'] = 'amount';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'amount-low':
                    $queryParams['sort_by'] = 'amount';
                    $queryParams['sort_order'] = 'asc';
                    break;
                default:
                    // Pengurutan default (terbaru pertama)
                    $queryParams['sort_by'] = 'transaction_date';
                    $queryParams['sort_order'] = 'desc';
            }

            // Mengambil transaksi aset dari API
            $result = $this->apiService->request('GET', '/asset-transactions', [
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
                $errorData = $result['errors'] ?? 'Gagal mengambil transaksi aset';

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

                return view('Report.FinanceReport.FinanceReport', [
                    'transactions' => [],
                    'pagination' => null,
                    'search' => $search,
                    'sort' => $sort,
                    'filter' => $filter,
                    'error' => $errorMessage
                ]);
            }

            // Mendapatkan data transaksi dan paginasi
            $transactions = $result['data'] ?? [];
            $pagination = $result['pagination'] ?? null;

            // Untuk permintaan AJAX, kembalikan respons JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Daftar transaksi aset berhasil diambil',
                    'data' => $transactions,
                    'pagination' => $pagination
                ]);
            }

            // Untuk permintaan reguler, kembalikan tampilan
            return view('Report.FinanceReport.FinanceReport', [
                'transactions' => $transactions,
                'pagination' => $pagination,
                'search' => $search,
                'sort' => $sort,
                'filter' => $filter
            ]);

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal mengambil transaksi aset: ' . $e->getMessage()
                ], 500);
            }

            return view('Report.FinanceReport.FinanceReport', [
                'transactions' => [],
                'pagination' => null,
                'search' => $search,
                'sort' => $sort,
                'filter' => $filter,
                'error' => 'Gagal mengambil transaksi aset: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mengekspor laporan keuangan sebagai PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportFinanceReportPDF(Request $request)
    {
        try {
            // Mendapatkan parameter pencarian dan pengurutan
            $search = $request->input('search', '');
            $sort = $request->input('sort', 'newest');
            $filter = $request->input('filter', 'all');

            // Membangun parameter kueri - menggunakan batas besar untuk mendapatkan semua data
            $queryParams = [
                'page' => 1,
                'limit' => 1000 // Batas besar untuk mendapatkan lebih banyak data untuk PDF
            ];

            // Menambahkan parameter pencarian jika disediakan
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Menambahkan parameter filter jika bukan 'all'
            if ($filter !== 'all') {
                // Hanya meneruskan nilai filter yang valid (pendapatan atau pengeluaran)
                if (in_array($filter, ['income', 'expense'])) {
                    $queryParams['type'] = $filter; // Menggunakan parameter 'type' untuk API
                }
            }

            // Menangani pengurutan
            switch ($sort) {
                case 'newest':
                    $queryParams['sort_by'] = 'transaction_date';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'oldest':
                    $queryParams['sort_by'] = 'transaction_date';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'amount-high':
                    $queryParams['sort_by'] = 'amount';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'amount-low':
                    $queryParams['sort_by'] = 'amount';
                    $queryParams['sort_order'] = 'asc';
                    break;
                default:
                    // Pengurutan default (terbaru pertama)
                    $queryParams['sort_by'] = 'transaction_date';
                    $queryParams['sort_order'] = 'desc';
            }

            // Mengambil transaksi aset dari API
            $result = $this->apiService->request('GET', '/asset-transactions', [
                'query' => $queryParams
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API atau respons tidak berhasil
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal mengambil data laporan keuangan';

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

            // Mendapatkan data transaksi
            $transactions = $result['data'] ?? [];

            // Membuat PDF dengan data
            $pdf = Pdf::loadView('Report.FinanceReport.FinanceReportPDF', [
                'transactions' => $transactions,
                'search' => $search,
                'sort' => $sort,
                'filter' => $filter
            ]);

            // Menetapkan ukuran kertas dan orientasi
            $pdf->setPaper('a4', 'portrait');

            return $pdf->stream("laporan_keuangan.pdf");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor laporan keuangan sebagai PDF: ' . $e->getMessage());
        }
    }
}
