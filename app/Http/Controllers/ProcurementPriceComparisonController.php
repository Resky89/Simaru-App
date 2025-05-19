<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class ProcurementPriceComparisonController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Get all price comparisons with pagination
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
                    case 'title_asc':
                        $queryParams['sort_by'] = 'title';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'title_desc':
                        $queryParams['sort_by'] = 'title';
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

            $result = $this->apiService->request('GET', '/price-comparison', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during price comparison retrieval:', [
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
                $errorData = $result['errors'] ?? 'Failed to retrieve price comparisons';

                \Log::warning('Error during price comparison retrieval:', [
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

                // Return view with empty comparisons data and error message
                return view('Procurement.Comparison.PriceComparison', [
                    'comparisons' => [],
                    'pagination' => null,
                    'error' => $errorMessage
                ]);
            }

            // Prepare data for the view
            $comparisons = $result['data'] ?? [];
            $pagination = $result['pagination'] ?? null;
            $message = $result['message'] ?? 'Daftar perbandingan harga berhasil diambil';

            // Check if request is AJAX (wants JSON)
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $comparisons,
                    'pagination' => $pagination
                ]);
            }

            // If not AJAX request, return view with data
            return view('Procurement.Comparison.PriceComparison', [
                'comparisons' => $comparisons,
                'pagination' => $pagination,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during price comparison retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Failed to fetch price comparisons: ' . $e->getMessage()]
                ], status: 500);
            }

            // Always pass an empty array for comparisons in case of error
            return view('Procurement.Comparison.PriceComparison', [
                'comparisons' => [],
                'pagination' => null,
                'error' => 'Failed to fetch price comparison data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Create a new price comparison
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Debug the incoming data
            \Log::info('Received price comparison request data:', [
                'all_data' => $request->all(),
                'procurement_id_type' => gettype($request->input('procurement_id')),
                'procurement_id_value' => $request->input('procurement_id')
            ]);

            // Ensure procurement_id is converted to integer
            $request->merge([
                'procurement_id' => (int)$request->input('procurement_id')
            ]);

            // Check if comparison_title is provided instead of title
            if ($request->has('comparison_title') && !$request->has('title')) {
                $request->merge(['title' => $request->input('comparison_title')]);
            }

            // Validate request
            $validated = $request->validate([
                'procurement_id' => 'required|integer',
                'title' => 'required|string|max:255',
            ]);

            // Send request to API service
            $result = $this->apiService->request('POST', '/price-comparison', [
                'json' => [
                    'procurement_id' => (int)$validated['procurement_id'],
                    'title' => $validated['title']
                ]
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during price comparison creation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to create price comparison';

                \Log::warning('Error during price comparison creation:', [
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

            // Return successful response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Perbandingan harga berhasil dibuat',
                'data' => $result['data'] ?? null,
                'redirect_url' => route('procurement.price-comparison')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation error during price comparison creation:', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Exception during price comparison creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Failed to create price comparison: ' . $e->getMessage()]],
            ], 500);
        }
    }

     /**
     * Create a new price comparison from the procurement detail page
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createFromDetail(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'procurement_id' => 'required|integer',
                'title' => 'required|string|max:255',
            ]);

            // Send request to API service
            $result = $this->apiService->request('POST', '/price-comparison', [
                'json' => $validated
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during price comparison creation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to create price comparison';

                \Log::warning('Error during price comparison creation from detail page:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                ], 400);
            }

            // Return successful response with redirect URL
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Perbandingan harga berhasil dibuat',
                'data' => $result['data'] ?? null,
                'redirect_url' => route('procurement.detail-comparison', ['id' => $result['data']['comparison_id'] ?? 0])
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation error during price comparison creation from detail page:', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Exception during price comparison creation from detail page:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => 'Failed to create price comparison: ' . $e->getMessage()]
            ], 500);
        }
    }



    /**
     * Get price comparison by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        try {
            // Log the request
            \Log::info('Fetching price comparison by ID:', [
                'comparison_id' => $id
            ]);

            // Send request to API service
            $result = $this->apiService->request('GET', "/price-comparison/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during price comparison retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], 401);
                }

                return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve price comparison';

                \Log::warning('Error during price comparison retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'comparison_id' => $id
                ]);

                if (request()->ajax() || request()->wantsJson()) {
                    // Format error message for better display
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
                    ], 404);
                }

                // For web requests, return with error message
                return redirect()->route('procurement.price-comparison')
                    ->with('error', is_string($errorData) ? $errorData : 'Tidak dapat menemukan data perbandingan harga.');
            }

            // Get the comparison data
            $comparison = $result['data'] ?? [];

            // Make sure comparison is an array even if API returns null
            if (!is_array($comparison)) {
                $comparison = [];
            }

            // For AJAX requests, return JSON
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Perbandingan harga berhasil ditemukan',
                    'data' => $comparison
                ]);
            }

            // For web requests, return the view with data
            return view('Procurement.Comparison.DetailComparison', [
                'comparison' => $comparison,
                'id' => $id
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during price comparison retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'comparison_id' => $id
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => ['Failed to retrieve price comparison: ' . $e->getMessage()]],
                ], 500);
            }

            // For web requests, redirect with error
            return redirect()->route('procurement.price-comparison')
                ->with('error', 'Terjadi kesalahan saat memuat data perbandingan harga: ' . $e->getMessage());
        }
    }

    /**
     * Create a new vendor offer for a price comparison
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createVendorOffer(Request $request)
    {
        try {
            // Log the incoming request
            \Log::info('Received vendor offer request data:', [
                'all_data' => $request->all()
            ]);

            // Validate request
            $validated = $request->validate([
                'comparison_id' => 'required|integer',
                'vendor_id' => 'required|integer',
                'payment_terms' => 'nullable|string',
                'delivery_terms' => 'nullable|string',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.price_comparison_item_id' => 'required|integer',
                'items.*.unit_price' => 'required|numeric|min:0',
            ]);

            // Ensure numeric values are properly formatted
            foreach ($validated['items'] as $key => $item) {
                $validated['items'][$key]['unit_price'] = (float) $item['unit_price'];
                $validated['items'][$key]['price_comparison_item_id'] = (int) $item['price_comparison_item_id'];
            }

            // Send request to API service
            $result = $this->apiService->request('POST', '/price-comparison/vendor-offer', [
                'json' => $validated
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during vendor offer creation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to create vendor offer';

                \Log::warning('Error during vendor offer creation:', [
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

            // Return successful response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Penawaran vendor berhasil ditambahkan',
                'data' => $result['data'] ?? null,
                'redirect_url' => route('procurement.detail-comparison', ['id' => $validated['comparison_id']])
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation error during vendor offer creation:', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Exception during vendor offer creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Failed to create vendor offer: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Update an existing vendor offer for a price comparison
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateVendorOffer($id, Request $request)
    {
        try {
            // Log the incoming request
            \Log::info('Received vendor offer update request data:', [
                'agreement_id' => $id,
                'all_data' => $request->all()
            ]);

            // Validate request
            $validated = $request->validate([
                'comparison_id' => 'required|integer',
                'vendor_id' => 'required|integer',
                'payment_terms' => 'nullable|string',
                'delivery_terms' => 'nullable|string',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.price_comparison_item_id' => 'required|integer',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.vendor_offer_id' => 'required|integer',
            ]);

            // Ensure numeric values are properly formatted
            foreach ($validated['items'] as $key => $item) {
                $validated['items'][$key]['unit_price'] = (float) $item['unit_price'];
                $validated['items'][$key]['price_comparison_item_id'] = (int) $item['price_comparison_item_id'];
                $validated['items'][$key]['vendor_offer_id'] = (int) $item['vendor_offer_id'];
            }

            // Send request to API service
            $result = $this->apiService->request('PUT', "/price-comparison/vendor-offer/{$id}", [
                'json' => $validated
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during vendor offer update:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to update vendor offer';

                \Log::warning('Error during vendor offer update:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'agreement_id' => $id
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

            // Return successful response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Penawaran vendor berhasil diperbarui',
                'data' => $result['data'] ?? null,
                'redirect_url' => route('procurement.detail-comparison', ['id' => $validated['comparison_id']])
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation error during vendor offer update:', [
                'errors' => $e->errors(),
                'agreement_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Exception during vendor offer update:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'agreement_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Failed to update vendor offer: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Delete an existing vendor offer for a price comparison
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteVendorOffer($id, Request $request)
    {
        try {
            // Log the incoming request
            \Log::info('Received vendor offer delete request data:', [
                'vendor_offer_id' => $id,
                'all_data' => $request->all()
            ]);

            // Validate request
            $validated = $request->validate([
                'comparison_id' => 'required|integer',
            ]);

            // Send request to API service
            $result = $this->apiService->request('DELETE', "/price-comparison/vendor-offer/{$id}", [
                'json' => $validated
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during vendor offer deletion:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to delete vendor offer';

                \Log::warning('Error during vendor offer deletion:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'vendor_offer_id' => $id
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

            // Return successful response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Perjanjian dan penawaran vendor berhasil dihapus',
                'data' => $result['data'] ?? null,
                'redirect_url' => route('procurement.detail-comparison', ['id' => $validated['comparison_id']])
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation error during vendor offer deletion:', [
                'errors' => $e->errors(),
                'vendor_offer_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Exception during vendor offer deletion:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'vendor_offer_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Failed to delete vendor offer: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Get a vendor offer by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function getVendorOffer($id)
    {
        try {
            // Log the request
            \Log::info('Fetching vendor offer by ID:', [
                'vendor_offer_id' => $id
            ]);

            // Send request to API service
            $result = $this->apiService->request('GET', "/price-comparison/vendor-offer/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during vendor offer retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], 401);
                }

                return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve vendor offer';

                \Log::warning('Error during vendor offer retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'vendor_offer_id' => $id
                ]);

                // Format error message for better display
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

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $formattedErrors,
                    ], 404);
                }

                // For web requests, redirect with error message
                return redirect()->route('procurement.price-comparison')
                    ->with('error', is_string($errorData) ? $errorData : 'Tidak dapat menemukan data penawaran vendor.');
            }

            // Get the vendor offer data
            $vendorOffer = $result['data'] ?? [];

            // Make sure vendor offer is an array even if API returns null
            if (!is_array($vendorOffer)) {
                $vendorOffer = [];
            }

            // For AJAX requests, return JSON
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Penawaran vendor berhasil ditemukan',
                    'data' => $vendorOffer
                ]);
            }

            // For web requests, redirect to the comparison detail page with the comparison ID
            $comparisonId = $vendorOffer['comparison_id'] ?? null;
            if ($comparisonId) {
                return redirect()->route('procurement.detail-comparison', ['id' => $comparisonId]);
            } else {
                return redirect()->route('procurement.price-comparison')
                    ->with('error', 'Tidak dapat menentukan perbandingan harga untuk penawaran vendor ini.');
            }
        } catch (\Exception $e) {
            \Log::error('Exception during vendor offer retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'vendor_offer_id' => $id
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => ['Failed to retrieve vendor offer: ' . $e->getMessage()]],
                ], 500);
            }

            // For web requests, redirect with error
            return redirect()->route('procurement.price-comparison')
                ->with('error', 'Terjadi kesalahan saat memuat data penawaran vendor: ' . $e->getMessage());
        }
    }

    /**
     * Complete a price comparison
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function completeComparison($id, Request $request)
    {
        try {
            // Log the request
            \Log::info('Completing price comparison:', [
                'comparison_id' => $id,
                'all_data' => $request->all()
            ]);

            // Send request to API service
            $result = $this->apiService->request('POST', "/price-comparison/{$id}/complete");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during price comparison completion:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], 401);
                }

                return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to complete price comparison';

                \Log::warning('Error during price comparison completion:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'comparison_id' => $id
                ]);

                // Format error message for better display
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

            // Return successful response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Perbandingan harga berhasil diselesaikan',
                'data' => $result['data'] ?? null,
                'redirect_url' => route('procurement.detail-comparison', ['id' => $id])
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during price comparison completion:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'comparison_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Failed to complete price comparison: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Update a price comparison
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        try {
            // Log the incoming request
            \Log::info('Received price comparison update request data:', [
                'comparison_id' => $id,
                'all_data' => $request->all()
            ]);

            // Validate request
            $validated = $request->validate([
                'title' => 'required|string|max:255',
            ]);

            // Send request to API service
            $result = $this->apiService->request('PUT', "/price-comparison/{$id}", [
                'json' => $validated
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during price comparison update:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to update price comparison';

                \Log::warning('Error during price comparison update:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'comparison_id' => $id
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

            // Return successful response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Perbandingan harga berhasil diperbarui',
                'data' => $result['data'] ?? null,
                'redirect_url' => route('procurement.price-comparison')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation error during price comparison update:', [
                'errors' => $e->errors(),
                'comparison_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Exception during price comparison update:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'comparison_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Failed to update price comparison: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Show edit form for price comparison
     *
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        try {
            // Check permission
            if (!hasPermission('price-comparison:edit')) {
                return redirect()->route('procurement.price-comparison')
                    ->with('error', 'Anda tidak memiliki izin untuk mengedit perbandingan harga');
            }

            // Get the price comparison data
            $result = $this->apiService->request('GET', "/price-comparison/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')
                    ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }

            // Check if data is found
            if (!isset($result['success']) || $result['success'] !== true || !isset($result['data'])) {
                return redirect()->route('procurement.price-comparison')
                    ->with('error', 'Data perbandingan harga tidak ditemukan');
            }

            // Get the comparison data
            $comparison = $result['data'];

            // Return the form view with edit mode enabled
            return view('Procurement.Comparison.FormComparison', [
                'editMode' => true,
                'comparison' => $comparison
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during price comparison edit form load:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'comparison_id' => $id
            ]);

            return redirect()->route('procurement.price-comparison')
                ->with('error', 'Terjadi kesalahan saat memuat form edit: ' . $e->getMessage());
        }
    }
}
