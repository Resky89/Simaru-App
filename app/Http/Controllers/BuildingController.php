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
            if (isset($buildingResult['error'])) {
                $error = $buildingResult['error'];

                if (strpos($error, 'login') !== false) {
                    return redirect()->route('login')->with('error', $error);
                }

                throw new \Exception($error);
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

            return view('Building', [
                'buildings' => $buildingResult['data'] ?? [],
                'buildingPagination' => $buildingPagination
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal mengambil data gedung', [
                'error' => $e->getMessage()
            ]);

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
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during building creation:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (isset($result['errors']) || (isset($result['success']) && $result['success'] === false)) {
                \Log::warning('Error during building creation:', [
                    'error' => $result['errors'] ?? null,
                    'success' => $result['success'] ?? null,
                    'message' => $result['message'] ?? 'Failed to create building'
                ]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Gagal membuat gedung');
            }

            // Successfully created
            \Log::info('Building created successfully');
            return redirect()->route('buildings')
                ->with('success', 'Gedung berhasil dibuat');
        } catch (\Exception $e) {
            \Log::error('Exception during building creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'building_data' => $request->except('_token')
            ]);

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
            if (isset($result['errors']) && in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('errors', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (isset($result['errors']) || (isset($result['success']) && $result['success'] === false)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Gagal mengubah gedung');
            }

            // Successfully updated
            return redirect()->route('buildings')
                ->with('success', 'Gedung berhasil diubah');
        } catch (\Exception $e) {
            \Log::error('Failed to update building', [
                'error' => $e->getMessage(),
                'building_id' => $id,
                'building_data' => $request->except(['_token', '_method'])
            ]);

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
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (isset($result['error']) || (isset($result['success']) && $result['success'] === false)) {
                return redirect()->back()
                    ->with('error', $result['message'] ?? 'Gagal menghapus gedung');
            }

            // Successfully deleted
            return redirect()->route('buildings')
                ->with('success', 'Gedung berhasil dihapus');
        } catch (\Exception $e) {
            \Log::error('Gagal menghapus gedung', [
                'error' => $e->getMessage(),
                'building_id' => $id
            ]);

            return redirect()->back()
                ->with('error', 'Gagal menghapus gedung: ' . $e->getMessage());
        }
    }
}
