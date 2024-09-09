<?php

/**
 * View log file route
 */
$router->group([
    'namespace' => '\Rap2hpoutre\LaravelLogViewer',
], function () use ($router) {
    $router->get('logs', 'LogViewerController@index');
});


/**
 * Api for Admin
 */
include('admin/collections.php');



/**
 * Api for Mobile
 */
include('mobile/collections.php');
