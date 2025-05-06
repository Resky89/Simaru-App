<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Barryvdh\DomPDF\Facade\Pdf;

class DepreciationReportController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Get all depreciation report data.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function getDepreciationReport(Request $request)
    {
        try {
            // Get pagination parameters
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');
            $sort = $request->input('sort', 'newest');
            $asOfDate = $request->input('as_of_date', now()->format('Y-m-d'));
            $assetType = $request->input('asset_type', '');

            // Log request info
            \Log::info('Fetching depreciation report with parameters:', [
                'page' => $page,
                'limit' => $limit,
                'search' => $search,
                'sort' => $sort,
                'as_of_date' => $asOfDate,
                'asset_type' => $assetType,
                'request_url' => $request->fullUrl(),
                'ajax' => $request->ajax()
            ]);

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
                'as_of_date' => $asOfDate
            ];

            // Add search parameter if provided
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Add asset type filter if provided
            if (!empty($assetType)) {
                $queryParams['asset_type'] = $assetType;
            }

            // Handle sorting
            switch ($sort) {
                case 'asset_name_asc':
                    $queryParams['sort_by'] = 'asset_name';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'asset_name_desc':
                    $queryParams['sort_by'] = 'asset_name';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'date_acquired_asc':
                    $queryParams['sort_by'] = 'date_acquired';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'date_acquired_desc':
                    $queryParams['sort_by'] = 'date_acquired';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'book_value_asc':
                    $queryParams['sort_by'] = 'book_value_at_month_end';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'book_value_desc':
                    $queryParams['sort_by'] = 'book_value_at_month_end';
                    $queryParams['sort_order'] = 'desc';
                    break;
                default:
                    // Default sort (by asset name ascending)
                    $queryParams['sort_by'] = 'asset_name';
                    $queryParams['sort_order'] = 'asc';
            }

            // Fetch depreciation report data from API
            $result = $this->apiService->request('GET', '/depreciations/report', [
                'query' => $queryParams
            ]);

            // Log API response for debugging
            \Log::info('API response for depreciation report:', [
                'api_response_success' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'data_count' => isset($result['data']) && isset($result['data']['items']) ? count($result['data']['items']) : 0
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during depreciation report retrieval:', [
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
                $errorData = $result['errors'] ?? 'Failed to retrieve depreciation report';

                \Log::warning('Error during depreciation report retrieval:', [
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

                return view('Report.DepreciationReport.DepreciationReport', [
                    'items' => [],
                    'pagination' => null,
                    'summary' => null,
                    'search' => $search,
                    'sort' => $sort,
                    'as_of_date' => $asOfDate,
                    'asset_type' => $assetType,
                    'error' => $errorMessage
                ]);
            }

            // Get report data and prepare for view
            $items = $result['data']['items'] ?? [];
            $pagination = $result['pagination'] ?? null;

            // Summary data
            $summary = [
                'total_items' => $result['data']['total_items'] ?? 0,
                'total_acquisition_cost' => $result['data']['total_acquisition_cost'] ?? 0,
                'total_book_value' => $result['data']['total_book_value'] ?? 0,
                'total_depreciation' => $result['data']['total_depreciation'] ?? 0,
                'as_of_date' => $result['data']['as_of_date'] ?? $asOfDate
            ];

            // For AJAX requests, return JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Depreciation report retrieved successfully',
                    'data' => [
                        'items' => $items,
                        'summary' => $summary
                    ],
                    'pagination' => $pagination
                ]);
            }

            // For regular requests, return view
            return view('Report.DepreciationReport.DepreciationReport', [
                'items' => $items,
                'pagination' => $pagination,
                'summary' => $summary,
                'search' => $search,
                'sort' => $sort,
                'as_of_date' => $asOfDate,
                'asset_type' => $assetType
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during depreciation report retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Failed to retrieve depreciation report: ' . $e->getMessage()]
                ], status: 500);
            }

            return view('Report.DepreciationReport.DepreciationReport', [
                'items' => [],
                'pagination' => null,
                'summary' => null,
                'search' => $search,
                'sort' => $sort,
                'as_of_date' => $asOfDate ?? now()->format('Y-m-d'),
                'asset_type' => $assetType,
                'error' => 'Failed to retrieve depreciation report: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export depreciation report as PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportDepreciationReportPDF(Request $request)
    {
        try {
            \Log::info('Starting depreciation report PDF export');

            // Get search, sort and filter parameters
            $search = $request->input('search', '');
            $sort = $request->input('sort', 'asset_name_asc');
            $asOfDate = $request->input('as_of_date', now()->format('Y-m-d'));
            $assetType = $request->input('asset_type', '');

            // Build query parameters - use a large limit to get all data
            $queryParams = [
                'page' => 1,
                'limit' => 1000, // Large limit to get more data for PDF
                'as_of_date' => $asOfDate
            ];

            // Add search parameter if provided
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Add asset type filter if provided
            if (!empty($assetType)) {
                $queryParams['asset_type'] = $assetType;
            }

            // Handle sorting
            switch ($sort) {
                case 'asset_name_asc':
                    $queryParams['sort_by'] = 'asset_name';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'asset_name_desc':
                    $queryParams['sort_by'] = 'asset_name';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'date_acquired_asc':
                    $queryParams['sort_by'] = 'date_acquired';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'date_acquired_desc':
                    $queryParams['sort_by'] = 'date_acquired';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'book_value_asc':
                    $queryParams['sort_by'] = 'book_value_at_month_end';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'book_value_desc':
                    $queryParams['sort_by'] = 'book_value_at_month_end';
                    $queryParams['sort_order'] = 'desc';
                    break;
                default:
                    // Default sort (by asset name ascending)
                    $queryParams['sort_by'] = 'asset_name';
                    $queryParams['sort_order'] = 'asc';
            }

            // Fetch depreciation report data from API
            $result = $this->apiService->request('GET', '/depreciations/report', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during depreciation report PDF export', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Failed to retrieve depreciation report data';

                \Log::warning('Error during depreciation report data retrieval:', [
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

            // Get depreciation items and summary data
            $items = $result['data']['items'] ?? [];

            // Summary data
            $summary = [
                'total_items' => $result['data']['total_items'] ?? 0,
                'total_acquisition_cost' => $result['data']['total_acquisition_cost'] ?? 0,
                'total_book_value' => $result['data']['total_book_value'] ?? 0,
                'total_depreciation' => $result['data']['total_depreciation'] ?? 0,
                'as_of_date' => $result['data']['as_of_date'] ?? $asOfDate
            ];

            \Log::info('Data prepared for depreciation report PDF export', [
                'items_count' => count($items)
            ]);

            // Create the PDF with the data
            $pdf = Pdf::loadView('Report.DepreciationReport.DepreciationReportPDF', [
                'items' => $items,
                'summary' => $summary,
                'search' => $search,
                'sort' => $sort,
                'as_of_date' => $asOfDate,
                'asset_type' => $assetType
            ]);

            // Set paper size and orientation
            $pdf->setPaper('a4', 'landscape');

            \Log::info('Depreciation report PDF generation successful, streaming to browser');
            return $pdf->stream("depreciation_report.pdf");

        } catch (\Exception $e) {
            \Log::error('Exception during depreciation report PDF export', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to export depreciation report as PDF: ' . $e->getMessage());
        }
    }
}
