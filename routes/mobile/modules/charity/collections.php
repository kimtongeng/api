<?php
$router->group(['middleware' => 'auth:mobile', 'prefix' => 'mobile'], function ($router) {
    include('routes/charity_organization_crud.php');
    include('routes/charity_home_screen.php');
    include('routes/charity_donor_list.php');
});
