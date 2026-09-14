<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'storage/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_merge(
        explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000,http://127.0.0.1:3000,http://10.100.0.203:3000')),
        ['*']
    ),

    'allowed_origins_patterns' => [
        '#^http://.*:3000$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
