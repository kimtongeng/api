<?php

namespace App\Models;

use App\Helpers\StringHelper;
use App\Helpers\Utils\ImagePath;
use App\Models\Main\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Auth\Authorizable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use function auth;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, SoftDeletes;

    const TABLE_NAME = 'users';
    const ID = 'id';
    const USERNAME = 'username';
    const FULL_NAME = 'full_name';
    const IMAGE = 'image';
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const ROLE_ID = 'role_id';
    const USER_TYPE_ID = 'user_type_id';
    const NOTIFICATION_KEY_NAME = 'notification_key_name';
    const AUTH_TOKEN = 'auth_token';
    const STATUS = 'status';
    
const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';
    protected $table = self::TABLE_NAME;


    protected $fillable = [
        self::ID,
        self::USERNAME,
        self::FULL_NAME,
        self::IMAGE,
        self::EMAIL,
        self::PASSWORD,
        self::ROLE_ID,
        self::USER_TYPE_ID,
        self::NOTIFICATION_KEY_NAME,
        self::AUTH_TOKEN,
        self::STATUS,
        self::CREATED_AT,
        self::UPDATED_AT,
        self::DELETED_AT
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function getAuthId()
    {
        return auth()->user()->id;
    }

    /**
     * get list user
     *
     * @param Request $request
     * @return void
     */
    public function getId()
    {
        return $this->id;
    }

    public function setPassword($password)
    {
        $this->password = Hash::make($password);
    }

    public function getLists($data)
    {
        $tableSize = $data['table_size'];
        $sortType  = $data['sort_type'];
        $sortBy    = $data['sort_by'];
        if (empty($tableSize)) {
            $tableSize = 10;
        }

        // filter
        $search = isset($data['filter']['search']) ? $data['filter']['search'] : null;

        //Created At Range
        $createdAtRange = isset($data['filter']['date_time_picker']) ? $data['filter']['date_time_picker'] : null;
        $createdAtStartDate = empty($createdAtRange['startDate']) ? null : Carbon::parse($createdAtRange['startDate'])->format('Y-m-d H:i:s');
        $createdAtEndDate = empty($createdAtRange['endDate']) ? null : Carbon::parse($createdAtRange['endDate'])->format('Y-m-d H:i:s');

        // sort
        $sortUsername = $sortBy == 'username' ? 'username' : null;
        $sortEmail = $sortBy == 'email' ? 'email' : null;
        $sortUserType = $sortBy == 'user_type_id' ? 'user_type_id' : null;
        $sortRole = $sortBy == 'role_id' ? 'role_id' : null;
        $sortCreatedAt = $sortBy == 'created_at' ? 'created_at' : null;

        $isNotVipUser = !\App\Models\UserType::isVIPUser();

        $data = self::join('user_type', 'user_type.id', 'users.user_type_id')
            ->leftJoin('role', 'role.id', 'users.role_id')
            ->whereNull('users.deleted_at')
            ->where('user_type.level', '<', UserType::getIdgLevel())
            ->when($isNotVipUser, function ($query) {
                $query->where(function ($query) {
                    $query->where('user_type.level', '<', \App\Models\UserType::getSuperAdminLevel());
                });
            })
            ->when($search, function ($query) use ($search) {
                $query->where('users.username', 'LIKE', '%' . $search . '%');
            })
            ->when(!empty($createdAtStartDate) && !empty($createdAtEndDate), function ($query) use ($createdAtStartDate, $createdAtEndDate) {
                $query->whereBetween('users.created_at', [$createdAtStartDate, $createdAtEndDate]);
            })
            ->when($sortUsername, function($query) use ($sortType) {
                $query->orderBy('users.username', $sortType);
            })
            ->when($sortEmail, function ($query) use ($sortType) {
                $query->orderBy('users.email', $sortType);
            })
            ->when($sortUserType, function ($query) use ($sortType) {
                $query->orderBy('users.user_type_id', $sortType);
            })
            ->when($sortRole, function ($query) use ($sortType) {
                $query->orderBy('users.role_id', $sortType);
            })
            ->when($sortCreatedAt, function ($query) use ($sortType) {
                $query->orderBy('users.created_at', $sortType);
            })
            // ->orderBy('users.created_at')
            ->select(
                'users.id',
                'users.username',
                'users.image',
                'users.full_name',
                'users.email',
                'users.role_id',
                'role.role_name',
                'user_type.id as user_type_id',
                'user_type.type as user_type',
                'users.status',
                'users.created_at',
            )
            ->paginate($tableSize);
        return $data;
    }

    /**
     * add user
     *
     * @param [type] $data
     * @return void
     */
    public function setData($data)
    {
        $this->username = $data['username'];
        $this->full_name = $data['full_name'];
        isset($data['password']) && $this->setPassword($data['password']);
        $this->email = $data['email'];
        $this->role_id = $data['role_id'];
        $this->user_type_id = $data['user_type_id'];
    }

    public static function updateToken($token)
    {
        $id = auth()->user()->id;
        self::where('id', $id)
            ->update(['auth_token' => $token]);
    }
}
