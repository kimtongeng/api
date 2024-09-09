<?php
$router->group(['middleware' => 'auth:mobile', 'prefix' => 'mobile'], function ($router) {
    include('routes/property_crud.php');
    include('routes/property_asset_crud.php');
    include('routes/property_asset_category_crud.php');
    include('routes/property_list.php');
    include('routes/sale_property.php');
    include('routes/commission_property.php');
    include('routes/property_chat.php');
});
