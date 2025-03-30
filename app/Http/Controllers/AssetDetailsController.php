<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use App\Http\Controllers\AssetDocumentController;

class AssetDetailsController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display the details of a specific asset.
     *
     * @param int $id The asset ID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            // Log request info
            \Log::info('Fetching asset details with ID:', [
                'asset_id' => $id,
                'request_url' => request()->fullUrl()
            ]);

            // Fetch the asset with the given ID
            $result = $this->apiService->request('GET', "/assets/{$id}");

            // Log API response for debugging
            \Log::info('API response for asset details:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'asset_id' => $id
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during asset details retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors based on status flag
            if (!isset($result['status']) || $result['status'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to retrieve asset details';

                \Log::warning('Error during asset details retrieval:', [
                    'status' => $result['status'] ?? false,
                    'message' => $errorMessage
                ]);

                return redirect()->back()->with('error', $errorMessage);
            }

            $asset = $result['data'] ?? null;

            if (!$asset) {
                $errorMessage = 'Asset not found or response data is invalid';
                return redirect()->back()->with('error', $errorMessage);
            }

            // Return the view with asset details
            return view('Asset.AssetDetail', ['asset' => $asset]);

        } catch (\Exception $e) {
            \Log::error('Exception during asset details retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $id
            ]);

            return redirect()->back()->with('error', 'Failed to retrieve asset details: ' . $e->getMessage());
        }
    }

    /**
     * Get asset details as JSON (for API requests)
     *
     * @param int $id The asset ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssetJson($id)
    {
        try {
            // Fetch the asset with the given ID
            $result = $this->apiService->request('GET', "/assets/{$id}");

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return response()->json([
                    'status' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check for API errors
            if (!isset($result['status']) || $result['status'] !== true) {
                return response()->json([
                    'status' => false,
                    'message' => $result['message'] ?? 'Failed to retrieve asset details'
                ], 400);
            }

            // Return the asset data as JSON
            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Exception during JSON asset details retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $id
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve asset details: ' . $e->getMessage()
            ], 500);
        }
    }
}
