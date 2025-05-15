<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class BrandController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display a listing of all brands.
     * Can return either HTML view or JSON depending on the request.
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');
            $sort = $request->input('sort', '');

            // For JSON/AJAX requests, increase the limit to load more items
            if ($request->expectsJson() || $request->ajax()) {
                $limit = $request->input('limit', 100);
            }

            // Log request info
            \Log::info('Fetching brands with parameters:', [
                'page' => $page,
                'limit' => $limit,
                'search' => $search,
                'sort' => $sort,
                'request_url' => $request->fullUrl(),
                'is_ajax' => $request->ajax(),
                'expects_json' => $request->expectsJson()
            ]);

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
                'sort_by' => 'brand_id',
                'sort_order' => 'asc'
            ];

            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Custom sorting
            if (!empty($sort)) {
                switch ($sort) {
                    case 'name_asc':
                        $queryParams['sort_by'] = 'brand_name';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'name_desc':
                        $queryParams['sort_by'] = 'brand_name';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    case 'id_asc':
                        $queryParams['sort_by'] = 'brand_id';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'id_desc':
                        $queryParams['sort_by'] = 'brand_id';
                        $queryParams['sort_order'] = 'desc';
                        break;
                }
            }

            // Fetch brands
            $brandsResult = $this->apiService->request('GET', '/brands', [
                'query' => $queryParams
            ]);

            // Log API responses for debugging
            \Log::info('API response for brands list:', [
                'brands_success' => $brandsResult['success'] ?? null,
                'brands_count' => isset($brandsResult['data']) ? count($brandsResult['data']) : 0
            ]);

            // Check for auth errors
            if (isset($brandsResult['errors']) && is_string($brandsResult['errors']) &&
                in_array($brandsResult['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')->with('error', 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($brandsResult['success']) || $brandsResult['success'] !== true) {
                $errorData = $brandsResult['errors'] ?? 'Failed to fetch data';

                \Log::warning('Error during data retrieval:', [
                    'brands_success' => $brandsResult['success'] ?? false,
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

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return view('Brand.Brand', [
                    'brands' => [],
                    'brands_pagination' => null,
                    'error' => $errorMessage
                ]);
            }

            $brands = $brandsResult['data'] ?? [];

            // If this is an AJAX or JSON request, return the brands as JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json($brands);
            }

            // For HTML view, continue with normal flow

            // Format pagination for brands
            $brandsPagination = null;
            if (isset($brandsResult['pagination'])) {
                $pagination = $brandsResult['pagination'];
                $brandsPagination = [
                    'current_page' => $pagination['current_page'] ?? 1,
                    'last_page' => $pagination['total_pages'] ?? 1,
                    'from' => (($pagination['current_page'] ?? 1) - 1) * ($pagination['limit'] ?? 10) + 1,
                    'to' => min(($pagination['current_page'] ?? 1) * ($pagination['limit'] ?? 10), $pagination['total_items'] ?? 0),
                    'total' => $pagination['total_items'] ?? 0,
                    'per_page' => $pagination['limit'] ?? 10,
                    'next_page_url' => ($pagination['has_next'] ?? false) ? url()->current() . '?page=' . (($pagination['current_page'] ?? 1) + 1) : null,
                    'prev_page_url' => ($pagination['has_prev'] ?? false) ? url()->current() . '?page=' . (($pagination['current_page'] ?? 1) - 1) : null,
                ];
            }

            return view('Brand.Brand', [
                'brands' => $brands,
                'brands_pagination' => $brandsPagination
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during data retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Failed to fetch data: ' . $e->getMessage()
                ], 500);
            }

            return view('Brand.Brand', [
                'brands' => [],
                'brands_pagination' => null,
                'error' => 'Failed to fetch data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created brand.
     */
    public function store(Request $request)
    {
        try {
            // Log the request data
            \Log::info('Attempting to create brand with data:', [
                'request_data' => $request->all()
            ]);

            $result = $this->apiService->request('POST', '/brands', [
                'json' => [
                    'brand_name' => $request->input('brand_name')
                ]
            ]);

            // Log the API response
            \Log::info('API response for brand creation:', [
                'api_response' => $result
            ]);

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during brand creation:', [
                    'error' => $result['errors']
                ]);
                return redirect()->route('login')->with('error', 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Failed to create brand';

                \Log::warning('Error during brand creation:', [
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
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Successfully created
            \Log::info('Brand created successfully');
            return redirect()->route('brands')
                ->with('success', 'Brand created successfully');
        } catch (\Exception $e) {
            \Log::error('Exception during brand creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'brand_data' => $request->except('_token')
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create brand: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified brand.
     */
    public function update(Request $request, $id)
    {
        try {
            $result = $this->apiService->request('PUT', "/brands/{$id}", [
                'json' => [
                    'brand_id' => $id,
                    'brand_name' => $request->input('brand_name')
                ]
            ]);

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Failed to update brand';

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
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Successfully updated
            return redirect()->route('brands')
                ->with('success', 'Brand updated successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to update brand', [
                'error' => $e->getMessage(),
                'brand_id' => $id,
                'brand_data' => $request->except(['_token', '_method'])
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update brand: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified brand.
     */
    public function destroy($id)
    {
        try {
            $result = $this->apiService->request('DELETE', "/brands/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Failed to delete brand';

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

            // Successfully deleted
            return redirect()->route('brands')
                ->with('success', 'Brand deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to delete brand', [
                'error' => $e->getMessage(),
                'brand_id' => $id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete brand: ' . $e->getMessage());
        }
    }

    /**
     * Get a single brand for editing.
     */
    public function getBrand($id)
    {
        try {
            // Fetch the brand with the given ID
            $result = $this->apiService->request('GET', "/brands/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], 401);
                }
                return redirect()->route('login')->with('error', 'Authentication failed');
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve brand';

                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], 400);
                }

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

                return redirect()->back()->with('error', $errorMessage);
            }

            $brand = $result['data'] ?? null;

            if (!$brand) {
                $errorMessage = 'Brand not found or response data is invalid';

                if (request()->ajax()) {
                    return response()->json(['error' => $errorMessage], 404);
                }
                return redirect()->back()->with('error', $errorMessage);
            }

            if (request()->ajax() || request()->expectsJson()) {
                return response()->json($brand);
            }

            return view('Brand.EditBrand', ['brand' => $brand]);
        } catch (\Exception $e) {
            $errorMessage = 'Failed to retrieve brand: ' . $e->getMessage();

            if (request()->ajax()) {
                return response()->json(['error' => $errorMessage], 500);
            }
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Import brands from Excel/CSV file.
     */
    public function import(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
            ]);

            // Log the import attempt
            \Log::info('Brand import requested', [
                'file_name' => $request->file('excel_file')->getClientOriginalName(),
                'file_size' => $request->file('excel_file')->getSize(),
                'file_type' => $request->file('excel_file')->getMimeType()
            ]);

            // Create multipart form data for the API request
            $multipart = [
                [
                    'name' => 'excel_file',
                    'contents' => fopen($request->file('excel_file')->getPathname(), 'r'),
                    'filename' => $request->file('excel_file')->getClientOriginalName()
                ]
            ];

            // Send the import request to the API
            $result = $this->apiService->request('POST', '/brands/import', [
                'multipart' => $multipart
            ]);

            // Log the API response
            \Log::info('API response for brand import:', [
                'success' => $result['success'] ?? false,
                'message' => $result['message'] ?? 'No message',
                'data_summary' => isset($result['data']) ? [
                    'total' => $result['data']['total'] ?? 0,
                    'success' => $result['data']['success'] ?? 0,
                    'failed' => $result['data']['failed'] ?? 0,
                    'error_count' => isset($result['data']['errors']) ? count($result['data']['errors']) : 0
                ] : 'No data'
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], status: 401);
                }

                return redirect()->route('login')->with('error', 'Authentication failed');
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Failed to import brands';

                \Log::warning('Error during brand import:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'data' => $result['data'] ?? null
                ]);

                if ($request->ajax()) {
                    // Format detailed error response for AJAX requests
                    $formattedErrors = $errorData;
                    $errorDetails = [];

                    // Extract error details from array structure
                    if (is_array($errorData)) {
                        foreach ($errorData as $field => $messages) {
                            if (is_array($messages)) {
                                foreach ($messages as $msg) {
                                    $errorDetails[] = $msg;
                                }
                            } else {
                                $errorDetails[] = $messages;
                            }
                        }
                    } else {
                        $errorDetails[] = $errorData;
                    }

                    // Check if there are detailed errors in the data section
                    if (isset($result['data']) && isset($result['data']['errors']) && !empty($result['data']['errors'])) {
                        $dataErrors = $result['data']['errors'];
                        if (is_array($dataErrors)) {
                            foreach ($dataErrors as $error) {
                                if (is_array($error)) {
                                    // Format each error object into a readable message
                                    if (isset($error['brand_name']) && isset($error['reason'])) {
                                        $rowInfo = isset($error['row']) ? "Row {$error['row']}: " : '';
                                        $errorDetails[] = "{$rowInfo}\"{$error['brand_name']}\" - {$error['reason']}";
                                    } else if (isset($error['reason'])) {
                                        $rowInfo = isset($error['row']) ? "Row {$error['row']}: " : '';
                                        $errorDetails[] = "{$rowInfo}{$error['reason']}";
                                    } else if (isset($error['message'])) {
                                        $errorDetails[] = $error['message'];
                                    }
                                } else if (is_string($error)) {
                                    $errorDetails[] = $error;
                                }
                            }
                        }
                    }

                    return response()->json([
                        'success' => false,
                        'errors' => $formattedErrors,
                        'errorDetails' => $errorDetails,
                        'data' => $result['data'] ?? null
                    ], status: 400);
                }

                // Format error message for redirect response
                $errorMessage = '';

                // First check if we have structured errors in the data
                if (isset($result['data']) && isset($result['data']['errors']) && !empty($result['data']['errors'])) {
                    $errorList = '<ul class="mt-2 ml-4 list-disc">';
                    foreach ($result['data']['errors'] as $error) {
                        if (is_array($error)) {
                            if (isset($error['brand_name']) && isset($error['reason'])) {
                                $rowInfo = isset($error['row']) ? "Baris {$error['row']}: " : '';
                                $errorList .= "<li>{$rowInfo}\"{$error['brand_name']}\" - {$error['reason']}</li>";
                            } else if (isset($error['reason'])) {
                                $rowInfo = isset($error['row']) ? "Baris {$error['row']}: " : '';
                                $errorList .= "<li>{$rowInfo}{$error['reason']}</li>";
                            } else if (isset($error['message'])) {
                                $errorList .= "<li>{$error['message']}</li>";
                            }
                        } else if (is_string($error)) {
                            $errorList .= "<li>{$error}</li>";
                        }
                    }
                    $errorList .= '</ul>';
                    $errorMessage = 'Failed to import brands: ' . $errorList;
                }
                // If no structured errors in data, format the general errors
                else if (is_array($errorData)) {
                    $errorList = '<ul class="mt-2 ml-4 list-disc">';
                    foreach ($errorData as $field => $messages) {
                        if (is_array($messages)) {
                            foreach ($messages as $msg) {
                                $errorList .= "<li>{$msg}</li>";
                            }
                        } else {
                            $errorList .= "<li>{$messages}</li>";
                        }
                    }
                    $errorList .= '</ul>';
                    $errorMessage = 'Failed to import brands: ' . $errorList;
                } else {
                    $errorMessage = $errorData;
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Successfully imported
            $successMessage = $result['message'] ?? 'Brands imported successfully';
            $importData = $result['data'] ?? null;

            // Format the success message with import counts if available
            if ($importData && isset($importData['total'])) {
                $successMessage = sprintf(
                    'Successfully imported %d of %d brands',
                    $importData['success'] ?? 0,
                    $importData['total'] ?? 0
                );

                // Add info about failed imports if any
                if (isset($importData['failed']) && $importData['failed'] > 0) {
                    $successMessage .= sprintf(', %d failed', $importData['failed']);
                }
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => $importData
                ]);
            }

            return redirect()->route('brands')->with('success', $successMessage);
        } catch (\Exception $e) {
            \Log::error('Exception during brand import:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Failed to import brands: ' . $e->getMessage()
                ], status: 500);
            }

            return redirect()->back()->with('error', 'Failed to import brands: ' . $e->getMessage());
        }
    }
}
