<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Services\ApiService;

class VendorController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Menampilkan halaman vendor.
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

            // Pengaturan pengurutan default
            $sortBy = 'vendor_id';
            $sortOrder = 'asc';

            // Pengurutan kustom
            if (!empty($sort)) {
                switch ($sort) {
                    case 'id_asc':
                $sortBy = 'vendor_id';
                $sortOrder = 'asc';
                        break;
                    case 'id_desc':
                $sortBy = 'vendor_id';
                $sortOrder = 'desc';
                        break;
                    case 'name_asc':
                $sortBy = 'vendor_name';
                $sortOrder = 'asc';
                        break;
                    case 'name_desc':
                $sortBy = 'vendor_name';
                $sortOrder = 'desc';
                        break;
                }
            }

            // Membangun parameter kueri
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder
            ];

            // Tambahkan parameter pencarian jika disediakan
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Mengambil vendor dari API
            $result = $this->apiService->request('GET', '/vendors', [
                'query' => $queryParams
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

            // Memeriksa kesalahan API berdasarkan flag sukses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil data vendor';

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

                return view('Vendor', [
                    'vendors' => [],
                    'error' => $errorMessage
                ]);
            }

            $vendors = $result['data'] ?? [];

            // Format pagination
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
                    'next_page_url' => ($paginationData['has_next'] ?? false) ? url()->current() . '?page=' . (($paginationData['current_page'] ?? 1) + 1) : null,
                    'prev_page_url' => ($paginationData['has_prev'] ?? false) ? url()->current() . '?page=' . (($paginationData['current_page'] ?? 1) - 1) : null,
                ];
            }

            // Jika ini adalah permintaan AJAX atau JSON, kembalikan vendor sebagai JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Data berhasil diambil',
                    'data' => $vendors,
                    'pagination' => $pagination
                ]);
            }

            return view('Vendor', [
                'vendors' => $vendors,
                'pagination' => $pagination
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Gagal mengambil data vendor: ' . $e->getMessage()
                ], 500);
            }

            return view('Vendor', [
                'vendors' => [],
                'error' => 'Gagal mengambil data vendor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Menyimpan vendor baru.
     */
    public function store(Request $request)
    {
        try {
            // Memvalidasi request
            $validated = $request->validate([
                'vendor_name' => 'required|string',
                'contact_person' => 'nullable|string',
                'phone_number' => 'nullable|string',
                'email' => 'nullable|string|email',
                'website' => 'nullable|string|url',
                'address' => 'nullable|string'
            ]);

            // Hapus field kosong dari body request
            $optionalFields = ['contact_person', 'phone_number', 'email', 'website', 'address'];
            foreach ($optionalFields as $field) {
                if (!isset($validated[$field]) || $validated[$field] === null || $validated[$field] === '') {
                    unset($validated[$field]);
                }
            }

            $result = $this->apiService->request('POST', '/vendors', [
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
                $errorData = $result['errors'] ?? 'Gagal membuat vendor';

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
                    'message' => $result['message'] ?? 'Vendor berhasil dibuat',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('vendor')
                ->with('success', $result['message'] ?? 'Vendor berhasil dibuat');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal membuat vendor: ' . $e->getMessage()],
                    'data' => null
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat vendor: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui vendor yang ditentukan.
     */
    public function update(Request $request, $id)
    {
        try {
            // Memvalidasi request
            $validated = $request->validate([
                'vendor_name' => 'required|string',
                'contact_person' => 'nullable|string',
                'phone_number' => 'nullable|string',
                'email' => 'nullable|string|email',
                'website' => 'nullable|string|url',
                'address' => 'nullable|string'
            ]);

            // Hapus field kosong dari body request
            $optionalFields = ['contact_person', 'phone_number', 'email', 'website', 'address'];
            foreach ($optionalFields as $field) {
                if (!isset($validated[$field]) || $validated[$field] === null || $validated[$field] === '') {
                    unset($validated[$field]);
                }
            }

            // Tambahkan vendor_id ke data tervalidasi
            $validated['vendor_id'] = $id;

            $result = $this->apiService->request('PUT', "/vendors/{$id}", [
                'json' => $validated
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
                $errorData = $result['errors'] ?? 'Gagal mengubah vendor';

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
                    'message' => $result['message'] ?? 'Vendor berhasil diubah',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('vendor')
                ->with('success', $result['message'] ?? 'Vendor berhasil diubah');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengubah vendor: ' . $e->getMessage()],
                    'data' => null
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengubah vendor: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus vendor yang ditentukan.
     */
    public function destroy($id, Request $request)
    {
        try {
            $result = $this->apiService->request('DELETE', "/vendors/{$id}");

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
                $errorData = $result['errors'] ?? 'Gagal menghapus vendor';

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
                    'message' => $result['message'] ?? 'Vendor berhasil dihapus',
                    'data' => null
                ]);
            }

            return redirect()->route('vendor')
                ->with('success', $result['message'] ?? 'Vendor berhasil dihapus');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal menghapus vendor: ' . $e->getMessage()],
                    'data' => null
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus vendor: ' . $e->getMessage());
        }
    }

    /**
     * Mengimpor vendor dari file Excel.
     */
    public function import(Request $request)
    {
        try {
            // Memvalidasi request
            $validated = $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv'
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
            $result = $this->apiService->request('POST', '/vendors/import', [
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
                $errorData = $result['errors'] ?? 'Gagal mengimpor data vendor';

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
            $successMessage = $result['message'] ?? 'Data vendor berhasil diimpor';
            $importData = $result['data'] ?? null;

            // Format pesan sukses dengan jumlah impor jika tersedia
            if ($importData && isset($importData['total'])) {
                $successMessage = sprintf(
                    'Berhasil mengimpor %d dari %d vendor',
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

            return redirect()->route('vendor')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengimpor data vendor: ' . $e->getMessage()],
                    'data' => null
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal mengimpor data vendor: ' . $e->getMessage());
        }
    }
}
