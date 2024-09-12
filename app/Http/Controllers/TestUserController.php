<?php

namespace App\Http\Controllers;

use App\Models\TestUser;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\error;

class TestUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:testUser', ['except' => ['login', 'register', 'getContent']]);
    }

    public function register(Request $request){
        
        $validator = Validator::make($request->all(),[
            "username"=>"required|string",
            "email"=>"required|email",
            "password"=>"required"
        ]);
        if ($validator->fails()) {
            // Return error response
            return response()->json([
                'entity' => 'users',
                'action' => 'create',
                'result' => 'failed'
            ], 422);
        }
        try{
            $testUser = new TestUser();
            $testUser->username = $request->input("username");
            $testUser->email = $request->input("email");
            $testUser->password = Hash::make($request->input("password"));
            $testUser->save();
            return response()->json([
                'entity' => 'users',
                'action' => 'create',
                'result' => 'success'
            ], 201);
        }
        catch( Exception $error){
            return response()->json([
                'entity' => 'users',
                'action' => 'create',
                'result' => 'failed'
            ], 409);
        }

    }

    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            "username"=>"required",
            "password"=>"required"
        ]);
        $credential = $request->only(["username","password"]);
        if(!$token = Auth::guard("testUser")->attempt($credential,["exp"=>Carbon::now()->addDay(1)->timestamp])){
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        return [
            "token"=>$token,
            "user"=>Auth::guard("testUser")->user()
        ];

    }
    public function getUser(){
        return [
            "user"=> Auth::guard("testUser")->user()
        ];
    }
    public function logout(){
        $user = new TestUser();
        $user->can("update",Auth::guard("testUser")->user());
        // Auth::guard('testUser')->logout();
        return [
            "message"=>"user has been logout"
        ];
    }
}
