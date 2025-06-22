<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Barryvdh\DomPDF\Facade\Pdf;

class OpnameReportController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Menampilkan halaman laporan opname dengan data.
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');
            $sortOrder = $request->input('sort_order', 'desc');

            $result = $this->apiService->request('GET', '/asset-opnames', [
                'query' => [
                    'page' => $page,
                    'limit' => $limit,
                    'search' => $search,
                    'sort_by' => 'created_at',
                    'sort_order' => $sortOrder
                ]
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

            // Memeriksa kesalahan API
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil laporan opname';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
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

                return view('Report.OpnameReport.OpnameReport', [
                    'opnames' => [],
                    'pagination' => null,
                    'search' => $search,
                    'sort_order' => $sortOrder,
                    'error' => $errorMessage
                ]);
            }

            $opnames = $result['data'] ?? [];

            // Format paginasi
            $pagination = null;
            if (isset($result['pagination'])) {
                $paginationData = $result['pagination'];
                $pagination = [
                    'current_page' => $paginationData['current_page'] ?? 1,
                    'last_page' => ceil(($paginationData['total_items'] ?? 0) / ($paginationData['limit'] ?? 10)),
                    'from' => (($paginationData['current_page'] ?? 1) - 1) * ($paginationData['limit'] ?? 10) + 1,
                    'to' => min(($paginationData['current_page'] ?? 1) * ($paginationData['limit'] ?? 10), $paginationData['total_items'] ?? 0),
                    'total' => $paginationData['total_items'] ?? 0,
                    'per_page' => $paginationData['limit'] ?? 10,
                    'next_page_url' => $paginationData['has_next'] ? url()->current() . '?page=' . ($paginationData['current_page'] + 1) : null,
                    'prev_page_url' => $paginationData['has_prev'] ? url()->current() . '?page=' . ($paginationData['current_page'] - 1) : null,
                ];
            }

            return view('Report.OpnameReport.OpnameReport', [
                'opnames' => $opnames,
                'pagination' => $pagination,
                'search' => $search,
                'sort_order' => $sortOrder
            ]);
        } catch (\Exception $e) {
            return view('Report.OpnameReport.OpnameReport', [
                'opnames' => [],
                'pagination' => null,
                'search' => $request->input('search', ''),
                'sort_order' => $request->input('sort_order', 'desc'),
                'error' => 'Gagal mengambil laporan opname: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mendapatkan semua opname aset.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllOpnames(Request $request)
    {
        try {
            // Mendapatkan parameter paginasi
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');

            // Membangun parameter kueri
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
                'sort_by' => 'created_at',
                'sort_order' => 'desc'
            ];

            // Menambahkan parameter pencarian jika disediakan
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Mengambil data opname dari API
            $result = $this->apiService->request('GET', '/asset-opnames', [
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

            // Memeriksa kesalahan API
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil data opname aset';

                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                ], 400);
            }

            // Mengembalikan respons
            return response()->json([
                'success' => true,
                'message' => 'Data opname aset berhasil diambil',
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
                'errors' => 'Gagal mengambil data opname: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan halaman detail opname.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showOpnameDetail($id, Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $scanStatus = $request->input('scan_status', '');

            $queryParams = [
                'page' => $page,
                'limit' => $limit
            ];

            // Add scan_status parameter if it's provided
            if (!empty($scanStatus)) {
                $queryParams['scan_status'] = $scanStatus;
            }

            $result = $this->apiService->request('GET', "/asset-opname-details/opname/{$id}", [
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

            // Memeriksa kesalahan API
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil detail opname';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
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

                return view('Report.OpnameReport.OpnameDetail', [
                    'opnameId' => $id,
                    'opnameCode' => null,
                    'details' => [],
                    'pagination' => null,
                    'summary' => null,
                    'roomInfo' => null,
                    'error' => $errorMessage
                ]);
            }

            // Ekstrak data dari respons API - Diperbarui untuk struktur respons baru
            $responseData = $result['data'] ?? [];
            $details = $responseData['details'] ?? [];
            $pagination = $responseData['pagination'] ?? null;
            $summary = $responseData['summary'] ?? null;
            $roomInfo = $responseData['room_info'] ?? null;
            $opnameInfo = $responseData['opname_info'] ?? null;

            // Mendapatkan kode opname dari opname_info
            $opnameCode = $opnameInfo['opname_code'] ?? null;

            return view('Report.OpnameReport.OpnameDetail', [
                'opnameId' => $id,
                'opnameCode' => $opnameCode,
                'details' => $details,
                'pagination' => $pagination,
                'summary' => $summary,
                'roomInfo' => $roomInfo,
                'opnameInfo' => $opnameInfo,
            ]);

        } catch (\Exception $e) {
            return view('Report.OpnameReport.OpnameDetail', [
                'opnameId' => $id,
                'opnameCode' => null,
                'details' => [],
                'pagination' => null,
                'summary' => null,
                'roomInfo' => null,
                'opnameInfo' => null,
                'error' => 'Gagal mengambil detail opname: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mengekspor detail opname sebagai PDF
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportOpnameDetailPDF($id, Request $request)
    {
        try {
            // Menggunakan pendekatan pengambilan data yang sama dengan showOpnameDetail
            $scanStatus = $request->input('scan_status', '');

            $queryParams = [
                'page' => 1,
                'limit' => 100 // Batas besar untuk mendapatkan semua data
            ];

            // Add scan_status parameter if it's provided
            if (!empty($scanStatus)) {
                $queryParams['scan_status'] = $scanStatus;
            }

            $result = $this->apiService->request('GET', "/asset-opname-details/opname/{$id}", [
                'query' => $queryParams
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil detail opname untuk ekspor PDF';

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

            // Ekstrak data menggunakan struktur respons baru
            $responseData = $result['data'] ?? [];
            $details = $responseData['details'] ?? [];
            $pagination = $responseData['pagination'] ?? null;
            $summary = $responseData['summary'] ?? null;
            $roomInfo = $responseData['room_info'] ?? null;
            $opnameInfo = $responseData['opname_info'] ?? null;

            // Mendapatkan kode opname dari opname_info
            $opnameCode = $opnameInfo['opname_code'] ?? 'N/A';

            // Membuat PDF dengan data - termasuk opnameInfo
            $pdf = Pdf::loadView('Report.OpnameReport.OpnameDetailPDF', [
                'opnameId' => $id,
                'opnameCode' => $opnameCode,
                'details' => $details,
                'pagination' => null, // Tidak diperlukan untuk PDF
                'summary' => $summary,
                'roomInfo' => $roomInfo,
                'opnameInfo' => $opnameInfo,
            ]);

            // Menetapkan ukuran kertas dan orientasi
            $pdf->setPaper('a4', 'portrait');

            return $pdf->stream("detail_opname_{$id}.pdf");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor detail opname sebagai PDF: ' . $e->getMessage());
        }
    }
}
