<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Barryvdh\DomPDF\Facade\Pdf;

class FinanceReportController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Get all asset transactions.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function getAllTransactions(Request $request)
    {
        try {
            // Get pagination parameters
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');
            $sort = $request->input('sort', 'newest');

            // Log request info
            \Log::info('Fetching all asset transactions with parameters:', [
                'page' => $page,
                'limit' => $limit,
                'search' => $search,
                'sort' => $sort,
                'request_url' => $request->fullUrl(),
                'ajax' => $request->ajax()
            ]);

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit
            ];

            // Add search parameter if provided
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Handle sorting
            switch ($sort) {
                case 'newest':
                    $queryParams['sort_by'] = 'created_at';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'oldest':
                    $queryParams['sort_by'] = 'created_at';
                    $queryParams['sort_order'] = 'asc';
                    break;
                default:
                    // Default sort (newest first)
                    $queryParams['sort_by'] = 'created_at';
                    $queryParams['sort_order'] = 'desc';
            }

            // Fetch asset transactions from API
            $result = $this->apiService->request('GET', '/asset-transactions', [
                'query' => $queryParams
            ]);

            // Log API response for debugging
            \Log::info('API response for asset transactions:', [
                'api_response_success' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'data_count' => isset($result['data']) ? count($result['data']) : 0
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during asset transactions retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], status: 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Failed to retrieve asset transactions';

                \Log::warning('Error during asset transactions retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], status: 400);
                }

                // Format error message for view
                $errorMessage = '';
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $errorMessage .= implode(', ', $messages) . '; ';
                        } else {
                            $errorMessage .= $messages . '; ';
                        }
                    }
                } else {
                    $errorMessage = $errorData;
                }

                return view('Report.FinanceReport.FinanceReport', [
                    'transactions' => [],
                    'pagination' => null,
                    'search' => $search,
                    'sort' => $sort,
                    'error' => $errorMessage
                ]);
            }

            // Get transactions and pagination data
            $transactions = $result['data'] ?? [];
            $pagination = $result['pagination'] ?? null;

            // For AJAX requests, return JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Asset transactions list retrieved successfully',
                    'data' => $transactions,
                    'pagination' => $pagination
                ]);
            }

            // For regular requests, return view
            return view('Report.FinanceReport.FinanceReport', [
                'transactions' => $transactions,
                'pagination' => $pagination,
                'search' => $search,
                'sort' => $sort
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during asset transactions retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Failed to retrieve asset transactions: ' . $e->getMessage()]
                ], status: 500);
            }

            return view('Report.FinanceReport.FinanceReport', [
                'transactions' => [],
                'pagination' => null,
                'search' => $search,
                'sort' => $sort,
                'error' => 'Failed to retrieve asset transactions: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export finance report as PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportFinanceReportPDF(Request $request)
    {
        try {
            \Log::info('Starting finance report PDF export');

            // Get search and sort parameters
            $search = $request->input('search', '');
            $sort = $request->input('sort', 'newest');

            // Build query parameters - use a large limit to get all data
            $queryParams = [
                'page' => 1,
                'limit' => 1000 // Large limit to get more data for PDF
            ];

            // Add search parameter if provided
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Handle sorting
            switch ($sort) {
                case 'newest':
                    $queryParams['sort_by'] = 'created_at';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'oldest':
                    $queryParams['sort_by'] = 'created_at';
                    $queryParams['sort_order'] = 'asc';
                    break;
                default:
                    // Default sort (newest first)
                    $queryParams['sort_by'] = 'created_at';
                    $queryParams['sort_order'] = 'desc';
            }

            // Fetch asset transactions from API
            $result = $this->apiService->request('GET', '/asset-transactions', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during finance report PDF export', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Failed to retrieve finance report data';

                \Log::warning('Error during finance report data retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                // Format error message
                $errorMessage = '';
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $errorMessage .= implode(', ', $messages) . '; ';
                        } else {
                            $errorMessage .= $messages . '; ';
                        }
                    }
                } else {
                    $errorMessage = $errorData;
                }

                return redirect()->back()
                    ->with('error', $errorMessage);
            }

            // Get transactions data
            $transactions = $result['data'] ?? [];

            \Log::info('Data prepared for finance report PDF export', [
                'transactions_count' => count($transactions)
            ]);

            // Create the PDF with the data
            $pdf = Pdf::loadView('Report.FinanceReport.FinanceReportPDF', [
                'transactions' => $transactions,
                'search' => $search,
                'sort' => $sort
            ]);

            // Set paper size and orientation
            $pdf->setPaper('a4', 'portrait');

            \Log::info('Finance report PDF generation successful, streaming to browser');
            return $pdf->stream("finance_report.pdf");

        } catch (\Exception $e) {
            \Log::error('Exception during finance report PDF export', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to export finance report as PDF: ' . $e->getMessage());
        }
    }
}
