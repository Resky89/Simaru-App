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
            // Fetch the documents for the given asset ID
            $result = $this->apiService->request('GET', "/asset-documents/asset/{$id}");

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
            // Keep minimal necessary logging for debugging
            \Log::info('Document upload initiated for asset: ' . $request->asset_id);

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

            // Create multipart data with the EXACT field names expected by the API
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
                    'name' => 'document_file',
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                    'headers' => [
                        'Content-Type' => $file->getMimeType()
                    ]
                ]
            ];

            // Send to API with multipart data
            $result = $this->apiService->request('POST', '/asset-documents', ['multipart' => $multipartData]);

            // Only log errors
            if (!isset($result['status']) || $result['status'] !== true) {
                \Log::warning('API document upload failed:', $result);
            }

            // Check for success in the response (handles both 'status' and 'success' formats)
            $isSuccess = (isset($result['status']) && $result['status'] === true) ||
                        (isset($result['success']) && $result['success'] === true);

            if (!$isSuccess) {
                $errorMessage = $result['message'] ?? $result['errors'] ?? 'Failed to upload document';
                throw new \Exception($errorMessage);
            }

            \Log::info('Document uploaded successfully for asset: ' . $request->asset_id);

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
            // Send delete request to the API
            $result = $this->apiService->request('DELETE', "/asset-documents/{$id}");

            // Only log errors
            if (!isset($result['status']) || $result['status'] !== true) {
                \Log::warning('API document deletion failed:', [
                    'document_id' => $id,
                    'response' => $result
                ]);
            }

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

            \Log::info('Document deleted successfully: ' . $id);

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
