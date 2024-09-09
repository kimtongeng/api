<?php
$router->group(['middleware' => 'auth:admin', 'prefix' => 'admin'], function ($router) {
    include('routes/notification_broadcast.php');
    include('routes/notification_list.php');
});
