<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use App\Http\Controllers\Traits\ApiResourceOperations;
use App\Helpers\DataFormatter;

class ProcurementPriceComparisonController extends Controller
{
    use ApiResourceOperations;

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
        return $this->getResourceList(
            $request,
            '/price-comparison',
            'comparisons',
            'Procurement.Comparison.PriceComparison',
            'created_at',
            [],
            [
                'newest' => ['sort_by' => 'created_at', 'sort_order' => 'desc'],
                'oldest' => ['sort_by' => 'created_at', 'sort_order' => 'asc'],
                'title_asc' => ['sort_by' => 'title', 'sort_order' => 'asc'],
                'title_desc' => ['sort_by' => 'title', 'sort_order' => 'desc']
            ]
        );
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
                'procurement_id' => (int) $request->input('procurement_id')
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

            return $this->storeResource(
                $request,
                '/price-comparison',
                $validated,
                'Perbandingan harga berhasil dibuat',
                'procurement.price-comparison'
            );
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

            $response = $this->storeResource(
                $request,
                '/price-comparison',
                $validated,
                'Perbandingan harga berhasil dibuat'
            );

            if ($response instanceof \Illuminate\Http\JsonResponse && $response->getStatusCode() === 200) {
                $data = json_decode($response->getContent(), true);
                $data['redirect_url'] = route('procurement.detail-comparison', ['id' => $data['data']['comparison_id'] ?? 0]);
                return response()->json($data);
            }

            return $response;
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
            $result = $this->apiService->request('GET', "/price-comparison/{$id}");

            $authError = $this->handleAuthError($result, $request);
            if ($authError)
                return $authError;

            $apiError = $this->handleApiError($result, $request, 'Procurement.Comparison.DetailComparison', "Gagal mengambil data comparison");
            if ($apiError) {
                return $apiError->with('id', $id);
            }

            $comparison = $result['data'] ?? [];

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'data' => $comparison]);
            }

            return view('Procurement.Comparison.DetailComparison', [
                'comparison' => $comparison,
                'id' => $id
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Procurement.Comparison.DetailComparison', ['id' => $id]);
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
                'items.*.additional_info' => 'nullable|string',
            ]);

            // Ensure numeric values are properly formatted
            foreach ($validated['items'] as $key => $item) {
                $validated['items'][$key]['unit_price'] = (float) $item['unit_price'];
                $validated['items'][$key]['price_comparison_item_id'] = (int) $item['price_comparison_item_id'];
            }

            return $this->storeResource(
                $request,
                '/price-comparison/vendor-offer',
                $validated,
                'Penawaran vendor berhasil ditambahkan',
                null // We'll handle redirect in the response
            );
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
                'items.*.additional_info' => 'nullable|string',
            ]);

            // Ensure numeric values are properly formatted
            foreach ($validated['items'] as $key => $item) {
                $validated['items'][$key]['unit_price'] = (float) $item['unit_price'];
                $validated['items'][$key]['price_comparison_item_id'] = (int) $item['price_comparison_item_id'];
                $validated['items'][$key]['vendor_offer_id'] = (int) $item['vendor_offer_id'];
            }

            return $this->updateResource(
                $request,
                "/price-comparison/vendor-offer/{$id}",
                $validated,
                'Penawaran vendor berhasil diperbarui',
                null // We'll handle redirect in the response
            );
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
            if (
                isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])
            ) {
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
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getVendorOffer(Request $request, $id)
    {
        try {
            // Support for querying by agreement_id
            $endpoint = "/price-comparison/vendor-offer/{$id}";

            if ($request->has('use_agreement_id') && $request->input('use_agreement_id') === 'true') {
                $endpoint .= "?use_agreement_id=true";
            }

            if ($request->has('detailed') && $request->input('detailed') === 'true') {
                $endpoint .= (strpos($endpoint, '?') !== false ? '&' : '?') . "detailed=true";
            }

            $result = $this->apiService->request('GET', $endpoint);

            // Handle authentication errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Handle API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mendapatkan data penawaran vendor';

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], 400);
                }

                return redirect()->back()->with('error', is_string($errorData) ? $errorData : 'Gagal mendapatkan data penawaran vendor');
            }

            // Return successful response
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data'] ?? null
                ]);
            }

            return view('Procurement.Comparison.FormComparisonVendor', [
                'vendorOffer' => $result['data'],
                'comparison_id' => $result['data']['comparison_id'] ?? null
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => $e->getMessage()]
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
            if (
                isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])
            ) {
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

                // Format error using DataFormatter
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                return response()->json([
                    'success' => false,
                    'errors' => $errorMessage,
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

            return $this->updateResource(
                $request,
                "/price-comparison/{$id}",
                $validated,
                'Perbandingan harga berhasil diperbarui',
                'procurement.price-comparison'
            );
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

            // Get the price comparison data using trait
            $response = $this->getResource(
                $request,
                "/price-comparison/{$id}",
                'comparison',
                'Procurement.Comparison.FormComparison'
            );

            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }

            // Since getResource returns view, we need to adjust
            return $response->with('editMode', true);
        } catch (\Exception $e) {
            return redirect()->route('procurement.price-comparison')
                ->with('error', 'Terjadi kesalahan saat memuat form edit: ' . $e->getMessage());
        }
    }
}
