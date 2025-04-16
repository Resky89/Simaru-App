<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Services\ApiService;

class LocationController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display the location page with buildings and rooms.
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

            // Fetch rooms
            $roomPage = $request->input('room_page', 1);
            $roomLimit = $request->input('room_limit', 10);

            $roomResult = $this->apiService->request('GET', '/rooms', [
                'query' => [
                    'page' => $roomPage,
                    'limit' => $roomLimit,
                    'sort_by' => 'room_id',
                    'sort_order' => 'asc'
                ]
            ]);

            // Check if we got an error response from the ApiService
            if (isset($buildingResult['error']) || isset($roomResult['error'])) {
                $error = isset($buildingResult['error']) ? $buildingResult['error'] : $roomResult['error'];

                if (strpos($error, 'login') !== false) {
                    return redirect()->route('login')->with('error', $error);
                }

                throw new \Exception($error);
            }

            $rooms = $roomResult['data'] ?? [];
            $buildings = $buildingResult['data'] ?? [];

            foreach ($rooms as &$room) {
                $buildingId = $room['building_id'];
                $building = collect($buildings)->first(function($building) use ($buildingId) {
                    return $building['building_id'] == $buildingId;
                });
                $room['building_name'] = $building ? $building['building_name'] : 'Unknown';
            }

            return view('Location', [
                'buildings' => $buildingResult['data'] ?? [],
                'buildingPagination' => $buildingResult['pagination'] ?? null,
                'rooms' => $rooms,
                'roomPagination' => $roomResult['pagination'] ?? null
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal mengambil data lokasi', [
                'error' => $e->getMessage()
            ]);

            return view('Location', [
                'buildings' => [],
                'rooms' => [],
                'error' => 'Gagal mengambil data lokasi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created building.
     */
    public function storeBuilding(Request $request)
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
            return redirect()->route('location')
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
    public function updateBuilding(Request $request, $id)
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
            return redirect()->route('location')
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
    public function destroyBuilding($id)
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
            return redirect()->route('location')
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

    /**
     * Store a newly created room.
     */
    public function storeRoom(Request $request)
    {
        try {
            // Log the request data
            \Log::info('Attempting to create room with data:', [
                'request_data' => $request->all()
            ]);

            // Cast building_id to integer
            $buildingId = (int)$request->input('building_id');

            \Log::info('Building ID cast to integer:', ['building_id' => $buildingId]);

            $payload = [
                'room_name' => $request->input('room_name'),
                'building_id' => $buildingId,
                'floor_number' => $request->input('floor_number'),
                'description' => $request->input('description')
            ];

            \Log::info('Sending API request with payload:', ['payload' => $payload]);

            $result = $this->apiService->request('POST', '/rooms', [
                'json' => $payload
            ]);

            // Log the API response
            \Log::info('API response for room creation:', [
                'api_response' => $result
            ]);

            // Check if we got an auth error response
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during room creation:', [
                    'error' => $result['error'],
                    'message' => $result['message'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (isset($result['error']) || (isset($result['success']) && $result['success'] === false)) {
                \Log::warning('Error during room creation:', [
                    'error' => $result['error'] ?? null,
                    'success' => $result['success'] ?? null,
                    'message' => $result['message'] ?? 'Gagal membuat ruangan'
                ]);
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Gagal membuat ruangan');
            }

            // Successfully created
            \Log::info('Ruangan berhasil dibuat');
            return redirect()->route('location')
                ->with('success', 'Ruangan berhasil dibuat');
        } catch (\Exception $e) {
            \Log::error('Gagal membuat ruangan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'room_data' => $request->except('_token')
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat ruangan: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified room.
     */
    public function updateRoom(Request $request, $id)
    {
        try {
            // Log the request data
            \Log::info('Attempting to update room with data:', [
                'room_id' => $id,
                'request_data' => $request->all()
            ]);

            // Cast building_id to integer
            $buildingId = (int)$request->input('building_id');

            \Log::info('Building ID cast to integer:', ['building_id' => $buildingId]);

            $payload = [
                'room_id' => (int)$id,
                'room_name' => $request->input('room_name'),
                'building_id' => $buildingId,
                'floor_number' => $request->input('floor_number'),
                'description' => $request->input('description')
            ];

            \Log::info('Sending API request with payload:', ['payload' => $payload]);

            $result = $this->apiService->request('PUT', "/rooms/{$id}", [
                'json' => $payload
            ]);

            // Log the API response
            \Log::info('API response for room update:', [
                'api_response' => $result
            ]);

            // Check if we got an auth error response
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (isset($result['error']) || (isset($result['success']) && $result['success'] === false)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message'] ?? 'Gagal mengubah ruangan');
            }

            // Successfully updated
            return redirect()->route('location')
                ->with('success', 'Ruangan berhasil diubah');
        } catch (\Exception $e) {
            \Log::error('Exception during room update:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'room_id' => $id,
                'room_data' => $request->except(['_token', '_method'])
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengubah ruangan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified room.
     */
    public function destroyRoom($id)
    {
        try {
            $result = $this->apiService->request('DELETE', "/rooms/{$id}");

            // Check for auth errors
            if (isset($result['error']) && in_array($result['error'], ['auth_failed', 'session_expired'])) {
                return redirect()->route('login')->with('error', $result['message'] ?? 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (isset($result['error']) || (isset($result['success']) && $result['success'] === false)) {
                return redirect()->back()
                    ->with('error', $result['message'] ?? 'Gagal menghapus ruangan');
            }

            // Successfully deleted
            return redirect()->route('location')
                ->with('success', 'Ruangan berhasil dihapus');
        } catch (\Exception $e) {
            \Log::error('Failed to delete room', [
                'error' => $e->getMessage(),
                'room_id' => $id
            ]);

            return redirect()->back()
                ->with('error', 'Gagal menghapus ruangan: ' . $e->getMessage());
        }
    }
}
