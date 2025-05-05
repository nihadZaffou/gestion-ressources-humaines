<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    // 📌 GESTION DES GUARDS (ajout de 'admin' avec JWT)
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'token',
            'provider' => 'users',
            'hash' => false,
        ],

        // ➕ Guard JWT pour les admins
        'admin' => [
            'driver' => 'jwt',
            'provider' => 'admins',
        ],
        'employe' => [
    'driver' => 'jwt',
    'provider' => 'employes',
],

    ],

    // 📌 GESTION DES PROVIDERS (ajout de 'admins')
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        // ➕ Provider pour les admins
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],
        'employes' => [
    'driver' => 'eloquent',
    'model' => App\Models\Employe::class,
],

    ],

    // 📌 MOTS DE PASSE OUBLIÉS (facultatif si tu veux activer plus tard pour admin aussi)
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
