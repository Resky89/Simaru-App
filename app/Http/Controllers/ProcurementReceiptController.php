<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class ProcurementReceiptController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Menampilkan daftar penerimaan barang dengan pagination
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            // Mendapatkan parameter paginasi dengan nilai default
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            // Mendapatkan parameter pencarian dan filter
            $search = $request->input('search');
            $status = $request->input('status');
            $sort = $request->input('sort');

            // Membangun parameter query
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
            ];

            // Menambahkan parameter pencarian jika disediakan
            if ($search) {
                $queryParams['search'] = $search;
            }

            // Menambahkan filter status jika disediakan
            if ($status) {
                $queryParams['status'] = $status;
            }

            // Mengatur parameter pengurutan berdasarkan pilihan
            if ($sort) {
                switch ($sort) {
                    case 'newest':
                        $queryParams['sort_by'] = 'created_at';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    case 'oldest':
                        $queryParams['sort_by'] = 'created_at';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'code_asc':
                        $queryParams['sort_by'] = 'receipt_code';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'code_desc':
                        $queryParams['sort_by'] = 'receipt_code';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    case 'date_asc':
                        $queryParams['sort_by'] = 'receipt_date';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'date_desc':
                        $queryParams['sort_by'] = 'receipt_date';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    default:
                        $queryParams['sort_by'] = 'created_at';
                        $queryParams['sort_order'] = 'desc';
                }
            } else {
                // Pengurutan default jika tidak ditentukan
                $queryParams['sort_by'] = 'created_at';
                $queryParams['sort_order'] = 'desc';
            }

            $result = $this->apiService->request('GET', '/receipts', [
                'query' => $queryParams
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API atau respons tidak berhasil
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil daftar penerimaan';

                if ($request->expectsJson() || $request->ajax()) {
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

                // Mengembalikan tampilan dengan data penerimaan kosong dan pesan kesalahan
                return view('Procurement.Receipt.Receipt', [
                    'receipts' => [],
                    'pagination' => null,
                    'error' => $errorMessage
                ]);
            }

            // Menyiapkan data untuk tampilan
            $receipts = $result['data'] ?? [];
            $pagination = $result['pagination'] ?? null;
            $message = $result['message'] ?? 'Daftar penerimaan barang berhasil diambil';

            // Periksa jika permintaan adalah AJAX (menginginkan JSON)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $receipts,
                    'pagination' => $pagination
                ]);
            }

            // Jika bukan permintaan AJAX, kembalikan tampilan dengan data
            return view('Procurement.Receipt.Receipt', [
                'receipts' => $receipts,
                'pagination' => $pagination,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengambil data penerimaan: ' . $e->getMessage()]
                ], 500);
            }

            // Selalu berikan array kosong untuk penerimaan jika terjadi kesalahan
            return view('Procurement.Receipt.Receipt', [
                'receipts' => [],
                'pagination' => null,
                'error' => 'Gagal mengambil data penerimaan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mendapatkan detail penerimaan berdasarkan ID
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $id)
    {
        try {
            // Membuat panggilan API untuk mendapatkan detail penerimaan
            $result = $this->apiService->request('GET', '/receipts/' . $id);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API atau respons tidak berhasil
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil detail penerimaan';

                if ($request->expectsJson() || $request->ajax()) {
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

                // Mengembalikan tampilan dengan pesan kesalahan
                return view('Procurement.Receipt.DetailReceipt', [
                    'receipt' => null,
                    'error' => $errorMessage
                ]);
            }

            // Mendapatkan data penerimaan
            $receipt = $result['data'] ?? null;
            $message = $result['message'] ?? 'Penerimaan barang berhasil ditemukan';

            // Periksa jika permintaan adalah AJAX (menginginkan JSON)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $receipt
                ]);
            }

            // Jika bukan permintaan AJAX, kembalikan tampilan dengan data
            return view('Procurement.Receipt.DetailReceipt', [
                'receipt' => $receipt,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengambil detail penerimaan: ' . $e->getMessage()]
                ], 500);
            }

            // Mengembalikan tampilan dengan kesalahan
            return view('Procurement.Receipt.DetailReceipt', [
                'receipt' => null,
                'error' => 'Gagal mengambil detail penerimaan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Membuat penerimaan barang baru
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        try {
            // Memvalidasi data permintaan
            $validatedData = $request->validate([
                'purchase_order_id' => 'required|integer',
                'receipt_date' => 'required|date',
                'received_by' => 'required|integer',
                'delivered_by' => 'nullable|string',
                'notes' => 'nullable|string',
                'items' => 'required|array',
                'items.*.purchase_order_item_id' => 'required|integer',
                'items.*.notes' => 'nullable|string',
            ]);

            // Membuat panggilan API untuk membuat penerimaan
            $result = $this->apiService->request('POST', '/receipts', [
                'json' => $validatedData
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API atau respons tidak berhasil
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat penerimaan';

                if ($request->expectsJson() || $request->ajax()) {
                    // Mengembalikan struktur kesalahan terperinci untuk respons JSON
                    if (is_array($errorData)) {
                        // Pertahankan struktur kesalahan asli untuk tampilan yang tepat
                        return response()->json([
                            'success' => false,
                            'errors' => $errorData,
                            'message' => 'Terdapat kesalahan pada pengiriman data'
                        ], 400);
                    } else {
                        return response()->json([
                            'success' => false,
                            'errors' => ['general' => $errorData]
                        ], 400);
                    }
                }

                // Format pesan kesalahan untuk redirect
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

                return redirect()->route('procurement.receipt')->with('error', $errorMessage);
            }

            // Mendapatkan data penerimaan
            $receipt = $result['data'] ?? null;
            $message = $result['message'] ?? 'Penerimaan barang berhasil dibuat';

            // Periksa jika permintaan adalah AJAX (menginginkan JSON)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $receipt
                ]);
            }

            // Jika bukan permintaan AJAX, redirect dengan pesan sukses
            return redirect()->route('procurement.receipt.show', ['id' => $receipt['receipt_id']])
                ->with('success', $message);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal membuat penerimaan: ' . $e->getMessage()]
                ], 500);
            }

            // Redirect dengan kesalahan
            return redirect()->route('procurement.receipt')
                ->with('error', 'Gagal membuat penerimaan: ' . $e->getMessage());
        }
    }

    /**
     * Mengekspor penerimaan ke PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportReceiptDetailPDF($id)
    {
        try {
            // Mengambil penerimaan dari API
            $result = $this->apiService->request('GET', '/receipts/' . $id);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa apakah penerimaan ada
            if (!isset($result['data']) || empty($result['data'])) {
                return redirect()->route('procurement.receipt')->with('error', $result['message'] ?? 'Penerimaan tidak ditemukan');
            }

            // Mendapatkan data penerimaan
            $receipt = $result['data'];

            // Menghasilkan PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('Procurement.Receipt.DetailReceiptPDF', [
                'receipt' => $receipt,
                'date_generated' => now()->format('Y-m-d H:i:s')
            ]);

            // Menampilkan PDF ke browser
            return $pdf->stream('receipt_' . $id . '_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor penerimaan sebagai PDF: ' . $e->getMessage());
        }
    }
}
