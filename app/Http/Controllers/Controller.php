<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Services\ApiService;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Handle API authentication errors
     */
    protected function handleAuthError($result, Request $request)
    {
        if (isset($result['errors']) && is_string($result['errors']) &&
            in_array($result['errors'], ['auth_failed', 'session_expired'])) {

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Autentikasi gagal'
                ], 401);
            }

            return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Autentikasi gagal');
        }

        return null;
    }

    /**
     * Handle API errors
     */
    protected function handleApiError($result, Request $request, $viewName, $defaultErrorMessage = 'Gagal mengambil data')
    {
        if (!isset($result['success']) || $result['success'] !== true) {
            $errorData = $result['errors'] ?? $defaultErrorMessage;

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

            return view($viewName, [
                'error' => $errorMessage
            ]);
        }

        return null;
    }

    /**
     * Format pagination data from API response
     */
    protected function formatPagination($pagination)
    {
        if (!$pagination) return null;

        return [
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

    /**
     * Apply sorting options to query parameters
     */
    protected function applySorting($sortOrder, $defaultSortBy = 'created_at', $customSortMappings = [])
    {
        $queryParams = [];

        if (!empty($sortOrder)) {
            switch ($sortOrder) {
                case 'newest':
                    $queryParams['sort_by'] = $defaultSortBy;
                    $queryParams['sort_order'] = 'desc';
                    break;
                case 'oldest':
                    $queryParams['sort_by'] = $defaultSortBy;
                    $queryParams['sort_order'] = 'asc';
                    break;
                default:
                    // Check if we have a custom mapping for this sort option
                    if (isset($customSortMappings[$sortOrder])) {
                        $mapping = $customSortMappings[$sortOrder];
                        if (isset($mapping['sort_by'])) {
                            $queryParams['sort_by'] = $mapping['sort_by'];
                        }
                        if (isset($mapping['sort_order'])) {
                            $queryParams['sort_order'] = $mapping['sort_order'];
                        }
                        if (isset($mapping['sort'])) {
                            $queryParams['sort'] = $mapping['sort'];
                        }
                    } else {
                        // Default sort
                        $queryParams['sort_by'] = $defaultSortBy;
                        $queryParams['sort_order'] = 'desc';
                    }
            }
        } else {
            // Default sort
            $queryParams['sort_by'] = $defaultSortBy;
            $queryParams['sort_order'] = 'desc';
        }

        return $queryParams;
    }

    /**
     * Handle exceptions in controllers
     */
    protected function handleException(\Exception $e, Request $request, $viewName, $viewData = [])
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'errors' => 'Gagal memproses permintaan: ' . $e->getMessage()
            ], 500);
        }

        return view($viewName, array_merge([
            'error' => 'Gagal memproses permintaan: ' . $e->getMessage()
        ], $viewData));
    }
}
