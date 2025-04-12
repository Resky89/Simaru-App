<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class ProcurementDetailRequestController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display the procurement detail page.
     */
    public function show($id)
    {
        try {
            $result = $this->apiService->request('GET', "/procurements/{$id}");

            // Check if we got an error response from the ApiService
            if (isset($result['error'])) {
                if (strpos($result['error'], 'login') !== false) {
                    return redirect()->route('login')->with('error', $result['error']);
                }
                throw new \Exception($result['error']);
            }

            // Make sure procurement data exists
            if (!isset($result['data'])) {
                throw new \Exception('Procurement data not found');
            }

            return view('Procurement.Request.DetailRequest', [
                'procurement' => $result['data']
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch procurement details', [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('procurement.request')->with('error', 'Failed to fetch procurement details: ' . $e->getMessage());
        }
    }
}
