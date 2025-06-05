<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CsrfTokenController extends Controller
{
    /**
     * Get a new CSRF token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        // Regenerate the session to ensure a fresh token
        Session::regenerateToken();
        
        return response()->json([
            'token' => csrf_token(),
            'message' => 'CSRF token refreshed successfully',
            'timestamp' => now()->timestamp
        ]);
    }
}
