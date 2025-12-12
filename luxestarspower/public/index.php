<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

$config = require __DIR__ . '/../config/config.php';

if (isset($_SESSION['locale'])) {
    \App\I18n::setLocale($_SESSION['locale']);
} elseif (isset($_GET['lang']) && in_array($_GET['lang'], $config['locales']['available'])) {
    \App\I18n::setLocale($_GET['lang']);
} else {
    \App\I18n::setLocale($config['locales']['default']);
}
\App\I18n::load('main');

$router = new \App\Router();

// Public routes
$router->get('/', 'HomeController@index', 'home');
$router->get('/produits', 'ProductController@index', 'products');
$router->get('/produit/{slug}', 'ProductController@show', 'product.show');
$router->get('/recherche', 'SearchController@index', 'search');

// Auth routes
$router->get('/login', 'AuthController@loginForm', 'login');
$router->post('/login', 'AuthController@login', 'login.post');
$router->get('/register', 'AuthController@registerForm', 'register');
$router->post('/register', 'AuthController@register', 'register.post');
$router->get('/logout', 'AuthController@logout', 'logout');

// Account routes
$router->get('/compte', 'AccountController@dashboard', 'account');
$router->get('/compte/achats', 'AccountController@purchases', 'account.purchases');
$router->get('/compte/telechargements', 'AccountController@downloads', 'account.downloads');
$router->get('/compte/parametres', 'AccountController@settings', 'account.settings');
$router->post('/compte/parametres', 'AccountController@updateSettings', 'account.settings.update');

// Seller routes
$router->get('/vendre', 'SellerController@onboarding', 'seller.onboarding');
$router->post('/vendre', 'SellerController@becomeS eller', 'seller.become');
$router->get('/vendeur/produits', 'Seller\ProductController@index', 'seller.products');
$router->get('/vendeur/produit/nouveau', 'Seller\ProductController@create', 'seller.product.create');
$router->post('/vendeur/produit/nouveau', 'Seller\ProductController@store', 'seller.product.store');
$router->get('/vendeur/produit/{id}/edit', 'Seller\ProductController@edit', 'seller.product.edit');
$router->post('/vendeur/produit/{id}/edit', 'Seller\ProductController@update', 'seller.product.update');
$router->get('/vendeur/commandes', 'Seller\OrderController@index', 'seller.orders');
$router->get('/vendeur/payouts', 'Seller\PayoutController@index', 'seller.payouts');
$router->get('/vendeur/dashboard', 'Seller\DashboardController@index', 'seller.dashboard');

// Checkout & Payment
$router->post('/checkout', 'CheckoutController@create', 'checkout');
$router->post('/checkout/complete', 'CheckoutController@complete', 'checkout.complete');
$router->get('/download/{token}', 'DownloadController@serve', 'download');

// Webhooks
$router->post('/webhooks/stripe', 'WebhookController@stripe', 'webhook.stripe');
$router->post('/webhooks/paypal', 'WebhookController@paypal', 'webhook.paypal');

// Admin routes
$router->get('/admin/login', 'Admin\AuthController@loginForm', 'admin.login');
$router->post('/admin/login', 'Admin\AuthController@login', 'admin.login.post');
$router->get('/admin/dashboard', 'Admin\DashboardController@index', 'admin.dashboard');
$router->get('/admin/users', 'Admin\UserController@index', 'admin.users');
$router->get('/admin/products', 'Admin\ProductController@index', 'admin.products');
$router->post('/admin/product/{id}/toggle', 'Admin\ProductController@toggle', 'admin.product.toggle');
$router->post('/admin/product/{id}/feature', 'Admin\ProductController@feature', 'admin.product.feature');
$router->get('/admin/orders', 'Admin\OrderController@index', 'admin.orders');
$router->post('/admin/order/{id}/refund', 'Admin\OrderController@refund', 'admin.order.refund');
$router->get('/admin/payouts', 'Admin\PayoutController@index', 'admin.payouts');
$router->post('/admin/payout/{id}/process', 'Admin\PayoutController@process', 'admin.payout.process');
$router->get('/admin/settings', 'Admin\SettingsController@index', 'admin.settings');
$router->post('/admin/settings', 'Admin\SettingsController@update', 'admin.settings.update');

try {
    echo $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (\Exception $e) {
    if ($config['app']['debug']) {
        echo "<pre>Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
    } else {
        http_response_code(500);
        require __DIR__ . '/../views/errors/500.php';
    }
}
