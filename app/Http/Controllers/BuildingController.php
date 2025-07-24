<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DataFormatter;
use App\Http\Controllers\Traits\ApiResourceOperations;

class BuildingController extends Controller
{
    use ApiResourceOperations;

    /**
     * Menampilkan halaman gedung.
     */
    public function index(Request $request)
    {
        $extraParams = [];

        // Parameter pencarian sudah dihandle oleh trait

        // Custom sort mappings
        $sortMappings = [
            'name_asc' => ['sort_by' => 'building_name', 'sort_order' => 'asc'],
            'name_desc' => ['sort_by' => 'building_name', 'sort_order' => 'desc'],
            'address_asc' => ['sort_by' => 'address', 'sort_order' => 'asc'],
            'address_desc' => ['sort_by' => 'address', 'sort_order' => 'desc'],
            'id_asc' => ['sort_by' => 'building_id', 'sort_order' => 'asc'],
            'id_desc' => ['sort_by' => 'building_id', 'sort_order' => 'desc'],
        ];

        return $this->getResourceList(
            $request,
            '/buildings',
            'buildings',
            'Building',
            'building_id',
            $extraParams,
            $sortMappings
        );
    }

    /**
     * Menyimpan gedung baru.
     */
    public function store(Request $request)
    {
        try {
            // Memvalidasi request
            $validated = $request->validate([
                'building_name' => 'required|string',
                'address' => 'required|string'
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                try {
                    // Kirim ke API
                    $result = $this->apiService->request('POST', '/buildings', ['json' => $validated]);

                    // Periksa kesalahan autentikasi
                    $authError = $this->handleAuthError($result, $request);
                    if ($authError) {
                        return $authError;
                    }

                    // Periksa kesalahan API atau respons tidak berhasil
                    if (!isset($result['success']) || $result['success'] === false) {
                        $errorMessage = DataFormatter::formatErrorMessage($result['errors'] ?? 'Gagal membuat gedung');
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage,
                            'errors' => $result['errors'] ?? null
                        ], 400);
                    }

                    // Berhasil
                    return response()->json([
                        'success' => true,
                        'message' => $result['message'] ?? 'Gedung berhasil dibuat',
                        'data' => $result['data'] ?? null
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal membuat gedung: ' . $e->getMessage(),
                        'errors' => ['exception' => $e->getMessage()]
                    ], 500);
                }
            }

            return $this->storeResource(
                $request,
                '/buildings',
                $validated,
                'Gedung berhasil dibuat',
                'buildings'
            );
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                $errors = $e instanceof \Illuminate\Validation\ValidationException
                    ? $e->errors()
                    : ['exception' => $e->getMessage()];

                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $errors
                ], 422);
            }

            return $this->handleException($e, $request, 'Building');
        }
    }

    /**
     * Memperbarui gedung yang ditentukan.
     */
    public function update(Request $request, $id)
    {
        try {
            // Memvalidasi request
            $validated = $request->validate([
                'building_name' => 'required|string',
                'address' => 'required|string'
            ]);

            // Add building_id to the data
            $data = array_merge(['building_id' => $id], $validated);

            if ($request->expectsJson() || $request->ajax()) {
                try {
                    // Kirim ke API
                    $result = $this->apiService->request('PUT', "/buildings/{$id}", ['json' => $data]);

                    // Periksa kesalahan autentikasi
                    $authError = $this->handleAuthError($result, $request);
                    if ($authError) {
                        return $authError;
                    }

                    // Periksa kesalahan API atau respons tidak berhasil
                    if (!isset($result['success']) || $result['success'] === false) {
                        $errorMessage = DataFormatter::formatErrorMessage($result['errors'] ?? 'Gagal mengubah gedung');
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage,
                            'errors' => $result['errors'] ?? null
                        ], 400);
                    }

                    // Berhasil
                    return response()->json([
                        'success' => true,
                        'message' => $result['message'] ?? 'Gedung berhasil diubah',
                        'data' => $result['data'] ?? null
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengubah gedung: ' . $e->getMessage(),
                        'errors' => ['exception' => $e->getMessage()]
                    ], 500);
                }
            }

            return $this->updateResource(
                $request,
                "/buildings/{$id}",
                $data,
                'Gedung berhasil diubah',
                'buildings'
            );
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                $errors = $e instanceof \Illuminate\Validation\ValidationException
                    ? $e->errors()
                    : ['exception' => $e->getMessage()];

                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $errors
                ], 422);
            }

            return $this->handleException($e, $request, 'Building');
        }
    }

    /**
     * Menghapus gedung yang ditentukan.
     */
    public function destroy($id, Request $request)
    {
        if ($request->expectsJson() || $request->ajax()) {
            try {
                // Kirim ke API
                $result = $this->apiService->request('DELETE', "/buildings/{$id}");

                // Periksa kesalahan autentikasi
                $authError = $this->handleAuthError($result, $request);
                if ($authError) {
                    return $authError;
                }

                // Periksa kesalahan API atau respons tidak berhasil
                if (!isset($result['success']) || $result['success'] === false) {
                    $errorMessage = DataFormatter::formatErrorMessage($result['errors'] ?? 'Gagal menghapus gedung');
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $result['errors'] ?? null
                    ], 400);
                }

                // Berhasil
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Gedung berhasil dihapus',
                    'data' => $result['data'] ?? null
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus gedung: ' . $e->getMessage(),
                    'errors' => ['exception' => $e->getMessage()]
                ], 500);
            }
        }

        return $this->deleteResource(
            $request,
            "/buildings/{$id}",
            'Gedung berhasil dihapus',
            'buildings'
        );
    }

    /**
     * Mendapatkan detail gedung tertentu.
     */
    public function show($id, Request $request)
    {
        return $this->getResource(
            $request,
            "/buildings/{$id}",
            'building',
            'BuildingDetails'
        );
    }

    /**
     * Mengimpor gedung dari file Excel.
     */
    public function import(Request $request)
    {
        try {
            // Memvalidasi request
            $validated = $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
            ]);

            // Membuat data formulir multipart untuk permintaan API
            $multipart = [
                [
                    'name' => 'excel_file',
                    'contents' => fopen($request->file('excel_file')->getPathname(), 'r'),
                    'filename' => $request->file('excel_file')->getClientOriginalName()
                ]
            ];

            // Mengirim permintaan impor ke API
            $result = $this->apiService->request('POST', '/buildings/import', [
                'multipart' => $multipart
            ]);

            // Memeriksa kesalahan autentikasi
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Memeriksa kesalahan API atau respon tidak berhasil
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal mengimpor gedung';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson()) {
                    // Format respons kesalahan terperinci untuk permintaan AJAX
                    $errorDetails = [];

                    // Ekstrak detail kesalahan dari struktur array
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

                    return response()->json([
                        'success' => false,
                        'errors' => $errorData,
                        'errorDetails' => $errorDetails,
                        'data' => $result['data'] ?? null
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Berhasil diimpor
            $successMessage = $result['message'] ?? 'Data gedung berhasil diimpor';
            $importData = $result['data'] ?? null;

            // Format pesan sukses dengan jumlah impor jika tersedia
            if ($importData && isset($importData['total'])) {
                $successMessage = sprintf(
                    'Berhasil mengimpor %d dari %d gedung',
                    $importData['success'] ?? 0,
                    $importData['total'] ?? 0
                );

                // Tambahkan info tentang impor yang gagal jika ada
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

            return redirect()->route('buildings')->with('success', $successMessage);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengimpor data gedung: ' . $e->getMessage()],
                    'data' => null
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal mengimpor data gedung: ' . $e->getMessage());
        }
    }
}
