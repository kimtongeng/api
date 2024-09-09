<?php
$router->group(['middleware' => 'auth:mobile', 'prefix' => 'mobile'], function ($router) {
    include('routes/accommodation_crud.php');
    include('routes/accommodation_list.php');
    include('routes/accommodation_booking.php');
    include('routes/profile/room_crud.php');
    include('routes/profile/room_list.php');
    include('routes/profile/room_type_crud.php');
});
