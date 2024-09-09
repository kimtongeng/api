<?php

namespace App\Http\Controllers\Admin\Modules\Authentication;

use App\Enums\CollectionEnum;
use App\Helpers\FCM;
use App\Http\Controllers\Admin\Response;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\RoleModule;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserLog;
use App\Models\UserType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function app;
use function auth;
use function env;
use function response;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['login', 'register', 'getContent']]);
    }


    /**
     * Store a new user.
     *
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'username' => 'required|string|unique:users',
            'password' => 'required|confirmed',
        ]);

        try {
            $user = new User;
            $user->username = $request->input('username');
            $user->password = app('hash')->make($request->input('password'));
            $user->save();

            return response()->json([
                'entity' => 'users',
                'action' => 'create',
                'result' => 'success'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'entity' => 'users',
                'action' => 'create',
                'result' => 'failed'
            ], 409);
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['username', 'password']);
        $credentials['deleted_at'] = null;

        if (!$token = Auth::attempt($credentials, ['exp' => Carbon::now()->addDays(1)->timestamp])) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $description = 'Login to admin';
        UserLog::setLog('auth', 'login', $description);
        return $this->getResponseData($token);
    }

    /**
     * get user information
     *
     * @param Request $request
     * @return void
     */
    public function getUser(Request $request)
    {
        return $this->getResponseData();
    }

    public function logout(Request $request)
    {
        $device_id = !empty($request->device_id) ? $request->device_id : 'default';

        //Unsubscribe topic notification
        $user_id = Auth::guard('admin')->user()->id;
        $userDevice = UserDevice::where(UserDevice::USER_ID, $user_id)
            ->where(UserDevice::DEVICE_ID, $device_id)
            ->whereNotNull(UserDevice::FCM_TOKEN)
            ->first();

        if (!empty($userDevice)) {
            $fcmToken = $userDevice->fcm_token;
            $TOPIC_ADMIN = env('TOPIC_MAIN_ADMIN');

            FCM::unSubscribeToTopic($TOPIC_ADMIN, $fcmToken);
        }

        //remove user device token
        UserDevice::updateToken($device_id, null);

        Auth::guard('admin')->logout();

        return response()->json(['success' => 1, 'message' => 'Successfully logged out.']);
    }


    private function getResponseData($token = null)
    {
        $response = [
            'data' => [
                'user' => Auth::guard('admin')->user(),
                'auth_level' => UserType::userAuthLevel(),
                'role_modules' => RoleModule::getRoleModuleLists(auth()->user()->role_id),
                'enums' => CollectionEnum::getForAdmin(),

            ],
            'success' => 1,
            'message' => 'Your action has been completed successfully.',
        ];
        if (!empty($token)) {
            $response['data']['token'] = $token;
            $response['data']['token_type'] = 'bearer';
            $response['data']['expires_in'] = '';
        }
        return response()->json($response, 200);
    }
}
