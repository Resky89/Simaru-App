<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\DataFormatter;
use App\Http\Controllers\Traits\ApiResourceOperations;
use App\Http\Controllers\Traits\ExportableToPdf;

class ComplainRepairController extends Controller
{
    use ApiResourceOperations, ExportableToPdf;

    /**
     * Display a listing of complaints.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Get pagination parameters
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');
            $sort_order = $request->input('sort_order', '');
            $sort_by = $request->input('sort_by', '');

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit
            ];

            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            if (!empty($sort_order)) {
                $queryParams['sort_order'] = $sort_order;

                // Map sort_order to sort_by if not specified
                if (empty($sort_by)) {
                    if (in_array($sort_order, ['asc', 'desc'])) {
                        $queryParams['sort_by'] = 'created_at';
                    }
                }
            }

            // Add status filter if provided
            if ($request->filled('status')) {
                $queryParams['status'] = $request->input('status');
            }

            // Get data from API
            $result = $this->apiService->request('GET', '/complaints', [
                'query' => $queryParams
            ]);

            // Handle auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Handle API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to retrieve complaints',
                        'errors' => $result['errors'] ?? 'Unknown error'
                    ], 400);
                }

                return redirect()->route('dashboard')
                    ->with('error', 'Gagal mengambil data keluhan');
            }

            // Get complaints and pagination data
            $complaints = $result['data'] ?? [];
            $pagination = $result['pagination'] ?? null;

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'complaints' => $complaints,
                    'pagination' => $pagination
                ]);
            }

            // Check if we need to set a success message from the with_success parameter
            if ($request->has('with_success')) {
                session()->flash('success', $request->input('with_success'));
            }

            return view('ComplainRepair.ComplainRepair', [
                'complaints' => $complaints,
                'complaints_pagination' => $pagination,
                'search' => $search,
                'sort_order' => $sort_order,
                'status' => $request->input('status')
            ]);

        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'ComplainRepair.ComplainRepair');
        }
    }

    /**
     * Display the specified complaint.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|\Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        try {
            $result = $this->apiService->request('GET', '/complaints/' . $id);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError instanceof \Illuminate\Http\RedirectResponse) {
                return $authError;
            }

            // Check if complaint exists
            if (!isset($result['data']) || empty($result['data'])) {
                return redirect()->route('complaint-repair.index')->with('error', 'Keluhan tidak ditemukan');
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data keluhan berhasil diambil',
                    'complaint' => $result['data']
                ]);
            }

            // Return view with complaint data
            return view('ComplainRepair.ComplainRepairDetail', [
                'complaint' => $result['data']
            ]);

        } catch (\Exception $e) {
            return redirect()->route('complaint-repair.index')->with('error', 'Gagal mengambil detail keluhan: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created complaint.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'asset_id' => 'required|integer',
                'description' => 'required|string',
                'image_file' => 'required|image',
            ]);

            if ($validator->fails()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal. Silakan periksa kembali data yang dimasukkan.',
                        'errors' => $validator->errors()
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->withErrors($validator)
                    ->with('error', 'Silakan periksa formulir untuk kesalahan.');
            }

            // Prepare multipart data for file upload
            $multipart = [
                [
                    'name' => 'asset_id',
                    'contents' => $request->input('asset_id')
                ],
                [
                    'name' => 'description',
                    'contents' => $request->input('description')
                ]
            ];

            // Handle image file
            if ($request->hasFile('image_file') && $request->file('image_file')->isValid()) {
                $multipart[] = [
                    'name' => 'image_file',
                    'contents' => fopen($request->file('image_file')->getPathname(), 'r'),
                    'filename' => $request->file('image_file')->getClientOriginalName()
                ];
            }

            // Send request to API
            $result = $this->apiService->request('POST', '/complaints', [
                'multipart' => $multipart
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat keluhan';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $result['errors'] ?? ['general' => 'Gagal membuat keluhan']
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Success response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Keluhan berhasil dibuat',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('complaint-repair.index')
                ->with('success', 'Keluhan berhasil dibuat');

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                    'errors' => ['exception' => $e->getMessage()]
                ], 500);
            }

            return $this->handleException($e, $request, 'ComplainRepair.ComplainRepair');
        }
    }

    /**
     * Remove the specified complaint.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        try {
            // Send delete request to API
            $result = $this->apiService->request('DELETE', "/complaints/{$id}");

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menghapus keluhan';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $result['errors'] ?? ['general' => 'Gagal menghapus keluhan']
                    ], 422);
                }

                return redirect()->back()
                    ->with('error', $errorMessage);
            }

            // Success response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Keluhan berhasil dihapus',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('complaint-repair.index')
                ->with('success', 'Keluhan berhasil dihapus');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                    'errors' => ['exception' => $e->getMessage()]
                ], 500);
            }

            return $this->handleException($e, $request, 'ComplainRepair.ComplainRepair');
        }
    }

    /**
     * Store a newly created repair record.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function storeRepair(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'complaint_id' => 'required|integer',
                'repair_description' => 'required|string',
                'final_result' => 'required|string|in:Good,Slightly Damage,Heavy Damage,Waiting for Part',
                'repair_cost' => 'required|numeric',
                'parts_replaced' => 'required|string',
                'file' => 'required|image',
            ]);

            if ($validator->fails()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validasi gagal. Silakan periksa kembali data yang dimasukkan.',
                        'errors' => $validator->errors()
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->withErrors($validator)
                    ->with('error', 'Silakan periksa formulir untuk kesalahan.');
            }

            // Prepare multipart data
            $multipart = [
                [
                    'name' => 'complaint_id',
                    'contents' => $request->input('complaint_id')
                ],
                [
                    'name' => 'repair_description',
                    'contents' => $request->input('repair_description')
                ],
                [
                    'name' => 'final_result',
                    'contents' => $request->input('final_result')
                ],
                [
                    'name' => 'repair_cost',
                    'contents' => $request->input('repair_cost')
                ],
                [
                    'name' => 'parts_replaced',
                    'contents' => $request->input('parts_replaced')
                ]
            ];

            // Handle file upload
            if ($request->hasFile('file') && $request->file('file')->isValid()) {
                $multipart[] = [
                    'name' => 'file',
                    'contents' => fopen($request->file('file')->getPathname(), 'r'),
                    'filename' => $request->file('file')->getClientOriginalName()
                ];
            }

            // Send request to API
            $result = $this->apiService->request('POST', '/repairs', [
                'multipart' => $multipart
            ]);

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat perbaikan';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $result['errors'] ?? ['general' => 'Gagal membuat perbaikan']
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Success response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Perbaikan berhasil dibuat',
                    'data' => $result['data'] ?? null
                ]);
            }

            return $this->index($request->merge(['with_success' => 'Perbaikan berhasil dibuat']));

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                    'errors' => ['exception' => $e->getMessage()]
                ], 500);
            }

            return $this->handleException($e, $request, 'ComplainRepair.ComplainRepair');
        }
    }

    /**
     * Start repair process for a complaint.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function startRepair($id, Request $request)
    {
        try {
            // Send PATCH request to API
            $result = $this->apiService->request('PATCH', "/complaints/{$id}/start");

            // Check for auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal memulai proses perbaikan';
                $errorMessage = DataFormatter::formatErrorMessage($errorData);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $result['errors'] ?? ['general' => 'Gagal memulai proses perbaikan']
                    ], 422);
                }

                return redirect()->back()->with('error', 'Gagal memulai proses perbaikan');
            }

            // Return success response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Proses perbaikan berhasil dimulai',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->back()->with('success', 'Proses perbaikan berhasil dimulai');

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                    'errors' => ['exception' => $e->getMessage()]
                ], 500);
            }

            return $this->handleException($e, $request, 'ComplainRepair.ComplainRepairDetail');
        }
    }

    /**
     * Export complaints list to PDF.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function exportPDF(Request $request)
    {
        try {
            // Get filter parameters
            $queryParams = [
                'page' => 1,
                'limit' => 1000
            ];

            if ($request->filled('search')) {
                $queryParams['search'] = $request->input('search');
            }

            if ($request->filled('status')) {
                $queryParams['status'] = $request->input('status');
            }

            if ($request->filled('sort_order')) {
                $queryParams['sort_order'] = $request->input('sort_order');
            }

            if ($request->filled('sort_by')) {
                $queryParams['sort_by'] = $request->input('sort_by');
            }

            // Get data from API
            $result = $this->apiService->request('GET', '/complaints', [
                'query' => $queryParams
            ]);

            // Handle auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Handle API errors
            $apiError = $this->handleApiError(
                $result,
                $request,
                'ComplainRepair.ComplainRepair',
                'Gagal mengambil data untuk ekspor'
            );

            if ($apiError) {
                return $apiError;
            }

            $complaints = $result['data'] ?? [];

            // Generate filename
            $timestamp = date('YmdHis');
            $filename = "laporan_keluhan_{$timestamp}.pdf";

            // Generate PDF
            return $this->generatePdf(
                'ComplainRepair.ComplainRepairPDF',
                [
                    'complaints' => $complaints,
                    'search' => $request->input('search', ''),
                    'sort_order' => $request->input('sort_order', ''),
                    'status' => $request->input('status', ''),
                    'date_generated' => date('d M Y H:i:s')
                ],
                $filename
            );

        } catch (\Exception $e) {
            return $this->handleException($e, $request, 'ComplainRepair.ComplainRepair');
        }
    }

    /**
     * Export complaint details to PDF.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function exportDetailPDF($id, Request $request)
    {
        try {
            // Get complaint details from API
            $result = $this->apiService->request('GET', "/complaints/{$id}");

            // Handle auth errors
            $authError = $this->handleAuthError($result, $request);
            if ($authError) {
                return $authError;
            }

            // Check if complaint exists
            if (!isset($result['data'])) {
                return redirect()->route('complaint-repair.index')
                    ->with('error', 'Keluhan tidak ditemukan');
            }

            $complaint = $result['data'];
             // Convert complaint image to base64 if exists
             if (!empty($complaint['complaint_picture_path'])) {
                try {
                    $backendUrl = config('app.backend_url', 'https://web-magangunbin2025.rsummi.co.id/api');
                    $imagePath = $backendUrl . '/public/images/' . basename($complaint['asset_image_path']);
                    $imageData = file_get_contents($imagePath);
                    if ($imageData !== false) {
                        $complaint['asset_image_base64'] = base64_encode($imageData);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to convert complaint image to base64: ' . $e->getMessage());
                }
            }

            // Convert complaint image to base64 if exists
            if (!empty($complaint['complaint_picture_path'])) {
                try {
                    $backendUrl = config('app.backend_url', 'https://web-magangunbin2025.rsummi.co.id/api');
                    $imagePath = $backendUrl . '/public/images/' . basename($complaint['complaint_picture_path']);
                    $imageData = file_get_contents($imagePath);
                    if ($imageData !== false) {
                        $complaint['complaint_picture_base64'] = base64_encode($imageData);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to convert complaint image to base64: ' . $e->getMessage());
                }
            }

            // Convert repair image to base64 if exists
            if (!empty($complaint['repair']) && !empty($complaint['repair']['repair_picture_path'])) {
                try {
                    $backendUrl = config('app.backend_url', 'https://web-magangunbin2025.rsummi.co.id/api');
                    $repairImagePath = $backendUrl . '/public/images/' . basename($complaint['repair']['repair_picture_path']);
                    $repairImageData = file_get_contents($repairImagePath);
                    if ($repairImageData !== false) {
                        $complaint['repair']['repair_picture_base64'] = base64_encode($repairImageData);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to convert repair image to base64: ' . $e->getMessage());
                }
            }

            // Generate filename
            $timestamp = date('YmdHis');
            $filename = "detail_keluhan_{$id}_{$timestamp}.pdf";

            // Generate PDF
            return $this->generatePdf(
                'ComplainRepair.ComplainRepairDetailPDF',
                [
                    'complaint' => $complaint
                ],
                $filename
            );

        } catch (\Exception $e) {
            \Log::error('PDF export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor detail keluhan sebagai PDF: ' . $e->getMessage());
        }
    }
}
