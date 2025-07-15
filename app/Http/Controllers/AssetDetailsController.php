<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DataFormatter;
use App\Http\Controllers\Traits\ApiResourceOperations;
use App\Http\Controllers\Traits\ExportableToPdf;

class AssetDetailsController extends Controller
{
    use ApiResourceOperations, ExportableToPdf;

    /**
     * Display a detail of the specified asset.
     *
     * @param int $id Asset ID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            // Get asset details by ID
            $result = $this->apiService->request('GET', "/assets/{$id}");

            // Log API response for debugging
            \Log::info('API response for asset details:', [
                'api_response_status' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'asset_id' => $id
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, request());
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            $apiError = $this->handleApiError($result, request(), 'Asset.AssetDetail', 'Gagal mengambil detail aset');
            if ($apiError) {
                return $apiError;
            }

            $asset = $result['data'] ?? null;

            if (!$asset) {
                $errorMessage = 'Aset tidak ditemukan atau data respons tidak valid';
                return redirect()->back()->with('error', $errorMessage);
            }

            // Generate QR code for the asset using API bulk endpoint
            if (isset($asset['asset_id'])) {
                try {
                    // Create array with single asset ID
                    $assetIds = [$asset['asset_id']];

                    // Call bulk QR code generation API
                    $qrResult = $this->apiService->request('POST', "/assets/qr/generate-bulk", [
                        'json' => [
                            'asset_ids' => $assetIds
                        ]
                    ]);

                    // If successful response with data, get QR code for the asset
                    if (isset($qrResult['success']) && $qrResult['success'] === true &&
                        isset($qrResult['data']) && is_array($qrResult['data']) && count($qrResult['data']) > 0) {

                        // Find QR data for this asset
                        foreach ($qrResult['data'] as $qrData) {
                            if (isset($qrData['asset_id']) && $qrData['asset_id'] == $asset['asset_id']) {
                                // If there's a base64 QR code in the response
                                if (isset($qrData['qr_base64'])) {
                                    $asset['qr_base64'] = $qrData['qr_base64'];
                                    break;
                                }
                                // Or if there's a QR URL that needs to be converted to base64
                                else if (isset($qrData['qr_url'])) {
                                    try {
                                        // Get correct API URL from backend config
                                        $backendUrl = rtrim(config('app.backend_url'), '/');
                                        $imageUrl = $backendUrl . "/public" . $qrData['qr_url'];

                                        // Try to get image content
                                        $imageData = @file_get_contents($imageUrl);
                                        if ($imageData !== false) {
                                            $asset['qr_base64'] = 'data:image/png;base64,' . base64_encode($imageData);
                                        }
                                    } catch (\Exception $qrImageEx) {
                                        // Continue without QR code
                                    }
                                    break;
                                }
                            }
                        }
                    } else if (isset($asset['qr_code']) && !empty($asset['qr_code'])) {
                        // If QR code is already provided in asset data
                        try {
                            $backendUrl = rtrim(config('app.backend_url'), '/');
                            $imageUrl = $backendUrl . "/public" . $asset['qr_code'];

                            $imageData = @file_get_contents($imageUrl);
                            if ($imageData !== false) {
                                $asset['qr_base64'] = 'data:image/png;base64,' . base64_encode($imageData);
                            }
                        } catch (\Exception $qrImageEx) {
                            // Continue without QR code
                        }
                    }
                } catch (\Exception $qrEx) {
                    // Continue without QR code if failed
                }
            }

            // Get asset masters for asset master dropdown
            $assetMastersResult = $this->apiService->request('GET', '/asset-masters', [
                'query' => [
                    'limit' => 1000,
                    'sort_by' => 'asset_master_id',
                    'sort_order' => 'asc'
                ]
            ]);
            $assetMasters = $assetMastersResult['data'] ?? [];

            // Convert asset image to base64
            if (!empty($asset['image_path'])) {
                try {
                    $backendUrl = config('app.backend_url', 'https://web-magangunbin2025.rsummi.co.id/api');
                    $imageUrl = $backendUrl . '/public/images/' . basename($asset['image_path']);
                    $imageData = file_get_contents($imageUrl);

                    if ($imageData !== false) {
                        $asset['image_base64'] = base64_encode($imageData);
                    }
                } catch (\Exception $e) {
                    // Continue without image if failed
                }
            }

            // Convert QR code to base64 if needed
            if (!empty($asset['qr_code_path'])) {
                try {
                    $backendUrl = config('app.backend_url', 'https://web-magangunbin2025.rsummi.co.id/api');
                    $qrPath = $backendUrl . '/public/qrcodes/' . basename($asset['qr_code_path']);
                    $qrData = file_get_contents($qrPath);
                    if ($qrData !== false) {
                        $asset['qr_base64'] = base64_encode($qrData);
                    }
                } catch (\Exception $e) {
                    // Continue without QR code if failed
                }
            }

            // Return view with asset details
            return view('Asset.AssetDetail', [
                'asset' => $asset,
                'assetMasters' => $assetMasters,
            ]);

        } catch (\Exception $e) {
            return $this->handleException($e, request(), 'Asset.AssetDetail');
        }
    }

    /**
     * Get asset details as JSON (for API requests)
     *
     * @param int $id Asset ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssetJson($id)
    {
        try {
            // Get asset by ID
            $result = $this->apiService->request('GET', "/assets/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Autentikasi gagal'
                ], 401);
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorMessage = DataFormatter::formatErrorMessage($result['errors'] ?? 'Gagal mengambil detail aset');

                return response()->json([
                    'success' => false,
                    'errors' => $errorMessage
                ], 400);
            }

            // Return asset data as JSON
            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal mengambil detail aset: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified asset.
     *
     * @param Request $request
     * @param int $id Asset ID
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function updateAsset(Request $request, $id)
    {
        try {
            // Define fields with their types
            $fields = [
                'serial_number' => 'string',
                'purchase_date' => 'string',
                'purchase_cost' => 'float',
                'warranty_end_date' => 'string',
                'user_id' => 'integer',
                'current_status' => 'string',
                'condition' => 'string',
                'room_id' => 'integer',
                'depreciation_method' => 'string',
                'acquisition_cost' => 'float',
                'salvage_value' => 'float',
                'asset_life_months' => 'integer',
                'date_acquired' => 'string'
            ];

            // Format request data
            $assetData = DataFormatter::formatRequestData($request, $fields);

            // Always include the asset ID
            $assetData['asset_id'] = $id;

            // Handle image upload if present
            if ($request->hasFile('image_file')) {
                $multipartData = [];

                // Send each asset data field individually in multipart
                foreach ($assetData as $key => $value) {
                    // Convert values appropriately for multipart
                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    } elseif (is_array($value)) {
                        $value = json_encode($value);
                    } elseif ($value === null) {
                        $value = ''; // Convert null to empty string for multipart
                    }

                        $multipartData[] = [
                            'name' => $key,
                        'contents' => $value
                        ];
                }

                // Add file upload
                $multipartData[] = [
                    'name' => 'image_file',
                    'contents' => fopen($request->file('image_file')->getPathname(), 'r'),
                    'filename' => $request->file('image_file')->getClientOriginalName()
                ];

                $options = ['multipart' => $multipartData];
                $result = $this->apiService->request('PUT', "/assets/{$id}", $options);
            } else {
                // No file upload, just send JSON data
                $options = ['json' => $assetData];
                $result = $this->apiService->request('PUT', "/assets/{$id}", $options);
            }

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] === false) {
                $errorMessage = DataFormatter::formatErrorMessage($result['errors'] ?? 'Gagal memperbarui aset');

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Success response
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Aset berhasil diperbarui',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('asset.details', ['id' => $id])
                ->with('success', 'Aset berhasil diperbarui');
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Asset.AssetDetail');
        }
    }

    /**
     * Checkout an asset.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function checkoutAsset(Request $request)
    {
        try {
            // Define checkout fields based on checkout type
            $fields = [
                'asset_id' => ['type' => 'integer', 'required' => true],
                'checkout_notes' => 'string'
            ];

            // Get basic checkout data
            $checkoutData = DataFormatter::formatRequestData($request, $fields);

            // Determine checkout type based on checkout_to_type
            if ($request->input('checkout_to_type') === 'location') {
                // Location checkout - include room_id
                $roomId = (int)($request->input('room_id') ?? $request->input('location_id') ?? 0);
                if ($roomId > 0) {
                    $checkoutData['room_id'] = $roomId;
                } else {
                    return redirect()->back()->with('error', 'ID ruangan harus berupa angka positif');
                }
            } else {
                // Employee checkout - include assigned_to
                $userId = (int)$request->input('assigned_to');
                if ($userId > 0) {
                    $checkoutData['assigned_to'] = $userId;
                } else {
                    return redirect()->back()->with('error', 'ID karyawan harus berupa angka positif');
                }
            }

            // Send request to API
            $result = $this->apiService->request('POST', '/asset-transfers/checkout', [
                'json' => $checkoutData
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] === false) {
                $errorMessage = DataFormatter::formatErrorMessage($result['errors'] ?? 'Gagal melakukan checkout aset');

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Return success response
            $successMessage = $result['message'] ?? 'Aset berhasil di-checkout';

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->back()->with('success', $successMessage);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Asset.AssetDetail');
        }
    }

    /**
     * Check in (return) an asset.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function checkinAsset(Request $request)
    {
        try {
            // Define fields for checkin
            $fields = [
                'asset_id' => ['type' => 'integer', 'required' => true],
                'return_notes' => 'string',
                'condition' => 'string'
            ];

            // Get checkin data
            $checkinData = DataFormatter::formatRequestData($request, $fields);

            // Send request to API
            $result = $this->apiService->request('POST', '/asset-transfers/return', [
                'json' => $checkinData
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] === false) {
                $errorMessage = DataFormatter::formatErrorMessage($result['errors'] ?? 'Gagal mengembalikan aset');

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Return success response
            $successMessage = $result['message'] ?? 'Aset berhasil dikembalikan';

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->back()->with('success', $successMessage);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Asset.AssetDetail');
        }
    }

    /**
     * Report asset as lost.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reportAssetLost(Request $request)
    {
        try {
            // Define fields for lost report
            $fields = [
                'asset_id' => ['type' => 'integer', 'required' => true],
                'loss_reason' => 'string'
            ];

            // Get lost data
            $lostData = DataFormatter::formatRequestData($request, $fields);

            // Send request to API
            $result = $this->apiService->request('POST', '/asset-transfers/loss', [
                'json' => $lostData
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] === false) {
                $errorMessage = DataFormatter::formatErrorMessage($result['errors'] ?? 'Gagal melaporkan aset hilang');

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Return success response
            $successMessage = $result['message'] ?? 'Aset berhasil dilaporkan hilang';

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->back()->with('success', $successMessage);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Asset.AssetDetail');
        }
    }

    /**
     * Report asset as found.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reportAssetFound(Request $request)
    {
        try {
            // Define fields for found report
            $fields = [
                'asset_id' => ['type' => 'integer', 'required' => true],
                'found_notes' => 'string'
            ];

            // Get found data
            $foundData = DataFormatter::formatRequestData($request, $fields);

            // Send request to API
            $result = $this->apiService->request('POST', '/asset-transfers/found', [
                'json' => $foundData
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] === false) {
                $errorMessage = DataFormatter::formatErrorMessage($result['errors'] ?? 'Gagal melaporkan aset ditemukan');

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Return success response
            $successMessage = $result['message'] ?? 'Aset berhasil dilaporkan ditemukan';

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->back()->with('success', $successMessage);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Asset.AssetDetail');
        }
    }

    /**
     * Dispose asset.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function disposeAsset(Request $request)
    {
        try {
            // Define fields for disposal
            $fields = [
                'asset_id' => ['type' => 'integer', 'required' => true],
                'disposal_reason' => ['type' => 'string', 'required' => true],
                'disposal_method' => 'string',
                'disposal_notes' => 'string'
            ];

            // Get disposal data
            $disposeData = DataFormatter::formatRequestData($request, $fields);

            // Send request to API
            $result = $this->apiService->request('POST', '/asset-transfers/dispose', [
                'json' => $disposeData
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] === false) {
                $errorMessage = DataFormatter::formatErrorMessage($result['errors'] ?? 'Gagal menghapus aset');

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Return success response
            $successMessage = $result['message'] ?? 'Aset berhasil dihapus';

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->back()->with('success', $successMessage);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Asset.AssetDetail');
        }
    }

    /**
     * Export asset details to PDF
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportAssetDetailPDF($id, Request $request)
    {
        try {
            // Get asset details
            $result = $this->apiService->request('GET', "/assets/{$id}");

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['data'])) {
                return redirect()->route('assets.index')->with('error', 'Aset tidak ditemukan');
            }

            $asset = $result['data'];

            // Convert asset image to base64
            if (!empty($asset['asset_master']['reference_image_path'])) {
                try {
                    $imagePath = 'https://web-magangunbin2025.rsummi.co.id/api/public' . $asset['asset_master']['reference_image_path'];
                    $imageData = file_get_contents($imagePath);
                    if ($imageData !== false) {
                        $asset['image_base64'] = base64_encode($imageData);
                    }
                } catch (\Exception $e) {
                    // Continue without image
                }
            }

            // Convert QR code to base64
            if (!empty($asset['qr_code'])) {
                try {
                    $qrPath = 'https://web-magangunbin2025.rsummi.co.id/api/public' . $asset['qr_code'];
                    $qrData = file_get_contents($qrPath);
                    if ($qrData !== false) {
                        $asset['qr_base64'] = base64_encode($qrData);
                    }
                } catch (\Exception $e) {
                    // Continue without QR code
                }
            }

            // Get depreciation data
            $depreciationData = null;
            try {
                $depreciationResult = $this->apiService->request('GET', "/depreciations/calculate/asset/{$id}");
                if (isset($depreciationResult['success']) && $depreciationResult['success'] === true) {
                    $depreciationData = $depreciationResult['data']['depreciation'] ?? $depreciationResult['depreciation'] ?? null;
                }
            } catch (\Exception $e) {
                // Continue without depreciation data
            }

            // Get finance transaction data
            $financeData = null;
            try {
                $financeResult = $this->apiService->request('GET', "/asset-transactions/asset/{$id}", [
                    'query' => [
                        'limit' => 100,
                        'sort_by' => 'transaction_date',
                        'sort_order' => 'desc'
                    ]
                ]);

                if (isset($financeResult['success']) && $financeResult['success'] === true) {
                    $financeData = [
                        'transactions' => $financeResult['data']['transactions'] ?? [],
                        'summary' => $financeResult['data']['summary'] ?? null
                    ];
                }
            } catch (\Exception $e) {
                // Continue without finance data
            }

            // Generate filename
            $filename = 'detail_aset_' . $id . '_' . now()->format('YmdHis') . '.pdf';

            // Stream the PDF to browser
            return $this->streamPdf('Asset.AssetDetailPDF', [
                'asset' => $asset,
                'date_generated' => now()->format('d M Y H:i:s'),
                'depreciation' => $depreciationData,
                'finance' => $financeData
            ], $filename);

        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Asset.AssetDetail');
        }
    }
}
