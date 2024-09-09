<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use function auth;

class UserType extends Model
{
    const TABLE_NAME = 'user_type';
    const ID = 'id';
    const TYPE = 'type';
    const LEVEL = 'level';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /** Declare table name */
    protected $table = self::TABLE_NAME;
    /**
     * Get list user type with lower level
     *
     * @return void
     */
    const IDG = [
        'ID' => 1,
        'LEVEL' => 7,
    ];
    const SUPER_ADMIN = [
        'ID' => 2,
        'LEVEL' => 6,
    ];
    const COMPANY = [
        'ID' => 3,
        'LEVEL' => 5,
    ];
    const BRANCH = [
        'ID' => 4,
        'LEVEL' => 4,
    ];
    const USER = [
        'ID' => 5,
        'LEVEL' => 2,
    ];

    /** Declare table name */

    public static function getIdgLevel()
    {
        return self::IDG['LEVEL'];
    }

    public static function getSuperAdminLevel()
    {
        return self::SUPER_ADMIN['LEVEL'];
    }

    public static function getCompanyLevel()
    {
        return self::COMPANY['LEVEL'];
    }

    public static function getBranchLevel()
    {
        return self::BRANCH['LEVEL'];
    }

    public static function isVIPUser()
    {
        $user_type = auth()->user()->user_type_id;
        if ($user_type == self::IDG['ID'] || $user_type == self::SUPER_ADMIN['ID']) {
            return true;
        }

        return false;
    }

    public static function isSuper()
    {
        $user_type = auth()->user()->user_type_id;
        if ($user_type == self::IDG['ID']) {
            return true;
        }

        return false;
    }

    /**
     * get user auth level
     *
     * @return void
     */
    public static function userAuthLevel()
    {
        $auth = self::where('id', Auth()->user()->user_type_id)->select('level')->first();
        if (!empty($auth)) {
            return $auth->level;
        }
        return null;
    }

    /**
     * list
     *
     * @return void
     */
    public static function lists()
    {
        $authLevel = self::userAuthLevel();
        return self::where('level', '<', $authLevel)->get();
    }

    /**
     * check auth type
     *
     * @param [type] $type
     * @return void
     */
    public static function authType($type)
    {
        if (Auth()->user()->user_type_id == $type) {
            return true;
        }

        return false;
    }

    public static function isNotificationSaleUser($id = null)
    {
        $role_id = $id != null ? $id : Auth()->user()->role_id;
        $isAllowed = DB::table('role_module')
            ->where([
                ['role_id', $role_id],
                ['permission_id', Permission::getViewPermission()],
                ['module_id', 78],
            ])
            ->exists();
        return $isAllowed;
    }
}
