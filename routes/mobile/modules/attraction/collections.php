<?php
$router->group(['middleware' => 'auth:mobile', 'prefix' => 'mobile'], function ($router) {
    include('routes/attraction_crud.php');
    include('routes/attraction_list.php');
    include('routes/attraction_category_crud.php');
    include('routes/attraction_booking.php');
    include('routes/attraction_place_price.php');
    include('routes/attraction_place_price_list.php');
});
