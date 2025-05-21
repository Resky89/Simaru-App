<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display a listing of notifications.
     * Can return either HTML view or JSON depending on the request.
     */
    public function index(Request $request)
    {
        try {
            // Get query parameters
            $page = $request->query('page', 1);
            $limit = $request->query('limit', 10);
            $search = $request->query('search', '');
            $sort = $request->query('sort', '');

            // Build query parameters
            $queryParams = [
                'page' => $page,
                'limit' => $limit
            ];

            // Search parameter
            if (!empty($search)) {
                $queryParams['search'] = $search;
            }

            // Sorting parameters
            if (!empty($sort)) {
                switch ($sort) {
                    case 'date_asc':
                        $queryParams['sort_by'] = 'created_at';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'date_desc':
                        $queryParams['sort_by'] = 'created_at';
                        $queryParams['sort_order'] = 'desc';
                        break;
                    case 'title_asc':
                        $queryParams['sort_by'] = 'title';
                        $queryParams['sort_order'] = 'asc';
                        break;
                    case 'title_desc':
                        $queryParams['sort_by'] = 'title';
                        $queryParams['sort_order'] = 'desc';
                        break;
                }
            }

            // Get notifications from API
            $result = $this->apiService->request('GET', '/notifications', ['query' => $queryParams]);

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                Log::warning('Authentication error while fetching notifications:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to fetch notifications';

                Log::warning('Error while fetching notifications:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

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

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return view('Notifications', [
                    'notifications' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => $limit ?? 10,
                        'total' => 0,
                        'from' => 0,
                        'to' => 0,
                        'next_page_url' => null,
                        'prev_page_url' => null
                    ],
                    'error' => $errorMessage
                ]);
            }

            // Format data from the API result
            $notifications = $result['data'] ?? [];
            $message = $result['message'] ?? 'Notifications fetched successfully';

            // If this is an AJAX or JSON request, return the notifications as JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $notifications,
                    'pagination' => $result['pagination'] ?? null
                ]);
            }

            // Format pagination for view
            $pagination = null;
            if (isset($result['pagination'])) {
                $paginationData = $result['pagination'];
                $pagination = [
                    'current_page' => $paginationData['current_page'] ?? 1,
                    'last_page' => $paginationData['total_pages'] ?? 1,
                    'per_page' => $paginationData['limit'] ?? 10,
                    'total' => $paginationData['total_items'] ?? count($notifications),
                    'from' => (($paginationData['current_page'] ?? 1) - 1) * ($paginationData['limit'] ?? 10) + 1,
                    'to' => min(($paginationData['current_page'] ?? 1) * ($paginationData['limit'] ?? 10), $paginationData['total_items'] ?? count($notifications)),
                    'next_page_url' => isset($paginationData['has_next']) && $paginationData['has_next'] ?
                        request()->fullUrlWithQuery(['page' => ($paginationData['current_page'] + 1)]) : null,
                    'prev_page_url' => isset($paginationData['has_prev']) && $paginationData['has_prev'] ?
                        request()->fullUrlWithQuery(['page' => ($paginationData['current_page'] - 1)]) : null,
                ];
            } else {
                $pagination = [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $limit,
                    'total' => count($notifications),
                    'from' => 1,
                    'to' => count($notifications),
                    'next_page_url' => null,
                    'prev_page_url' => null
                ];
            }

            return view('Notifications', compact('notifications', 'pagination'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch notifications:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Failed to load notifications: ' . $e->getMessage()
                ], 500);
            }

            return view('Notifications', [
                'notifications' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $limit ?? 10,
                    'total' => 0,
                    'from' => 0,
                    'to' => 0,
                    'next_page_url' => null,
                    'prev_page_url' => null
                ],
                'error' => 'Failed to load notifications: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead($id, Request $request)
    {
        try {
            // Call API to mark notification as read
            $result = $this->apiService->request('POST', "/notifications/{$id}/read");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                Log::warning('Authentication error while marking notification as read:', [
                    'errors' => $result['errors'] ?? 'Authentication failed',
                    'notification_id' => $id
                ]);

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => 'Authentication failed'
                    ], 401);
                }

                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to mark notification as read';

                Log::warning('Error while marking notification as read:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData,
                    'notification_id' => $id
                ]);

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

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $errorMessage
                    ], 400);
                }

                return redirect()->back()->with('error', $errorMessage);
            }

            // Successfully marked as read
            $message = $result['message'] ?? 'Notification marked as read successfully';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $result['data'] ?? null
                ]);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'notification_id' => $id
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => 'Failed to mark notification as read: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to mark notification as read: ' . $e->getMessage());
        }
    }
}
