<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class AssetFinanceController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Get all financial transactions for a specific asset
     *
     * @param int $assetId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllTransactions($assetId, Request $request)
    {
        try {
            // Log request info
            \Log::info('Fetching asset transactions for asset ID:', [
                'asset_id' => $assetId,
                'request_url' => request()->fullUrl()
            ]);

            // Get pagination parameters
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit
            ];

            // Fetch the transactions for the given asset ID
            $result = $this->apiService->request('GET', "/asset-transactions/asset/{$assetId}", [
                'query' => $queryParams
            ]);

            // Log API response for debugging
            \Log::info('API response for asset transactions:', [
                'api_response_status' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'asset_id' => $assetId
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during asset transactions retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to retrieve asset transactions';

                \Log::warning('Error during asset transactions retrieval:', [
                    'success' => $result['success'] ?? false,
                    'message' => $errorMessage
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }

            // Return the transactions data as JSON
            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Exception during asset transactions retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $assetId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve asset transactions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new financial transaction for an asset
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTransaction(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'asset_id' => 'required|integer',
                'type' => 'required|string|in:expense,income',
                'amount' => 'required|numeric',
                'transaction_date' => 'required|date',
                'description' => 'nullable|string'
            ]);

            // Log request info
            \Log::info('Creating asset transaction:', [
                'asset_id' => $request->asset_id,
                'type' => $request->type,
                'request_url' => request()->fullUrl()
            ]);

            // Create the transaction via API service
            $result = $this->apiService->request('POST', '/asset-transactions', [
                'json' => $request->all()
            ]);

            // Log API response for debugging
            \Log::info('API response for transaction creation:', [
                'api_response_status' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'asset_id' => $request->asset_id
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during transaction creation:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to create transaction';

                \Log::warning('Error during transaction creation:', [
                    'success' => $result['success'] ?? false,
                    'message' => $errorMessage
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }

            // Return the transaction data as JSON
            return response()->json($result, 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation error during transaction creation:', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Exception during transaction creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing financial transaction
     *
     * @param int $transactionId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTransaction($transactionId, Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'asset_id' => 'sometimes|required|integer',
                'type' => 'sometimes|required|string|in:expense,income',
                'amount' => 'sometimes|required|numeric',
                'transaction_date' => 'sometimes|required|date',
                'description' => 'nullable|string'
            ]);

            // Log request info
            \Log::info('Updating asset transaction:', [
                'transaction_id' => $transactionId,
                'request_url' => request()->fullUrl()
            ]);

            // Update the transaction via API service
            $result = $this->apiService->request('PUT', "/asset-transactions/{$transactionId}", [
                'json' => $request->all()
            ]);

            // Log API response for debugging
            \Log::info('API response for transaction update:', [
                'api_response_status' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'transaction_id' => $transactionId
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during transaction update:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to update transaction';

                \Log::warning('Error during transaction update:', [
                    'success' => $result['success'] ?? false,
                    'message' => $errorMessage
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }

            // Return the updated transaction data as JSON
            return response()->json($result);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation error during transaction update:', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Exception during transaction update:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'transaction_id' => $transactionId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a financial transaction
     *
     * @param int $transactionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteTransaction($transactionId)
    {
        try {
            // Log request info
            \Log::info('Deleting asset transaction:', [
                'transaction_id' => $transactionId,
                'request_url' => request()->fullUrl()
            ]);

            // Delete the transaction via API service
            $result = $this->apiService->request('DELETE', "/asset-transactions/{$transactionId}");

            // Log API response for debugging
            \Log::info('API response for transaction deletion:', [
                'api_response_status' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'transaction_id' => $transactionId
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during transaction deletion:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to delete transaction';

                \Log::warning('Error during transaction deletion:', [
                    'success' => $result['success'] ?? false,
                    'message' => $errorMessage
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during transaction deletion:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'transaction_id' => $transactionId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single financial transaction
     *
     * @param int $transactionId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransaction($transactionId)
    {
        try {
            // Log request info
            \Log::info('Fetching transaction details:', [
                'transaction_id' => $transactionId,
                'request_url' => request()->fullUrl()
            ]);

            // Fetch the transaction details
            $result = $this->apiService->request('GET', "/asset-transactions/{$transactionId}");

            // Log API response for debugging
            \Log::info('API response for transaction details:', [
                'api_response_status' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'transaction_id' => $transactionId
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during transaction retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to retrieve transaction';

                \Log::warning('Error during transaction retrieval:', [
                    'success' => $result['success'] ?? false,
                    'message' => $errorMessage
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }

            // Return the transaction data as JSON
            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Exception during transaction retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'transaction_id' => $transactionId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve transaction: ' . $e->getMessage()
            ], 500);
        }
    }
}
