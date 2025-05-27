<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class AssetFinanceController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mendapatkan semua transaksi keuangan untuk aset tertentu
     *
     * @param int $assetId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllTransactions($assetId, Request $request)
    {
        try {
            // Mendapatkan parameter paginasi
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            // Membangun parameter kueri
            $queryParams = [
                'page' => $page,
                'limit' => $limit
            ];

            // Parameter filter
            if ($request->has('filter')) {
                $filter = $request->input('filter');

                // Hanya meneruskan nilai filter yang valid (income atau expense)
                if (in_array($filter, ['income', 'expense'])) {
                    $queryParams['type'] = $filter; // Gunakan parameter 'type' untuk API
                }
            }

            // Parameter pengurutan
            if ($request->has('sort')) {
                $sort = $request->input('sort');

                // Petakan nilai pengurutan frontend ke parameter API
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
                        // Pengurutan default (terbaru terlebih dahulu)
                        $queryParams['sort_by'] = 'transaction_date';
                        $queryParams['sort_order'] = 'desc';
                }
            }

            // Mengambil transaksi untuk ID aset yang diberikan
            $result = $this->apiService->request('GET', "/asset-transactions/asset/{$assetId}", [
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
                $errorData = $result['errors'] ?? 'Gagal mengambil transaksi aset';

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

            // Mengembalikan data transaksi sebagai JSON
            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal mengambil transaksi aset: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Membuat transaksi keuangan baru untuk aset
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function createTransaction(Request $request)
    {
        try {
            // Memvalidasi request
            $validated = $request->validate([
                'asset_id' => 'required|numeric',
                'type' => 'required|string|in:expense,income',
                'amount' => 'required|numeric',
                'transaction_date' => 'required|date',
                'description' => 'nullable|string'
            ]);

            // Memastikan nilai numerik diformat dengan benar
            $data = [
                'asset_id' => (int) $request->asset_id,
                'type' => $request->type,
                'amount' => (float) $request->amount,
                'transaction_date' => $request->transaction_date,
                'description' => $request->description
            ];

            // Membuat transaksi melalui layanan API
            $result = $this->apiService->request('POST', '/asset-transactions', [
                'json' => $data
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

                return redirect()->route('login')->with('error', 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API atau respon tidak berhasil
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal membuat transaksi';

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

                if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                        'errors' => $errorMessage
                    ], 400);
            }

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Periksa apakah ini adalah permintaan AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Transaksi berhasil ditambahkan',
                    'data' => $result['data'] ?? null
                ]);
            }

            // Jika ini adalah pengajuan formulir biasa, alihkan dengan pesan sukses
            return redirect()->back()->with('success', 'Transaksi berhasil ditambahkan');

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal membuat transaksi: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal membuat transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui transaksi keuangan yang ada
     *
     * @param int $transactionId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTransaction($transactionId, Request $request)
    {
        try {
            // Memvalidasi request
            $validated = $request->validate([
                'asset_id' => 'required|numeric',
                'type' => 'required|string|in:expense,income',
                'amount' => 'required|numeric',
                'transaction_date' => 'required|date',
                'description' => 'nullable|string'
            ]);

            // Memastikan nilai numerik diformat dengan benar
            $data = [
                'asset_id' => (int) $request->asset_id,
                'type' => $request->type,
                'amount' => (float) $request->amount,
                'transaction_date' => $request->transaction_date,
                'description' => $request->description
            ];

            // Memperbarui transaksi melalui layanan API
            $result = $this->apiService->request('PUT', "/asset-transactions/{$transactionId}", [
                'json' => $data
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Autentikasi gagal'
                ], 401);
            }

            // Memeriksa kesalahan API atau respon tidak berhasil
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui transaksi';

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

            // Mengembalikan respons JSON untuk notifikasi toast
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Transaksi berhasil diperbarui',
                'data' => $result['data'] ?? null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal memperbarui transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus transaksi keuangan
     *
     * @param int $transactionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteTransaction($transactionId)
    {
        try {
            // Menghapus transaksi melalui layanan API
            $result = $this->apiService->request('DELETE', "/asset-transactions/{$transactionId}");

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Autentikasi gagal'
                ], 401);
            }

            // Memeriksa kesalahan API atau respon tidak berhasil
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal menghapus transaksi';

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

            // Mengembalikan respons JSON untuk notifikasi toast
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Transaksi berhasil dihapus',
                'data' => null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal menghapus transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan satu transaksi keuangan
     *
     * @param int $transactionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransaction($transactionId)
    {
        try {
            // Mengambil detail transaksi
            $result = $this->apiService->request('GET', "/asset-transactions/{$transactionId}");

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
                $errorData = $result['errors'] ?? 'Gagal mengambil data transaksi';

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

            // Mengembalikan data transaksi sebagai JSON
            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal mengambil data transaksi: ' . $e->getMessage()
            ], 500);
        }
    }
}
