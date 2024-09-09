<?php

return [
    'default' => env('DB_MAIN_CONNECTION'),
    'migrations' => 'migrations',
    'connections' => [
        env('DB_MAIN_CONNECTION') => [
            'driver'    => 'mysql',
            'host'      => env('DB_MAIN_HOST'),
            'port'      => env('DB_MAIN_PORT'),
            'database'  => env('DB_MAIN_DATABASE'),
            'username'  => env('DB_MAIN_USERNAME'),
            'password'  => env('DB_MAIN_PASSWORD'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => env('DB_MAIN_STRICT_MODE'),
            'unix_socket' => env('DB_MAIN_SOCKET', ''),
        ],
        env('DB_PMS_CONNECTION') => [
            'driver'    => 'mysql',
            'host'      => env('DB_PMS_HOST'),
            'port'      => env('DB_PMS_PORT'),
            'database'  => env('DB_PMS_DATABASE'),
            'username'  => env('DB_PMS_USERNAME'),
            'password'  => env('DB_PMS_PASSWORD'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => env('DB_PMS_STRICT_MODE'),
            'unix_socket' => env('DB_PMS_SOCKET', ''),
        ],
    ],
];
