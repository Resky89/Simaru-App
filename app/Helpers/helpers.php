<?php

use App\Helpers\PermissionHelper;

if (!function_exists('hasPermission')) {
    /**
     * Check if user has a specific permission
     *
     * @param string $permissionName
     * @return bool
     */
    function hasPermission($permissionName)
    {
        return PermissionHelper::hasPermission($permissionName);
    }
}

if (!function_exists('hasAnyPermission')) {
    /**
     * Check if user has any of the given permissions
     *
     * @param array|string $permissionNames
     * @return bool
     */
    function hasAnyPermission($permissionNames)
    {
        return PermissionHelper::hasAnyPermission($permissionNames);
    }
}

if (!function_exists('permissionUI')) {
    /**
     * Conditionally show UI elements based on permission
     *
     * @param string|array $permissions Required permissions (any one is sufficient)
     * @param mixed $content Content to display if authorized
     * @param mixed $fallback Optional content to display if not authorized (default: empty string)
     * @return mixed
     */
    function permissionUI($permissions, $content, $fallback = '')
    {
        return PermissionHelper::permissionUI($permissions, $content, $fallback);
    }
}

if (!function_exists('logAccessAttempt')) {
    /**
     * Log access attempt when permission is denied
     *
     * @param string $permissionRequired
     * @param string $resourceName
     * @return void
     */
    function logAccessAttempt($permissionRequired, $resourceName)
    {
        return PermissionHelper::logAccessAttempt($permissionRequired, $resourceName);
    }
}

if (!function_exists('api_url')) {
    /**
     * Generate URL for the API backend
     *
     * @param string $path
     * @return string
     */
    function api_url($path = '')
    {
        $baseUrl = config('app.backend_url', env('BACKEND_URL', env('API_BASE_URL', 'http://localhost:9000/api')));
        $baseUrl = rtrim($baseUrl, '/');
        
        if ($path) {
            return $baseUrl . '/' . ltrim($path, '/');
        }
        return $baseUrl;
    }
}

if (!function_exists('api_public_url')) {
    /**
     * Generate URL for the public assets in the API backend
     *
     * @param string $path
     * @return string
     */
    function api_public_url($path = '')
    {
        return api_url('public/' . ltrim($path, '/'));
    }
}
