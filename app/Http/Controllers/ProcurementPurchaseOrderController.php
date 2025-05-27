<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class ProcurementPurchaseOrderController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Menampilkan daftar pesanan pembelian dengan pagination
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
                        $queryParams['sort_by'] = 'purchase_order_code';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'code_desc':
                        $queryParams['sort_by'] = 'purchase_order_code';
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

            $result = $this->apiService->request('GET', '/purchase-orders', [
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
                $errorData = $result['errors'] ?? 'Gagal mengambil daftar pesanan pembelian';

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

                // Mengembalikan tampilan dengan data pesanan pembelian kosong dan pesan kesalahan
                return view('Procurement.PurchaseOrder.PurchaseOrder', [
                    'purchaseOrders' => [],
                    'pagination' => null,
                    'error' => $errorMessage
                ]);
            }

            // Menyiapkan data untuk tampilan
            $purchaseOrders = $result['data'] ?? [];
            $pagination = $result['pagination'] ?? null;
            $message = $result['message'] ?? 'Daftar pesanan pembelian berhasil diambil';

            // Periksa jika permintaan adalah AJAX (menginginkan JSON)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $purchaseOrders,
                    'pagination' => $pagination
                ]);
            }

            // Jika bukan permintaan AJAX, kembalikan tampilan dengan data
            return view('Procurement.PurchaseOrder.PurchaseOrder', [
                'purchaseOrders' => $purchaseOrders,
                'pagination' => $pagination,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengambil data pesanan pembelian: ' . $e->getMessage()]
                ], 500);
            }

            // Selalu berikan array kosong untuk pesanan pembelian jika terjadi kesalahan
            return view('Procurement.PurchaseOrder.PurchaseOrder', [
                'purchaseOrders' => [],
                'pagination' => null,
                'error' => 'Gagal mengambil data pesanan pembelian: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mendapatkan detail pesanan pembelian berdasarkan ID
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $id)
    {
        try {
            // Membuat panggilan API untuk mendapatkan detail pesanan pembelian
            $result = $this->apiService->request('GET', '/purchase-orders/' . $id);

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
                $errorData = $result['errors'] ?? 'Gagal mengambil detail pesanan pembelian';

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
                return view('Procurement.PurchaseOrder.DetailPurchaseOrder', [
                    'purchaseOrder' => null,
                    'error' => $errorMessage
                ]);
            }

            // Mendapatkan data pesanan pembelian
            $purchaseOrder = $result['data'] ?? null;
            $message = $result['message'] ?? 'Pesanan pembelian berhasil ditemukan';

            // Periksa jika permintaan adalah AJAX (menginginkan JSON)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $purchaseOrder
                ]);
            }

            // Jika bukan permintaan AJAX, kembalikan tampilan dengan data
            return view('Procurement.PurchaseOrder.DetailPurchaseOrder', [
                'purchaseOrder' => $purchaseOrder,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengambil detail pesanan pembelian: ' . $e->getMessage()]
                ], 500);
            }

            // Mengembalikan tampilan dengan kesalahan
            return view('Procurement.PurchaseOrder.DetailPurchaseOrder', [
                'purchaseOrder' => null,
                'error' => 'Gagal mengambil detail pesanan pembelian: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Membuat pesanan pembelian dari penawaran vendor yang dipilih
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createFromVendorOffers(Request $request)
    {
        try {
            // Memvalidasi data permintaan
            $validated = $request->validate([
                'comparison_id' => 'required|integer',
                'selections' => 'required|array|min:1',
                'selections.*.price_comparison_item_id' => 'required|integer',
                'selections.*.vendor_offer_id' => 'required|integer',
            ]);

            // Membuat panggilan API untuk membuat pesanan pembelian
            $result = $this->apiService->request('POST', '/purchase-orders/vendor-offers/select-multiple', [
                'json' => $validated
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Autentikasi gagal']
                ], 401);
            }

            // Memeriksa kesalahan API atau respons tidak berhasil
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat pesanan pembelian';

                // Format kesalahan untuk tampilan yang lebih baik
                $formattedErrors = [];
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $formattedErrors[$field] = $messages;
                        } else {
                            $formattedErrors[$field] = [$messages];
                        }
                    }
                } else {
                    $formattedErrors['general'] = [$errorData];
                }

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors,
                ], 400);
            }

            // Mengembalikan respons sukses dengan data
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Pesanan pembelian berhasil dibuat',
                'data' => $result['data'] ?? null
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Gagal membuat pesanan pembelian: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Mengekspor pesanan pembelian ke PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportPurchaseOrderDetailPDF($id)
    {
        try {
            // Mengambil pesanan pembelian dari API
            $result = $this->apiService->request('GET', '/purchase-orders/' . $id);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa apakah pesanan pembelian ada
            if (!isset($result['data']) || empty($result['data'])) {
                return redirect()->route('procurement.purchase-order')->with('error', $result['message'] ?? 'Pesanan pembelian tidak ditemukan');
            }

            // Mendapatkan data pesanan pembelian
            $purchaseOrder = $result['data'];

            // Menghasilkan PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('Procurement.PurchaseOrder.DetailPurchaseOrderPDF', [
                'purchaseOrder' => $purchaseOrder,
                'date_generated' => now()->format('d M Y H:i:s')
            ]);

            // Menampilkan PDF ke browser
            return $pdf->stream('purchase_order_' . $id . '_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor pesanan pembelian sebagai PDF: ' . $e->getMessage());
        }
    }
}
