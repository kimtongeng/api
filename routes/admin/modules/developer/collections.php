<?php
$router->group(['middleware' => 'auth:admin', 'prefix' => 'admin'], function ($router) {
    include('routes/module.php');
    include('routes/permission.php');
    include('routes/prefix_code.php');
    include('routes/support_management.php');
});
