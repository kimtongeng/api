<?php
$router->group(['middleware' => 'auth:admin', 'prefix' => 'admin'], function ($router) {
    include('routes/position_video.php');
    include('routes/video.php');
});
