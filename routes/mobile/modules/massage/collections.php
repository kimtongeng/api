<?php
$router->group(['middleware' => 'auth:mobile', 'prefix' => 'mobile'], function ($router) {
    include('routes/massage_crud.php');
    include('routes/massage_list.php');
    include('routes/massage_booking.php');
    include('routes/profile/massage_service_crud.php');
    include('routes/profile/massage_service_list.php');
    include('routes/profile/massage_therapist_crud.php');
    include('routes/profile/massage_therapist_list.php');
});
