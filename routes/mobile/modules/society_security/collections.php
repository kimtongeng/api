<?php
$router->group(['middleware' => 'auth:mobile', 'prefix' => 'mobile'], function ($router) {
    include('routes/event_type.php');
    include('routes/position_group.php');
    include('routes/news.php');
    include('routes/news_list.php');
    include('routes/news_visitors.php');
    include('routes/news_comment.php');
});
