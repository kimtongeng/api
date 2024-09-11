<?php
$router->group(['middleware' => 'auth:admin', 'prefix' => 'admin'], function ($router) {
    include('routes/dropdown.php');
    include('routes/contact.php');
    include('routes/media.php');
    include('routes/chat.php');
    include('routes/dashboard.php');
});
include("routes/itemTypeFake.php");
include("routes/testUser.php");