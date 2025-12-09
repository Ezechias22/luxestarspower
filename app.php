<?php

return [
    'name' => env('APP_NAME', 'LuxeStarsPower'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'https://luxestarspower.com'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'locale' => env('APP_LOCALE', 'fr'),
    'fallback_locale' => 'en',
    
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    
    'supported_locales' => explode(',', env('SUPPORTED_LOCALES', 'fr,en,es,de,it,pt,ar,zh')),
    
    'maintenance' => [
        'enabled' => env('MAINTENANCE_MODE', false),
        'secret' => env('MAINTENANCE_SECRET'),
        'allowed_ips' => [],
    ],
];
