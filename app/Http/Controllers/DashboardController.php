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

            return view('Dashboard', ['dashboardData' => $dashboardData]);

        } catch (\Exception $e) {
            \Log::error('Exception during dashboard data retrieval', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return view with default empty data and error message
            return view('Dashboard', [
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
     * Get calendar events data for a specific year and month
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCalendarEvents(Request $request)
    {
        try {
            // Get year and month from request (default to current year and month if not provided)
            $year = $request->query('year', date('Y'));
            $month = $request->query('month', date('m'));

            // Log request info
            \Log::info('Fetching calendar events', [
                'year' => $year,
                'month' => $month
            ]);

            // Fetch calendar data from API
            $result = $this->apiService->request('GET', "/dashboard/calendar?year={$year}&month={$month}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during calendar data retrieval', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], 401);
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve calendar data';

                \Log::warning('Error during calendar data retrieval', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                ], 400);
            }

            // Get API data
            $calendarData = $result['data'] ?? [];

            // If no events data, initialize with an empty array
            if (!isset($calendarData['events'])) {
                $calendarData['events'] = [];
            }

            // Prepare metadata if not provided by API
            if (!isset($calendarData['meta'])) {
                $events = $calendarData['events'] ?? [];
                $calendarData['meta'] = [
                    'total_events' => count($events),
                    'total_by_type' => [
                        'calibration' => count(array_filter($events, function($e) { return $e['type'] === 'calibration'; })),
                        'maintenance' => count(array_filter($events, function($e) { return $e['type'] === 'maintenance'; })),
                        'warranty' => count(array_filter($events, function($e) { return $e['type'] === 'warranty'; }))
                    ],
                    'period' => [
                        'year' => $year,
                        'month' => $month,
                        'month_name' => $this->getIndonesianMonthName($month)
                    ]
                ];
            }

            // Return the calendar data with success message
            return response()->json([
                'success' => true,
                'message' => 'Daftar acara kalender berhasil diambil',
                'data' => $calendarData
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during calendar data retrieval', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => 'Failed to retrieve calendar data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Indonesian month name from month number
     *
     * @param  int|string  $month
     * @return string
     */
    private function getIndonesianMonthName($month)
    {
        $monthNumber = (int) $month;
        $indonesianMonths = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        return $indonesianMonths[$monthNumber] ?? 'Unknown';
    }

    /**
     * Get asset activities history
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssetActivities(Request $request)
    {
        try {
            // Get pagination parameters from request
            $page = $request->query('page', 1);
            $limit = $request->query('limit', 10);

            // Log request info
            \Log::info('Fetching asset activities', [
                'page' => $page,
                'limit' => $limit
            ]);

            // Fetch asset activities from API
            $result = $this->apiService->request('GET', "/asset-histories/activities/all?page={$page}&limit={$limit}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during asset activities retrieval', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => ['authentication' => 'Authentication failed']
                ], 401);
            }

            // Check for API errors
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to retrieve asset activities';

                \Log::warning('Error during asset activities retrieval', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                return response()->json([
                    'success' => false,
                    'errors' => is_array($errorData) ? $errorData : ['general' => $errorData]
                ], 400);
            }

            // Return the activities data with success message
            return response()->json([
                'success' => true,
                'message' => 'Aktivitas aset berhasil diambil',
                'data' => $result['data'] ?? [
                    'histories' => [],
                    'pagination' => [
                        'total_items' => 0,
                        'total_pages' => 0,
                        'current_page' => (int)$page,
                        'limit' => (int)$limit,
                        'has_next' => false,
                        'has_prev' => false
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Exception during asset activities retrieval', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'errors' => 'Failed to retrieve asset activities: ' . $e->getMessage()
            ], 500);
        }
    }
}
