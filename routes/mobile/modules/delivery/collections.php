<?php

$router->group(['middleware' => 'auth:mobile', 'prefix' => 'mobile'], function ($router) {
    include('routes/geocoder.php');
});
