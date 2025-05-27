<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class AssetMutationController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mendapatkan riwayat mutasi untuk aset tertentu
     *
     * @param int $id ID aset
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View
     */
    public function getAssetMutationHistory($id)
    {
        try {
            // Mengambil riwayat mutasi untuk ID aset yang diberikan
            $result = $this->apiService->request('GET', "/asset-histories/status/{$id}");

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if (request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil riwayat mutasi aset';

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

                if (request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return back()->with('error', $errorMessage);
            }

            // Mengembalikan data sebagai JSON jika diminta
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Data riwayat mutasi aset berhasil diambil',
                    'data' => $result['data'] ?? []
                ]);
            }

            // Untuk tampilan web, kembalikan view dengan data
            return view('asset.mutation-history', [
                'assetId' => $id,
                'mutations' => $result['data']['histories'] ?? [],
                'assetDetails' => $result['data'] ?? []
            ]);

        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal mengambil riwayat mutasi aset: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Gagal mengambil riwayat mutasi aset: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan riwayat mutasi untuk aset dengan tampilan web
     *
     * @param int $id ID aset
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showAssetMutationHistory($id)
    {
        try {
            // Menggunakan kembali metode panggilan API
            $response = $this->getAssetMutationHistory($id);

            // Jika sudah merupakan respons yang ditujukan untuk view, kembalikan
            if (!($response instanceof \Illuminate\Http\JsonResponse)) {
                return $response;
            }

            // Jika itu respons JSON, kita perlu mengekstrak data
            $responseData = json_decode($response->getContent(), true);

            if (!isset($responseData['success']) || $responseData['success'] !== true) {
                // Format pesan kesalahan jika diperlukan
                $errorData = $responseData['errors'] ?? 'Gagal mengambil riwayat mutasi aset';

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

                return back()->with('error', $errorMessage);
            }

            return view('asset.mutation-history', [
                'assetId' => $id,
                'mutations' => $responseData['data']['histories'] ?? [],
                'assetDetails' => $responseData['data'] ?? []
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menampilkan riwayat mutasi aset: ' . $e->getMessage());
        }
    }
}
