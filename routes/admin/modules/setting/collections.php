<?php
$router->group(['middleware' => 'auth:admin', 'prefix' => 'admin'], function ($router) {
    include('routes/user/role.php');
    include('routes/user/user.php');
    include('routes/user/userLog.php');
    include('routes/bank_account.php');
    include('routes/general_setting.php');
    include('routes/privacy_policy.php');
    include('routes/support.php');
    include('routes/api_version.php');
    include('routes/app_country.php');
});
