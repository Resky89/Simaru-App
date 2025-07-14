<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DataFormatter;
use App\Http\Controllers\Traits\ApiResourceOperations;

class AssetDocumentsController extends Controller
{
    use ApiResourceOperations;

    /**
     * Menampilkan daftar dokumen aset.
     */
    public function index(Request $request)
    {
        $extraParams = [];

        // Custom sort mappings
        $sortMappings = [
            'oldest' => ['sort_by' => 'document_id', 'sort_order' => 'asc'],
            'title_asc' => ['sort_by' => 'document_title', 'sort_order' => 'asc'],
            'title_desc' => ['sort_by' => 'document_title', 'sort_order' => 'desc'],
            'date_asc' => ['sort_by' => 'upload_date', 'sort_order' => 'asc'],
            'date_desc' => ['sort_by' => 'upload_date', 'sort_order' => 'desc'],
            'newest' => ['sort_by' => 'document_id', 'sort_order' => 'desc'],
        ];

        return $this->getResourceList(
            $request,
            '/asset-documents/documents',
            'documents',
            'AssetDocument.AssetDocument',
            'document_id',
            $extraParams,
            $sortMappings
        );
    }

    /**
     * Mendapatkan dokumen berdasarkan ID.
     */
    public function getDocument($id)
    {
        return $this->getResource(
            request(),
            "/asset-documents/documents/{$id}",
            'document',
            'AssetDocument.DocumentDetail'
        );
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

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat dokumen';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

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
            return $this->handleException($e, $request, 'AssetDocument.AssetDocument');
        }
    }

    /**
     * Menghapus dokumen yang ditentukan.
     */
    public function destroy($id)
    {
        return $this->deleteResource(
            request(),
            "/asset-documents/documents/{$id}",
            'Dokumen berhasil dihapus',
            'asset-documents'
        );
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

            // Tambahkan flag untuk menghapus file jika diminta
            if ($request->has('remove_file')) {
                $data['remove_file'] = true;
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

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memperbarui dokumen';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

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
            return $this->handleException($e, $request, 'AssetDocument.AssetDocument');
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

            // Gunakan metode dari trait untuk membuat permintaan API
            $result = $this->apiService->request('POST', "/asset-documents/documents/{$id}/assign", [
                'json' => $data
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menetapkan dokumen ke aset';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

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
                    'message' => 'Dokumen berhasil ditetapkan ke aset',
                'data' => $result['data'] ?? []
            ]);
            }

            // Untuk permintaan non-AJAX, redirect dengan pesan sukses
            if ($redirectUrl) {
                return redirect($redirectUrl)->with('success', 'Dokumen berhasil ditetapkan ke aset');
            }

            return redirect()->back()->with('success', 'Dokumen berhasil ditetapkan ke aset');
        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'AssetDocument.DocumentDetail');
        }
    }

    /**
     * Menghapus hubungan antara dokumen dan aset.
     */
    public function unlinkFromAsset($assetId, $documentId)
    {
        try {
            // Gunakan metode dari trait untuk membuat permintaan API
            $result = $this->apiService->request('DELETE', "/asset-documents/asset/{$assetId}/documents/{$documentId}");

            // Check for auth errors
            $authError = $this->handleAuthError($result, request());
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menghapus dokumen dari aset';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

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
                    'message' => 'Dokumen berhasil dihapus dari aset',
                'data' => $result['data'] ?? []
            ]);
            }

            // Untuk permintaan non-AJAX, redirect dengan pesan sukses
            if ($redirectUrl) {
                return redirect($redirectUrl)->with('success', 'Dokumen berhasil dihapus dari aset');
            }

            return redirect()->back()->with('success', 'Dokumen berhasil dihapus dari aset');
        } catch (\Exception $e) {
            return $this->handleException($e, request(), 'AssetDocument.DocumentDetail');
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

            // Check for auth errors
            $authError = $this->handleAuthError($result, request());
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengambil dokumen aset';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

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
            return $this->handleException($e, request(), 'AssetDocument.AssetDocument');
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

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat dokumen untuk aset';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

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
            return $this->handleException($e, $request, 'AssetDocument.AssetDocument');
        }
    }
}
