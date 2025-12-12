<?php

return [
    'app' => [
        'name' => getenv('APP_NAME') ?: 'LuxeStarsPower',
        'env' => getenv('APP_ENV') ?: 'production',
        'debug' => getenv('APP_DEBUG') === 'true',
        'url' => getenv('APP_URL') ?: 'https://luxestarspower-production.up.railway.app',
        'locale' => 'fr',
        'supported_locales' => ['fr', 'en'],
        'default_currency' => 'USD',
    ],
    
    'db' => [  // ← CHANGÉ de 'database' à 'db'
        'host' => getenv('DB_HOST'),
        'port' => getenv('DB_PORT') ?: 3306,
        'name' => getenv('DB_NAME'),
        'user' => getenv('DB_USER'),
        'password' => getenv('DB_PASS'),
        'charset' => 'utf8mb4',
    ],
    
    'storage' => [
        'driver' => getenv('STORAGE_DRIVER') ?: 'local',
        'aws_key' => getenv('AWS_ACCESS_KEY_ID'),
        'aws_secret' => getenv('AWS_SECRET_ACCESS_KEY'),
        'aws_region' => getenv('AWS_REGION') ?: 'us-east-1',
        'aws_bucket' => getenv('AWS_BUCKET'),
        'cdn_url' => getenv('CDN_URL'),
    ],
    
    'payment' => [
        'stripe_key' => getenv('STRIPE_PUBLIC_KEY'),
        'stripe_secret' => getenv('STRIPE_SECRET_KEY'),
        'paypal_client_id' => getenv('PAYPAL_CLIENT_ID'),
        'paypal_secret' => getenv('PAYPAL_SECRET'),
        'paypal_mode' => getenv('PAYPAL_MODE') ?: 'sandbox',
    ],
    
    'mail' => [
        'driver' => getenv('MAIL_DRIVER') ?: 'smtp',
        'host' => getenv('MAIL_HOST'),
        'port' => getenv('MAIL_PORT') ?: 587,
        'username' => getenv('MAIL_USERNAME'),
        'password' => getenv('MAIL_PASSWORD'),
        'from_address' => getenv('MAIL_FROM_ADDRESS'),
        'from_name' => getenv('MAIL_FROM_NAME'),
    ],
    
    'security' => [
        'csrf_token_name' => '_csrf_token',
    ],
];