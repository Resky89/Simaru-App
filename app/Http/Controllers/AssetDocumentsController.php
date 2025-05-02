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
     * Display a listing of all documents.
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);
            $search = $request->input('search', '');
            $sortOrder = $request->input('sort', 'newest');

            // Log request info
            \Log::info('Fetching asset documents with parameters:', [
                'page' => $page,
                'limit' => $limit,
                'search' => $search,
                'sort' => $sortOrder,
                'request_url' => $request->fullUrl()
            ]);

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
                case 'newest':
                default:
                    $queryParams['sort_by'] = 'document_id';
                    $queryParams['sort_order'] = 'desc';
                    break;
            }

            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Fetch documents
            $documentsResult = $this->apiService->request('GET', '/asset-documents/documents', [
                'query' => $queryParams
            ]);

            // Log API response for debugging
            \Log::info('API response for documents list:', [
                'documents_status' => $documentsResult['success'] ?? false,
                'documents_count' => isset($documentsResult['data']) ? count($documentsResult['data']) : 0
            ]);

            // Check for auth errors
            if (isset($documentsResult['error']) && in_array($documentsResult['error'], ['auth_failed', 'session_expired'])) {
                $errorMessage = $documentsResult['message'] ?? 'Authentication failed';
                return redirect()->route('login')->with('error', $errorMessage);
            }

            // Check for API errors based on success flag
            if (!isset($documentsResult['success']) || $documentsResult['success'] !== true) {
                $errorMessage = $documentsResult['message'] ?? 'Failed to fetch documents';

                \Log::warning('Error during documents retrieval:', [
                    'success' => $documentsResult['success'] ?? false,
                    'message' => $errorMessage
                ]);

                return view('AssetDocument.AssetDocument', [
                    'documents' => [],
                    'documents_pagination' => null,
                    'error' => $errorMessage
                ]);
            }

            // Parse data
            $documents = $documentsResult['data'] ?? [];

            // Format pagination for documents
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

            // Check if this is an AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'documents' => $documents,
                    'documents_pagination' => $documentsPagination
                ]);
            }

            return view('AssetDocument.AssetDocument', [
                'documents' => $documents,
                'documents_pagination' => $documentsPagination
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception during documents retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to fetch documents: ' . $e->getMessage()
                ], 500);
            }

            return view('AssetDocument.AssetDocument', [
                'documents' => [],
                'documents_pagination' => null,
                'error' => 'Failed to fetch documents: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get a single document by ID.
     */
    public function getDocument($id)
    {
        try {
            // Log request info
            \Log::info('Fetching single document with ID:', [
                'document_id' => $id,
                'request_url' => request()->fullUrl(),
                'ajax' => request()->ajax() ? 'Yes' : 'No',
                'wants_json' => request()->wantsJson() ? 'Yes' : 'No',
                'accepts_json' => request()->expectsJson() ? 'Yes' : 'No',
                'headers' => request()->header('Accept')
            ]);

            // Fetch the document with the given ID
            $result = $this->apiService->request('GET', "/asset-documents/documents/{$id}");

            // Log API response for debugging
            \Log::info('API response for single document:', [
                'api_response_success' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'document_id' => $id
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during document retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                if (request()->ajax() || request()->wantsJson() || request()->expectsJson() || request()->header('X-Requested-With') == 'XMLHttpRequest') {
                    return response()->json(['error' => $result['message'] ?? 'Authentication failed'], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to retrieve document';

                \Log::warning('Error during document retrieval:', [
                    'success' => $result['success'] ?? false,
                    'message' => $errorMessage
                ]);

                if (request()->ajax() || request()->wantsJson() || request()->expectsJson() || request()->header('X-Requested-With') == 'XMLHttpRequest') {
                    return response()->json(['error' => $errorMessage], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            $document = $result['data'] ?? null;

            if (!$document) {
                $errorMessage = 'Document not found or response data is invalid';

                if (request()->ajax() || request()->wantsJson() || request()->expectsJson() || request()->header('X-Requested-With') == 'XMLHttpRequest') {
                    return response()->json(['error' => $errorMessage], 404);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // For AJAX requests, return JSON response with the expected format
            if (request()->ajax() || request()->wantsJson() || request()->expectsJson() || request()->header('X-Requested-With') == 'XMLHttpRequest' || request()->header('Accept') == 'application/json') {
                return response()->json([
                    'success' => true,
                    'message' => 'Document retrieved successfully',
                    'data' => $document
                ]);
            }

            // Return view with document data for non-AJAX requests
            return view('AssetDocument.DocumentDetail', ['document' => $document]);
        } catch (\Exception $e) {
            $errorMessage = 'Failed to retrieve document: ' . $e->getMessage();

            \Log::error('Exception during document retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'document_id' => $id
            ]);

            if (request()->ajax() || request()->wantsJson() || request()->expectsJson() || request()->header('X-Requested-With') == 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => null
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Store a newly created document in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'document_title' => 'required|string|max:255',
                'notes' => 'nullable|string',
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
                'asset_ids' => 'nullable|array',
                'asset_ids.*' => 'nullable|integer|exists:assets,asset_id',
            ]);

            // Log request info
            \Log::info('Creating new document:', [
                'document_title' => $request->input('document_title'),
                'has_file' => $request->hasFile('file') ? 'Yes' : 'No',
                'request_url' => $request->fullUrl()
            ]);

            // Prepare data for API request
            $data = [
                'document_title' => $request->input('document_title'),
                'notes' => $request->input('notes') ?? '',
            ];

            // Add asset_ids if provided
            if ($request->has('asset_ids')) {
                $data['asset_ids'] = $request->input('asset_ids');
            }

            // Create multipart request for file upload
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

            // Add file if provided
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $multipart[] = [
                    'name' => 'file',
                    'contents' => fopen($file->getPathname(), 'r'),
                    'filename' => $file->getClientOriginalName()
                ];
            }

            // Make API request to create document
            $result = $this->apiService->request('POST', '/asset-documents/documents', [
                'multipart' => $multipart
            ]);

            // Log API response for debugging
            \Log::info('API response for document creation:', [
                'api_response_success' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during document creation:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['error' => $result['message'] ?? 'Authentication failed'], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to create document';

                \Log::warning('Error during document creation:', [
                    'success' => $result['success'] ?? false,
                    'message' => $errorMessage
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['error' => $errorMessage], 400);
                }

                return redirect()->back()->with('error', $errorMessage)->withInput();
            }

            $document = $result['data'] ?? null;

            // For AJAX requests, return JSON response with the expected format
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Document created successfully',
                    'data' => $document
                ]);
            }

            // Redirect with success message for form submissions
            return redirect()->route('asset-documents')->with('success', 'Document created successfully');
        } catch (\Exception $e) {
            $errorMessage = 'Failed to create document: ' . $e->getMessage();

            \Log::error('Exception during document creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => null
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage)->withInput();
        }
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy($id)
    {
        try {
            // Log request info
            \Log::info('Deleting document with ID:', [
                'document_id' => $id,
                'request_url' => request()->fullUrl(),
                'ajax' => request()->ajax() ? 'Yes' : 'No'
            ]);

            // Make API request to delete the document
            $result = $this->apiService->request('DELETE', "/asset-documents/documents/{$id}");

            // Log API response for debugging
            \Log::info('API response for document deletion:', [
                'api_response_success' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'document_id' => $id
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during document deletion:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['error' => $result['message'] ?? 'Authentication failed'], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to delete document';

                \Log::warning('Error during document deletion:', [
                    'success' => $result['success'] ?? false,
                    'message' => $errorMessage
                ]);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['error' => $errorMessage], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // For AJAX requests, return JSON response with the expected format
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Document deleted successfully'
                ]);
            }

            // Redirect with success message for non-AJAX requests
            return redirect()->route('asset-documents')->with('success', 'Document deleted successfully');
        } catch (\Exception $e) {
            $errorMessage = 'Failed to delete document: ' . $e->getMessage();

            \Log::error('Exception during document deletion:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'document_id' => $id
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Update the specified document in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate the request
            $request->validate([
                'document_title' => 'required|string|max:255',
                'notes' => 'nullable|string',
                'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
                'asset_ids' => 'nullable|array',
                'asset_ids.*' => 'nullable|integer|exists:assets,asset_id',
            ]);

            // Log request info
            \Log::info('Updating document with ID:', [
                'document_id' => $id,
                'document_title' => $request->input('document_title'),
                'has_file' => $request->hasFile('file') ? 'Yes' : 'No',
                'request_url' => $request->fullUrl()
            ]);

            // Prepare data for API request
            $data = [
                'document_title' => $request->input('document_title'),
                'notes' => $request->input('notes') ?? '',
            ];

            // Add asset_ids if provided
            if ($request->has('asset_ids')) {
                $data['asset_ids'] = $request->input('asset_ids');
            }

            // Create multipart request for file upload
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

            // Add file if provided
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $multipart[] = [
                    'name' => 'file',
                    'contents' => fopen($file->getPathname(), 'r'),
                    'filename' => $file->getClientOriginalName()
                ];
            }

            // Make API request to update document
            $result = $this->apiService->request('PUT', "/asset-documents/documents/{$id}", [
                'multipart' => $multipart
            ]);

            // Log API response for debugging
            \Log::info('API response for document update:', [
                'api_response_success' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'document_id' => $id
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during document update:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['error' => $result['message'] ?? 'Authentication failed'], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to update document';

                \Log::warning('Error during document update:', [
                    'success' => $result['success'] ?? false,
                    'message' => $errorMessage
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['error' => $errorMessage], 400);
                }

                return redirect()->back()->with('error', $errorMessage)->withInput();
            }

            $document = $result['data'] ?? null;

            // For AJAX requests, return JSON response with the expected format
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Document updated successfully',
                    'data' => $document
                ]);
            }

            // Redirect with success message for form submissions
            return redirect()->route('asset-documents')->with('success', 'Document updated successfully');
        } catch (\Exception $e) {
            $errorMessage = 'Failed to update document: ' . $e->getMessage();

            \Log::error('Exception during document update:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'document_id' => $id
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => null
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage)->withInput();
        }
    }

    /**
     * Assign a document to multiple assets.
     */
    public function assignToAssets(Request $request, $id)
    {
        try {
            // Validate the request
            $request->validate([
                'asset_ids' => 'required|array',
                'asset_ids.*' => 'required|integer',
            ]);

            // Log request info
            \Log::info('Assigning document to assets:', [
                'document_id' => $id,
                'asset_ids' => $request->input('asset_ids'),
                'request_url' => $request->fullUrl()
            ]);

            // Prepare request data
            $data = [
                'asset_ids' => $request->input('asset_ids')
            ];

            // Make API request to assign document to assets
            $result = $this->apiService->request('POST', "/asset-documents/documents/{$id}/assign", [
                'json' => $data
            ]);

            // Log API response for debugging
            \Log::info('API response for document assignment:', [
                'api_response_success' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'document_id' => $id
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during document assignment:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => $result['message'] ?? 'Authentication failed'], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to assign document to assets';

                \Log::warning('Error during document assignment:', [
                    'success' => $result['success'] ?? false,
                    'message' => $errorMessage
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => $errorMessage], 400);
            }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Check if we have a redirect URL in the query parameters
            $redirectUrl = $request->query('redirect');

            // For AJAX/JSON requests
            if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Document assigned to assets successfully',
                'data' => $result['data'] ?? []
            ]);
            }

            // For non-AJAX requests, redirect with success message
            if ($redirectUrl) {
                return redirect($redirectUrl)->with('success', 'Document assigned to assets successfully');
            }

            return redirect()->back()->with('success', 'Document assigned to assets successfully');
        } catch (\Exception $e) {
            $errorMessage = 'Failed to assign document to assets: ' . $e->getMessage();

            \Log::error('Exception during document assignment:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'document_id' => $id
            ]);

            if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'data' => null
            ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Remove association between a document and an asset.
     */
    public function unlinkFromAsset($assetId, $documentId)
    {
        try {
            // Log request info
            \Log::info('Unlinking document from asset:', [
                'asset_id' => $assetId,
                'document_id' => $documentId,
                'request_url' => request()->fullUrl()
            ]);

            // Make API request to unlink document from asset
            $result = $this->apiService->request('DELETE', "/asset-documents/asset/{$assetId}/documents/{$documentId}");

            // Log API response for debugging
            \Log::info('API response for document unlinking:', [
                'api_response_success' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'asset_id' => $assetId,
                'document_id' => $documentId
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during document unlinking:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                if (request()->ajax()) {
                return response()->json(['error' => $result['message'] ?? 'Authentication failed'], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to remove document from asset';

                \Log::warning('Error during document unlinking:', [
                    'success' => $result['success'] ?? false,
                    'message' => $errorMessage
                ]);

                if (request()->ajax()) {
                return response()->json(['error' => $errorMessage], 400);
            }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Check if we have a redirect URL in the query parameters
            $redirectUrl = request()->query('redirect');

            // Return JSON response for AJAX requests
            if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Document removed from asset successfully',
                'data' => $result['data'] ?? []
            ]);
            }

            // For non-AJAX requests, redirect with success message
            if ($redirectUrl) {
                return redirect($redirectUrl)->with('success', 'Document removed from asset successfully');
            }

            return redirect()->back()->with('success', 'Document removed from asset successfully');
        } catch (\Exception $e) {
            $errorMessage = 'Failed to remove document from asset: ' . $e->getMessage();

            \Log::error('Exception during document unlinking:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $assetId,
                'document_id' => $documentId
            ]);

            if (request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'data' => null
            ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Get all documents related to a specific asset.
     */
    public function getAssetDocuments($assetId)
    {
        try {
            // Log request info
            \Log::info('Fetching all documents for asset:', [
                'asset_id' => $assetId,
                'request_url' => request()->fullUrl()
            ]);

            // Make API request to get all documents for the asset
            $result = $this->apiService->request('GET', "/asset-documents/asset/{$assetId}/all-documents");

            // Log API response for debugging
            \Log::info('API response for asset documents retrieval:', [
                'api_response_success' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'asset_id' => $assetId
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during asset documents retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['error' => $result['message'] ?? 'Authentication failed'], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to retrieve asset documents';

                \Log::warning('Error during asset documents retrieval:', [
                    'success' => $result['success'] ?? false,
                    'message' => $errorMessage
                ]);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['error' => $errorMessage], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Return JSON response with all document types
            return response()->json([
                'success' => true,
                'message' => 'All related documents retrieved successfully',
                'data' => [
                    'documents' => $result['data']['documents'] ?? [],
                    'calibrationDocuments' => $result['data']['calibrationDocuments'] ?? [],
                    'maintenanceDocuments' => $result['data']['maintenanceDocuments'] ?? []
                ]
            ]);
        } catch (\Exception $e) {
            $errorMessage = 'Failed to retrieve asset documents: ' . $e->getMessage();

            \Log::error('Exception during asset documents retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $assetId
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => null
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Create a document directly associated with a specific asset.
     */
    public function createAssetDocument(Request $request, $assetId)
    {
        try {
            // Validate the request
            $request->validate([
                'document_title' => 'required|string|max:255',
                'notes' => 'nullable|string',
                'document' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
            ]);

            // Log request info
            \Log::info('Creating new document for asset:', [
                'asset_id' => $assetId,
                'document_title' => $request->input('document_title'),
                'has_file' => $request->hasFile('document') ? 'Yes' : 'No',
                'request_url' => $request->fullUrl()
            ]);

            // Prepare data for API request
            $data = [
                'document_title' => $request->input('document_title'),
                'notes' => $request->input('notes') ?? '',
            ];

            // Create multipart request for file upload
            $multipart = [];
            foreach ($data as $key => $value) {
                $multipart[] = [
                    'name' => $key,
                    'contents' => $value
                ];
            }

            // Add file (required for this endpoint)
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $multipart[] = [
                    'name' => 'file',
                    'contents' => fopen($file->getPathname(), 'r'),
                    'filename' => $file->getClientOriginalName()
                ];
            }

            // Make API request to create document directly associated with the asset
            $result = $this->apiService->request('POST', "/asset-documents/asset/{$assetId}/documents", [
                'multipart' => $multipart
            ]);

            // Log API response for debugging
            \Log::info('API response for asset document creation:', [
                'api_response_success' => $result['success'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'asset_id' => $assetId
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during asset document creation:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                    return response()->json(['error' => $result['message'] ?? 'Authentication failed'], 401);
                }

                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to create document for asset';

                \Log::warning('Error during asset document creation:', [
                    'success' => $result['success'] ?? false,
                    'message' => $errorMessage
                ]);

                if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage)->withInput();
            }

            // For AJAX requests, return JSON response
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Document created and assigned to asset successfully',
                    'data' => $result['data'] ?? null
                ]);
            }

            // Check if we have a redirect URL in the query parameters
            $redirectUrl = $request->query('redirect');
            if ($redirectUrl) {
                return redirect($redirectUrl)->with('success', 'Document created and assigned to asset successfully');
            }

            // Redirect with success message
            return redirect()->back()->with('success', 'Document created and assigned to asset successfully');
        } catch (\Exception $e) {
            $errorMessage = 'Failed to create document for asset: ' . $e->getMessage();

            \Log::error('Exception during asset document creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $assetId
            ]);

            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'data' => null
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage)->withInput();
        }
    }
}
