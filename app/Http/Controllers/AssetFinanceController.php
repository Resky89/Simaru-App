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
            // Get pagination parameters
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit
            ];

            // Filter parameters
            if ($request->has('filter')) {
                $queryParams['filter'] = $request->input('filter');
            }

            // Sort parameters
            if ($request->has('sort')) {
                $queryParams['sort'] = $request->input('sort');
            }

            // Fetch the transactions for the given asset ID
            $result = $this->apiService->request('GET', "/asset-transactions/asset/{$assetId}", [
                'query' => $queryParams
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
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function createTransaction(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'asset_id' => 'required|numeric',
                'type' => 'required|string|in:expense,income',
                'amount' => 'required|numeric',
                'transaction_date' => 'required|date',
                'description' => 'nullable|string'
            ]);

            // Ensure numeric values are properly formatted
            $data = [
                'asset_id' => (int) $request->asset_id,
                'type' => $request->type,
                'amount' => (float) $request->amount,
                'transaction_date' => $request->transaction_date,
                'description' => $request->description
            ];

            // Create the transaction via API service
            $result = $this->apiService->request('POST', '/asset-transactions', [
                'json' => $data
            ]);

            // Only log errors
            if (!isset($result['success']) || $result['success'] !== true) {
                \Log::warning('API transaction creation failed:', $result);
            }

            // Check for success in the response
            $isSuccess = isset($result['success']) && $result['success'] === true;

            if (!$isSuccess) {
                $errorMessage = $result['message'] ?? $result['errors'] ?? 'Gagal membuat transaksi';

                // Fix: Convert array to string if errorMessage is an array
                if (is_array($errorMessage)) {
                    $messageString = '';
                    foreach ($errorMessage as $key => $value) {
                        if (is_array($value)) {
                            $messageString .= implode(', ', $value) . '; ';
                        } else {
                            $messageString .= (is_string($key) ? "$key: " : '') . "$value; ";
                        }
                    }
                    $errorMessage = trim($messageString);
                }

                throw new \Exception($errorMessage);
            }

            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil ditambahkan',
                    'data' => $result['data'] ?? null
                ]);
            }

            // If it's a regular form submission, redirect with success message
            return redirect()->back()->with('success', 'Transaksi berhasil ditambahkan');

        } catch (\Exception $e) {
            \Log::error('Transaction creation error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat transaksi: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal membuat transaksi: ' . $e->getMessage());
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
                'asset_id' => 'required|numeric',
                'type' => 'required|string|in:expense,income',
                'amount' => 'required|numeric',
                'transaction_date' => 'required|date',
                'description' => 'nullable|string'
            ]);

            // Ensure numeric values are properly formatted
            $data = [
                'asset_id' => (int) $request->asset_id,
                'type' => $request->type,
                'amount' => (float) $request->amount,
                'transaction_date' => $request->transaction_date,
                'description' => $request->description
            ];

            // Update the transaction via API service
            $result = $this->apiService->request('PUT', "/asset-transactions/{$transactionId}", [
                'json' => $data
            ]);

            // Only log errors
            if (!isset($result['success']) || $result['success'] !== true) {
                \Log::warning('API transaction update failed:', [
                    'transaction_id' => $transactionId,
                    'response' => $result
                ]);
            }

            // Check for success in the response
            $isSuccess = isset($result['success']) && $result['success'] === true;

            if (!$isSuccess) {
                $errorMessage = $result['message'] ?? $result['errors'] ?? 'Gagal mengupdate transaksi';

                // Fix: Convert array to string if errorMessage is an array
                if (is_array($errorMessage)) {
                    $messageString = '';
                    foreach ($errorMessage as $key => $value) {
                        if (is_array($value)) {
                            $messageString .= implode(', ', $value) . '; ';
                        } else {
                            $messageString .= (is_string($key) ? "$key: " : '') . "$value; ";
                        }
                    }
                    $errorMessage = trim($messageString);
                }

                throw new \Exception($errorMessage);
            }

            // Return JSON response for toast notification
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diperbarui',
                'data' => $result['data'] ?? null
            ]);

        } catch (\Exception $e) {
            \Log::error('Transaction update error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'transaction_id' => $transactionId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate transaksi: ' . $e->getMessage()
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
            // Delete the transaction via API service
            $result = $this->apiService->request('DELETE', "/asset-transactions/{$transactionId}");

            // Only log errors
            if (!isset($result['success']) || $result['success'] !== true) {
                \Log::warning('API transaction deletion failed:', [
                    'transaction_id' => $transactionId,
                    'response' => $result
                ]);
            }

            // Check for success in the response
            $isSuccess = isset($result['success']) && $result['success'] === true;

            if (!$isSuccess) {
                $errorMessage = $result['message'] ?? $result['errors'] ?? 'Gagal menghapus transaksi';

                // Fix: Convert array to string if errorMessage is an array
                if (is_array($errorMessage)) {
                    $messageString = '';
                    foreach ($errorMessage as $key => $value) {
                        if (is_array($value)) {
                            $messageString .= implode(', ', $value) . '; ';
                        } else {
                            $messageString .= (is_string($key) ? "$key: " : '') . "$value; ";
                        }
                    }
                    $errorMessage = trim($messageString);
                }

                throw new \Exception($errorMessage);
            }

            // Return JSON response for toast notification
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            \Log::error('Transaction deletion error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'transaction_id' => $transactionId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi: ' . $e->getMessage()
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
            // Fetch the transaction details
            $result = $this->apiService->request('GET', "/asset-transactions/{$transactionId}");

            // Check for success in the response
            $isSuccess = isset($result['success']) && $result['success'] === true;

            if (!$isSuccess) {
                $errorMessage = $result['message'] ?? $result['errors'] ?? 'Failed to retrieve transaction';

                // Fix: Convert array to string if errorMessage is an array
                if (is_array($errorMessage)) {
                    $messageString = '';
                    foreach ($errorMessage as $key => $value) {
                        if (is_array($value)) {
                            $messageString .= implode(', ', $value) . '; ';
                        } else {
                            $messageString .= (is_string($key) ? "$key: " : '') . "$value; ";
                        }
                    }
                    $errorMessage = trim($messageString);
                }

                throw new \Exception($errorMessage);
            }

            // Return the transaction data as JSON
            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Transaction retrieval error:', [
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
