<?php
$router->group(['middleware' => 'auth:mobile', 'prefix' => 'mobile'], function ($router) {
    include('routes/ktv_crud.php');
    include('routes/ktv_list.php');
    include('routes/profile/ktv_category_crud.php');
    include('routes/profile/ktv_product_crud.php');
    include('routes/profile/ktv_product_list.php');
    include('routes/profile/ktv_room_crud.php');
    include('routes/profile/ktv_room_list.php');
    include('routes/profile/ktv_girl_crud.php');
    include('routes/profile/ktv_girl_list.php');
    include('routes/ktv_booking.php');
});
