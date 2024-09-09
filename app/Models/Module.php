<?php

namespace App\Models;

use App\Lib;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function auth;
use const YAF\ERR\NOTFOUND\MODULE;

class Module extends Model
{
    const TABLE_NAME = 'module';
    protected $table = self::TABLE_NAME;
    public $timestamps = false;
    protected $fillable = [
        'id',
        'module_name',
        'module_key',
        'menu_title',
        'featured',
        'sequence',
        'permission',
        'created_at',
        'updated_at',
    ];

    const MODULE_NAME = 'module_name';
    const MODULE_KEY = 'module_key';
    const FEATURED = 'featured';
    const PERMISSION = 'permission';
    const SEQUENCE = 'sequence';
    const MENU_TITLE = 'menu_title';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function byAttributed($module)
    {
        return self::where('module_key', $module)->first();
    }

    /**
     * Store Modules
     */
    public static function storeData($data)
    {
        /**
         * Convert array to string
         */
        $permission = null;
        foreach ($data['permission'] as $value) {
            $permission .= $value . ',';
        }

        /**
         * Save to database
         */
        return self::create([
            'module_name' => $data['module_name'],
            'module_key' => $data['module_key'],
            'menu_title' => $data['menu_title'],
            'featured' => $data['featured'],
            'sequence' => $data['sequence'],
            'permission' => rtrim($permission, ','), // Cut out the comma at the end of the string
            'created_at' => Carbon::now()
        ]);
    }

    function permission()
    {
        return $this->hasMany('App\Models\Permission', 'module_id', 'id')
            ->select(
                'permission.*',
                DB::raw("false as checked"),
            );
    }

    /*lists*/
    public static function lists($filter = [], $sortBy = '', $sortType = 'asc')
    {
        //Filter
        $search = empty($filter['search']) ? null : $filter['search'];

        //Created At Range
        $createdAtRange = isset($filter['created_at_range']) ? $filter['created_at_range'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        //Sort
        $sortModuleName = $sortBy == 'module_name' ? 'module_name' : null;
        $sortFeatured = $sortBy == 'featured' ? 'featured' : null;
        $sortSequence = $sortBy == 'sequence' ? 'sequence' : null;
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;
        $sortUpdatedAt = $sortBy == 'updated_at' ? 'updated_at' : null;

        return self::leftJoin('role_module', 'role_module.module_id', 'module.id')
            ->with(['permission' => function ($query) {
                $query->when(!UserType::isSuper(), function ($query) {
                    $query->where('role_module.role_id', Auth()->user()->role_id)
                        ->join('role_module', 'role_module.permission_id', 'permission.id');
                })
                    ->groupBy('permission.id');
            }])
            ->when(!UserType::isSuper(), function ($query) {
                $query->where('role_module.role_id', Auth()->user()->role_id);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('module.module_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('module.module_key', 'LIKE', '%' . $search . '%')
                        ->orWhere('module.menu_title', 'LIKE', '%' . $search . '%');
                });
            })
            ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
                $query->whereBetween('module.created_at', [$createdAtStartDate, $createdAtEndDate]);
            })
            ->when($sortModuleName, function ($query) use ($sortType) {
                $query->orderBy('module.module_name', $sortType);
            })
            ->when($sortFeatured, function ($query) use ($sortType) {
                $query->orderBy('module.featured', $sortType);
            })
            ->when($sortSequence, function ($query) use ($sortType) {
                $query->orderBy('module.sequence', $sortType);
            })
            ->when($sortCreatedAt, function ($query) use ($sortType) {
                $query->orderBy('module.created_at', $sortType);
            })
            ->when($sortUpdatedAt, function ($query) use ($sortType) {
                $query->orderBy('module.updated_at', $sortType);
            })
            ->when(empty($sortBy), function ($query) {
                $query->orderBy('module.sequence', 'ASC');
            })
            ->groupBy('module.id')
            ->select(
                'module.*',
                DB::raw('false as checked')
            );
    }

    //set data
    public function setData($data)
    {
        $this->{self::MODULE_NAME} = $data[self::MODULE_NAME];
        $this->{self::MODULE_KEY} = $data[self::MODULE_KEY];
        $this->{self::FEATURED} = $data[self::FEATURED];
        $this->{self::SEQUENCE} = $data[self::SEQUENCE];
        !empty($data[self::MENU_TITLE]) && $this->{self::MENU_TITLE} = $data[self::MENU_TITLE];
    }

    /**
     * Validation
     */
    public static function checkValidation($data)
    {
        /**
         * Validate fields
         */
        return Validator::make($data, [
            'module_name' => 'required',
            'module_key' => 'required|alpha_dash',
            'menu_title' => 'nullable|alpha_dash',
            'featured' => 'required|numeric',
            'sequence' => 'required|numeric',
            'permission' => 'required'
        ]);
    }

    /**
     * Edit Module
     */
    public static function edit($data)
    {
        /**
         * Convert array to string
         */
        $permission = implode(',', $data['permission']);

        /**
         * Update fields
         */
        return $update_module = DB::table('module')->where('id', $data)->update([
            'module_name' => $data['module_name'],
            'module_key' => $data['module_key'],
            'menu_title' => $data['menu_title'],
            'featured' => $data['featured'],
            'sequence' => $data['sequence'],
            'permission' => $permission,
            'updated_at' => Carbon::now()
        ]);
    }
}
