<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class AssetHistoryController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mendapatkan riwayat aset tertentu.
     *
     * @param int $id ID aset
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssetHistory($id)
    {
        try {
            // Mengambil riwayat aset dengan ID yang diberikan
            $result = $this->apiService->request('GET', "/asset-histories/{$id}");

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
                $errorData = $result['errors'] ?? 'Gagal mengambil riwayat aset';

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

            // Mengembalikan respons apa adanya
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Data riwayat aset berhasil diambil',
                'data' => $result['data'] ?? []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal mengambil riwayat aset: ' . $e->getMessage()
            ], 500);
        }
    }
}
