<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;
use function request;

class UserLog extends Model
{
    protected $table = 'user_log';
    public $timestamps = false;

    public static function getList($tableSize, $filter = [], $sortBy = '', $sortType = 'desc')
    {
        $isNotVipUser = !UserType::isVIPUser();
        $idgLevel = UserType::getIdgLevel();
        $superAdminLevel = UserType::getSuperAdminLevel();

        $module = empty($filter['module']) ? null : $filter['module'];
        $action = empty($filter['action']) ? null : $filter['action'];
        $search = empty($filter['search']) ? null : $filter['search'];

        //Created At Range
        $createdAtRange = isset($filter['date_time_picker']) ? $filter['date_time_picker'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        //Sort
        $sortUserName = $sortBy == 'user_name' ? 'user_name' : null;
        $sortModule = $sortBy == 'module' ? 'module' : null;
        $sortBrowser = $sortBy == 'browser' ? 'browser' : null;
        $sortIp = $sortBy == 'ip' ? 'ip' : null;
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;

        return self::join('users', 'users.id', 'user_log.user_id')
            ->join('user_type', 'user_type.id', 'users.user_type_id')
            ->leftJoin('module', 'module.module_key', 'user_log.module')
            ->when($module, function ($query) use ($module) {
                $query->where('user_log.module', $module);
            })
            ->when($action, function ($query) use ($action) {
                $query->where('user_log.action', $action);
            })
            ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
                $query->whereBetween('user_log.created_at', [$createdAtStartDate, $createdAtEndDate]);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('user_log.detail', 'LIKE', '%' . $search . '%')
                        ->orWhere('user_log.module', 'LIKE', '%' . $search . '%')
                        ->orWhere('user_log.action', 'LIKE', '%' . $search . '%')
                        ->orWhere('user_log.platform', 'LIKE', '%' . $search . '%')
                        ->orWhere('user_log.module', 'LIKE', '%' . $search . '%')
                        ->orWhere('user_log.browser', 'LIKE', '%' . $search . '%')
                        ->orWhere('users.full_name', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($isNotVipUser, function ($query) use ($idgLevel, $superAdminLevel) {
                $query->where(function ($query) use ($idgLevel, $superAdminLevel) {
                    $query->where('user_type.level', '<', $idgLevel)
                        ->orWhere('user_type.level', '<', $superAdminLevel);
                });
            })
            ->when($sortCreatedAt, function ($query) use ($sortType) {
                $query->orderBy('user_log.created_at', $sortType);
            })
            ->when($sortUserName, function ($query) use ($sortType) {
                $query->orderBy('users.full_name', $sortType);
            })
            ->when($sortModule, function ($query) use ($sortType) {
                $query->orderBy('user_log.module', $sortType);
            })
            ->when($sortBrowser, function ($query) use ($sortType) {
                $query->orderBy('user_log.browser', $sortType);
            })
            ->when($sortIp, function ($query) use ($sortType) {
                $query->orderBy('user_log.ip', $sortType);
            })
            ->select(
                'user_log.*',
                'module.module_name',
                'users.full_name as user_name',
            )
            ->paginate($tableSize);
    }

    public static function setLog($module, $action, $detail = null, $mobileAuth = false)
    {
        $detail = ucfirst($action) . ' ' . $module . '[ ' . $detail . ' ]';
        $agent = new Agent();
        $userLog = new UserLog();
        $userLog->user_id = Auth::user()->id;
        $userLog->ip = request()->ip();
        $userLog->module = $module;
        $userLog->action = $action;
        $userLog->browser = $agent->browser();
        $userLog->platform = $agent->platform();
        $userLog->device = static::getDeviceName();
        $userLog->detail = $detail;
        $userLog->created_at = Carbon::now();
        $userLog->save();
    }

    /**
     *
     * @return int
     */
    public static function getDeviceName()
    {
        $agent = new Agent;
        if ($agent->isDesktop()) {
            return 'desktop';
        }
        if ($agent->isMobile()) {
            return 'mobile';
        }
        if ($agent->isTablet()) {
            return 'tablet';
        }
        return 'unknown';
    }
}
