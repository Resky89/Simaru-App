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
                $filter = $request->input('filter');

                // Only pass valid filter values (income or expense)
                if (in_array($filter, ['income', 'expense'])) {
                    $queryParams['type'] = $filter; // Use 'type' parameter for the API
                }
            }

            // Sort parameters
            if ($request->has('sort')) {
                $sort = $request->input('sort');

                // Map frontend sort values to API parameters
                switch ($sort) {
                    case 'newest':
                        $queryParams['sort_by'] = 'transaction_date';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    case 'oldest':
                        $queryParams['sort_by'] = 'transaction_date';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'amount-high':
                        $queryParams['sort_by'] = 'amount';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    case 'amount-low':
                        $queryParams['sort_by'] = 'amount';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    default:
                        // Default sort (newest first)
                        $queryParams['sort_by'] = 'transaction_date';
                        $queryParams['sort_order'] = 'desc';
                }
            }

            // Fetch the transactions for the given asset ID
            $result = $this->apiService->request('GET', "/asset-transactions/asset/{$assetId}", [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($result['errors']) && (is_array($result['errors']) &&
                (isset($result['errors']['auth_failed']) || isset($result['errors']['session_expired'])) ||
                in_array($result['errors'], ['auth_failed', 'session_expired']))) {
                \Log::warning('Authentication error during asset transactions retrieval:', [
                    'errors' => $result['errors']
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['auth' => 'Authentication failed']
                ], status: 401);
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $formattedErrors = ['general' => 'Failed to retrieve asset transactions'];

                if (isset($result['errors'])) {
                    if (is_string($result['errors'])) {
                        $formattedErrors = ['general' => $result['errors']];
                    } elseif (is_array($result['errors'])) {
                        $formattedErrors = $result['errors'];
                    }
                }

                \Log::warning('Error during asset transactions retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $formattedErrors
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors
                ], status: 400);
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
                'errors' => ['exception' => 'Failed to retrieve asset transactions: ' . $e->getMessage()]
            ], status: 500);
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

            // Check for success in the response
            $isSuccess = isset($result['success']) && $result['success'] === true;

            if (!$isSuccess) {
                $formattedErrors = ['general' => 'Gagal membuat transaksi'];

                if (isset($result['errors'])) {
                    if (is_string($result['errors'])) {
                        $formattedErrors = ['general' => $result['errors']];
                    } elseif (is_array($result['errors'])) {
                        $formattedErrors = $result['errors'];
                    }
                }

                \Log::warning('API transaction creation failed:', [
                    'response' => $result,
                    'errors' => $formattedErrors
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors
                ], status: 400);
            }

            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
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
                    'errors' => ['exception' => 'Gagal membuat transaksi: ' . $e->getMessage()]
                ], status: 500);
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

            // Check for success in the response
            $isSuccess = isset($result['success']) && $result['success'] === true;

            if (!$isSuccess) {
                $formattedErrors = ['general' => 'Gagal mengupdate transaksi'];

                if (isset($result['errors'])) {
                    if (is_string($result['errors'])) {
                        $formattedErrors = ['general' => $result['errors']];
                    } elseif (is_array($result['errors'])) {
                        $formattedErrors = $result['errors'];
                    }
                }

                \Log::warning('API transaction update failed:', [
                    'transaction_id' => $transactionId,
                    'response' => $result,
                    'errors' => $formattedErrors
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors
                ], status: 400);
            }

            // Return JSON response for toast notification
            return response()->json([
                'success' => true,
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
                'errors' => ['exception' => 'Gagal mengupdate transaksi: ' . $e->getMessage()]
            ], status: 500);
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

            // Check for success in the response
            $isSuccess = isset($result['success']) && $result['success'] === true;

            if (!$isSuccess) {
                $formattedErrors = ['general' => 'Gagal menghapus transaksi'];

                if (isset($result['errors'])) {
                    if (is_string($result['errors'])) {
                        $formattedErrors = ['general' => $result['errors']];
                    } elseif (is_array($result['errors'])) {
                        $formattedErrors = $result['errors'];
                    }
                }

                \Log::warning('API transaction deletion failed:', [
                    'transaction_id' => $transactionId,
                    'response' => $result,
                    'errors' => $formattedErrors
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors
                ], status: 400);
            }

            // Return JSON response for toast notification
            return response()->json([
                'success' => true,
                'data' => null
            ]);

        } catch (\Exception $e) {
            \Log::error('Transaction deletion error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'transaction_id' => $transactionId
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => 'Gagal menghapus transaksi: ' . $e->getMessage()]
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
                $formattedErrors = ['general' => 'Failed to retrieve transaction'];

                if (isset($result['errors'])) {
                    if (is_string($result['errors'])) {
                        $formattedErrors = ['general' => $result['errors']];
                    } elseif (is_array($result['errors'])) {
                        $formattedErrors = $result['errors'];
                    }
                }

                \Log::warning('Transaction retrieval failed:', [
                    'transaction_id' => $transactionId,
                    'response' => $result,
                    'errors' => $formattedErrors
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors
                ], status: 400);
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
                'errors' => ['exception' => 'Failed to retrieve transaction: ' . $e->getMessage()]
            ], 500);
        }
    }
}
