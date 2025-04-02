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
}
