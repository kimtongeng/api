<?php

namespace App\Http\Controllers\Admin\Modules\Setting\User;

use App\Enums\Types\IsHasThumbnail;
use App\Enums\Types\IsResizeImage;
use App\Helpers\FCM;
use App\Helpers\StringHelper;
use App\Helpers\Utils\ImagePath;
use App\Http\Controllers\Controller;
use App\Models\Lib;
use App\Models\Permission;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use function env;
use function info;
use function response;

class UserController extends Controller
{
    const MODULE_KEY = 'user_list';
    const INSERT = 1;
    const UPDATE = 2;
    const REMOVE = 3;

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * Get list users
     */
    public function get(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getViewPermission())) {
            $data = $this->user->getLists($request->all());
            $response = [
                'pagination' => [
                    'total' => $data->total(),
                    'per_page' => $data->perPage(),
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'from' => intval($data->firstItem()),
                    'to' => intval($data->lastItem()),
                ],
                'data' => $data->items(),
                'success' => 1,
                'message' => 'Your action has been completed successfully.',
            ];
            return response()->json($response, 200);
        } else {
            return response()->json(['success' => 0, 'message' => 'Insufficient permission.'], 403);
        }
    }

    /**
     * Store user
     */
    public function store(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getCreatePermission())) {

            $this->checkFormValidation($request, self::INSERT);

            $user = new User();
            $user->setData($request);
            $user->save();
            if ($user->save()) {
                //upload image
                if (!empty($request['image'])) {
                    $image = StringHelper::uploadImage(
                        $request['image'],
                        ImagePath::userAdminImagePath,
                        IsHasThumbnail::getYes(),
                        IsResizeImage::getYes()
                    );
                    $user->image = $image;
                    $user->save();
                }
            }

            UserLog::setLog(self::MODULE_KEY, Permission::getCreatePermission(), 'Created Id : ' . $user->id);
            return $this->responseWithSuccess();
        } else {
            return response()->json(['success' => 0, 'message' => 'Insufficient permission.'], 403);
        }
    }

    /**
     * Update user
     */
    public function update(Request $request)
    {
        $this->checkFormValidation($request, self::UPDATE);

        DB::beginTransaction();
        $user = User::find($request['id']);
        $user->setData($request);

        $user->save();

        if ($user->save()) {
            $image = StringHelper::editImage(
                $request['image'],
                $request['old_image'],
                ImagePath::userAdminImagePath,
                IsHasThumbnail::getYes(),
                IsResizeImage::getYes()
            );
            $user->image = $image;
            $user->save();
        }

        UserLog::setLog(self::MODULE_KEY, Permission::getUpdatePermission(), 'Update Id : ' . $user->id);
        DB::commit();
        return $this->responseWithSuccess();
    }

    /**
     * Delete User
     */
    public function delete(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getDeletePermission())) {

            $this->checkFormValidation($request, self::REMOVE);


            DB::beginTransaction();

            if ($request->id != Auth::guard('admin')->user()->id) {
                $user = User::find($request->id)->delete();
                StringHelper::deleteImage($user->image, ImagePath::userAdminImagePath, IsHasThumbnail::getYes());

                UserLog::setLog(self::MODULE_KEY, Permission::getDeletePermission(), 'Delete Id : ' . $user->id);
            }

            DB::commit();
            return $this->responseWithSuccess();
        } else {
            return response()->json(['success' => 0, 'message' => 'Insufficient permission.'], 403);
        }
    }

    /**
     * update user flag
     *
     * @param Request $request
     * @return void
     */
    public function updateStatus(Request $request)
    {
        if (Permission::authorize(self::MODULE_KEY, Permission::getUpdatePermission())) {

            $this->validate($request, [
                'id' => 'required|max:255',
                'status' => 'required',
            ]);

            DB::beginTransaction();

            if ($request['id'] != Auth::user()->id) {
                DB::table('users')
                    ->where('id', $request['id'])
                    ->update([
                        'status' => $request['status'],
                        'updated_at' => Carbon::now(),
                    ]);
            }
            DB::commit();
            return $this->responseWithSuccess();
        } else {
            return response()->json(['success' => 0, 'message' => 'Insufficient permission.'], 403);
        }
    }

    /**
     * check form validation
     *
     * @param [type] $data
     * @param [type] $type
     * @return void
     */
    public function checkFormValidation($data, $type)
    {
        $uniqueEmail = false;
        $uniqueUserName = false;
        $oldUser = User::find($data['id']);
        if (!empty($oldUser)) {
            //When Update
            if ($data['email'] != $oldUser->email) {
                $uniqueEmail = true;
            }

            if ($data['username'] != $oldUser->username) {
                $uniqueUserName = true;
            }
        } else {
            //When Add
            $uniqueEmail = true;
            $uniqueUserName = true;
        }

        if ($type == self::INSERT || $type == self::UPDATE) {
            $this->validate($data, [
                'id' => $type == self::INSERT ? 'nullable' : 'required',
                'email' => ['nullable', 'max:50', Rule::when($uniqueEmail, [
                    Rule::unique('users')->where(function ($query) use ($data) {
                        return $query->where('email', $data['email'])
                            ->whereNull('users.deleted_at');
                    })
                ])],
                'username' => ['required', 'max:50', Rule::when($uniqueUserName, [
                    Rule::unique('users')->where(function ($query) use ($data) {
                        return $query->where('username', $data['username'])
                            ->whereNull('users.deleted_at');
                    })
                ])],
                'user_type_id' => 'required',
                'password' => empty($data['id']) ? 'required|min:6|max:50' : 'nullable'
            ]);
        } else if ($type == self::REMOVE) {
            $this->validate($data, [
                'id' => 'required|exists:users,id'
            ]);
        }
    }

    public function updateFCMToken(Request $request)
    {

        $this->validate($request, [
            'token' => 'required',
        ]);

        //multi user token
        $token = $request->token;
        $device_id = !empty($request->device_id) ? $request->device_id : 'default';
        $user_id = Auth::guard('admin')->user()->id;

        $checkUserDeviceID = UserDevice::where(UserDevice::USER_ID, $user_id)
            ->where(UserDevice::DEVICE_ID, $device_id)
            ->get();

        $user_device_data = [
            UserDevice::USER_ID => $user_id,
            UserDevice::DEVICE_ID => $device_id,
            UserDevice::FCM_TOKEN => $token
        ];

        if (count($checkUserDeviceID) > 0) {
            $user_device = UserDevice::where(UserDevice::USER_ID, $user_id)
                ->where(UserDevice::DEVICE_ID, $device_id)
                ->update([
                    UserDevice::FCM_TOKEN => $token
                ]);
        } else {
            $user_device = new UserDevice();
            $user_device->setData($user_device_data);
            $user_device->save();
        }

        $registrationToken = $token;
        $TOPIC_ADMIN = env('TOPIC_MAIN_ADMIN');

        FCM::subscribeToTopic($TOPIC_ADMIN, $registrationToken);

        return response()->json(['success' => 1, 'message' => Lib::SUCCESSFULLY], 200);
    }

    public function updateFCMTokenOld(Request $request)
    {

        $this->validate($request, [
            'token' => 'required',
        ]);

        //multi user token
        $token = $request->token;
        $device_id = !empty($request->device_id) ? $request->device_id : 'default';
        $user_id = Auth::guard('admin')->user()->id;

        $checkUserDeviceID = UserDevice::where('user_id', $user_id)
            ->where('device_id', $device_id)
            ->get();

        $user_device_data = [
            UserDevice::USER_ID => $user_id,
            UserDevice::DEVICE_ID => $device_id,
            UserDevice::FCM_TOKEN => $token
        ];

        if (count($checkUserDeviceID) > 0) {
            $user_device = UserDevice::where('user_id', $user_id)
                ->where('device_id', $device_id)
                ->update([
                    UserDevice::FCM_TOKEN => $token
                ]);
        } else {
            $user_device = new UserDevice();
            $user_device->setData($user_device_data);
            $user_device->save();
        }

        //store or update token notification group
        $userDevices = UserDevice::where('user_id', $user_id)->get();
        $fcmTokens = [];
        foreach ($userDevices as $obj) {
            if (!empty($obj['fcm_token'])) {
                $fcmTokens[] = $obj['fcm_token'];
            }
        }

        $user = User::where('id', $user_id)->first();
        $auth_token = null;

        if (empty($user->notification_key_name)) {
            $notificationKeyName = env('DEVICE_GROUP_ADMIN_KEY_NAME') . '-' . $user_id;
            $auth_token = FCM::createDeviceGroup($notificationKeyName, $fcmTokens);

            if (isset(json_decode($auth_token)->error)) {
                if (Str::contains(json_decode($auth_token)->error, 'exists')) {

                    $notificationKey = $user->auth_token;

                    $auth_token = FCM::addDeviceGroup($notificationKeyName, $notificationKey, $fcmTokens);
                }
            }

            info('Admin Login (' . $user->username . ') Create');
            info($auth_token);

            if (isset(json_decode($auth_token)->notification_key)) {
                User::where('id', $user->id)
                    ->update([
                        'notification_key_name' => $notificationKeyName,
                        'auth_token' => json_decode($auth_token)->notification_key
                    ]);
            }
        } else {

            $notificationKey = $user->auth_token;
            $notificationKeyName = $user->notification_key_name;

            $auth_token = FCM::addDeviceGroup($notificationKeyName, $notificationKey, $fcmTokens);

            info('Admin Login (' . $user->username . ') Add');
            info($auth_token);

            if (isset(json_decode($auth_token)->notification_key)) {
                User::where('id', $user->id)
                    ->update([
                        'notification_key_name' => $notificationKeyName,
                        'auth_token' => json_decode($auth_token)->notification_key
                    ]);
            }
        }

        return response()->json(['success' => 1, 'message' => Lib::SUCCESSFULLY], 200);
    }
}
