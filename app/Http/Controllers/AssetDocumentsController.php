<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class AssetDocumentsController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Menampilkan daftar dokumen aset.
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');
            $sortOrder = $request->input('sort', 'newest');

            // Untuk permintaan JSON, tingkatkan batas untuk memuat lebih banyak item
            if ($request->expectsJson() || $request->ajax()) {
                $limit = $request->input('limit', 100);
            }

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit
            ];

            // Set sort parameters based on sortOrder
            switch ($sortOrder) {
                case 'oldest':
                    $queryParams['sort_by'] = 'document_id';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'title_asc':
                    $queryParams['sort_by'] = 'document_title';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'title_desc':
                    $queryParams['sort_by'] = 'document_title';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'date_asc':
                    $queryParams['sort_by'] = 'upload_date';
                    $queryParams['sort_order'] = 'asc';
                    break;
                case 'date_desc':
                    $queryParams['sort_by'] = 'upload_date';
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'newest':
                default:
                    $queryParams['sort_by'] = 'document_id';
                    $queryParams['sort_order'] = 'desc';
                    break;
            }

            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Mengambil dokumen dari API
            $documentsResult = $this->apiService->request('GET', '/asset-documents/documents', [
                'query' => $queryParams
            ]);

            // Memeriksa kesalahan autentikasi
            if (isset($documentsResult['errors']) && is_string($documentsResult['errors']) &&
                in_array($documentsResult['errors'], ['auth_failed', 'session_expired'])) {

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $documentsResult['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($documentsResult['errors']) ? $documentsResult['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API berdasarkan flag sukses
            if (!isset($documentsResult['success']) || $documentsResult['success'] !== true) {
                $errorData = $documentsResult['errors'] ?? 'Gagal mengambil dokumen';

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

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return view('AssetDocument.AssetDocument', [
                    'documents' => [],
                    'documents_pagination' => null,
                    'error' => $errorMessage
                ]);
            }

            // Parse data
            $documents = $documentsResult['data'] ?? [];

            // Format pagination
            $documentsPagination = null;
            if (isset($documentsResult['pagination'])) {
                $pagination = $documentsResult['pagination'];
                $documentsPagination = [
                    'current_page' => $pagination['current_page'] ?? 1,
                    'last_page' => $pagination['total_pages'] ?? ceil(($pagination['total_items'] ?? 0) / ($pagination['limit'] ?? 10)),
                    'from' => (($pagination['current_page'] ?? 1) - 1) * ($pagination['limit'] ?? 10) + 1,
                    'to' => min(($pagination['current_page'] ?? 1) * ($pagination['limit'] ?? 10), $pagination['total_items'] ?? 0),
                    'total' => $pagination['total_items'] ?? 0,
                    'per_page' => $pagination['limit'] ?? 10,
                    'next_page_url' => ($pagination['has_next'] ?? false) ? url()->current() . '?page=' . ($pagination['current_page'] + 1) : null,
                    'prev_page_url' => ($pagination['has_prev'] ?? false) ? url()->current() . '?page=' . ($pagination['current_page'] - 1) : null,
                ];
            }

            // Jika ini adalah permintaan AJAX atau JSON, kembalikan dokumen sebagai JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                    'documents' => $documents,
                    'documents_pagination' => $documentsPagination
                    ]
                ]);
            }

            return view('AssetDocument.AssetDocument', [
                'documents' => $documents,
                'documents_pagination' => $documentsPagination
            ]);
        } catch (\Exception $e) {
            $errorMessage = 'Gagal mengambil dokumen: ' . $e->getMessage();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => $errorMessage]
                ], 500);
            }

            return view('AssetDocument.AssetDocument', [
                'documents' => [],
                'documents_pagination' => null,
                'error' => $errorMessage
            ]);
        }
    }

    /**
     * Mendapatkan dokumen berdasarkan ID.
     */
    public function getDocument($id)
    {
        try {
            // Mengambil dokumen dengan ID yang diberikan
            $result = $this->apiService->request('GET', "/asset-documents/documents/{$id}");

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if (request()->ajax() || request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API berdasarkan flag sukses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil dokumen';

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

                if (request()->ajax() || request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            $document = $result['data'] ?? null;

            if (!$document) {
                $errorMessage = 'Dokumen tidak ditemukan atau data respons tidak valid';

                if (request()->ajax() || request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['general' => $errorMessage]
                    ], 404);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Untuk permintaan AJAX, kembalikan respons JSON dengan format yang diharapkan
            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $document
                ]);
            }

            // Return view dengan data dokumen untuk permintaan non-AJAX
            return view('AssetDocument.DocumentDetail', ['document' => $document]);
        } catch (\Exception $e) {
            $errorMessage = 'Gagal mengambil dokumen: ' . $e->getMessage();

            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => $errorMessage]
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Menyimpan dokumen baru ke dalam penyimpanan.
     */
    public function store(Request $request)
    {
        try {
            // Memvalidasi permintaan
            $request->validate([
                'document_title' => 'required|string',
                'notes' => 'nullable|string',
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
                'asset_ids' => 'nullable|array',
                'asset_ids.*' => 'nullable|integer|exists:assets,asset_id',
            ]);

            // Menyiapkan data untuk permintaan API
            $data = [
                'document_title' => $request->input('document_title'),
                'notes' => $request->input('notes') ?? '',
            ];

            // Tambahkan asset_ids jika disediakan
            if ($request->has('asset_ids')) {
                $data['asset_ids'] = $request->input('asset_ids');
            }

            // Buat permintaan multipart untuk unggah file
            $multipart = [];
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $item) {
                        $multipart[] = [
                            'name' => $key . '[]',
                            'contents' => $item
                        ];
                    }
                } else {
                    $multipart[] = [
                        'name' => $key,
                        'contents' => $value
                    ];
                }
            }

            // Tambahkan file jika disediakan
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $multipart[] = [
                    'name' => 'file',
                    'contents' => fopen($file->getPathname(), 'r'),
                    'filename' => $file->getClientOriginalName()
                ];
            }

            // Buat permintaan API untuk membuat dokumen
            $result = $this->apiService->request('POST', '/asset-documents/documents', [
                'multipart' => $multipart
            ]);

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

            // Memeriksa kesalahan API berdasarkan flag sukses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat dokumen';

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

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage)->withInput();
            }

            $document = $result['data'] ?? null;

            // Untuk permintaan AJAX, kembalikan respons JSON dengan format yang diharapkan
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $document
                ]);
            }

            // Redirect dengan pesan sukses untuk pengiriman formulir
            return redirect()->route('asset-documents')->with('success', 'Dokumen berhasil dibuat');
        } catch (\Exception $e) {
            $errorMessage = 'Gagal membuat dokumen: ' . $e->getMessage();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => $errorMessage]
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage)->withInput();
        }
    }

    /**
     * Menghapus dokumen yang ditentukan.
     */
    public function destroy($id)
    {
        try {
            // Buat permintaan API untuk menghapus dokumen
            $result = $this->apiService->request('DELETE', "/asset-documents/documents/{$id}");

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if (request()->ajax() || request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API berdasarkan flag sukses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menghapus dokumen';

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

                if (request()->ajax() || request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Untuk permintaan AJAX, kembalikan respons JSON dengan format yang diharapkan
            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => null
                ]);
            }

            // Redirect dengan pesan sukses untuk permintaan non-AJAX
            return redirect()->route('asset-documents')->with('success', 'Dokumen berhasil dihapus');
        } catch (\Exception $e) {
            $errorMessage = 'Gagal menghapus dokumen: ' . $e->getMessage();

            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => $errorMessage]
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Memperbarui dokumen yang ditentukan.
     */
    public function update(Request $request, $id)
    {
        try {
            // Memvalidasi permintaan
            $request->validate([
                'document_title' => 'required|string',
                'notes' => 'nullable|string',
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
                'asset_ids' => 'nullable|array',
                'asset_ids.*' => 'nullable|integer|exists:assets,asset_id',
            ]);

            // Menyiapkan data untuk permintaan API
            $data = [
                'document_title' => $request->input('document_title'),
                'notes' => $request->input('notes') ?? '',
            ];

            // Tambahkan asset_ids jika disediakan
            if ($request->has('asset_ids')) {
                $data['asset_ids'] = $request->input('asset_ids');
            }

            // Buat permintaan multipart untuk unggah file
            $multipart = [];
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $item) {
                        $multipart[] = [
                            'name' => $key . '[]',
                            'contents' => $item
                        ];
                    }
                } else {
                    $multipart[] = [
                        'name' => $key,
                        'contents' => $value
                    ];
                }
            }

            // Tambahkan file jika disediakan
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $multipart[] = [
                    'name' => 'file',
                    'contents' => fopen($file->getPathname(), 'r'),
                    'filename' => $file->getClientOriginalName()
                ];
            }

            // Buat permintaan API untuk memperbarui dokumen
            $result = $this->apiService->request('PUT', "/asset-documents/documents/{$id}", [
                'multipart' => $multipart
            ]);

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

            // Memeriksa kesalahan API berdasarkan flag sukses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui dokumen';

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

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage)->withInput();
            }

            $document = $result['data'] ?? null;

            // Untuk permintaan AJAX, kembalikan respons JSON dengan format yang diharapkan
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $document
                ]);
            }

            // Redirect dengan pesan sukses untuk pengiriman formulir
            return redirect()->route('asset-documents')->with('success', 'Dokumen berhasil diperbarui');
        } catch (\Exception $e) {
            $errorMessage = 'Gagal memperbarui dokumen: ' . $e->getMessage();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => $errorMessage]
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage)->withInput();
        }
    }

    /**
     * Menetapkan dokumen ke beberapa aset.
     */
    public function assignToAssets(Request $request, $id)
    {
        try {
            // Memvalidasi permintaan
            $request->validate([
                'asset_ids' => 'required|array',
                'asset_ids.*' => 'required|integer',
            ]);

            // Menyiapkan data permintaan
            $data = [
                'asset_ids' => $request->input('asset_ids')
            ];

            // Buat permintaan API untuk menetapkan dokumen ke aset
            $result = $this->apiService->request('POST', "/asset-documents/documents/{$id}/assign", [
                'json' => $data
            ]);

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

            // Memeriksa kesalahan API berdasarkan flag sukses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menetapkan dokumen ke aset';

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

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], 400);
            }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Periksa apakah kita memiliki URL redirect dalam parameter kueri
            $redirectUrl = $request->query('redirect');

            // Untuk permintaan AJAX/JSON
            if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $result['data'] ?? []
            ]);
            }

            // Untuk permintaan non-AJAX, redirect dengan pesan sukses
            if ($redirectUrl) {
                return redirect($redirectUrl)->with('success', 'Dokumen berhasil ditetapkan ke aset');
            }

            return redirect()->back()->with('success', 'Dokumen berhasil ditetapkan ke aset');
        } catch (\Exception $e) {
            $errorMessage = 'Gagal menetapkan dokumen ke aset: ' . $e->getMessage();

            if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                    'errors' => ['exception' => $errorMessage]
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Menghapus hubungan antara dokumen dan aset.
     */
    public function unlinkFromAsset($assetId, $documentId)
    {
        try {
            // Buat permintaan API untuk menghapus hubungan dokumen dari aset
            $result = $this->apiService->request('DELETE', "/asset-documents/asset/{$assetId}/documents/{$documentId}");

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if (request()->ajax() || request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API berdasarkan flag sukses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menghapus dokumen dari aset';

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

                if (request()->ajax() || request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], 400);
            }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Periksa apakah kita memiliki URL redirect dalam parameter kueri
            $redirectUrl = request()->query('redirect');

            // Kembalikan respons JSON untuk permintaan AJAX
            if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $result['data'] ?? []
            ]);
            }

            // Untuk permintaan non-AJAX, redirect dengan pesan sukses
            if ($redirectUrl) {
                return redirect($redirectUrl)->with('success', 'Dokumen berhasil dihapus dari aset');
            }

            return redirect()->back()->with('success', 'Dokumen berhasil dihapus dari aset');
        } catch (\Exception $e) {
            $errorMessage = 'Gagal menghapus dokumen dari aset: ' . $e->getMessage();

            if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => false,
                    'errors' => ['exception' => $errorMessage]
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Mendapatkan semua dokumen terkait dengan aset tertentu.
     */
    public function getAssetDocuments($assetId)
    {
        try {
            // Buat permintaan API untuk mendapatkan semua dokumen untuk aset
            $result = $this->apiService->request('GET', "/asset-documents/asset/{$assetId}/all-documents");

            // Memeriksa kesalahan autentikasi
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {

                if (request()->ajax() || request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $result['errors'] ?? 'Autentikasi gagal'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
            }

            // Memeriksa kesalahan API berdasarkan flag sukses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil dokumen aset';

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

                if (request()->ajax() || request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Kembalikan respons JSON dengan semua jenis dokumen
            return response()->json([
                'success' => true,
                'message' => 'Semua dokumen terkait berhasil diambil',
                'data' => [
                    'documents' => $result['data']['documents'] ?? [],
                    'calibrationDocuments' => $result['data']['calibrationDocuments'] ?? [],
                    'maintenanceDocuments' => $result['data']['maintenanceDocuments'] ?? []
                ]
            ]);
        } catch (\Exception $e) {
            $errorMessage = 'Gagal mengambil dokumen aset: ' . $e->getMessage();

            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => $errorMessage]
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Membuat dokumen yang langsung terkait dengan aset tertentu.
     */
    public function createAssetDocument(Request $request, $assetId)
    {
        try {
            // Memvalidasi permintaan
            $request->validate([
                'document_title' => 'required|string',
                'notes' => 'nullable|string',
                'document' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            ]);

            // Menyiapkan data untuk permintaan API
            $data = [
                'document_title' => $request->input('document_title'),
                'notes' => $request->input('notes') ?? '',
            ];

            // Buat permintaan multipart untuk unggah file
            $multipart = [];
            foreach ($data as $key => $value) {
                $multipart[] = [
                    'name' => $key,
                    'contents' => $value
                ];
            }

            // Tambahkan file (wajib untuk endpoint ini)
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $multipart[] = [
                    'name' => 'file',
                    'contents' => fopen($file->getPathname(), 'r'),
                    'filename' => $file->getClientOriginalName()
                ];
            }

            // Buat permintaan API untuk membuat dokumen langsung terkait dengan aset
            $result = $this->apiService->request('POST', "/asset-documents/asset/{$assetId}/documents", [
                'multipart' => $multipart
            ]);

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

            // Memeriksa kesalahan API berdasarkan flag sukses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat dokumen untuk aset';

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

                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage)->withInput();
            }

            // Untuk permintaan AJAX, kembalikan respons JSON
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data'] ?? null
                ]);
            }

            // Periksa apakah kita memiliki URL redirect dalam parameter kueri
            $redirectUrl = $request->query('redirect');
            if ($redirectUrl) {
                return redirect($redirectUrl)->with('success', 'Dokumen berhasil dibuat dan ditetapkan ke aset');
            }

            // Redirect dengan pesan sukses
            return redirect()->back()->with('success', 'Dokumen berhasil dibuat dan ditetapkan ke aset');
        } catch (\Exception $e) {
            $errorMessage = 'Gagal membuat dokumen untuk aset: ' . $e->getMessage();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => $errorMessage]
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage)->withInput();
        }
    }
}
