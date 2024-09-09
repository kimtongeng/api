<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Validator;

class Role extends Model
{
    use SoftDeletes;

    const TABLE_NAME = 'role';
    const ID = 'id';
    const ROLE_NAME = 'role_name';
    const ROLE_DESC = 'role_desc';
    const USER_TYPE_ID = 'user_type_id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const ADD = 1;
    const EDIT = 2;

    /** Declare table name */
    protected $table = self::TABLE_NAME;


    public function get()
    {
    }

    public function setData($data)
    {
        $this->role_name = $data['role_name'];
        $this->role_desc = $data['role_desc'];
        $this->user_type_id = $data['user_type_id'];
    }

    public function remove($id)
    {
        self::where('id', $id)->delete();
    }

    public static function byPK($role_id)
    {
        $role = self::where('id', $role_id)->first();

        if ($role) {
            return $role;
        }
        return false;
    }

    public static function getLists($tableSize, $filter = [] , $sortBy = '' , $sortType = 'desc')
    {
        // filter
        $search = isset($filter['search']) ? $filter['search'] : null;
        $authLevel = \App\Models\UserType::userAuthLevel();

        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        // sort
        $sortRole = $sortBy == 'role_name' ? 'role_name' : null;
        $sortUserType = $sortBy == 'user_type_id' ? 'user_type_id' : null;
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;


        return self::join('user_type', 'role.user_type_id', '=', 'user_type.id')
            ->where('user_type.level', '<', $authLevel)
            ->when($search, function ($query) use ($search) {
                $query->where('role.role_name', 'LIKE', "%{$search}%")
                    ->orWhere('user_type.type', 'LIKE', "%{$search}%");
            })
            ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
                $query->whereBetween('role.created_at', [$createdAtStartDate, $createdAtEndDate]);
            })
            ->when($sortRole, function($query) use($sortType) {
                $query->orderBy('role.role_name', $sortType);
            })
            ->when($sortUserType, function ($query) use ($sortType) {
                $query->orderBy('role.user_type_id', $sortType);
            })
            ->when($sortCreatedAt, function ($query) use ($sortType) {
                $query->orderBy('role.created_at', $sortType);
            })
            ->select(
                'role.*',
                'user_type.type',
                'role.created_at'
            )
            ->orderBy('role.id', 'desc')
            ->paginate($tableSize);
    }


    public static function getModulePermission()
    {
        $data = \App\Models\Module::with(['permission'])
            ->select(
                'id as module_id',
                'module_name',
                'module_key',
                'featured',
                'menu_title',
                DB::raw('1 as \'create\''),
                DB::raw('2 as \'update\''),
                DB::raw('3 as \'delete\''),
                DB::raw('4 as \'view\'')
            // DB::raw('(SELECT id FROM permission WHERE permission_name = "CREATE") AS \'create\''),
            // DB::raw('(SELECT id FROM permission WHERE permission_name = "UPDATE") AS \'update\''),
            // DB::raw('(SELECT id FROM permission WHERE permission_name = "DELETE") AS \'delete\''),
            // DB::raw('(SELECT id FROM permission WHERE permission_name = "VIEW") AS \'view\'')
            )->orderBy('sequence')
            ->get();
        return $data;
    }

    /**
     *
     */
    public function getByUserType($user_type_id)
    {
        return self::where('user_type_id', $user_type_id)->select('id', 'role_name')->get();
    }
}
