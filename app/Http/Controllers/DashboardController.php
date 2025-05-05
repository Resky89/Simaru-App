<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class DashboardController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index()
    {
        try {
            // Log request info
            \Log::info('Fetching dashboard data for view');

            // Fetch dashboard summary from API
            $result = $this->apiService->request('GET', '/dashboard/summary');

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during dashboard data retrieval', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return redirect()->route('login')
                    ->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed. Please login again.');
            }

            // Fetch depreciation data from API
            $depreciationResult = $this->getDepreciationData();

            // Prepare dashboard data
            $dashboardData = [
                'total_users' => 0,
                'total_assets' => 0,
                'total_acquisition_cost' => 0,
                'total_book_value' => 0,
                'total_depreciation' => 0,
                'as_of_date' => date('Y-m-d'),
                'assets_by_type' => [
                    'medical' => 0,
                    'non_medical' => 0
                ],
                'assets_by_status' => [
                    'available' => 0,
                    'check out' => 0,
                    'dispose' => 0,
                    'lost' => 0,
                    'under repair' => 0
                ],
                'assets_by_condition' => [
                    'good' => 0,
                    'slighly damage' => 0,
                    'high damage' => 0
                ],
                'assets_by_subcategory' => [],
                'assets_by_location' => [],
                'upcoming_calibrations' => [
                    'medical' => [],
                    'non_medical' => [],
                    'all' => []
                ]
            ];

            // Update with API data if available
            if (isset($result['data']) && is_array($result['data'])) {
                $dashboardData = array_merge($dashboardData, $result['data']);
            }

            // Merge depreciation data if available
            if (isset($depreciationResult['success']) && $depreciationResult['success'] === true && isset($depreciationResult['data'])) {
                $dashboardData['total_acquisition_cost'] = $depreciationResult['data']['total_acquisition_cost'] ?? 0;
                $dashboardData['total_book_value'] = $depreciationResult['data']['total_book_value'] ?? 0;
                $dashboardData['total_depreciation'] = $depreciationResult['data']['total_depreciation'] ?? 0;
                $dashboardData['as_of_date'] = $depreciationResult['data']['as_of_date'] ?? date('Y-m-d');
            }

            return view('dashboard', ['dashboardData' => $dashboardData]);

        } catch (\Exception $e) {
            \Log::error('Exception during dashboard data retrieval', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return view with default empty data and error message
            return view('dashboard', [
                'dashboardData' => [
                    'total_users' => 0,
                    'total_assets' => 0,
                    'total_acquisition_cost' => 0,
                    'total_book_value' => 0,
                    'total_depreciation' => 0,
                    'as_of_date' => date('Y-m-d'),
                    'assets_by_type' => [
                        'medical' => 0,
                        'non_medical' => 0
                    ],
                    'assets_by_status' => [
                        'available' => 0,
                        'check out' => 0,
                        'dispose' => 0,
                        'lost' => 0,
                        'under repair' => 0
                    ],
                    'assets_by_condition' => [
                        'good' => 0,
                        'slighly damage' => 0,
                        'high damage' => 0
                    ],
                    'assets_by_subcategory' => [],
                    'assets_by_location' => [],
                    'upcoming_calibrations' => [
                        'medical' => [],
                        'non_medical' => [],
                        'all' => []
                    ]
                ],
                'error' => 'Failed to load dashboard data: ' . $e->getMessage()
            ])->with('error', 'Failed to load dashboard data: ' . $e->getMessage());
        }
    }

    /**
     * Get total depreciation data
     *
     * @return array
     */
    private function getDepreciationData()
    {
        try {
            // Log request info
            \Log::info('Fetching depreciation data');

            // Fetch depreciation data from API
            $result = $this->apiService->request('GET', '/depreciations/total-value');

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during depreciation data retrieval', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return [
                    'success' => false,
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ];
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve depreciation data';

                \Log::warning('Error during depreciation data retrieval', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                return [
                    'success' => false,
                    'errors' => $errorData
                ];
            }

            return $result;

        } catch (\Exception $e) {
            \Log::error('Exception during depreciation data retrieval', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'errors' => 'Failed to retrieve depreciation data: ' . $e->getMessage()
            ];
        }
    }

    /**
     * API endpoint for dashboard summary data (for AJAX requests)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSummary()
    {
        try {
            // Log request info
            \Log::info('Fetching dashboard summary data via API endpoint');

            // Fetch dashboard summary from API
            $result = $this->apiService->request('GET', '/dashboard/summary');

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during dashboard summary retrieval', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], 401);
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve dashboard summary';

                \Log::warning('Error during dashboard summary retrieval', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                ], 400);
            }

            // Fetch depreciation data from API
            $depreciationResult = $this->getDepreciationData();

            // Merge depreciation data with summary data if available
            if (isset($depreciationResult['success']) && $depreciationResult['success'] === true && isset($depreciationResult['data'])) {
                $result['data']['total_acquisition_cost'] = $depreciationResult['data']['total_acquisition_cost'] ?? 0;
                $result['data']['total_book_value'] = $depreciationResult['data']['total_book_value'] ?? 0;
                $result['data']['total_depreciation'] = $depreciationResult['data']['total_depreciation'] ?? 0;
                $result['data']['as_of_date'] = $depreciationResult['data']['as_of_date'] ?? date('Y-m-d');
            }

            // Return the summary data
            return response()->json([
                'success' => true,
                'data' => $result['data'] ?? [
                    'total_users' => 0,
                    'total_assets' => 0,
                    'total_acquisition_cost' => 0,
                    'total_book_value' => 0,
                    'total_depreciation' => 0,
                    'as_of_date' => date('Y-m-d'),
                    'assets_by_type' => [
                        'medical' => 0,
                        'non_medical' => 0
                    ],
                    'assets_by_status' => [
                        'available' => 0,
                        'check out' => 0,
                        'dispose' => 0,
                        'lost' => 0,
                        'under repair' => 0
                    ],
                    'assets_by_condition' => [
                        'good' => 0,
                        'slighly damage' => 0,
                        'high damage' => 0
                    ],
                    'assets_by_subcategory' => [],
                    'assets_by_location' => [],
                    'upcoming_calibrations' => [
                        'medical' => [],
                        'non_medical' => [],
                        'all' => []
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during dashboard summary retrieval', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => 'Failed to retrieve dashboard summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get depreciation data (direct endpoint)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDepreciation()
    {
        try {
            // Log request info
            \Log::info('Fetching depreciation data via direct endpoint');

            // Fetch depreciation data from API
            $result = $this->apiService->request('GET', '/depreciations/total-value');

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during depreciation data retrieval', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], 401);
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve depreciation data';

                \Log::warning('Error during depreciation data retrieval', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                ], 400);
            }

            // Return the depreciation data with consistent success key
            $result['success'] = $result['success'] ?? $result['status'] ?? true;
            unset($result['status']);

            return response()->json($result);

        } catch (\Exception $e) {
            \Log::error('Exception during depreciation data retrieval', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => 'Failed to retrieve depreciation data: ' . $e->getMessage()
            ], 500);
        }
    }
}
