<?php

namespace App\Helpers;

class PermissionHelper
{
    /**
     * Check if user has a specific permission
     *
     * @param string $permissionName
     * @return bool
     */
    public static function hasPermission($permissionName)
    {
        // Get all user permissions from session
        $userPermissions = session('user_permission_names', []);
        $tokenPermissions = session('token_permissions', []);

        // Use combined permissions from both sources
        $allPermissions = array_merge($userPermissions, $tokenPermissions);

        // Check wildcard permission
        if (in_array('*', $allPermissions)) {
            return true;
        }

        // Check direct match with original format
        if (in_array($permissionName, $allPermissions)) {
            return true;
        }

        return false;
    }

    /**
     * Check if user has any of the given permissions
     *
     * @param array|string $permissionNames
     * @return bool
     */
    public static function hasAnyPermission($permissionNames)
    {
        if (!is_array($permissionNames)) {
            return self::hasPermission($permissionNames);
        }

        foreach ($permissionNames as $permission) {
            if (self::hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Conditionally show UI elements based on permission
     *
     * @param string|array $permissions Required permissions (any one is sufficient)
     * @param mixed $content Content to display if authorized
     * @param mixed $fallback Optional content to display if not authorized (default: empty string)
     * @return mixed
     */
    public static function permissionUI($permissions, $content, $fallback = '')
    {
        if (self::hasAnyPermission($permissions)) {
            return $content;
        }
        return $fallback;
    }

    /**
     * Log access attempt when permission is denied
     *
     * @param string $permissionRequired
     * @param string $resourceName
     * @return void
     */
    public static function logAccessAttempt($permissionRequired, $resourceName)
    {
        \Log::warning('Unauthorized access attempt', [
            'user_id' => session('user_id', 'unknown'),
            'permission_required' => $permissionRequired,
            'resource' => $resourceName,
            'url' => request()->fullUrl()
        ]);
    }
}
