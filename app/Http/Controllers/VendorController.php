<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Services\ApiService;

class VendorController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display a listing of vendors.
     */
    public function index(Request $request)
    {
        try {
            // Get pagination parameters
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            // Get search parameter
            $search = $request->input('search', '');

            // Get sort parameter
            $sort = $request->input('sort', '');

            // Define sort_by and sort_order based on sort parameter
            $sortBy = 'vendor_id';
            $sortOrder = 'asc';

            if ($sort === 'id_asc') {
                $sortBy = 'vendor_id';
                $sortOrder = 'asc';
            } elseif ($sort === 'id_desc') {
                $sortBy = 'vendor_id';
                $sortOrder = 'desc';
            } elseif ($sort === 'name_asc') {
                $sortBy = 'vendor_name';
                $sortOrder = 'asc';
            } elseif ($sort === 'name_desc') {
                $sortBy = 'vendor_name';
                $sortOrder = 'desc';
            }

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder
            ];

            // Add search parameter if provided
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            $result = $this->apiService->request('GET', '/vendors', [
                'query' => $queryParams
            ]);

            // Check if we got an error response from the ApiService
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during vendors retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to fetch vendors';

                \Log::warning('Error during vendors retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                // Return JSON response if requested
                if ($request->has('json') && $request->input('json') == 'true') {
                    \Log::info('Returning empty vendors as JSON due to API error');
                    return response()->json([]);
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

                return view('Vendor', [
                    'vendors' => [],
                    'error' => $errorMessage
                ]);
            }

            // Return JSON response if requested
            if ($request->has('json') && $request->input('json') == 'true') {
                \Log::info('Returning vendors as JSON', [
                    'count' => count($result['data'] ?? []),
                    'sample' => !empty($result['data']) ? $result['data'][0] : null
                ]);
                return response()->json($result['data'] ?? []);
            }

            // Format pagination data
            $pagination = null;
            if (isset($result['pagination'])) {
                $paginationData = $result['pagination'];

                // Preserve existing query parameters
                $queryParams = $request->query();

                // Build next and previous page URLs with all query parameters
                $nextPageUrl = null;
                $prevPageUrl = null;

                if ($paginationData['has_next']) {
                    $nextPageParams = array_merge($queryParams, ['page' => ($paginationData['current_page'] + 1)]);
                    $nextPageUrl = url()->current() . '?' . http_build_query($nextPageParams);
                }

                if ($paginationData['has_prev']) {
                    $prevPageParams = array_merge($queryParams, ['page' => ($paginationData['current_page'] - 1)]);
                    $prevPageUrl = url()->current() . '?' . http_build_query($prevPageParams);
                }

                $pagination = [
                    'current_page' => $paginationData['current_page'] ?? 1,
                    'last_page' => ceil(($paginationData['total_items'] ?? 0) / ($paginationData['limit'] ?? 10)),
                    'from' => (($paginationData['current_page'] ?? 1) - 1) * ($paginationData['limit'] ?? 10) + 1,
                    'to' => min(($paginationData['current_page'] ?? 1) * ($paginationData['limit'] ?? 10), $paginationData['total_items'] ?? 0),
                    'total' => $paginationData['total_items'] ?? 0,
                    'per_page' => $paginationData['limit'] ?? 10,
                    'next_page_url' => $nextPageUrl,
                    'prev_page_url' => $prevPageUrl,
                ];
            }

            return view('Vendor', [
                'vendors' => $result['data'] ?? [],
                'pagination' => $pagination
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch vendors', [
                'error' => $e->getMessage()
            ]);

            // Return empty array if JSON response is requested
            if ($request->has('json') && $request->input('json') == 'true') {
                return response()->json([]);
            }

            return view('Vendor', [
                'vendors' => [],
                'error' => 'Failed to fetch vendors: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created vendor.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'vendor_name' => 'required|string|max:50',
                'contact_person' => 'nullable|string|max:100',
                'phone_number' => 'nullable|string|max:20',
                'email' => 'nullable|string|email|max:100',
                'website' => 'nullable|string|url|max:255',
                'address' => 'nullable|string'
            ]);

            // Remove empty fields from request body
            $optionalFields = ['contact_person', 'phone_number', 'email', 'website', 'address'];
            foreach ($optionalFields as $field) {
                if (!isset($validated[$field]) || $validated[$field] === null || $validated[$field] === '') {
                    unset($validated[$field]);
                }
            }

            // Log the request data
            \Log::info('Attempting to create vendor with data:', [
                'request_data' => $validated
            ]);

            $result = $this->apiService->request('POST', '/vendors', [
                'json' => $validated
            ]);

            // Log the API response
            \Log::info('API response for vendor creation:', [
                'api_response' => $result
            ]);

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during vendor creation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to create vendor';

                \Log::warning('Error during vendor creation:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

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

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Successfully created
            \Log::info('Vendor created successfully');
            return redirect()->route('vendor')
                ->with('success', 'Vendor created successfully');
        } catch (\Exception $e) {
            \Log::error('Exception during vendor creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'vendor_data' => $request->except('_token')
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create vendor: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified vendor.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'vendor_name' => 'required|string|max:50',
                'contact_person' => 'nullable|string|max:100',
                'phone_number' => 'nullable|string|max:20',
                'email' => 'nullable|string|email|max:100',
                'website' => 'nullable|string|url|max:255',
                'address' => 'nullable|string'
            ]);

            // Remove empty fields from request body
            $optionalFields = ['contact_person', 'phone_number', 'email', 'website', 'address'];
            foreach ($optionalFields as $field) {
                if (!isset($validated[$field]) || $validated[$field] === null || $validated[$field] === '') {
                    unset($validated[$field]);
                }
            }

            // Add vendor_id to validated data
            $validated['vendor_id'] = $id;

            $result = $this->apiService->request('PUT', "/vendors/{$id}", [
                'json' => $validated
            ]);

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during vendor update:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to update vendor';

                \Log::warning('Error during vendor update:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

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

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Successfully updated
            return redirect()->route('vendor')
                ->with('success', 'Vendor updated successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to update vendor', [
                'error' => $e->getMessage(),
                'vendor_id' => $id,
                'vendor_data' => $request->except(['_token', '_method'])
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update vendor: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified vendor.
     */
    public function destroy($id)
    {
        try {
            $result = $this->apiService->request('DELETE', "/vendors/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during vendor deletion:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to delete vendor';

                \Log::warning('Error during vendor deletion:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

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

                return redirect()->back()
                    ->with('error', $errorMessage);
            }

            // Successfully deleted
            return redirect()->route('vendor')
                ->with('success', 'Vendor deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to delete vendor', [
                'error' => $e->getMessage(),
                'vendor_id' => $id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete vendor: ' . $e->getMessage());
        }
    }

    /**
     * Import vendors from Excel file.
     */
    public function import(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv',
            ]);

            // Log the import attempt
            \Log::info('Attempting to import vendors', [
                'file_name' => $request->file('excel_file')->getClientOriginalName(),
                'file_size' => $request->file('excel_file')->getSize()
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
            $result = $this->apiService->request('POST', '/vendors/import', [
                'multipart' => $multipart
            ]);

            // Log the API response
            \Log::info('API response for vendor import:', [
                'api_response' => $result
            ]);

            // Check for authentication errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during vendor import:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], status: 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to import vendors';

                \Log::warning('Error during vendor import:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData,
                        'data' => $result['data'] ?? null
                    ], status: 400);
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

            // Successfully imported
            $successMessage = $result['message'] ?? 'Vendors imported successfully';
            \Log::info('Vendors imported successfully', [
                'total' => $result['data']['total'] ?? 0,
                'success' => $result['data']['success'] ?? 0,
                'failed' => $result['data']['failed'] ?? 0
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('vendor')->with('success', $successMessage);
        } catch (\Exception $e) {
            \Log::error('Exception during vendor import:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Failed to import vendors: ' . $e->getMessage()]
                ], status: 500);
            }

            return redirect()->back()->with('error', 'Failed to import vendors: ' . $e->getMessage());
        }
    }
}
