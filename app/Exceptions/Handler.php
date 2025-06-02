<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Custom handler for CSRF token mismatch errors
        $this->renderable(function (TokenMismatchException $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'message' => 'CSRF token mismatch. Please refresh the page.',
                    'csrf_token' => csrf_token(),
                    'csrf_expired' => true
                ], 419);
            }

            // For non-AJAX requests, redirect back with error message
            return redirect()->back()->with('error', 'Your session has expired. Please try again.');
        });

        // Custom handler for 404 not found errors - captures all NotFoundHttpException instances
        $this->renderable(function (NotFoundHttpException $e) {
            if (request()->is('api/*')) {
                return response()->json(['message' => 'Not Found'], 404);
            }

            return response()->view('Error.NotFound', [], 404);
        });
    }
}
