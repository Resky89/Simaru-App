<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

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

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil subkategori aset';

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
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $subcategories
                ]);
            }

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
                    'prev_url' => isset($paginationData['has_prev']) && $paginationData['has_prev'] ?
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

            return view('Categories', compact('subcategories', 'pagination'));
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal memuat subkategori aset: ' . $e->getMessage()
                ], 500);
            }

            return view('Categories', [
                'subcategories' => [],
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
                'error' => 'Gagal memuat subkategori aset: ' . $e->getMessage()
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
                'subcategory_name' => 'required|string',
                'description' => 'nullable|string'
            ]);

            // Ensure description is an empty string instead of NULL
            if (!isset($validated['description'])) {
                $validated['description'] = '';
            }

            $result = $this->apiService->request('POST', '/asset-subcategories', ['json' => $validated]);

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal membuat subkategori';

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
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('categories')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal menambahkan subkategori: ' . $e->getMessage()],
                    'data' => null
                ], status: 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan subkategori: ' . $e->getMessage());
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
                'subcategory_name' => 'required|string',
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
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for other API errors
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui subkategori';

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

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('categories')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal memperbarui subkategori: ' . $e->getMessage()],
                    'data' => null
                ], status: 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui subkategori: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified subcategory.
     */
    public function destroy($id, Request $request)
    {
        try {
            $result = $this->apiService->request('DELETE', "/asset-subcategories/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal menghapus subkategori';

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
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => null
                ]);
            }

            return redirect()->route('categories')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal menghapus subkategori: ' . $e->getMessage()],
                    'data' => null
                ], status: 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus subkategori: ' . $e->getMessage());
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

                if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil detail subkategori';

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
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $subcategory
                ]);
            }

            // For HTML view, return a view with the subcategory details
            return view('CategoryDetails', compact('subcategory'));
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'errors' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal memuat detail subkategori: ' . $e->getMessage());
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

            // Check for authentication errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], status: 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Check for other API errors
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal mengimpor subkategori';

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
            $successMessage = $result['message'];

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('categories')->with('success', $successMessage);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengimpor subkategori: ' . $e->getMessage()]
                ], status: 500);
            }

            return redirect()->back()->with('error', 'Gagal mengimpor subkategori: ' . $e->getMessage());
        }
    }
}
