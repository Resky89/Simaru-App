<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RefreshCsrfToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Generate a new CSRF token if session is valid but token might be old
        if (Session::isStarted() && $request->session()->has('_token')) {
            $response = $next($request);

            // Check if the response is a view
            if (method_exists($response, 'getContent')) {
                $content = $response->getContent();

                // If response contains a meta tag with csrf-token, update it
                if (is_string($content) && str_contains($content, 'name="csrf-token"')) {
                    // Regenerate token
                    $token = csrf_token();

                    // Replace all CSRF tokens in forms and meta tags
                    $pattern = '/<input type="hidden" name="_token" value="[^"]*">/i';
                    $replacement = '<input type="hidden" name="_token" value="' . $token . '">';
                    $content = preg_replace($pattern, $replacement, $content);

                    $metaPattern = '/<meta name="csrf-token" content="[^"]*">/i';
                    $metaReplacement = '<meta name="csrf-token" content="' . $token . '">';
                    $content = preg_replace($metaPattern, $metaReplacement, $content);

                    $response->setContent($content);
                }
            }

            return $response;
        }

        return $next($request);
    }
}
