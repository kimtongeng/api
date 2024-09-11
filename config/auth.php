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
        'testUser' => [
            'driver' => 'jwt',
            'provider' => 'test_user',
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
        ],
        'test_user' => [
            'driver' => 'eloquent',
            'model' => \App\Models\TestUser::class
        ]
        
    ]
];
