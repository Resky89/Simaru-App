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
                'api_response_success' => $result['success'] ?? null,
                'api_response_errors' => $result['errors'] ?? null,
                'asset_id' => $assetId
            ]);

            // Check for auth errors
            if (isset($result['errors']) && (is_array($result['errors']) &&
                (isset($result['errors']['auth_failed']) || isset($result['errors']['session_expired'])) ||
                in_array($result['errors'], ['auth_failed', 'session_expired']))) {
                Log::warning('Authentication error during depreciation data retrieval:', [
                    'errors' => $result['errors']
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['auth' => 'Authentication failed']
                ], status: 401);
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                // Properly format errors based on response structure
                $formattedErrors = ['general' => 'Failed to retrieve depreciation data'];

                if (isset($result['errors'])) {
                    if (is_string($result['errors'])) {
                        $formattedErrors = ['general' => $result['errors']];
                    } elseif (is_array($result['errors'])) {
                        $formattedErrors = $result['errors'];
                    }
                }

                Log::warning('Error during depreciation data retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $formattedErrors
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors
                ], status: 400);
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
                'success' => false,
                'errors' => ['server' => 'Failed to retrieve depreciation data: ' . $e->getMessage()]
            ], status: 500);
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
            // Get request data properly depending on content type
            $requestData = $request->json()->all();

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
            $result = $this->apiService->request('PUT', "/depreciations/asset/{$assetId}", [
                'json' => $validated
            ]);

            // Log API response for debugging
            \Log::info('API response for depreciation update:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_success' => $result['success'] ?? null,
                'api_response_errors' => $result['errors'] ?? null,
                'asset_id' => $assetId
            ]);

            // Check for auth errors
            if (isset($result['errors']) && (is_array($result['errors']) &&
                (isset($result['errors']['auth_failed']) || isset($result['errors']['session_expired'])) ||
                in_array($result['errors'], ['auth_failed', 'session_expired']))) {
                \Log::warning('Authentication error during depreciation update:', [
                    'errors' => $result['errors']
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['auth' => 'Authentication failed']
                    ], status: 401);
                }

                return redirect()->route('login')->with('error', 'Authentication failed');
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                // Properly format errors based on response structure
                $formattedErrors = ['general' => 'Failed to update depreciation data'];

                if (isset($result['errors'])) {
                    if (is_string($result['errors'])) {
                        $formattedErrors = ['general' => $result['errors']];
                    } elseif (is_array($result['errors'])) {
                        $formattedErrors = $result['errors'];
                    }
                }

                \Log::warning('Error during depreciation update:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $formattedErrors
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $formattedErrors
                    ], status: 400);
                }

                // For redirect responses, format errors as string if needed
                $errorMessage = '';
                if (is_array($formattedErrors)) {
                    foreach ($formattedErrors as $field => $messages) {
                        if (is_array($messages)) {
                            $errorMessage .= implode(', ', $messages) . '; ';
                        } else {
                            $errorMessage .= $messages . '; ';
                        }
                    }
                } else {
                    $errorMessage = $formattedErrors;
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Return success response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'status' => true,
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
                    'success' => false,
                    'errors' => $e->errors()
                ], status: 422);
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
                    'success' => false,
                    'errors' => ['server' => 'Failed to update depreciation data: ' . $e->getMessage()]
                ], status: 500);
            }

            return redirect()->back()->with('error', 'Failed to update depreciation data: ' . $e->getMessage());
        }
    }
}
