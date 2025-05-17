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
}
