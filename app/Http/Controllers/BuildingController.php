<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class BuildingController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Menampilkan halaman gedung.
     */
    public function index(Request $request)
    {
        try {
            // Mendapatkan parameter kueri
            $buildingPage = $request->query('building_page', 1);
            $buildingLimit = $request->query('building_limit', 10);
            $search = $request->query('search', '');
            $sort = $request->query('sort', '');

            // Untuk permintaan JSON, tingkatkan batas untuk memuat lebih banyak item
            if ($request->expectsJson() || $request->ajax()) {
                $buildingLimit = $request->query('building_limit', 100);
            }

            $queryParams = [
                'page' => $buildingPage,
                'limit' => $buildingLimit,
                'sort_by' => 'building_id',
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
                        $queryParams['sort_by'] = 'building_name';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'name_desc':
                        $queryParams['sort_by'] = 'building_name';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    case 'id_asc':
                        $queryParams['sort_by'] = 'building_id';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'id_desc':
                        $queryParams['sort_by'] = 'building_id';
                        $queryParams['sort_order'] = 'desc';
                        break;
                }
            }

            // Mengambil gedung dari API
            $buildingResult = $this->apiService->request('GET', '/buildings', [
                'query' => $queryParams
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($buildingResult['errors']) && is_string($buildingResult['errors']) &&
                in_array($buildingResult['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $buildingResult['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($buildingResult['errors']) ? $buildingResult['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API berdasarkan flag sukses
            if (!isset($buildingResult['success']) || $buildingResult['success'] !== true) {
                $errorData = $buildingResult['errors'] ?? 'Gagal mengambil data';

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

                return view('Building', [
                    'buildings' => [],
                    'error' => $errorMessage
                ]);
            }

            $buildings = $buildingResult['data'] ?? [];

            // Format pagination
            $buildingPagination = null;
            if (isset($buildingResult['pagination'])) {
                $pagination = $buildingResult['pagination'];
                $buildingPagination = [
                    'current_page' => $pagination['current_page'] ?? 1,
                    'last_page' => ceil(($pagination['total_items'] ?? 0) / ($pagination['limit'] ?? 10)),
                    'from' => (($pagination['current_page'] ?? 1) - 1) * ($pagination['limit'] ?? 10) + 1,
                    'to' => min(($pagination['current_page'] ?? 1) * ($pagination['limit'] ?? 10), $pagination['total_items'] ?? 0),
                    'total' => $pagination['total_items'] ?? 0,
                    'per_page' => $pagination['limit'] ?? 10,
                    'next_page_url' => ($pagination['has_next'] ?? false) ? url()->current() . '?building_page=' . (($pagination['current_page'] ?? 1) + 1) : null,
                    'prev_page_url' => ($pagination['has_prev'] ?? false) ? url()->current() . '?building_page=' . (($pagination['current_page'] ?? 1) - 1) : null,
                ];
            }

            // Jika ini adalah permintaan AJAX atau JSON, kembalikan gedung sebagai JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $buildingResult['message'] ?? 'Data berhasil diambil',
                    'data' => $buildings,
                    'pagination' => $buildingPagination
                ]);
            }

            return view('Building', [
                'buildings' => $buildings,
                'buildingPagination' => $buildingPagination
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal mengambil data gedung: ' . $e->getMessage()
                ], 500);
            }

            return view('Building', [
                'buildings' => [],
                'error' => 'Gagal mengambil data gedung: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Menyimpan gedung baru.
     */
    public function store(Request $request)
    {
        try {
            // Memvalidasi request
            $validated = $request->validate([
                'building_name' => 'required|string|max:255',
                'address' => 'required|string'
            ]);

            $result = $this->apiService->request('POST', '/buildings', [
                'json' => $validated
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
                $errorData = $result['errors'] ?? 'Gagal membuat gedung';

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
                    'message' => $result['message'] ?? 'Gedung berhasil dibuat',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('buildings')
                ->with('success', $result['message'] ?? 'Gedung berhasil dibuat');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal membuat gedung: ' . $e->getMessage()],
                    'data' => null
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat gedung: ' . $e->getMessage());
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
                'building_name' => 'required|string|max:255',
                'address' => 'required|string'
            ]);

            $result = $this->apiService->request('PUT', "/buildings/{$id}", [
                'json' => array_merge(['building_id' => $id], $validated)
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
                $errorData = $result['errors'] ?? 'Gagal mengubah gedung';

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
                    'message' => $result['message'] ?? 'Gedung berhasil diubah',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('buildings')
                ->with('success', $result['message'] ?? 'Gedung berhasil diubah');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengubah gedung: ' . $e->getMessage()],
                    'data' => null
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengubah gedung: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus gedung yang ditentukan.
     */
    public function destroy($id, Request $request)
    {
        try {
            $result = $this->apiService->request('DELETE', "/buildings/{$id}");

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
                $errorData = $result['errors'] ?? 'Gagal menghapus gedung';

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
                    'message' => $result['message'] ?? 'Gedung berhasil dihapus',
                    'data' => null
                ]);
            }

            return redirect()->route('buildings')
                ->with('success', $result['message'] ?? 'Gedung berhasil dihapus');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal menghapus gedung: ' . $e->getMessage()],
                    'data' => null
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus gedung: ' . $e->getMessage());
        }
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
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API atau respon tidak berhasil
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal mengimpor gedung';

                if ($request->expectsJson()) {
                    // Format respons kesalahan terperinci untuk permintaan AJAX
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

                // Format pesan kesalahan untuk respons redirect
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

            return redirect()->route('buildings')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengimpor data gedung: ' . $e->getMessage()],
                    'data' => null
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal mengimpor data gedung: ' . $e->getMessage());
        }
    }
}
