<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class AssetDocumentController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Get documents for a specific asset
     *
     * @param int $id The asset ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssetDocuments($id)
    {
        try {
            // Log request info
            \Log::info('Fetching asset documents for asset ID:', [
                'asset_id' => $id,
                'request_url' => request()->fullUrl()
            ]);

            // Fetch the documents for the given asset ID
            $result = $this->apiService->request('GET', "/asset-documents/asset/{$id}");

            // Log API response for debugging
            \Log::info('API response for asset documents:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'asset_id' => $id
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during asset documents retrieval:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'status' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check for API errors
            if (!isset($result['status']) || $result['status'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to retrieve asset documents';

                \Log::warning('Error during asset documents retrieval:', [
                    'status' => $result['status'] ?? false,
                    'message' => $errorMessage
                ]);

                return response()->json([
                    'status' => false,
                    'message' => $errorMessage
                ], 400);
            }

            // Return the documents data as JSON
            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Exception during asset documents retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'asset_id' => $id
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve asset documents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created document in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Log request info
            \Log::info('Document upload request:', [
                'has_file' => $request->hasFile('document'),
                'asset_id' => $request->asset_id,
                'title' => $request->document_title
            ]);

            // Validate request
            $validated = $request->validate([
                'asset_id' => 'required',
                'document_title' => 'required|string|max:255',
                'notes' => 'nullable|string',
                'document' => 'required|file|max:10240' // 10MB max
            ]);

            if (!$request->hasFile('document') || !$request->file('document')->isValid()) {
                \Log::error('Invalid document file');
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid document file'
                ], 400);
            }

            // Get the file
            $file = $request->file('document');

            // Log file info
            \Log::info('File details:', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize()
            ]);

            // Prepare multipart data
            $multipartData = [
                [
                    'name' => 'asset_id',
                    'contents' => $request->asset_id
                ],
                [
                    'name' => 'document_title',
                    'contents' => $request->document_title
                ],
                [
                    'name' => 'notes',
                    'contents' => $request->notes ?? ''
                ],
                [
                    'name' => 'document',
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                    'headers' => [
                        'Content-Type' => $file->getMimeType()
                    ]
                ]
            ];

            // Send to API
            $result = $this->apiService->request('POST', '/asset-documents', ['multipart' => $multipartData]);

            // Log API response
            \Log::info('API response:', $result);

            if (!isset($result['status']) || $result['status'] !== true) {
                throw new \Exception($result['message'] ?? 'Failed to upload document');
            }

            return response()->json([
                'status' => true,
                'message' => 'Document uploaded successfully',
                'data' => $result['data'] ?? null
            ]);

        } catch (\Exception $e) {
            \Log::error('Document upload error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to upload document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an asset document
     *
     * @param int $id The document ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Log request info
            \Log::info('Deleting asset document:', [
                'document_id' => $id,
                'request_url' => request()->fullUrl()
            ]);

            // Send delete request to the API
            $result = $this->apiService->request('DELETE', "/asset-documents/{$id}");

            // Log API response for debugging
            \Log::info('API response for asset document deletion:', [
                'api_response_status' => $result['status'] ?? null,
                'api_response_message' => $result['message'] ?? null,
                'document_id' => $id
            ]);

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during asset document deletion:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'status' => false,
                    'message' => $result['message'] ?? 'Authentication failed'
                ], 401);
            }

            // Check for API errors
            if (!isset($result['status']) || $result['status'] !== true) {
                $errorMessage = $result['message'] ?? 'Failed to delete asset document';

                \Log::warning('Error during asset document deletion:', [
                    'status' => $result['status'] ?? false,
                    'message' => $errorMessage
                ]);

                return response()->json([
                    'status' => false,
                    'message' => $errorMessage
                ], 400);
            }

            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'Asset document deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during asset document deletion:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'document_id' => $id
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete asset document: ' . $e->getMessage()
            ], 500);
        }
    }
}
