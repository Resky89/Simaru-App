<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class RoomController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display the rooms page with available buildings for dropdown.
     */
    public function index(Request $request)
    {
        try {
            // Fetch buildings for dropdown
            $buildingResult = $this->apiService->request('GET', '/buildings', [
                'query' => [
                    'limit' => 1000, // Get all buildings for dropdown
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
                $building = collect($buildings)->first(function ($building) use ($buildingId) {
                    return $building['building_id'] == $buildingId;
                });
                $room['building_name'] = $building ? $building['building_name'] : 'Unknown';
            }

            // Format pagination for rooms
            $roomPagination = null;
            if (isset($roomResult['pagination'])) {
                $pagination = $roomResult['pagination'];
                $roomPagination = [
                    'current_page' => $pagination['current_page'] ?? 1,
                    'last_page' => ceil(($pagination['total_items'] ?? 0) / ($pagination['limit'] ?? 10)),
                    'from' => (($pagination['current_page'] ?? 1) - 1) * ($pagination['limit'] ?? 10) + 1,
                    'to' => min(($pagination['current_page'] ?? 1) * ($pagination['limit'] ?? 10), $pagination['total_items'] ?? 0),
                    'total' => $pagination['total_items'] ?? 0,
                    'per_page' => $pagination['limit'] ?? 10,
                    'next_page_url' => $pagination['has_next'] ? url()->current() . '?room_page=' . ($pagination['current_page'] + 1) : null,
                    'prev_page_url' => $pagination['has_prev'] ? url()->current() . '?room_page=' . ($pagination['current_page'] - 1) : null,
                ];
            }

            return view('Room', [
                'buildings' => $buildings,
                'rooms' => $rooms,
                'roomPagination' => $roomPagination
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal mengambil data ruangan', [
                'error' => $e->getMessage()
            ]);

            return view('Room', [
                'buildings' => [],
                'rooms' => [],
                'error' => 'Gagal mengambil data ruangan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created room.
     */
    public function store(Request $request)
    {
        try {
            // Log the request data
            \Log::info('Attempting to create room with data:', [
                'request_data' => $request->all()
            ]);

            // Cast building_id to integer
            $buildingId = (int) $request->input('building_id');

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
            return redirect()->route('rooms')
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
    public function update(Request $request, $id)
    {
        try {
            // Log the request data
            \Log::info('Attempting to update room with data:', [
                'room_id' => $id,
                'request_data' => $request->all()
            ]);

            // Cast building_id to integer
            $buildingId = (int) $request->input('building_id');

            \Log::info('Building ID cast to integer:', ['building_id' => $buildingId]);

            $payload = [
                'room_id' => (int) $id,
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
            return redirect()->route('rooms')
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
    public function destroy($id)
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
            return redirect()->route('rooms')
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
