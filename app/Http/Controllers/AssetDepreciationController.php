<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\ApiService;

class AssetDepreciationController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Get depreciation data for a specific asset
     *
     * @param int $assetId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssetDepreciation($assetId)
    {
        try {
            // Log request info
            Log::info('Fetching depreciation data for asset ID:', [
                'asset_id' => $assetId,
                'request_url' => request()->fullUrl()
            ]);

            // Fetch the depreciation data using ApiService
            $result = $this->apiService->request('GET', "/depreciations/calculate/asset/{$assetId}");

            // Log API response for debugging
            Log::info('API response for depreciation data:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'asset_id' => $assetId
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                Log::warning('Authentication error during depreciation data retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'status' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check for API errors
            if (!isset($result['status']) || $result['status'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to retrieve depreciation data';

                Log::warning('Error during depreciation data retrieval:', [
                    'status' => $result['status'] ?? false,
                    'message' => $errorMessage
                ]);

                return response()->json([
                    'status' => false,
                    'message' => $errorMessage
                ], 400);
            }

            // Return the depreciation data as JSON
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Exception during depreciation data retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $assetId
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve depreciation data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update depreciation data for a specific asset
     *
     * @param Request $request
     * @param int $assetId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updateAssetDepreciation(Request $request, $assetId)
    {
        try {
            // Validate request data
            $validated = $request->validate([
                'date_acquired' => 'required|date',
                'acquisition_cost' => 'required|numeric',
                'salvage_value' => 'required|numeric',
                'asset_life_months' => 'required|integer',
                'depreciation_method' => 'required|string'
            ]);

            // Log request info
            \Log::info('Updating depreciation data for asset ID:', [
                'asset_id' => $assetId,
                'request_url' => request()->fullUrl(),
                'request_data' => $validated
            ]);

            // Send update request to API
            $result = $this->apiService->request('PUT', "/depreciations/asset/{$assetId}", $validated);

            // Log API response for debugging
            \Log::info('API response for depreciation update:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'asset_id' => $assetId
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during depreciation update:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => false,
                        'message' => $result['message'] ?? 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors
            if (!isset($result['status']) || $result['status'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to update depreciation data';

                \Log::warning('Error during depreciation update:', [
                    'status' => $result['status'] ?? false,
                    'message' => $errorMessage
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => false,
                        'message' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Return success response
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Asset depreciation updated successfully',
                    'data' => [
                        'asset_id' => (int) $assetId
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Asset depreciation updated successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation error during depreciation update:', [
                'errors' => $e->errors(),
                'asset_id' => $assetId
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            \Log::error('Exception during depreciation update:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $assetId
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to update depreciation data: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update depreciation data: ' . $e->getMessage());
        }
    }
}
