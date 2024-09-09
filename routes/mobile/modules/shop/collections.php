<?php
$router->group(['middleware' => 'auth:mobile', 'prefix' => 'mobile'], function ($router) {
    include('routes/shop_crud.php');
    include('routes/profile/product_crud.php');
    include('routes/profile/product_category_crud.php');
    include('routes/profile/product_sub_category_crud.php');
    include('routes/profile/product_modifier_crud.php');
    include('routes/profile/product_brand_crud.php');
    include('routes/profile/product_model_crud.php');
    include('routes/shop_list.php');
    include('routes/profile/product_list.php');
    include('routes/shop_order.php');
});
