<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;

class CategoriesController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display a listing of the asset subcategories.
     * Can return either HTML view or JSON depending on the request.
     */
    public function index(Request $request)
    {
        try {
            // Get query parameters
            $page = $request->query('page', 1);
            $limit = $request->query('limit', 10);
            $assetType = $request->query('asset_type', '');
            $search = $request->query('search', '');
            $sort = $request->query('sort', '');

            // For JSON requests, increase the limit to load more items
            if ($request->expectsJson() || $request->ajax()) {
                $limit = $request->query('limit', 100);
            }

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
                'sort_by' => 'subcategory_id',
                'sort_order' => 'asc'
            ];

            // Asset type filter
            if (!empty($assetType)) {
                $queryParams['asset_type'] = $assetType;
            }

            // Search parameter
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Custom sorting
            if (!empty($sort)) {
                switch ($sort) {
                    case 'name_asc':
                        $queryParams['sort_by'] = 'subcategory_name';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'name_desc':
                        $queryParams['sort_by'] = 'subcategory_name';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    case 'id_asc':
                        $queryParams['sort_by'] = 'subcategory_id';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'id_desc':
                        $queryParams['sort_by'] = 'subcategory_id';
                        $queryParams['sort_order'] = 'desc';
                        break;
                }
            }

            // Get subcategories from API
            $result = $this->apiService->request('GET', '/asset-subcategories', ['query' => $queryParams]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                Log::warning('Authentication error while fetching asset subcategories:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to fetch asset subcategories';

                Log::warning('Error while fetching asset subcategories:', [
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

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return view('Categories', [
                    'subcategories' => [],
                    'assetTypes' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => $limit ?? 10,
                        'total' => 0,
                        'from' => 0,
                        'to' => 0,
                        'next_page_url' => null,
                        'prev_page_url' => null
                    ],
                    'error' => $errorMessage
                ]);
            }

            // Format data from the API result
            $subcategories = $result['data'] ?? [];

            // If this is an AJAX or JSON request, return the subcategories as JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json($subcategories);
            }

            // For HTML view, continue with normal flow

            // Get asset types for the dropdown
            $assetTypesResult = $this->apiService->request('GET', '/asset-types');
            $assetTypes = $assetTypesResult['data'] ?? [];

            // Format pagination similar to UserController
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
                    'next_page_url' => isset($paginationData['has_next']) && $paginationData['has_next'] ?
                        request()->fullUrlWithQuery(['page' => ($paginationData['current_page'] + 1)]) : null,
                    'prev_page_url' => isset($paginationData['has_prev']) && $paginationData['has_prev'] ?
                        request()->fullUrlWithQuery(['page' => ($paginationData['current_page'] - 1)]) : null,
                ];
            } else {
                $pagination = [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $limit,
                    'total' => count($subcategories),
                    'from' => 1,
                    'to' => count($subcategories),
                    'next_page_url' => null,
                    'prev_page_url' => null
                ];
            }

            return view('Categories', compact('subcategories', 'assetTypes', 'pagination'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch asset subcategories:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Failed to load asset subcategories: ' . $e->getMessage()
                ], 500);
            }

            return view('Categories', [
                'subcategories' => [],
                'assetTypes' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $limit ?? 10,
                    'total' => 0,
                    'from' => 0,
                    'to' => 0,
                    'next_page_url' => null,
                    'prev_page_url' => null
                ],
                'error' => 'Failed to load asset subcategories: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created subcategory.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'asset_type' => 'required|string',
                'subcategory_name' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);

            // Ensure description is an empty string instead of NULL
            if (!isset($validated['description'])) {
                $validated['description'] = '';
            }

            // Log the request data
            Log::info('Attempting to create subcategory with data:', [
                'request_data' => $validated
            ]);

            $result = $this->apiService->request('POST', '/asset-subcategories', ['json' => $validated]);

            // Log the API response
            Log::info('API response for subcategory creation:', [
                'api_response' => $result
            ]);

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                Log::warning('Authentication error during subcategory creation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Failed to create subcategory';

                Log::warning('Error during subcategory creation:', [
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
            Log::info('Subcategory created successfully');
            return redirect()->route('categories')
                ->with('success', 'Subcategory added successfully');
        } catch (\Exception $e) {
            Log::error('Exception during subcategory creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'subcategory_data' => $request->except('_token')
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add subcategory: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified subcategory.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'asset_type' => 'required|string',
                'subcategory_name' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);

            // Ensure description is an empty string instead of NULL
            if (!isset($validated['description'])) {
                $validated['description'] = '';
            }

            $result = $this->apiService->request('PUT', "/asset-subcategories/{$id}", [
                'json' => array_merge(['subcategory_id' => $id], $validated)
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Failed to update subcategory';

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

            return redirect()->route('categories')
                ->with('success', 'Subcategory updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update subcategory', [
                'error' => $e->getMessage(),
                'subcategory_id' => $id,
                'subcategory_data' => $request->except(['_token', '_method'])
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update subcategory: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified subcategory.
     */
    public function destroy($id)
    {
        try {
            $result = $this->apiService->request('DELETE', "/asset-subcategories/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Failed to delete subcategory';

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
            return redirect()->route('categories')
                ->with('success', 'Subcategory deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete subcategory', [
                'error' => $e->getMessage(),
                'subcategory_id' => $id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete subcategory: ' . $e->getMessage());
        }
    }

    /**
     * Get details for a specific subcategory.
     * Can return either HTML view or JSON depending on the request.
     */
    public function show($id, Request $request)
    {
        try {
            // Get subcategory from API
            $result = $this->apiService->request('GET', "/asset-subcategories/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                Log::warning('Authentication error while fetching subcategory details:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                        'errors' => 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to fetch subcategory details';

                Log::warning('Error while fetching subcategory details:', [
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

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
            }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Format data from the API result
            $subcategory = $result['data'] ?? null;

            // If this is an AJAX or JSON request, return the subcategory as JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json($subcategory);
            }

            // For HTML view, return a view with the subcategory details
            return view('CategoryDetails', compact('subcategory'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch subcategory details:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                    'errors' => 'Failed to load subcategory details: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to load subcategory details: ' . $e->getMessage());
        }
    }

    /**
     * Import asset subcategories.
     */
    public function import(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv',
            ]);

            // Log the import attempt
            Log::info('Attempting to import subcategories', [
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
            $result = $this->apiService->request('POST', '/asset-subcategories/import', [
                'multipart' => $multipart
            ]);

            // Log the API response
            Log::info('API response for subcategory import:', [
                'api_response' => $result
            ]);

            // Check for authentication errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                Log::warning('Authentication error during subcategory import:', [
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
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Failed to import subcategories';

                Log::warning('Error during subcategory import:', [
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
            $successMessage = $result['message'] ?? 'Subcategories imported successfully';
            Log::info('Subcategories imported successfully', [
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

            return redirect()->route('categories')->with('success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Exception during subcategory import:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Failed to import subcategories: ' . $e->getMessage()]
                ], status: 500);
            }

            return redirect()->back()->with('error', 'Failed to import subcategories: ' . $e->getMessage());
        }
    }
}
