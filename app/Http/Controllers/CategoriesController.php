<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\DataFormatter;
use App\Http\Controllers\Traits\ApiResourceOperations;

class CategoriesController extends Controller
{
    use ApiResourceOperations;




    /**
     * Display a listing of the asset subcategories.
     * Can return either HTML view or JSON depending on the request.
     */
    public function index(Request $request)
    {
        $extraParams = [];

            // Asset type filter
        if ($request->filled('asset_type')) {
            $extraParams['asset_type'] = $request->input('asset_type');
        }

        // Custom sort mappings
        $sortMappings = [
            'name_asc' => ['sort_by' => 'subcategory_name', 'sort_order' => 'asc'],
            'name_desc' => ['sort_by' => 'subcategory_name', 'sort_order' => 'desc'],
            'id_asc' => ['sort_by' => 'subcategory_id', 'sort_order' => 'asc'],
            'id_desc' => ['sort_by' => 'subcategory_id', 'sort_order' => 'desc'],
        ];

        return $this->getResourceList(
            $request,
            '/asset-subcategories',
            'subcategories',
            'Categories',
            'subcategory_id',
            $extraParams,
            $sortMappings
        );
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

            $result = $this->apiService->request('POST', '/asset-subcategories', [
                'json' => $validated
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat Kategori';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $result['errors'] ?? ['general' => 'Gagal membuat Kategori']
                    ]);
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
            return $this->handleException($e, $request, 'Categories');
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
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui Kategori';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $result['errors'] ?? ['general' => 'Gagal memperbarui Kategori']
                    ]);
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
            return $this->handleException($e, $request, 'Categories');
        }
    }

    /**
     * Remove the specified subcategory.
     */
    public function destroy($id, Request $request)
    {
        try {
            // Send delete request to API
            $result = $this->apiService->request('DELETE', "/asset-subcategories/{$id}");

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menghapus Kategori';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $result['errors'] ?? ['general' => 'Gagal menghapus Kategori']
                    ]);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Successfully deleted
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kategori berhasil dihapus',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('categories')
                ->with('success', 'Kategori berhasil dihapus');
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Categories');
        }
    }

    /**
     * Get details for a specific subcategory.
     */
    public function getCategory($id)
    {
        return $this->getResource(
            request(),
            "/asset-subcategories/{$id}",
            'subcategory',
            'CategoryDetails'
        );
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
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for other API errors
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal mengimpor Kategori';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData,
                        'data' => $result['data'] ?? null
                    ], 400);
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
                    'errors' => ['exception' => 'Gagal mengimpor Kategori: ' . $e->getMessage()]
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal mengimpor Kategori: ' . $e->getMessage());
        }
    }
}
