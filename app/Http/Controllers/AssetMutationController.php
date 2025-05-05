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
     * Get mutation history for a specific asset
     *
     * @param int $id The asset ID
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View
     */
    public function getAssetMutationHistory($id)
    {
        try {
            // Log request info
            \Log::info('Fetching asset mutation history for asset ID:', [
                'asset_id' => $id,
                'request_url' => request()->fullUrl()
            ]);

            // Fetch the mutation history for the given asset ID
            $result = $this->apiService->request('GET', "/asset-histories/status/{$id}");

            // Log API response for debugging
            \Log::info('API response for asset mutation history:', [
                'api_response_success' => $result['success'] ?? null,
                'api_response_errors' => $result['errors'] ?? null,
                'asset_id' => $id
            ]);

            // Check for auth errors
            if (isset($result['errors']) && (is_array($result['errors']) &&
                (isset($result['errors']['auth_failed']) || isset($result['errors']['session_expired'])) ||
                in_array($result['errors'], ['auth_failed', 'session_expired']))) {
                \Log::warning('Authentication error during asset mutation history retrieval:', [
                    'errors' => $result['errors']
                ]);

                if (request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['auth' => 'Authentication failed']
                    ], status: 401);
                }

                return redirect()->route('login')->with('error', 'Authentication failed');
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $formattedErrors = ['general' => 'Failed to retrieve asset mutation history'];

                if (isset($result['errors'])) {
                    if (is_string($result['errors'])) {
                        $formattedErrors = ['general' => $result['errors']];
                    } elseif (is_array($result['errors'])) {
                        $formattedErrors = $result['errors'];
                    }
                }

                \Log::warning('Error during asset mutation history retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $formattedErrors
                ]);

                if (request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $formattedErrors
                    ], status: 400);
                }

                // Format error message for redirect
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

                return back()->with('error', $errorMessage);
            }

            // Return the data as JSON if requested
            if (request()->wantsJson()) {
                return response()->json($result);
            }

            // For web views, return a view with the data
            return view('asset.mutation-history', [
                'assetId' => $id,
                'mutations' => $result['data']['histories'] ?? [],
                'assetDetails' => $result['data'] ?? []
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during asset mutation history retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $id
            ]);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Failed to retrieve asset mutation history: ' . $e->getMessage()]
                ], status: 500);
            }

            return back()->with('error', 'Failed to retrieve asset mutation history: ' . $e->getMessage());
        }
    }

    /**
     * Get mutation history for an asset with a web display
     *
     * @param int $id The asset ID
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showAssetMutationHistory($id)
    {
        try {
            // Reuse the API call method
            $response = $this->getAssetMutationHistory($id);

            // If it's already a response intended for a view, return it
            if (!($response instanceof \Illuminate\Http\JsonResponse)) {
                return $response;
            }

            // If it's a JSON response, we need to extract the data
            $responseData = json_decode($response->getContent(), true);

            if (!isset($responseData['success']) || $responseData['success'] !== true) {
                // Format error message if needed
                $formattedErrors = $responseData['errors'] ?? ['general' => 'Failed to retrieve asset mutation history'];

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

                return back()->with('error', $errorMessage);
            }

            return view('asset.mutation-history', [
                'assetId' => $id,
                'mutations' => $responseData['data']['histories'] ?? [],
                'assetDetails' => $responseData['data'] ?? []
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during asset mutation history display:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $id
            ]);

            return back()->with('error', 'Failed to display asset mutation history: ' . $e->getMessage());
        }
    }
}
