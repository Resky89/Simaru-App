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
     * Menampilkan daftar merek.
     * Dapat mengembalikan tampilan HTML atau JSON tergantung permintaan.
     */
    public function index(Request $request)
    {
        try {
            // Mendapatkan parameter kueri
            $page = $request->query('page', 1);
            $limit = $request->query('limit', 10);
            $search = $request->query('search', '');
            $sort = $request->query('sort', '');

            // Untuk permintaan JSON, tingkatkan batas untuk memuat lebih banyak item
            if ($request->expectsJson() || $request->ajax()) {
                $limit = $request->query('limit', 100);
            }

            // Membangun parameter kueri
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
                'sort_by' => 'brand_id',
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

            // Mengambil merek dari API
            $brandsResult = $this->apiService->request('GET', '/brands', [
                'query' => $queryParams
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($brandsResult['errors']) && is_string($brandsResult['errors']) &&
                in_array($brandsResult['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $brandsResult['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($brandsResult['errors']) ? $brandsResult['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API berdasarkan flag sukses
            if (!isset($brandsResult['success']) || $brandsResult['success'] !== true) {
                $errorData = $brandsResult['errors'] ?? 'Gagal mengambil data';

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

                return view('Brand', [
                    'brands' => [],
                    'brands_pagination' => null,
                    'error' => $errorMessage
                ]);
            }

            $brands = $brandsResult['data'] ?? [];

            // Jika ini adalah permintaan AJAX atau JSON, kembalikan merek sebagai JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $brandsResult['message'] ?? 'Data berhasil diambil',
                    'data' => $brands
                ]);
            }

            // Format pagination
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

            return view('Brand', [
                'brands' => $brands,
                'brands_pagination' => $brandsPagination
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal mengambil data: ' . $e->getMessage()
                ], 500);
            }

            return view('Brand', [
                'brands' => [],
                'brands_pagination' => null,
                'error' => 'Gagal mengambil data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Menyimpan merek baru.
     */
    public function store(Request $request)
    {
        try {
            // Memvalidasi request
            $validated = $request->validate([
                'brand_name' => 'required|string'
            ]);

            $brandsResult = $this->apiService->request('POST', '/brands', [
                'json' => $validated
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($brandsResult['errors']) && is_string($brandsResult['errors']) &&
                in_array($brandsResult['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $brandsResult['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($brandsResult['errors']) ? $brandsResult['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API atau respon tidak berhasil
            if (!isset($brandsResult['success']) || $brandsResult['success'] === false) {
                $errorData = $brandsResult['errors'] ?? 'Gagal membuat merek';

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

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Berhasil dibuat
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $brandsResult['message'] ?? 'Merek berhasil dibuat',
                    'data' => $brandsResult['data'] ?? null
                ]);
            }

            return redirect()->route('brands')
                ->with('success', $brandsResult['message'] ?? 'Merek berhasil dibuat');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal menambahkan merek: ' . $e->getMessage()],
                    'data' => null
                ], status: 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat merek: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui merek yang ditentukan.
     */
    public function update(Request $request, $id)
    {
        try {
            // Memvalidasi request
            $validated = $request->validate([
                'brand_name' => 'required|string'
            ]);

            $result = $this->apiService->request('PUT', "/brands/{$id}", [
                'json' => array_merge(['brand_id' => $id], $validated)
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
                $errorData = $result['errors'] ?? 'Gagal memperbarui merek';

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

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Berhasil diperbarui
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Merek berhasil diperbarui',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('brands')
                ->with('success', $result['message'] ?? 'Merek berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal memperbarui merek: ' . $e->getMessage()],
                    'data' => null
                ], status: 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui merek: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus merek yang ditentukan.
     */
    public function destroy($id, Request $request)
    {
        try {
            $result = $this->apiService->request('DELETE', "/brands/{$id}");

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
                $errorData = $result['errors'] ?? 'Gagal menghapus merek';

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

                return redirect()->back()
                    ->with('error', $errorMessage);
            }

            // Berhasil dihapus
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Merek berhasil dihapus',
                    'data' => null
                ]);
            }

            return redirect()->route('brands')
                ->with('success', $result['message'] ?? 'Merek berhasil dihapus');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal menghapus merek: ' . $e->getMessage()],
                    'data' => null
                ], status: 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus merek: ' . $e->getMessage());
        }
    }

    /**
     * Mendapatkan merek tunggal untuk pengeditan.
     */
    public function getBrand($id, Request $request)
    {
        try {
            // Mengambil merek dengan ID yang diberikan
            $result = $this->apiService->request('GET', "/brands/{$id}");

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil merek';

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], 400);
                }

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

                return redirect()->back()->with('error', $errorMessage);
            }

            $brand = $result['data'] ?? null;

            if (!$brand) {
                $errorMessage = 'Merek tidak ditemukan atau data respons tidak valid';

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json(['error' => $errorMessage], 404);
                }
                return redirect()->back()->with('error', $errorMessage);
            }

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Data merek berhasil diambil',
                    'data' => $brand
                ]);
            }

            return view('Brand', ['brand' => $brand]);
        } catch (\Exception $e) {
            $errorMessage = 'Gagal mengambil merek: ' . $e->getMessage();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => $errorMessage],
                    'data' => null
                ], 500);
            }
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Mengimpor merek dari file Excel/CSV.
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
            $result = $this->apiService->request('POST', '/brands/import', [
                'multipart' => $multipart
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], status: 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API atau respon tidak berhasil
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal mengimpor merek';

                if ($request->ajax()) {
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

                    // Periksa apakah ada kesalahan terperinci di bagian data
                    if (isset($result['data']) && isset($result['data']['errors']) && !empty($result['data']['errors'])) {
                        $dataErrors = $result['data']['errors'];
                        if (is_array($dataErrors)) {
                            foreach ($dataErrors as $error) {
                                if (is_array($error)) {
                                    // Format setiap objek kesalahan menjadi pesan yang dapat dibaca
                                    if (isset($error['brand_name']) && isset($error['reason'])) {
                                        $rowInfo = isset($error['row']) ? "Baris {$error['row']}: " : '';
                                        $errorDetails[] = "{$rowInfo}\"{$error['brand_name']}\" - {$error['reason']}";
                                    } else if (isset($error['reason'])) {
                                        $rowInfo = isset($error['row']) ? "Baris {$error['row']}: " : '';
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

                // Format pesan kesalahan untuk respons redirect
                $errorMessage = '';

                // Pertama periksa apakah kita memiliki kesalahan terstruktur di data
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
                    $errorMessage = 'Gagal mengimpor merek: ' . $errorList;
                }
                // Jika tidak ada kesalahan terstruktur dalam data, format kesalahan umum
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
                    $errorMessage = 'Gagal mengimpor merek: ' . $errorList;
                } else {
                    $errorMessage = $errorData;
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Berhasil diimpor
            $successMessage = $result['message'] ?? 'Merek berhasil diimpor';
            $importData = $result['data'] ?? null;

            // Format pesan sukses dengan jumlah impor jika tersedia
            if ($importData && isset($importData['total'])) {
                $successMessage = sprintf(
                    'Berhasil mengimpor %d dari %d merek',
                    $importData['success'] ?? 0,
                    $importData['total'] ?? 0
                );

                // Tambahkan info tentang impor yang gagal jika ada
                if (isset($importData['failed']) && $importData['failed'] > 0) {
                    $successMessage .= sprintf(', %d gagal', $importData['failed']);
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
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengimpor merek: ' . $e->getMessage()]
                ], status: 500);
            }

            return redirect()->back()->with('error', 'Gagal mengimpor merek: ' . $e->getMessage());
        }
    }
}
