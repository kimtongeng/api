<?php

return [
    'defaults' => [
        'guard' => 'admin',
        'passwords' => 'users',
    ],

    'guards' => [
        'admin' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
        'mobile' => [
            'driver' => 'jwt',
            'provider' => 'contact',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \App\Models\User::class
        ],
        'contact' => [
            'driver' => 'eloquent',
            'model' => \App\Models\Contact::class
        ]
    ]
];
