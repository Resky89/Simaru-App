<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DataFormatter;
use App\Http\Controllers\Traits\ApiResourceOperations;

class VendorController extends Controller
{
    use ApiResourceOperations;

    /**
     * Display a listing of vendors.
     */
    public function index(Request $request)
    {
        // Custom sort mappings
        $sortMappings = [
            'id_asc' => ['sort_by' => 'vendor_id', 'sort_order' => 'asc'],
            'id_desc' => ['sort_by' => 'vendor_id', 'sort_order' => 'desc'],
            'name_asc' => ['sort_by' => 'vendor_name', 'sort_order' => 'asc'],
            'name_desc' => ['sort_by' => 'vendor_name', 'sort_order' => 'desc'],
            'contact_asc' => ['sort_by' => 'contact_person', 'sort_order' => 'asc'],
            'contact_desc' => ['sort_by' => 'contact_person', 'sort_order' => 'desc'],
            'phone_asc' => ['sort_by' => 'phone_number', 'sort_order' => 'asc'],
            'phone_desc' => ['sort_by' => 'phone_number', 'sort_order' => 'desc'],
            'email_asc' => ['sort_by' => 'email', 'sort_order' => 'asc'],
            'email_desc' => ['sort_by' => 'email', 'sort_order' => 'desc'],
        ];

        return $this->getResourceList(
            $request,
            '/vendors',
            'vendors',
            'Vendor',
            'vendor_id',
            [],
            $sortMappings
        );
    }

    /**
     * Store a newly created vendor.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'vendor_name' => 'required|string',
                'contact_person' => 'nullable|string',
                'phone_number' => 'nullable|string',
                'second_phone_number' => 'nullable|string',
                'email' => 'nullable|string|email',
                'website' => 'nullable|string|url',
                'address' => 'nullable|string'
            ]);

            // Remove empty fields from request body
            $optionalFields = ['contact_person', 'phone_number', 'second_phone_number', 'email', 'website', 'address'];
            foreach ($optionalFields as $field) {
                if (!isset($validated[$field]) || $validated[$field] === null || $validated[$field] === '') {
                    unset($validated[$field]);
                }
            }

            $result = $this->apiService->request('POST', '/vendors', [
                'json' => $validated
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat vendor';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $result['errors'] ?? ['general' => 'Gagal membuat vendor']
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Successfully created
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Vendor berhasil dibuat',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('vendor')
                ->with('success', $result['message'] ?? 'Vendor berhasil dibuat');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                $errors = $e instanceof \Illuminate\Validation\ValidationException
                    ? $e->errors()
                    : ['exception' => $e->getMessage()];

                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . $e->getMessage(),
                    'errors' => $errors
                ], 422);
            }
            return $this->handleException($e, $request, 'Vendor');
        }
    }

    /**
     * Update the specified vendor.
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'vendor_name' => 'required|string',
                'contact_person' => 'nullable|string',
                'phone_number' => 'nullable|string',
                'second_phone_number' => 'nullable|string',
                'email' => 'nullable|string|email',
                'website' => 'nullable|string|url',
                'address' => 'nullable|string'
            ]);

            // Remove empty fields from request body
            $optionalFields = ['contact_person', 'phone_number', 'second_phone_number', 'email', 'website', 'address'];
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

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengubah vendor';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $result['errors'] ?? ['general' => 'Gagal mengubah vendor']
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Successfully updated
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Vendor berhasil diubah',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('vendor')
                ->with('success', $result['message'] ?? 'Vendor berhasil diubah');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                $errors = $e instanceof \Illuminate\Validation\ValidationException
                    ? $e->errors()
                    : ['exception' => $e->getMessage()];

                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . $e->getMessage(),
                    'errors' => $errors
                ], 422);
            }
            return $this->handleException($e, $request, 'Vendor');
        }
    }

    /**
     * Remove the specified vendor.
     */
    public function destroy($id, Request $request)
    {
        try {
            // Send delete request to API
            $result = $this->apiService->request('DELETE', "/vendors/{$id}");

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menghapus vendor';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $result['errors'] ?? ['general' => 'Gagal menghapus vendor']
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Successfully deleted
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vendor berhasil dihapus',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('vendor')
                ->with('success', 'Vendor berhasil dihapus');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus vendor: ' . $e->getMessage(),
                    'errors' => ['exception' => $e->getMessage()]
                ], 500);
            }

            return $this->handleException($e, $request, 'Vendor');
        }
    }

    /**
     * Get details for a specific vendor.
     */
    public function getVendor($id)
    {
        return $this->getResource(
            request(),
            "/vendors/{$id}",
            'vendor',
            'VendorDetails'
        );
    }

    /**
     * Import vendors from Excel file.
     */
    public function import(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv'
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

            // Check for authentication errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for other API errors
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal mengimpor data vendor';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $errorData,
                        'data' => $result['data'] ?? null
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Successfully imported
            $successMessage = $result['message'] ?? 'Data vendor berhasil diimpor';
            $importData = $result['data'] ?? null;

            // Format success message with import counts if available
            if ($importData && isset($importData['total'])) {
                $successMessage = sprintf(
                    'Berhasil mengimpor %d dari %d vendor',
                    $importData['success'] ?? 0,
                    $importData['total'] ?? 0
                );

                // Add info about failed imports if any
                if (isset($importData['failed']) && $importData['failed'] > 0) {
                    $successMessage .= sprintf(', %d gagal', $importData['failed']);
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => $importData
                ]);
            }

            return redirect()->route('vendor')->with('success', $successMessage);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengimpor data vendor: ' . $e->getMessage(),
                    'errors' => ['exception' => $e->getMessage()],
                    'data' => null
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal mengimpor data vendor: ' . $e->getMessage());
        }
    }
}
