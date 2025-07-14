<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DataFormatter;
use App\Http\Controllers\Traits\ApiResourceOperations;

class BrandController extends Controller
{
    use ApiResourceOperations;



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
            $result = $this->apiService->request('GET', '/brands', [
                'query' => $queryParams
            ]);

            // Memeriksa kesalahan autentikasi
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Memeriksa kesalahan API berdasarkan flag sukses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil data';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return view('Brand', [
                    'brands' => [],
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
                    'brands_pagination' => [
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => $limit ?? 10,
                        'total' => 0,
                        'from' => 0,
                        'to' => 0,
                        'next_page_url' => null,
                        'prev_page_url' => null
                    ]
                ]);
            }

            $brands = $result['data'] ?? [];

            // Jika ini adalah permintaan AJAX atau JSON, kembalikan merek sebagai JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Data berhasil diambil',
                    'data' => $brands
                ]);
            }

            // Format pagination
            $pagination = null;
            if (isset($result['pagination'])) {
                $paginationData = $result['pagination'];
                $pagination = [
                    'current_page' => $paginationData['current_page'] ?? 1,
                    'last_page' => $paginationData['total_pages'] ?? 1,
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
                    'total' => count($brands),
                    'from' => 1,
                    'to' => count($brands),
                    'next_page_url' => null,
                    'prev_page_url' => null
                ];
            }

            return view('Brand', [
                'brands' => $brands,
                'pagination' => $pagination,
                'brands_pagination' => $pagination
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
                'brands_pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $limit ?? 10,
                    'total' => 0,
                    'from' => 0,
                    'to' => 0,
                    'next_page_url' => null,
                    'prev_page_url' => null
                ]
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

            $result = $this->apiService->request('POST', '/brands', [
                'json' => $validated
            ]);

            // Memeriksa kesalahan autentikasi
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Memeriksa kesalahan API atau respon tidak berhasil
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat merek';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Berhasil dibuat
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('brands')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Brand');
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
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Memeriksa kesalahan API atau respon tidak berhasil
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui merek';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Berhasil diperbarui
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('brands')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'Brand');
        }
    }

    /**
     * Menghapus merek yang ditentukan.
     */
    public function destroy($id, Request $request)
    {
        return $this->deleteResource(
            $request,
            "/brands/{$id}",
            'Merek berhasil dihapus',
            'brands'
        );
    }

    /**
     * Mendapatkan merek tunggal untuk pengeditan.
     */
    public function show($id, Request $request)
    {
        return $this->getResource(
            $request,
            "/brands/{$id}",
            'brand',
            'BrandDetails'
        );
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
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Memeriksa kesalahan API atau respon tidak berhasil
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal mengimpor merek';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->ajax()) {
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
                        'errors' => $errorData,
                        'errorDetails' => $errorDetails,
                        'data' => $result['data'] ?? null
                    ], 400);
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
                    'errors' => ['exception' => 'Gagal mengimpor merek: ' . $e->getMessage()],
                    'data' => null
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal mengimpor merek: ' . $e->getMessage());
        }
    }
}
