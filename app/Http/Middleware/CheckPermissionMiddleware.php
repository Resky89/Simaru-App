<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;

class CheckPermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $permission
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $permission = null)
    {
        // If no permission required, proceed
        if (!$permission) {
            return $next($request);
        }

        // Check for multiple permissions (separated by |)
        if (strpos($permission, '|') !== false) {
            $permissions = explode('|', $permission);
            if (PermissionHelper::hasAnyPermission($permissions)) {
                return $next($request);
            }
        }
        // Check single permission
        else if (PermissionHelper::hasPermission($permission)) {
            return $next($request);
        }

        // Log unauthorized access attempt
        \Log::warning('Unauthorized access attempt', [
            'user_id' => session('user_id', 'unknown'),
            'route' => $request->route()->getName(),
            'permission_required' => $permission,
            'url' => $request->fullUrl()
        ]);

        // Return forbidden view for unauthorized access
        return response()->view('Error.Forbidden', [], 403);
    }
}
