<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class AssetDepreciationController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mendapatkan data depresiasi untuk aset tertentu
     *
     * @param int $assetId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssetDepreciation($assetId)
    {
        try {
            // Fetch the depreciation data using ApiService
            $result = $this->apiService->request('GET', "/depreciations/calculate/asset/{$assetId}");

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Autentikasi gagal'
                ], 401);
            }

            // Memeriksa kesalahan API berdasarkan flag sukses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil data depresiasi';

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

            // Return the depreciation data as JSON
            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal mengambil data depresiasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memperbarui data depresiasi untuk aset tertentu
     *
     * @param Request $request
     * @param int $assetId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updateAssetDepreciation(Request $request, $assetId)
    {
        try {
            // Memvalidasi request
            $validated = $request->validate([
                'date_acquired' => 'required|date',
                'acquisition_cost' => 'required|numeric',
                'salvage_value' => 'required|numeric',
                'asset_life_months' => 'required|integer',
                'depreciation_method' => 'required|string'
            ]);

            // Kirim permintaan perbarui ke API
            $result = $this->apiService->request('PUT', "/depreciations/asset/{$assetId}", [
                'json' => $validated
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API atau respon tidak berhasil
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui data depresiasi';

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

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Return success response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Data depresiasi berhasil diperbarui',
                    'data' => [
                        'asset_id' => (int) $assetId
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Data depresiasi berhasil diperbarui');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal memperbarui data depresiasi: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal memperbarui data depresiasi: ' . $e->getMessage());
        }
    }
}
