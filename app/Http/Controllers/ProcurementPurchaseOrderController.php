<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class ProcurementPurchaseOrderController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Get all purchase orders with pagination
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
                        $queryParams['sort_by'] = 'purchase_order_code';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'code_desc':
                        $queryParams['sort_by'] = 'purchase_order_code';
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

            $result = $this->apiService->request('GET', '/purchase-orders', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during purchase order retrieval:', [
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
                $errorData = $result['errors'] ?? 'Failed to retrieve purchase orders';

                \Log::warning('Error during purchase order retrieval:', [
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

                // Return view with empty purchase orders data and error message
                return view('Procurement.PurchaseOrder.PurchaseOrder', [
                    'purchaseOrders' => [],
                    'pagination' => null,
                    'error' => $errorMessage
                ]);
            }

            // Prepare data for the view
            $purchaseOrders = $result['data'] ?? [];
            $pagination = $result['pagination'] ?? null;
            $message = $result['message'] ?? 'Daftar pesanan pembelian berhasil diambil';

            // Check if request is AJAX (wants JSON)
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $purchaseOrders,
                    'pagination' => $pagination
                ]);
            }

            // If not AJAX request, return view with data
            return view('Procurement.PurchaseOrder.PurchaseOrder', [
                'purchaseOrders' => $purchaseOrders,
                'pagination' => $pagination,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during purchase order retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Failed to fetch purchase orders: ' . $e->getMessage()]
                ], status: 500);
            }

            // Always pass an empty array for purchase orders in case of error
            return view('Procurement.PurchaseOrder.PurchaseOrder', [
                'purchaseOrders' => [],
                'pagination' => null,
                'error' => 'Failed to fetch purchase order data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get a specific purchase order by ID
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $id)
    {
        try {
            // Make API call to get purchase order details
            $result = $this->apiService->request('GET', '/purchase-orders/' . $id);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during purchase order retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed',
                    'purchase_order_id' => $id
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
                $errorData = $result['errors'] ?? 'Failed to retrieve purchase order';

                \Log::warning('Error during purchase order retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'purchase_order_id' => $id
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
                return view('Procurement.PurchaseOrder.DetailPurchaseOrder', [
                    'purchaseOrder' => null,
                    'error' => $errorMessage
                ]);
            }

            // Get purchase order data
            $purchaseOrder = $result['data'] ?? null;
            $message = $result['message'] ?? 'Pesanan pembelian berhasil ditemukan';

            // Check if request is AJAX (wants JSON)
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $purchaseOrder
                ]);
            }

            // If not AJAX request, return view with data
            return view('Procurement.PurchaseOrder.DetailPurchaseOrder', [
                'purchaseOrder' => $purchaseOrder,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during purchase order retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'purchase_order_id' => $id
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Failed to fetch purchase order: ' . $e->getMessage()]
                ], 500);
            }

            // Return view with error
            return view('Procurement.PurchaseOrder.DetailPurchaseOrder', [
                'purchaseOrder' => null,
                'error' => 'Failed to fetch purchase order data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Create purchase orders from selected vendor offers
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createFromVendorOffers(Request $request)
    {
        try {
            // Log the request data for debugging
            \Log::info('Received purchase order creation request:', [
                'payload' => $request->all()
            ]);

            // Validate the request
            $validated = $request->validate([
                'comparison_id' => 'required|integer',
                'selections' => 'required|array|min:1',
                'selections.*.price_comparison_item_id' => 'required|integer',
                'selections.*.vendor_offer_id' => 'required|integer',
            ]);

            // Make API call to create purchase orders
            $result = $this->apiService->request('POST', '/purchase-orders/vendor-offers/select-multiple', [
                'json' => $validated
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during purchase order creation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to create purchase orders';

                \Log::warning('Error during purchase order creation:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                // Format error message for better display in toast notifications
                $formattedErrors = [];
                if (is_array($errorData)) {
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            $formattedErrors[$field] = $messages;
                        } else {
                            $formattedErrors[$field] = [$messages];
                        }
                    }
                } else {
                    $formattedErrors['general'] = [$errorData];
                }

                return response()->json([
                    'success' => false,
                    'errors' => $formattedErrors,
                ], 400);
            }

            // Return successful response with data
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Purchase orders created successfully',
                'data' => $result['data'] ?? null
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation error during purchase order creation:', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Exception during purchase order creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Failed to create purchase orders: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Export a purchase order to PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportPurchaseOrderDetailPDF($id)
    {
        try {
            // Log request info
            \Log::info('Exporting purchase order to PDF with ID: ' . $id);

            // Fetch purchase order from API
            $result = $this->apiService->request('GET', '/purchase-orders/' . $id);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during purchase order PDF export:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check if the purchase order exists
            if (!isset($result['data']) || empty($result['data'])) {
                \Log::warning('Purchase order not found:', [
                    'id' => $id,
                    'message' => $result['message'] ?? 'Purchase order not found'
                ]);

                return redirect()->route('procurement.purchase-order')->with('error', 'Purchase order not found');
            }

            // Get purchase order data
            $purchaseOrder = $result['data'];

            // Generate PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('Procurement.PurchaseOrder.DetailPurchaseOrderPDF', [
                'purchaseOrder' => $purchaseOrder,
                'date_generated' => now()->format('d M Y H:i:s')
            ]);

            // Log PDF generation
            \Log::info('Purchase order PDF generated successfully', [
                'purchase_order_id' => $purchaseOrder['purchase_order_id'] ?? 'N/A'
            ]);

            // Stream the PDF to browser
            return $pdf->stream('purchase_order_' . $id . '_' . now()->format('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Exception during purchase order PDF export:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to export Purchase Order as PDF: ' . $e->getMessage());
        }
    }
}
