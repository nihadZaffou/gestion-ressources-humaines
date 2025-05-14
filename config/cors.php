<?php

return [

    'supports_credentials' => true,

    'allowed_origins' => ['http://localhost:5173'],  // Permet uniquement les requêtes depuis cette URL
    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization'],
    'allowed_methods' => ['*'],  // Autorise toutes les méthodes (GET, POST, etc.)

    'exposed_headers' => [],
    'max_age' => 0,

    'paths' => ['api/*', 'sanctum/csrf-cookie'],
];