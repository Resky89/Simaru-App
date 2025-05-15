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
            $search = $request->input('search', '');
            $sortOrder = $request->input('sort', '');

            // Build query parameters
            $queryParams = [
                'page' => $roomPage,
                'limit' => $roomLimit
            ];

            // Add search parameter if provided
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Add sorting parameters
            if (!empty($sortOrder)) {
                switch ($sortOrder) {
                    case 'name_asc':
                        $queryParams['sort_by'] = 'room_name';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'name_desc':
                        $queryParams['sort_by'] = 'room_name';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    case 'id_asc':
                        $queryParams['sort_by'] = 'room_id';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'id_desc':
                        $queryParams['sort_by'] = 'room_id';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    default:
                        $queryParams['sort_by'] = 'room_id';
                        $queryParams['sort_order'] = 'asc';
                }
            } else {
                $queryParams['sort_by'] = 'room_id';
                $queryParams['sort_order'] = 'asc';
            }

            $roomResult = $this->apiService->request('GET', '/rooms', [
                'query' => $queryParams
            ]);

            // Check for auth errors in any of the results
            if ((isset($buildingResult['errors']) && is_string($buildingResult['errors']) &&
                in_array($buildingResult['errors'], ['auth_failed', 'session_expired'])) ||
                (isset($roomResult['errors']) && is_string($roomResult['errors']) &&
                in_array($roomResult['errors'], ['auth_failed', 'session_expired']))) {

                $errorMessage = isset($buildingResult['errors']) ?
                    ($buildingResult['errors']) :
                    ($roomResult['errors']);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($errorMessage) ? $errorMessage : 'Authentication failed');
            }

            // Check if any API call was unsuccessful
            if ((isset($buildingResult['success']) && $buildingResult['success'] !== true) ||
                (isset($roomResult['success']) && $roomResult['success'] !== true) ||
                !isset($buildingResult['success']) || !isset($roomResult['success'])) {

                $errorData = isset($buildingResult['success']) && $buildingResult['success'] !== true ?
                    ($buildingResult['errors'] ?? 'Failed to fetch buildings') :
                    ($roomResult['errors'] ?? 'Failed to fetch rooms');

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], 400);
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

                return view('Room', [
                    'buildings' => [],
                    'rooms' => [],
                    'error' => $errorMessage
                ]);
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

            // For AJAX requests, return JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'rooms' => $rooms,
                    'buildings' => $buildings,
                    'roomPagination' => $roomPagination
                ]);
            }

            return view('Room', [
                'buildings' => $buildings,
                'rooms' => $rooms,
                'roomPagination' => $roomPagination
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal mengambil data ruangan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengambil data ruangan: ' . $e->getMessage()]
                ], 500);
            }

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
                'floor_number' => $request->input('floor_number')
            ];

            // Only add description to payload if it's not empty
            if ($request->filled('description')) {
                $payload['description'] = $request->input('description');
            }

            \Log::info('Sending API request with payload:', ['payload' => $payload]);

            $result = $this->apiService->request('POST', '/rooms', [
                'json' => $payload
            ]);

            // Log the API response
            \Log::info('API response for room creation:', [
                'api_response' => $result
            ]);

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during room creation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal membuat ruangan';

                \Log::warning('Error during room creation:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], 400);
                }

                // Format error message for redirect
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
            \Log::info('Ruangan berhasil dibuat');

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ruangan berhasil dibuat',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('rooms')
                ->with('success', 'Ruangan berhasil dibuat');
        } catch (\Exception $e) {
            \Log::error('Gagal membuat ruangan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'room_data' => $request->except('_token')
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal membuat ruangan: ' . $e->getMessage()]
                ], 500);
            }

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
                'floor_number' => $request->input('floor_number')
            ];

            // Only add description to payload if it's not empty
            if ($request->filled('description')) {
                $payload['description'] = $request->input('description');
            }

            \Log::info('Sending API request with payload:', ['payload' => $payload]);

            $result = $this->apiService->request('PUT', "/rooms/{$id}", [
                'json' => $payload
            ]);

            // Log the API response
            \Log::info('API response for room update:', [
                'api_response' => $result
            ]);

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during room update:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal mengubah ruangan';

                \Log::warning('Error during room update:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], 400);
                }

                // Format error message for redirect
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
                    'message' => 'Ruangan berhasil diubah',
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('rooms')
                ->with('success', 'Ruangan berhasil diubah');
        } catch (\Exception $e) {
            \Log::error('Exception during room update:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'room_id' => $id,
                'room_data' => $request->except(['_token', '_method'])
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengubah ruangan: ' . $e->getMessage()]
                ], 500);
            }

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

            // Log the API response
            \Log::info('API response for room deletion:', [
                'room_id' => $id,
                'api_response' => $result
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during room deletion:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed']
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Gagal menghapus ruangan';

                \Log::warning('Error during room deletion:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                    ], 400);
                }

                // Format error message for redirect
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
                    'message' => 'Ruangan berhasil dihapus'
                ]);
            }

            return redirect()->route('rooms')
                ->with('success', 'Ruangan berhasil dihapus');
        } catch (\Exception $e) {
            \Log::error('Failed to delete room', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'room_id' => $id
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal menghapus ruangan: ' . $e->getMessage()]
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus ruangan: ' . $e->getMessage());
        }
    }

    /**
     * Get room data for dropdown, filtered by building ID if provided.
     * Returns JSON data suitable for AJAX requests.
     */
    public function getData(Request $request)
    {
        try {
            // Extract parameters
            $search = $request->input('search', '');
            $buildingId = $request->input('building_id');

            // Build query parameters
            $queryParams = [
                'limit' => 50, // Limit for dropdown
                'sort_by' => 'room_name',
                'sort_order' => 'asc'
            ];

            // Add search parameter if provided
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Add building_id filter if provided
            if (!empty($buildingId)) {
                $queryParams['building_id'] = $buildingId;
            }

            // Fetch rooms from API
            $result = $this->apiService->request('GET', '/rooms', [
                'query' => $queryParams
            ]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication failed'
                ], 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Failed to fetch rooms';
                return response()->json([
                    'success' => false,
                    'message' => is_array($errorData) ? implode(', ', $errorData) : $errorData
                ], 400);
            }

            // Return success response
            return response()->json([
                'success' => true,
                'data' => $result['data'] ?? []
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during rooms data retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch rooms: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific room by ID.
     */
    public function getById(Request $request, $id)
    {
        try {
            // Fetch room by ID
            $result = $this->apiService->request('GET', "/rooms/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication failed'
                ], 401);
            }

            // Check for API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Failed to fetch room data';
                return response()->json([
                    'success' => false,
                    'message' => is_array($errorData) ? implode(', ', $errorData) : $errorData
                ], 400);
            }

            // Return success response
            return response()->json([
                'success' => true,
                'data' => $result['data'] ?? []
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during room data retrieval:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch room data: ' . $e->getMessage()
            ], 500);
        }
    }

   /**
     * Import room data from Excel file.
     */
    public function import(Request $request)
    {
        try {
            // Validate request has file
            if (!$request->hasFile('excel_file')) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['excel_file' => 'File Excel tidak ditemukan'],
                        'data' => [
                            'errors' => ['File Excel tidak ditemukan']
                        ]
                    ], status: 400);
                }

                return redirect()->back()
                    ->with('error', 'File Excel tidak ditemukan');
            }

            // Get the file from the request
            $file = $request->file('excel_file');

            // Log import attempt
            \Log::info('Attempting to import rooms from Excel file', [
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize()
            ]);

            // Send request to API with file
            $result = $this->apiService->request('POST', '/rooms/import', [
                'multipart' => [
                    [
                        'name' => 'excel_file',
                        'contents' => fopen($file->getRealPath(), 'r'),
                        'filename' => $file->getClientOriginalName()
                    ]
                ]
            ]);

            // Log the API response
            \Log::info('API response for room import:', [
                'api_response' => $result
            ]);

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during room import:', [
                    'error' => $result['errors']
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['authentication' => 'Authentication failed'],
                        'data' => [
                            'errors' => ['Authentication failed']
                        ]
                    ], status: 401);
                }

                return redirect()->route('login')->with('error', 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] === false) {
                $errorData = $result['errors'] ?? 'Gagal mengimpor data ruangan';

                \Log::warning('Error during room import:', [
                'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorData,
                        'data' => isset($result['data']) ? $result['data'] : [
                            'errors' => is_array($errorData) ? array_values($errorData) : [$errorData]
                ]
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

            // Successfully imported
            $successMessage = $result['message'] ?? 'Data ruangan berhasil diimpor';
            $totalImported = $result['data']['total'] ?? 0;
            $successCount = $result['data']['success'] ?? 0;
            $failedCount = $result['data']['failed'] ?? 0;

            \Log::info('Rooms imported successfully', [
                'total' => $totalImported,
                'success' => $successCount,
                'failed' => $failedCount
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->route('rooms')
                ->with('success', 'Data ruangan berhasil diimpor: ' .
                    $successCount . ' sukses, ' .
                    $failedCount . ' gagal');
        } catch (\Exception $e) {
            \Log::error('Exception during room import:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['exception' => 'Gagal mengimpor data ruangan: ' . $e->getMessage()],
                    'data' => [
                        'total' => 0,
                        'success' => 0,
                        'failed' => 0,
                        'errors' => [
                            'Gagal mengimpor data ruangan: ' . $e->getMessage()
                        ],
                        'created' => []
                    ]
                ], status:  500);
            }

            return redirect()->back()
                ->with('error', 'Gagal mengimpor data ruangan: ' . $e->getMessage());
        }
    }
}
