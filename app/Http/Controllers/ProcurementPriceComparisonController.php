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

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }
            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil data perbandingan harga';

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
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengambil data perbandingan harga: ' . $e->getMessage()]
                ], status: 500);
            }

            // Always pass an empty array for comparisons in case of error
            return view('Procurement.Comparison.PriceComparison', [
                'comparisons' => [],
                'pagination' => null,
                'error' => 'Gagal mengambil data perbandingan harga: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Create a new price comparison
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
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
                'title' => 'required|string',
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

                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'errors' => $result['errors'] ?? 'Autentikasi gagal'
                        ], 401);
                    }

                    return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat perbandingan harga';

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
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Gagal membuat perbandingan harga: ' . $e->getMessage()]],
            ], 500);
        }
    }

     /**
     * Create a new price comparison from the procurement detail page
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function createFromDetail(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'procurement_id' => 'required|integer',
                'title' => 'required|string',
            ]);

            // Send request to API service
            $result = $this->apiService->request('POST', '/price-comparison', [
                'json' => $validated
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'errors' => $result['errors'] ?? 'Autentikasi gagal'
                        ], 401);
                    }

                    return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat perbandingan harga';

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
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['exception' => 'Gagal membuat perbandingan harga: ' . $e->getMessage()]
            ], 500);
        }
    }



    /**
     * Get price comparison by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $id)
    {
        try {
            // Send request to API service
            $result = $this->apiService->request('GET', "/price-comparison/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'errors' => $result['errors'] ?? 'Autentikasi gagal'
                        ], 401);
                    }

                    return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
                   }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil data perbandingan harga';

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
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => ['Gagal mengambil data perbandingan harga: ' . $e->getMessage()]],
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
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function createVendorOffer(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'comparison_id' => 'required|integer',
                'vendor_id' => 'required|integer',
                'payment_terms' => 'nullable|string',
                'delivery_terms' => 'nullable|string',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.price_comparison_item_id' => 'required|integer',
                'items.*.unit_price' => 'required|numeric',
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
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'errors' => $result['errors'] ?? 'Autentikasi gagal'
                        ], 401);
                    }

                    return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat penawaran vendor';

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
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Gagal membuat penawaran vendor: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Update an existing vendor offer for a price comparison
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function updateVendorOffer($id, Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'comparison_id' => 'required|integer',
                'vendor_id' => 'required|integer',
                'payment_terms' => 'nullable|string',
                'delivery_terms' => 'nullable|string',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.price_comparison_item_id' => 'required|integer',
                'items.*.unit_price' => 'required|numeric',
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
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'errors' => $result['errors'] ?? 'Autentikasi gagal'
                        ], 401);
                    }

                    return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui penawaran vendor';

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
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Gagal memperbarui penawaran vendor: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Delete an existing vendor offer for a price comparison
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function deleteVendorOffer($id, Request $request)
    {
        try {
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
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'errors' => $result['errors'] ?? 'Autentikasi gagal'
                        ], 401);
                    }

                    return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menghapus penawaran vendor';

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
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Gagal menghapus penawaran vendor: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Get a vendor offer by ID
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function getVendorOffer(Request $request, $id)
    {
        try {
            // Send request to API service
            $result = $this->apiService->request('GET', "/price-comparison/vendor-offer/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                if (request()->ajax() || request()->wantsJson()) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'errors' => $result['errors'] ?? 'Autentikasi gagal'
                        ], 401);
                    }

                    return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
                }

                return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil data penawaran vendor';

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
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => ['Gagal mengambil data penawaran vendor: ' . $e->getMessage()]],
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
            // Send request to API service
            $result = $this->apiService->request('POST', "/price-comparison/{$id}/complete");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'errors' => $result['errors'] ?? 'Autentikasi gagal'
                        ], 401);
                    }

                    return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menyelesaikan perbandingan harga';

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
            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Gagal menyelesaikan perbandingan harga: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Update a price comparison
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'title' => 'required|string',
            ]);

            // Send request to API service
            $result = $this->apiService->request('PUT', "/price-comparison/{$id}", [
                'json' => $validated
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'errors' => $result['errors'] ?? 'Autentikasi gagal'
                        ], 401);
                    }

                    return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui perbandingan harga';

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
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['exception' => ['Gagal memperbarui perbandingan harga: ' . $e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Show edit form for price comparison
     *
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, $id)
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
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'errors' => $result['errors'] ?? 'Autentikasi gagal'
                        ], 401);
                    }

                    return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
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
            return redirect()->route('procurement.price-comparison')
                ->with('error', 'Terjadi kesalahan saat memuat form edit: ' . $e->getMessage());
        }
    }
}
