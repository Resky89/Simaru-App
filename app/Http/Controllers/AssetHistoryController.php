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
     * Get the history of a specific asset.
     *
     * @param int $id The asset ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssetHistory($id)
    {
        try {
            // Log request info
            \Log::info('Fetching asset history with ID:', [
                'asset_id' => $id,
                'request_url' => request()->fullUrl()
            ]);

            // Fetch the asset history with the given ID
            $result = $this->apiService->request('GET', "/asset-histories/{$id}");

            // Log API response for debugging
            \Log::info('API response for asset history:', [
                'api_response_success' => $result['success'] ?? null,
                'api_response_errors' => $result['errors'] ?? null,
                'asset_id' => $id
            ]);

            // Check for auth errors
            if (isset($result['errors']) && (is_array($result['errors']) &&
                (isset($result['errors']['auth_failed']) || isset($result['errors']['session_expired'])) ||
                in_array($result['errors'], ['auth_failed', 'session_expired']))) {
                \Log::warning('Authentication error during asset history retrieval:', [
                    'errors' => $result['errors']
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['auth' => 'Authentication failed']
                ], status: 401);
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $formattedErrors = ['general' => 'Failed to retrieve asset history'];

                if (isset($result['errors'])) {
                    if (is_string($result['errors'])) {
                        $formattedErrors = ['general' => $result['errors']];
                    } elseif (is_array($result['errors'])) {
                        $formattedErrors = $result['errors'];
                    }
                }

                \Log::warning('Error during asset history retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $formattedErrors
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors
                ], status: 400);
            }

            // Return the response as is
            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Exception during asset history retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => 'Failed to retrieve asset history: ' . $e->getMessage()]
            ], status: 500);
        }
    }
}
