<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RoleModule extends Model
{
    use \Awobaz\Compoships\Compoships;

    /** Declare table name */
    protected $table = 'role_module';
    const ROLE_ID = 'role_id';
    const MODULE_ID = 'module_id';
    const PERMISSION_ID = 'permission_id';

    public static function add($roleId, $moduleId, $permissionId)
    {
        self::insert([
            'role_id' => $roleId,
            'module_id' => $moduleId,
            'permission_id' => $permissionId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    //set data
    public function setData($data)
    {
        $this->{self::ROLE_ID} = $data[self::ROLE_ID];
        $this->{self::MODULE_ID} = $data[self::MODULE_ID];
        $this->{self::PERMISSION_ID} = $data[self::PERMISSION_ID];
    }

    /**
     * remove role module by id
     *
     * @param [type] $roleId
     * @return void
     */
    public static function remove($roleId)
    {
        return self::where('role_id', $roleId)->delete();
    }

    /**
     * get module with permission by role id
     *
     * @param [type] $roleId
     * @return void
     */
    public static function getModulePermissionById($roleId)
    {
        $data = self::select('role_id', 'module_id', 'module.module_key', 'permission_id')
            ->join('module', 'module.id', 'role_module.module_id')
            ->where('role_id', $roleId)
            ->get();
        return $data;
    }

    public function permissions()
    {
        return $this->hasMany('App\Models\RoleModule', ['role_id', 'module_id'], ['role_id', 'module_id'])
            ->join('permission', 'permission.id', 'permission_id');
    }

    /**
     * get role module
     *
     * @param [type] $roleId
     * @return void
     */
    public static function getRoleModuleLists($roleId)
    {
        $data = [];
        $key = 'role_modules_' . $roleId;
        Cache::forget('role_modules_' . $roleId);
        if (Cache::has($key)) {
            $data = Cache::get($key);
        } else {
            DB::enableQueryLog();
            $data = Cache::remember($key, Carbon::now()->addMonth(1), function () use ($roleId) {
                return self::join('module', 'module.id', 'role_module.module_id')
                    ->where('role_module.role_id', $roleId)
                    ->select(
                        'role_module.role_id',
                        'role_module.module_id',
                        'module.module_key',
                        'module.featured',
                        'role_module.permission_id',
                    )
                    ->with(['permissions'])
                    ->groupBy('module.module_key', 'module.featured')
                    ->get();
            });
        }
        // DB::raw('group_concat(case when role_module.permission_id = ' . $permission->getCreatePermission() . ' then role_module.permission_id else null end) \'create\''),
        // DB::raw('group_concat(case when role_module.permission_id = ' . $permission->getUpdatePermission() . ' then role_module.permission_id else null end) \'update\''),
        // DB::raw('group_concat(case when role_module.permission_id = ' . $permission->getDeletePermission() . ' then role_module.permission_id else null end) \'delete\''),
        // DB::raw('group_concat(case when role_module.permission_id = ' . $permission->getViewPermission() . ' then role_module.permission_id else null end) \'view\'')
        return $data;
    }
}
