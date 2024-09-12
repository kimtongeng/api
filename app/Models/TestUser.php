<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class TestUser extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, SoftDeletes;
    const ID = "id";
    const NAME =  "username";
    const IMAGE = "image";
    const EMAIL = "email";
    const PASSWORD = "password";
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';
    const TABLE_NAME = "test_users";
    protected $table = self::TABLE_NAME;

    protected $fillable = [
        self::NAME,
        self::EMAIL,
        self::CREATED_AT,
        self::UPDATED_AT,
        self::DELETED_AT,
    ];
    protected $hidden = [
        self::PASSWORD,
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    
    public function can($abilities, $arguments = [])
{
    dd($abilities,$arguments);
    return true; // or false based on your logic
}
public function cannot($abilities, $arguments = [])
{
    // Implement your logic to check if the user does not have the given permission(s)
    // You can use the $abilities and $arguments parameters to determine the permission(s)
    return true; // or false based on your logic
}

}
