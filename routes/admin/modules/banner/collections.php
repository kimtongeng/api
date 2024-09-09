<?php
$router->group(['middleware' => 'auth:admin', 'prefix' => 'admin'], function ($router) {
    include('routes/banner.php');
    include('routes/position_banner.php');
});
