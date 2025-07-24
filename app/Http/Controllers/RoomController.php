<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DataFormatter;
use App\Http\Controllers\Traits\ApiResourceOperations;

class RoomController extends Controller
{
    use ApiResourceOperations;

    /**
     * Menampilkan halaman ruangan.
     */
    public function index(Request $request)
    {
        $extraParams = [];

            // Parameter building_id
        if ($request->filled('building_id')) {
            $extraParams['building_id'] = $request->input('building_id');
            }

        // Custom sort mappings
        $sortMappings = [
            'name_asc' => ['sort_by' => 'room_name', 'sort_order' => 'asc'],
            'name_desc' => ['sort_by' => 'room_name', 'sort_order' => 'desc'],
            'floor_asc' => ['sort_by' => 'floor_number', 'sort_order' => 'asc'],
            'floor_desc' => ['sort_by' => 'floor_number', 'sort_order' => 'desc'],
            'id_asc' => ['sort_by' => 'room_id', 'sort_order' => 'asc'],
            'id_desc' => ['sort_by' => 'room_id', 'sort_order' => 'desc'],
        ];

        return $this->getResourceList(
            $request,
            '/rooms',
            'rooms',
            'Room',
            'room_id',
            $extraParams,
            $sortMappings
        );
    }

    /**
     * Menyimpan ruangan baru.
     */
    public function store(Request $request)
    {
        try {
            // Memvalidasi request
            $validated = $request->validate([
                'room_name' => 'required|string',
                'building_id' => 'required|integer',
                'floor_number' => 'required|string',
                'description' => 'nullable|string'
            ]);

            // Menyiapkan payload untuk API
            $payload = [
                'room_name' => $validated['room_name'],
                'building_id' => (int) $validated['building_id'],
                'floor_number' => $validated['floor_number']
            ];

            // Hanya tambahkan deskripsi ke payload jika tidak kosong
            if (!empty($validated['description'])) {
                $payload['description'] = $validated['description'];
            }

            $result = $this->apiService->request('POST', '/rooms', [
                'json' => $payload
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat ruangan';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $result['errors'] ?? ['general' => 'Gagal membuat ruangan']
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Berhasil dibuat
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Ruangan berhasil dibuat',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('rooms')
                ->with('success', $result['message']);
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

            return $this->handleException($e, $request, 'Room');
        }
    }

    /**
     * Memperbarui ruangan yang ditentukan.
     */
    public function update(Request $request, $id)
    {
        try {
            // Memvalidasi request
            $validated = $request->validate([
                'room_name' => 'required|string',
                'building_id' => 'required|integer',
                'floor_number' => 'required|string',
                'description' => 'nullable|string'
            ]);

            // Menyiapkan payload untuk API
            $payload = [
                'room_id' => (int) $id,
                'room_name' => $validated['room_name'],
                'building_id' => (int) $validated['building_id'],
                'floor_number' => $validated['floor_number']
            ];

            // Hanya tambahkan deskripsi ke payload jika tidak kosong
            if (!empty($validated['description'])) {
                $payload['description'] = $validated['description'];
            }

            $result = $this->apiService->request('PUT', "/rooms/{$id}", [
                'json' => $payload
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengubah ruangan';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $result['errors'] ?? ['general' => 'Gagal mengubah ruangan']
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Berhasil diperbarui
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Ruangan berhasil diperbarui',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('rooms')
                ->with('success', $result['message']);
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

            return $this->handleException($e, $request, 'Room');
        }
    }

    /**
     * Menghapus ruangan yang ditentukan.
     */
    public function destroy($id, Request $request)
    {
        try {
            // Send delete request to API
            $result = $this->apiService->request('DELETE', "/rooms/{$id}");

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menghapus ruangan';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $result['errors'] ?? ['general' => 'Gagal menghapus ruangan']
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
                    'message' => 'Ruangan berhasil dihapus',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('rooms')
                ->with('success', 'Ruangan berhasil dihapus');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus ruangan: ' . $e->getMessage(),
                    'errors' => ['exception' => $e->getMessage()]
                ], 500);
            }

            return $this->handleException($e, $request, 'Room');
        }
    }

    /**
     * Mendapatkan ruangan berdasarkan ID.
     */
    public function show($id, Request $request)
    {
        return $this->getResource(
            $request,
            "/rooms/{$id}",
            'room',
            'RoomDetails'
        );
    }

    /**
     * Mengimpor data ruangan dari file Excel.
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
            $result = $this->apiService->request('POST', '/rooms/import', [
                'multipart' => $multipart
            ]);

            // Check for authentication errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
                }

            // Check for other API errors
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal mengimpor data ruangan';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson()) {
                    // Format respon kesalahan terperinci untuk permintaan AJAX
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
                        'message' => $errorMessage,
                        'errors' => $errorData,
                        'errorDetails' => $errorDetails,
                        'data' => $result['data'] ?? null
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Berhasil diimpor
            $successMessage = $result['message'] ?? 'Data ruangan berhasil diimpor';
            $importData = $result['data'] ?? null;

            // Format pesan sukses dengan jumlah impor jika tersedia
            if ($importData && isset($importData['total'])) {
                $successMessage = sprintf(
                    'Berhasil mengimpor %d dari %d ruangan',
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

            return redirect()->route('rooms')->with('success', $successMessage);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengimpor data ruangan: ' . $e->getMessage(),
                    'errors' => ['exception' => $e->getMessage()]
                ], 500);
            }

            return $this->handleException($e, $request, 'Room');
        }
    }
}
