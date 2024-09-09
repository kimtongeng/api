<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use function auth;

class Permission extends Model
{
    const TABLE_NAME = 'permission';
    const ID = 'id';
    const PERMISSION_NAME = 'permission_name';
    const MODULE_ID = 'module_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    //Permission
    const CREATE = 'create';
    const UPDATE = 'update';
    const DELETE = 'delete';
    const DELETE_SELL = 'delete_sale';
    const VIEW = 'view';

    /** Declare table name */
    protected $table = self::TABLE_NAME;

    public static function getCreatePermission()
    {
        return self::CREATE;
    }

    public static function getUpdatePermission()
    {
        return self::UPDATE;
    }

    public static function getDeletePermission()
    {
        return self::DELETE;
    }

    public static function getViewPermission()
    {
        return self::VIEW;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->permission_name;
    }

    public static function authorize($module = null, $action = null)
    {
        $isVIPUser = UserType::isVIPUser();
        if ($isVIPUser) {
            return true;
        } else {
            $check_role = Role::byPK(auth()->user()->role_id); //Check if User have Role
            if ($check_role) {
                $module = \App\Models\Module::byAttributed($module);
                //Check if User have Role
                if (!empty($module)) {
                    DB::enableQueryLog();
                    $permission = DB::table('role_module')
                        ->join('permission', 'permission.id', 'role_module.permission_id')
                        ->where('role_module.role_id', auth()->user()->role_id)
                        ->where('role_module.module_id', $module->id)
                        ->where('permission.permission_name', $action)
                        ->first();
                    if ($permission) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    //get list
    public static function lists()
    {
        return self::orderBy('id', 'desc')
            ->select('permission.*');
    }

    /**
     * Get list with no join other table
     *
     * @return void
     */
    public static function getComboList()
    {
        return self::select('id', 'permission_name', 'id as value', 'name as text')->orderBy('id', 'DESC')->get();
    }

    //set data
    public function setData($data)
    {
        $this->{self::PERMISSION_NAME} = $data[self::PERMISSION_NAME];
        $this->{self::MODULE_ID} = $data[self::MODULE_ID];
    }

    /**
     *
     * Get action type of permissions
     */
    public static function actionType($type = null)
    {
        if ($type) {
            $action_type = null;
            switch ($type) {
                case self::CREATE:
                    $action_type = 'Create';
                    break;
                case self::UPDATE:
                    $action_type = 'Update';
                    break;
                case self::DELETE:
                    $action_type = 'Delete';
                    break;
                default:
                    $action_type = 'View';
                    break;
            }
            return $action_type;
        } else {
            return $action_type = [
                self::CREATE => self::actionType(self::CREATE),
                self::UPDATE => self::actionType(self::UPDATE),
                self::DELETE => self::actionType(self::DELETE),
                self::VIEW => self::actionType(self::VIEW),
            ];
        }
    }
}
