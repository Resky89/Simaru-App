<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DataFormatter;
use App\Http\Controllers\Traits\ApiResourceOperations;
use App\Http\Controllers\Traits\ExportableToPdf;

class AssetDepreciationController extends Controller
{
    use ApiResourceOperations, ExportableToPdf;

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

            // Handle auth errors
            $authError = $this->handleAuthError($result, request());
            if ($authError) {
                return $authError;
            }

            // Check for the specific "asset cannot be depreciated" error in various formats
            if (isset($result['errors'])) {
                // Format 1: Array of objects with path and message
                if (is_array($result['errors'])) {
                    foreach ($result['errors'] as $error) {
                        if (is_array($error) &&
                            isset($error['path']) && $error['path'] === 'general' &&
                            isset($error['message']) && $error['message'] === 'Aset tidak dapat didepresiasi') {

                            // Return this as a valid response with a special flag
                            return response()->json([
                                'success' => true,
                                'no_depreciation' => true,
                                'message' => 'Aset tidak dapat didepresiasi',
                                'data' => [
                                    'asset_id' => (int) $assetId
                                ]
                            ]);
                        }
                    }
                }

                // Format 2: String that contains the specific message
                if (is_string($result['errors']) &&
                    strpos($result['errors'], 'Aset tidak dapat didepresiasi') !== false) {

                    return response()->json([
                        'success' => true,
                        'no_depreciation' => true,
                        'message' => 'Aset tidak dapat didepresiasi',
                        'data' => [
                            'asset_id' => (int) $assetId
                        ]
                    ]);
                }

                // Format 3: Message contained in a nested error structure
                if (isset($result['message']) &&
                    strpos($result['message'], 'Aset tidak dapat didepresiasi') !== false) {

                    return response()->json([
                        'success' => true,
                        'no_depreciation' => true,
                        'message' => 'Aset tidak dapat didepresiasi',
                        'data' => [
                            'asset_id' => (int) $assetId
                        ]
                    ]);
                }
            }

            // Handle API errors
            $apiError = $this->handleApiError(
                $result, 
                request(), 
                'Asset.Depreciation', 
                'Gagal mengambil data depresiasi'
            );
            if ($apiError) {
                return $apiError;
            }

            // Return the depreciation data as JSON
            return response()->json($result);

        } catch (\Exception $e) {
            return $this->handleException($e, request(), 'Asset.Depreciation');
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
            $fields = [
                'date_acquired' => ['type' => 'date', 'required' => true],
                'acquisition_cost' => ['type' => 'numeric', 'required' => true],
                'salvage_value' => ['type' => 'numeric', 'required' => true],
                'asset_life_months' => ['type' => 'integer', 'required' => true],
                'depreciation_method' => ['type' => 'string', 'required' => true]
            ];

            $data = DataFormatter::formatRequestData($request, $fields);

            // Kirim permintaan perbarui ke API
            $result = $this->apiService->request('PUT', "/depreciations/asset/{$assetId}", [
                'json' => $data
            ]);

            // Handle auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Handle API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui data depresiasi';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
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
            return $this->handleException($e, $request, 'Asset.Depreciation');
        }
    }

    /**
     * Export asset depreciation to PDF.
     */
    public function exportAssetDepreciationPDF(Request $request, $assetId)
    {
        try {
            // Get depreciation data for export
            $depreciationResult = $this->apiService->request('GET', "/depreciations/calculate/asset/{$assetId}");

            // Handle errors
            $authError = $this->handleAuthError($depreciationResult, $request);
            if ($authError) {
                return $authError;
            }

            $apiError = $this->handleApiError(
                $depreciationResult, 
                $request, 
                'Asset.Depreciation', 
                'Gagal mengambil data untuk ekspor'
            );
            if ($apiError) {
                return $apiError;
            }

            $depreciationData = $depreciationResult['data'] ?? [];
            $assetData = $depreciationResult['asset'] ?? [];

            // Generate filename
            $timestamp = date('YmdHis');
            $filename = "laporan_depresiasi_aset_{$assetId}_{$timestamp}.pdf";

            // Generate PDF
            return $this->generatePdf(
                'Asset.DepreciationPDF',
                [
                    'depreciationData' => $depreciationData,
                    'assetData' => $assetData,
                    'date_generated' => date('d M Y H:i:s')
                ],
                $filename,
                'portrait'
            );
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Asset.Depreciation');
        }
    }
}
