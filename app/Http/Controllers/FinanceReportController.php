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
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'data_count' => isset($result['data']) ? count($result['data']) : 0
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during asset transactions retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message'] ?? 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors or unsuccessful responses
            if (isset($result['error']) || (isset($result['success']) && $result['success'] === false)) {
                $errorMessage = $result['message'] ?? 'Failed to retrieve asset transactions';

                \Log::warning('Error during asset transactions retrieval:', [
                    'error' => $result['error'] ?? null,
                    'success' => $result['success'] ?? null,
                    'message' => $errorMessage
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 400);
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
                    'message' => 'Failed to retrieve asset transactions: ' . $e->getMessage()
                ], 500);
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
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during finance report PDF export', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
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
