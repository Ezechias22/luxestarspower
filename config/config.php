<?php

return [
    'app' => [
        'name' => $_ENV['APP_NAME'] ?? 'LuxeStarsPower',
        'env' => $_ENV['APP_ENV'] ?? 'production',
        'debug' => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
        'url' => $_ENV['APP_URL'] ?? 'https://www.luxestarspower.com',
        'locale' => 'fr',
        'supported_locales' => ['fr', 'en'],
        'default_currency' => 'USD',
    ],
    
    'locales' => [
        'default' => 'fr',
        'available' => ['fr', 'en', 'es', 'de', 'it'],
    ],
    
    'db' => [
        'host' => $_ENV['DB_HOST'] ?? null,
        'port' => $_ENV['DB_PORT'] ?? 3306,
        'name' => $_ENV['DB_NAME'] ?? null,
        'user' => $_ENV['DB_USER'] ?? null,
        'pass' => $_ENV['DB_PASS'] ?? null,
        'charset' => 'utf8mb4',
    ],
    
    'cloudinary' => [
        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'] ?? null,
        'api_key' => $_ENV['CLOUDINARY_API_KEY'] ?? null,
        'api_secret' => $_ENV['CLOUDINARY_API_SECRET'] ?? null,
    ],
    
    'storage' => [
        'driver' => $_ENV['STORAGE_DRIVER'] ?? 'local',
        'aws_key' => $_ENV['AWS_ACCESS_KEY_ID'] ?? null,
        'aws_secret' => $_ENV['AWS_SECRET_ACCESS_KEY'] ?? null,
        'aws_region' => $_ENV['AWS_REGION'] ?? 'us-east-1',
        'aws_bucket' => $_ENV['AWS_BUCKET'] ?? null,
        'cdn_url' => $_ENV['CDN_URL'] ?? null,
    ],
    
    'payment' => [
        'stripe_key' => $_ENV['STRIPE_PUBLIC_KEY'] ?? null,
        'stripe_secret' => $_ENV['STRIPE_SECRET_KEY'] ?? null,
        'paypal_client_id' => $_ENV['PAYPAL_CLIENT_ID'] ?? null,
        'paypal_secret' => $_ENV['PAYPAL_SECRET'] ?? null,
        'paypal_mode' => $_ENV['PAYPAL_MODE'] ?? 'sandbox',
    ],
    
    'mail' => [
        'driver' => $_ENV['MAIL_DRIVER'] ?? 'smtp',
        'host' => $_ENV['MAIL_HOST'] ?? null,
        'port' => $_ENV['MAIL_PORT'] ?? 587,
        'username' => $_ENV['MAIL_USERNAME'] ?? null,
        'password' => $_ENV['MAIL_PASSWORD'] ?? null,
        'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? null,
        'from_name' => $_ENV['MAIL_FROM_NAME'] ?? null,
    ],
    
    'security' => [
        'csrf_token_name' => '_csrf_token',
    ],
];