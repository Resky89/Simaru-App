<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Services\ApiService;

class VendorController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display a listing of vendors.
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 10);

            $result = $this->apiService->request('GET', '/vendors', [
                'query' => [
                    'page' => $page,
                    'limit' => $limit,
                    'sort_by' => 'vendor_id',
                    'sort_order' => 'asc'  // Use 'asc' for oldest first
                ]
            ]);

            // Check if we got an error response from the ApiService
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during vendors retrieval:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for API errors based on success flag
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to fetch vendors';

                \Log::warning('Error during vendors retrieval:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

                // Return JSON response if requested
                if ($request->has('json') && $request->input('json') == 'true') {
                    \Log::info('Returning empty vendors as JSON due to API error');
                    return response()->json([]);
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

                return view('Vendor', [
                    'vendors' => [],
                    'error' => $errorMessage
                ]);
            }

            // Return JSON response if requested
            if ($request->has('json') && $request->input('json') == 'true') {
                \Log::info('Returning vendors as JSON', [
                    'count' => count($result['data'] ?? []),
                    'sample' => !empty($result['data']) ? $result['data'][0] : null
                ]);
                return response()->json($result['data'] ?? []);
            }

            return view('Vendor', [
                'vendors' => $result['data'] ?? [],
                'pagination' => $result['pagination'] ?? null
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch vendors', [
                'error' => $e->getMessage()
            ]);

            // Return empty array if JSON response is requested
            if ($request->has('json') && $request->input('json') == 'true') {
                return response()->json([]);
            }

            return view('Vendor', [
                'vendors' => [],
                'error' => 'Failed to fetch vendors: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created vendor.
     */
    public function store(Request $request)
    {
        try {
            // Log the request data
            \Log::info('Attempting to create vendor with data:', [
                'request_data' => $request->all()
            ]);

            $result = $this->apiService->request('POST', '/vendors', [
                'json' => [
                    'vendor_name' => $request->input('vendor_name'),
                    'contact_person' => $request->input('contact_person'),
                    'phone_number' => $request->input('phone_number'),
                    'email' => $request->input('email'),
                    'website' => $request->input('website'),
                    'address' => $request->input('address')
                ]
            ]);

            // Log the API response
            \Log::info('API response for vendor creation:', [
                'api_response' => $result
            ]);

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during vendor creation:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to create vendor';

                \Log::warning('Error during vendor creation:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

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
            \Log::info('Vendor created successfully');
            return redirect()->route('vendor')
                ->with('success', 'Vendor created successfully');
        } catch (\Exception $e) {
            \Log::error('Exception during vendor creation:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'vendor_data' => $request->except('_token')
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create vendor: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified vendor.
     */
    public function update(Request $request, $id)
    {
        try {
            $result = $this->apiService->request('PUT', "/vendors/{$id}", [
                'json' => [
                    'vendor_id' => $id,
                    'vendor_name' => $request->input('vendor_name'),
                    'contact_person' => $request->input('contact_person'),
                    'phone_number' => $request->input('phone_number'),
                    'email' => $request->input('email'),
                    'website' => $request->input('website'),
                    'address' => $request->input('address')
                ]
            ]);

            // Check if we got an auth error response
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during vendor update:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to update vendor';

                \Log::warning('Error during vendor update:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

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
            return redirect()->route('vendor')
                ->with('success', 'Vendor updated successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to update vendor', [
                'error' => $e->getMessage(),
                'vendor_id' => $id,
                'vendor_data' => $request->except(['_token', '_method'])
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update vendor: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified vendor.
     */
    public function destroy($id)
    {
        try {
            $result = $this->apiService->request('DELETE', "/vendors/{$id}");

            // Check for auth errors
            if (isset($result['errors']) && is_string($result['errors']) &&
                in_array($result['errors'], ['auth_failed', 'session_expired'])) {
                \Log::warning('Authentication error during vendor deletion:', [
                    'errors' => $result['errors'] ?? 'Authentication failed'
                ]);
                return redirect()->route('login')->with('error', is_string($result['errors']) ? $result['errors'] : 'Authentication failed');
            }

            // Check for other API errors or unsuccessful responses
            if (!isset($result['success']) || $result['success'] !== true) {
                $errorData = $result['errors'] ?? 'Failed to delete vendor';

                \Log::warning('Error during vendor deletion:', [
                    'success' => $result['success'] ?? false,
                    'errors' => $errorData
                ]);

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
            return redirect()->route('vendor')
                ->with('success', 'Vendor deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to delete vendor', [
                'error' => $e->getMessage(),
                'vendor_id' => $id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete vendor: ' . $e->getMessage());
        }
    }
}
