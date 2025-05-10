<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class ProcurementReceiptController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Get all receipts with pagination
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            // Get pagination parameters with defaults
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            // Get search and filter parameters
            $search = $request->input('search');
            $status = $request->input('status');
            $sort = $request->input('sort');

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
            ];

            // Add search parameter if provided
            if ($search) {
                $queryParams['search'] = $search;
            }

            // Add status filter if provided
            if ($status) {
                $queryParams['status'] = $status;
            }

            // Set sort parameters based on selection
            if ($sort) {
                switch ($sort) {
                    case 'newest':
                        $queryParams['sort_by'] = 'created_at';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    case 'oldest':
                        $queryParams['sort_by'] = 'created_at';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'code_asc':
                        $queryParams['sort_by'] = 'receipt_code';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'code_desc':
                        $queryParams['sort_by'] = 'receipt_code';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    case 'date_asc':
                        $queryParams['sort_by'] = 'receipt_date';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'date_desc':
                        $queryParams['sort_by'] = 'receipt_date';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    default:
                        $queryParams['sort_by'] = 'created_at';
                        $queryParams['sort_order'] = 'desc';
                }
            } else {
                // Default sorting if not specified
                $queryParams['sort_by'] = 'created_at';
                $queryParams['sort_order'] = 'desc';
            }

            $result = $this->apiService->request('GET', '/receipts', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during receipts retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], 401);
                }

                return redirect()->route('login')->with('error', 'Authentication failed. Please log in again.');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve receipts';

                \Log::warning('Error during receipts retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], 400);
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

                // Return view with empty receipts data and error message
                return view('Procurement.Receipt.Receipt', [
                    'receipts' => [],
                    'pagination' => null,
                    'error' => $errorMessage
                ]);
            }

            // Prepare data for the view
            $receipts = $result['data'] ?? [];
            $pagination = $result['pagination'] ?? null;
            $message = $result['message'] ?? 'Daftar penerimaan barang berhasil diambil';

            // Check if request is AJAX (wants JSON)
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $receipts,
                    'pagination' => $pagination
                ]);
            }

            // If not AJAX request, return view with data
            return view('Procurement.Receipt.Receipt', [
                'receipts' => $receipts,
                'pagination' => $pagination,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during receipts retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Failed to fetch receipts: ' . $e->getMessage()]
                ], status: 500);
            }

            // Always pass an empty array for receipts in case of error
            return view('Procurement.Receipt.Receipt', [
                'receipts' => [],
                'pagination' => null,
                'error' => 'Failed to fetch receipt data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get a specific receipt by ID
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $id)
    {
        try {
            // Make API call to get receipt details
            $result = $this->apiService->request('GET', '/receipts/' . $id);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during receipt retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed',
                    'receipt_id' => $id
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], 401);
                }

                return redirect()->route('login')->with('error', 'Authentication failed. Please log in again.');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve receipt';

                \Log::warning('Error during receipt retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'receipt_id' => $id
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], 400);
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

                // Return view with error message
                return view('Procurement.Receipt.DetailReceipt', [
                    'receipt' => null,
                    'error' => $errorMessage
                ]);
            }

            // Get receipt data
            $receipt = $result['data'] ?? null;
            $message = $result['message'] ?? 'Penerimaan barang berhasil ditemukan';

            // Check if request is AJAX (wants JSON)
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $receipt
                ]);
            }

            // If not AJAX request, return view with data
            return view('Procurement.Receipt.DetailReceipt', [
                'receipt' => $receipt,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during receipt retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'receipt_id' => $id
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Failed to fetch receipt: ' . $e->getMessage()]
                ], 500);
            }

            // Return view with error
            return view('Procurement.Receipt.DetailReceipt', [
                'receipt' => null,
                'error' => 'Failed to fetch receipt data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Create a new receipt
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function create(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'purchase_order_id' => 'required|integer',
                'receipt_date' => 'required|date',
                'received_by' => 'required|integer',
                'delivered_by' => 'required|string',
                'notes' => 'nullable|string',
                'items' => 'required|array',
                'items.*.purchase_order_item_id' => 'required|integer',
                'items.*.notes' => 'nullable|string',
            ]);

            // Make API call to create a receipt
            $result = $this->apiService->request('POST', '/receipts', [
                'json' => $validatedData
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during receipt creation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], 401);
                }

                return redirect()->route('login')->with('error', 'Authentication failed. Please log in again.');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to create receipt';

                \Log::warning('Error during receipt creation:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    // Return detailed error structure for JSON responses
                    if (is_array($errorData)) {
                        // Keep the original error structure for proper display
                        return response()->json([
                            'success' => false,
                            'errors' => $errorData,
                            'message' => 'There were errors with your submission'
                        ], 400);
                    } else {
                        return response()->json([
                            'success' => false,
                            'errors' => ['general' => $errorData]
                        ], 400);
                    }
                }

                // Format error message for redirect
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

                return redirect()->route('procurement.receipt')->with('error', $errorMessage);
            }

            // Get receipt data
            $receipt = $result['data'] ?? null;
            $message = $result['message'] ?? 'Penerimaan barang berhasil dibuat';

            // Check if request is AJAX (wants JSON)
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $receipt
                ]);
            }

            // If not AJAX request, redirect with success message
            return redirect()->route('procurement.receipt.show', ['id' => $receipt['receipt_id']])
                ->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Exception during receipt creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Failed to create receipt: ' . $e->getMessage()]
                ], 500);
            }

            // Redirect with error
            return redirect()->route('procurement.receipt')
                ->with('error', 'Failed to create receipt: ' . $e->getMessage());
        }
    }

    /**
     * Export a receipt to PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportReceiptDetailPDF($id)
    {
        try {
            // Log request info
            \Log::info('Exporting receipt to PDF with ID: ' . $id);

            // Fetch receipt from API
            $result = $this->apiService->request('GET', '/receipts/' . $id);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during receipt PDF export:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check if the receipt exists
            if (!isset($result['data']) || empty($result['data'])) {
                \Log::warning('Receipt not found:', [
                    'id' => $id,
                    'message' => $result['message'] ?? 'Receipt not found'
                ]);

                return redirect()->route('procurement.receipt')->with('error', 'Receipt not found');
            }

            // Get receipt data
            $receipt = $result['data'];

            // Generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('Procurement.Receipt.DetailReceiptPDF', [
                'receipt' => $receipt,
                'date_generated' => now()->format('Y-m-d H:i:s')
            ]);

            // Log PDF generation
            \Log::info('Receipt PDF generated successfully', [
                'receipt_id' => $receipt['receipt_id'] ?? 'N/A'
            ]);

            // Stream the PDF to browser
            return $pdf->stream('receipt_' . $id . '_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Exception during receipt PDF export:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to export Receipt as PDF: ' . $e->getMessage());
        }
    }
}
