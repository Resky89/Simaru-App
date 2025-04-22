<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Barryvdh\DomPDF\Facade\Pdf;

class OpnameReportController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display the opname report page with data.
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');

            $result = $this->apiService->request('GET', '/asset-opnames', [
                'query' => [
                    'page' => $page,
                    'limit' => $limit,
                    'search' => $search,
                    'sort_by' => 'created_at',
                    'sort_order' => 'desc'
                ]
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during opname reports retrieval:', [
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

            $opnames = $result['data'] ?? [];

            // Format pagination
            $pagination = null;
            if (isset($result['pagination'])) {
                $paginationData = $result['pagination'];
                $pagination = [
                    'current_page' => $paginationData['current_page'] ?? 1,
                    'last_page' => ceil(($paginationData['total_items'] ?? 0) / ($paginationData['limit'] ?? 10)),
                    'from' => (($paginationData['current_page'] ?? 1) - 1) * ($paginationData['limit'] ?? 10) + 1,
                    'to' => min(($paginationData['current_page'] ?? 1) * ($paginationData['limit'] ?? 10), $paginationData['total_items'] ?? 0),
                    'total' => $paginationData['total_items'] ?? 0,
                    'per_page' => $paginationData['limit'] ?? 10,
                    'next_page_url' => $paginationData['has_next'] ? url()->current() . '?page=' . ($paginationData['current_page'] + 1) : null,
                    'prev_page_url' => $paginationData['has_prev'] ? url()->current() . '?page=' . ($paginationData['current_page'] - 1) : null,
                ];
            }

            return view('Report.OpnameReport.OpnameReport', [
                'opnames' => $opnames,
                'pagination' => $pagination,
                'search' => $search
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve opname reports', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('Report.OpnameReport.OpnameReport', [
                'opnames' => [],
                'pagination' => null,
                'error' => 'Failed to retrieve opname reports: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get all asset opnames.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllOpnames(Request $request)
    {
        try {
            // Get pagination parameters
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
                'sort_by' => 'created_at',
                'sort_order' => 'desc'
            ];

            // Add search parameter if provided
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Fetch opnames from API
            $result = $this->apiService->request('GET', '/asset-opnames', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during opnames retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Return the response
            return response()->json([
                'success' => true,
                'message' => 'Asset opnames retrieved successfully',
                'data' => $result['data'] ?? [],
                'pagination' => $result['pagination'] ?? [
                    'total_items' => 0,
                    'total_pages' => 0,
                    'current_page' => $page,
                    'limit' => $limit,
                    'has_next' => false,
                    'has_prev' => false
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during opnames retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve opnames: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the opname detail page.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showOpnameDetail($id, Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            $result = $this->apiService->request('GET', "/asset-opname-details/opname/{$id}", [
                'query' => [
                    'page' => $page,
                    'limit' => $limit
                ]
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during opname detail page retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed',
                    'opname_id' => $id
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $result['message'] ?? 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Extract data from API response
            $opnameData = $result ?? [];
            $details = $opnameData['data'] ?? [];
            $opnameCode = !empty($details) ? ($details[0]['opname_code'] ?? null) : null;
            $pagination = $opnameData['pagination'] ?? null;
            $summary = $opnameData['summary'] ?? null;
            $roomInfo = $opnameData['room_info'] ?? null;

            // Log response data for debugging
            \Log::debug('Opname detail API response structure:', [
                'has_data' => isset($opnameData['data']),
                'details_count' => count($details),
                'has_pagination' => isset($opnameData['pagination']),
                'has_summary' => isset($opnameData['summary']),
                'has_room_info' => isset($opnameData['room_info'])
            ]);

            return view('Report.OpnameReport.OpnameDetail', [
                'opnameId' => $id,
                'opnameCode' => $opnameCode,
                'details' => $details,
                'pagination' => $pagination,
                'summary' => $summary,
                'roomInfo' => $roomInfo,
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to retrieve opname detail page:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'opname_id' => $id
            ]);

            return view('Report.OpnameReport.OpnameDetail', [
                'opnameId' => $id,
                'opnameCode' => null,
                'details' => [],
                'pagination' => null,
                'summary' => null,
                'roomInfo' => null,
                'error' => 'Failed to retrieve opname details: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export opname detail as PDF
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportOpnameDetailPDF($id, Request $request)
    {
        try {
            \Log::info('Starting opname detail PDF export', ['id' => $id]);

            // Use the exact same data retrieval approach as showOpnameDetail
            $result = $this->apiService->request('GET', "/asset-opname-details/opname/{$id}", [
                'query' => [
                    'page' => 1,
                    'limit' => 100 // Large limit to get all data
                ]
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during opname detail PDF export', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed',
                    'opname_id' => $id
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Extract data exactly like in showOpnameDetail method - using data directly from server
            $opnameData = $result ?? [];
            $details = $opnameData['data'] ?? [];
            $opnameCode = !empty($details) && isset($details[0]['opname_code']) ? $details[0]['opname_code'] : 'N/A';
            $summary = $opnameData['summary'] ?? [];
            $roomInfo = $opnameData['room_info'] ?? [];

            \Log::info('Data prepared for PDF export', [
                'opnameCode' => $opnameCode,
                'details_count' => count($details),
                'has_summary' => !empty($summary),
                'has_roomInfo' => !empty($roomInfo)
            ]);

            // Create the PDF with the data - exactly matching the structure used in the view
            $pdf = Pdf::loadView('Report.OpnameReport.OpnameDetailPDF', [
                'opnameId' => $id,
                'opnameCode' => $opnameCode,
                'details' => $details,
                'pagination' => null, // Not needed for PDF
                'summary' => $summary,
                'roomInfo' => $roomInfo,
            ]);

            // Set paper size and orientation
            $pdf->setPaper('a4', 'portrait');

            \Log::info('PDF generation successful, streaming to browser');
            return $pdf->stream("opname_detail_{$id}.pdf");

        } catch (\Exception $e) {
            \Log::error('Exception during opname detail PDF export', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'opname_id' => $id
            ]);
            return redirect()->back()->with('error', 'Failed to export opname detail as PDF: ' . $e->getMessage());
        }
    }
}
