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
     * Display the buildings page.
     */
    public function index(Request $request)
    {
        try {
            // Fetch buildings
            $buildingPage = $request->input('building_page', 1);
            $buildingLimit = $request->input('building_limit', 10);

            $buildingResult = $this->apiService->request('GET', '/buildings', [
                'query' => [
                    'page' => $buildingPage,
                    'limit' => $buildingLimit,
                    'sort_by' => 'building_id',
                    'sort_order' => 'asc'
                ]
            ]);

            // Check if we got an error response from the ApiService
            if (!isset($buildingResult['success']) || $buildingResult['success'] !== true) {
                $errorData = $buildingResult['errors'] ?? 'Failed to fetch data';

                // Check for authentication errors
                if (is_string($errorData) && in_array($errorData, ['auth_failed', 'session_expired'])) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'errors' => ['authentication' => 'Authentication failed']
                        ], status: 401);
                    }
                    return redirect()->route('login')->with('error', 'Authentication failed');
                }

                \Log::warning('Error during data retrieval:', [
                    'success' => $buildingResult['success'] ?? false,
                    'errors' => $errorData
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], status: 400);
                }

                // Format error message for view
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

                return view('Building', [
                    'buildings' => [],
                    'error' => $errorMessage
                ]);
            }

            $buildings = $buildingResult['data'] ?? [];

            // Format pagination for buildings
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
                    'next_page_url' => $pagination['has_next'] ? url()->current() . '?building_page=' . ($pagination['current_page'] + 1) : null,
                    'prev_page_url' => $pagination['has_prev'] ? url()->current() . '?building_page=' . ($pagination['current_page'] - 1) : null,
                ];
            }

            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'buildings' => $buildings,
                    'buildingPagination' => $buildingPagination
                ]);
            }

            return view('Building', [
                'buildings' => $buildingResult['data'] ?? [],
                'buildingPagination' => $buildingPagination
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal mengambil data gedung', [
                'error' => $e->getMessage()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data gedung: ' . $e->getMessage()
                ], status: 500);
            }

            return view('Building', [
                'buildings' => [],
                'error' => 'Gagal mengambil data gedung: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created building.
     */
    public function store(Request $request)
    {
        try {
            // Log the request data
            \Log::info('Attempting to create building with data:', [
                'request_data' => $request->all()
            ]);

            $result = $this->apiService->request('POST', '/buildings', [
                'json' => [
                    'building_name' => $request->input('building_name'),
                    'address' => $request->input('address')
                ]
            ]);

            // Log the API response
            \Log::info('API response for building creation:', [
                'api_response' => $result
            ]);

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during building creation:', [
                    'error' => $result['errors']
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], status: 401);
                }

                return redirect()->route('login')->with('error', 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal membuat gedung';

                \Log::warning('Error during building creation:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], status: 400);
                }

                // Format error message
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

            // Successfully created
            \Log::info('Building created successfully');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Gedung berhasil dibuat',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('buildings')
                ->with('success', 'Gedung berhasil dibuat');
        } catch (\Exception $e) {
            \Log::error('Exception during building creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'building_data' => $request->except('_token')
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal membuat gedung: ' . $e->getMessage()]
                ], status: 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat gedung: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified building.
     */
    public function update(Request $request, $id)
    {
        try {
            $result = $this->apiService->request('PUT', "/buildings/{$id}", [
                'json' => [
                    'building_id' => $id,
                    'building_name' => $request->input('building_name'),
                    'address' => $request->input('address'),
                ]
            ]);

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], status: 401);
                }

                return redirect()->route('login')->with('error', 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal mengubah gedung';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], status: 400);
                }

                // Format error message
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

            // Successfully updated
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Gedung berhasil diubah',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('buildings')
                ->with('success', 'Gedung berhasil diubah');
        } catch (\Exception $e) {
            \Log::error('Failed to update building', [
                'error' => $e->getMessage(),
                'building_id' => $id,
                'building_data' => $request->except(['_token', '_method'])
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengubah gedung: ' . $e->getMessage()]
                ], status: 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengubah gedung: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified building.
     */
    public function destroy($id)
    {
        try {
            $result = $this->apiService->request('DELETE', "/buildings/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], status: 401);
                }

                return redirect()->route('login')->with('error', 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal menghapus gedung';

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData
                    ], status: 400);
                }

                // Format error message
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

            // Successfully deleted
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Gedung berhasil dihapus'
                ]);
            }

            return redirect()->route('buildings')
                ->with('success', 'Gedung berhasil dihapus');
        } catch (\Exception $e) {
            \Log::error('Gagal menghapus gedung', [
                'error' => $e->getMessage(),
                'building_id' => $id
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal menghapus gedung: ' . $e->getMessage()]
                ], status: 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus gedung: ' . $e->getMessage());
        }
    }

    /**
     * Get building data for dropdown.
     * Returns JSON data suitable for AJAX requests.
     */
    public function getData(Request $request)
    {
        try {
            // Extract search parameter
            $search = $request->input('search', '');

            // Build query parameters
            $queryParams = [
                'limit' => 50, // Limit for dropdown
                'sort_by' => 'building_name',
                'sort_order' => 'asc'
            ];

            // Add search parameter if provided
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Fetch buildings from API
            $result = $this->apiService->request('GET', '/buildings', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Failed to fetch buildings';
                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? implode(', ', $errorData) : $errorData
                ], 400);
            }

            // Return success response
            return response()->json([
                'success' => true,
                'data' => $result['data'] ?? []
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during buildings data retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => ['exception' => 'Failed to fetch buildings: ' . $e->getMessage()]
            ], 500);
        }
    }
}
