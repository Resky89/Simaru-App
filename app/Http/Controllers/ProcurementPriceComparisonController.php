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
                ], 500);
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

                \Log::warning('Error during price comparison creation:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                ], 400);
            }

            // Return successful response
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Perbandingan harga berhasil dibuat',
                'data' => $result['data'] ?? null
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation error during price comparison creation:', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Exception during price comparison creation:', [
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
}
