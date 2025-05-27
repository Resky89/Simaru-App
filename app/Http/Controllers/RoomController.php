<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class RoomController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Menampilkan halaman ruangan.
     */
    public function index(Request $request)
    {
        try {
            // Mendapatkan parameter kueri
            $roomPage = $request->query('room_page', 1);
            $roomLimit = $request->query('room_limit', 10);
            $search = $request->query('search', '');
            $sort = $request->query('sort', '');

            // Untuk permintaan JSON, tingkatkan batas untuk memuat lebih banyak item
            if ($request->expectsJson() || $request->ajax()) {
                $roomLimit = $request->query('room_limit', 100);
            }

            // Build query parameters
            $queryParams = [
                'page' => $roomPage,
                'limit' => $roomLimit,
                'sort_by' => 'room_id',
                'sort_order' => 'asc'
            ];

            // Parameter pencarian
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Pengurutan kustom
            if (!empty($sort)) {
                switch ($sort) {
                    case 'name_asc':
                        $queryParams['sort_by'] = 'room_name';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'name_desc':
                        $queryParams['sort_by'] = 'room_name';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    case 'id_asc':
                        $queryParams['sort_by'] = 'room_id';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'id_desc':
                        $queryParams['sort_by'] = 'room_id';
                        $queryParams['sort_order'] = 'desc';
                        break;
                }
            }

            // Mengambil ruangan dari API
            $roomResult = $this->apiService->request('GET', '/rooms', [
                'query' => $queryParams
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($roomResult['errors']) && is_string($roomResult['errors']) &&
                in_array($roomResult['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $roomResult['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($roomResult['errors']) ? $roomResult['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API berdasarkan flag sukses
            if (!isset($roomResult['success']) || $roomResult['success'] !== true) {
                $errorData = $roomResult['errors'] ?? 'Gagal mengambil data';

                // Format pesan kesalahan
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

                return view('Room', [
                    'rooms' => [],
                    'error' => $errorMessage
                ]);
            }

            $rooms = $roomResult['data'] ?? [];

            // Format pagination
            $roomPagination = null;
            if (isset($roomResult['pagination'])) {
                $pagination = $roomResult['pagination'];
                $roomPagination = [
                    'current_page' => $pagination['current_page'] ?? 1,
                    'last_page' => ceil(($pagination['total_items'] ?? 0) / ($pagination['limit'] ?? 10)),
                    'from' => (($pagination['current_page'] ?? 1) - 1) * ($pagination['limit'] ?? 10) + 1,
                    'to' => min(($pagination['current_page'] ?? 1) * ($pagination['limit'] ?? 10), $pagination['total_items'] ?? 0),
                    'total' => $pagination['total_items'] ?? 0,
                    'per_page' => $pagination['limit'] ?? 10,
                    'next_page_url' => ($pagination['has_next'] ?? false) ? url()->current() . '?room_page=' . (($pagination['current_page'] ?? 1) + 1) : null,
                    'prev_page_url' => ($pagination['has_prev'] ?? false) ? url()->current() . '?room_page=' . (($pagination['current_page'] ?? 1) - 1) : null,
                ];
            }

            // Jika ini adalah permintaan AJAX atau JSON, kembalikan ruangan sebagai JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $roomResult['message'] ?? 'Data berhasil diambil',
                    'data' => $rooms,
                    'pagination' => $roomPagination
                ]);
            }

            return view('Room', [
                'rooms' => $rooms,
                'roomPagination' => $roomPagination
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal mengambil data ruangan: ' . $e->getMessage()
                ], 500);
            }

            return view('Room', [
                'rooms' => [],
                'error' => 'Gagal mengambil data ruangan: ' . $e->getMessage()
            ]);
        }
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

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API atau respon tidak berhasil
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal membuat ruangan';

                // Format pesan kesalahan
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
                ->with('success', $result['message'] ?? 'Ruangan berhasil dibuat');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal membuat ruangan: ' . $e->getMessage()],
                    'data' => null
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat ruangan: ' . $e->getMessage());
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

            // Memeriksa kesalahan autentikasi
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

            // Memeriksa kesalahan API atau respon tidak berhasil
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal mengubah ruangan';

                // Format pesan kesalahan
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

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Berhasil diperbarui
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Ruangan berhasil diubah',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('rooms')
                ->with('success', $result['message'] ?? 'Ruangan berhasil diubah');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengubah ruangan: ' . $e->getMessage()],
                    'data' => null
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengubah ruangan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus ruangan yang ditentukan.
     */
    public function destroy($id, Request $request)
    {
        try {
            $result = $this->apiService->request('DELETE', "/rooms/{$id}");

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API atau respon tidak berhasil
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal menghapus ruangan';

                // Format pesan kesalahan
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

                return redirect()->back()
                    ->with('error', $errorMessage);
            }

            // Berhasil dihapus
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Ruangan berhasil dihapus',
                    'data' => null
                ]);
            }

            return redirect()->route('rooms')
                ->with('success', $result['message'] ?? 'Ruangan berhasil dihapus');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal menghapus ruangan: ' . $e->getMessage()],
                    'data' => null
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus ruangan: ' . $e->getMessage());
        }
    }

    /**
     * Mendapatkan ruangan berdasarkan ID.
     */
    public function getById(Request $request, $id)
    {
        try {
            // Ambil ruangan berdasarkan ID
            $result = $this->apiService->request('GET', "/rooms/{$id}");

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Autentikasi gagal'
                ], 401);
            }

            // Memeriksa kesalahan API atau respon tidak berhasil
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal mengambil data ruangan';
                return response()->json([
                    'success' => false,
                    'message' => is_array($errorData) ? implode(', ', $errorData) : $errorData
                ], 400);
            }

            // Kembalikan respon sukses
            return response()->json([
                'success' => true,
                'data' => $result['data'] ?? []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data ruangan: ' . $e->getMessage()
            ], 500);
        }
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

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API atau respon tidak berhasil
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal mengimpor data ruangan';

                if ($request->expectsJson()) {
                    // Format respon kesalahan terperinci untuk permintaan AJAX
                    $formattedErrors = $errorData;
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
                        'errors' => $formattedErrors,
                        'errorDetails' => $errorDetails,
                        'data' => $result['data'] ?? null
                    ], 400);
                }

                // Format pesan kesalahan untuk respon redirect
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

            return redirect()->route('rooms')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengimpor data ruangan: ' . $e->getMessage()],
                    'data' => null
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal mengimpor data ruangan: ' . $e->getMessage());
        }
    }
}
