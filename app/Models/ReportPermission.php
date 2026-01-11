<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportPermission extends Model
{
    use HasFactory;

    protected $fillable = ['report_key', 'role', 'access_level'];

    // Access Levels
    const ACCESS_NONE = 'none';
    const ACCESS_OWN = 'own'; // Authorized Committees Only
    const ACCESS_GLOBAL = 'global'; // All Data

    /**
     * Check if a role has permission to view a report.
     * Returns the access level (string) or FALSE/NONE if no access.
     * Use this when filtering data.
     */
    public static function getAccessLevel(string $key, $role): string
    {
        if ($role === 'top_management') {
            return self::ACCESS_GLOBAL;
        }

        $permission = self::where('report_key', $key)
            ->where('role', $role)
            ->first();

        return $permission ? $permission->access_level : self::ACCESS_NONE;
    }

    /**
     * Boolean check for simple protection (middleware/sidebar).
     * Returns TRUE if access is NOT 'none'.
     */
    public static function check(string $key, $role): bool
    {
        $level = self::getAccessLevel($key, $role);
        return $level !== self::ACCESS_NONE;
    }

    /**
     * Check if a role has ANY report permission.
     */
    public static function hasAnyAccess($role): bool
    {
        if ($role === 'top_management') {
            return true;
        }

        return self::where('role', $role)->where('access_level', '!=', self::ACCESS_NONE)->exists();
    }
}
