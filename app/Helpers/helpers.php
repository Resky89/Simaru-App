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
