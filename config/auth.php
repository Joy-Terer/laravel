<?php

// ============================================================
// FILE: config/auth.php
// REPLACE the entire file with this
// ============================================================

return [

    'defaults' => [
        'guard'     => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],

        // Superadmin guard
        'superadmin' => [
            'driver'   => 'session',
            'provider' => 'superadmins',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\User::class,
        ],

        // Superadmin provider
        'superadmins' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Superadmin::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];