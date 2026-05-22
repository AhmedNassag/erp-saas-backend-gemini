<?php

namespace App\Traits;

use Modules\Core\Models\RoleAndPermission\Permission;
use Illuminate\Support\Facades\Log;

trait PermissionSeederTrait
{
    /**
     * Create or update permissions for a set of models and actions
     *
     * @param array $models  // key = model name, value = module name
     * @param array $actions // list of actions
     */
    public function createOrUpdatePermissions(array $models, array $actions)
    {
        foreach ($models as $model => $moduleName) {
            foreach ($actions as $action) {
                try {
                    $permissionName = $action . '-' . strtolower($model);

                    $permission = Permission::where('name', $permissionName)
                        ->where('guard_name', 'sanctum')
                        ->first();

                    if ($permission) {
                        if (empty($permission->module)) {
                            $permission->update(['module' => $moduleName]);
                            Log::info("Updated permission: {$permissionName} with module: {$moduleName}");
                        }
                    } else {
                        Permission::create([
                            'name' => $permissionName,
                            'guard_name' => 'sanctum',
                            'module' => $moduleName,
                        ]);
                        Log::info("Created permission: {$permissionName} with module: {$moduleName}");
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to create/update permission {$permissionName}: " . $e->getMessage());
                }
            }
        }
    }
}
