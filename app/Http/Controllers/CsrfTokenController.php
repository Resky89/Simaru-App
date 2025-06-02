<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CsrfTokenController extends Controller
{
    /**
     * Get a new CSRF token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return response()->json([
            'token' => csrf_token(),
            'message' => 'CSRF token refreshed successfully'
        ]);
    }
}
