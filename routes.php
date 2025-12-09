<?php

/**
 * Routes principales de l'application
 * Toutes les URLs sont propres (sans .php)
 */

// ==================== PUBLIC ROUTES ====================

// Home
$router->get('/', 'HomeController@index', 'home');
$router->get('/recherche', 'SearchController@index', 'search');

// Products
$router->get('/produits', 'ProductController@index', 'products.index');
$router->get('/produit/{slug}', 'ProductController@show', 'product.show');
$router->get('/categorie/{slug}', 'CategoryController@show', 'category.show');

// Static pages
$router->get('/a-propos', 'PageController@about', 'about');
$router->get('/contact', 'PageController@contact', 'contact');
$router->post('/contact', 'PageController@contactSubmit', 'contact.submit');
$router->get('/faq', 'PageController@faq', 'faq');
$router->get('/conditions', 'PageController@terms', 'terms');
$router->get('/confidentialite', 'PageController@privacy', 'privacy');
$router->get('/politique-remboursement', 'PageController@refund', 'refund');

// Auth
$router->get('/connexion', 'AuthController@showLogin', 'login');
$router->post('/connexion', 'AuthController@login', 'login.post');
$router->get('/inscription', 'AuthController@showRegister', 'register');
$router->post('/inscription', 'AuthController@register', 'register.post');
$router->get('/deconnexion', 'AuthController@logout', 'logout');
$router->get('/mot-de-passe-oublie', 'AuthController@showForgotPassword', 'password.forgot');
$router->post('/mot-de-passe-oublie', 'AuthController@sendResetLink', 'password.email');
$router->get('/reinitialiser-mot-de-passe/{token}', 'AuthController@showResetPassword', 'password.reset');
$router->post('/reinitialiser-mot-de-passe', 'AuthController@resetPassword', 'password.update');
$router->get('/verifier-email/{token}', 'AuthController@verifyEmail', 'email.verify');

// Language switcher
$router->get('/langue/{locale}', 'LanguageController@switch', 'language.switch');

// ==================== AUTHENTICATED ROUTES ====================

// User account
$router->get('/compte', 'AccountController@dashboard', 'account.dashboard')
    ->middleware('Auth');
$router->get('/compte/achats', 'AccountController@purchases', 'account.purchases')
    ->middleware('Auth');
$router->get('/compte/telechargements', 'AccountController@downloads', 'account.downloads')
    ->middleware('Auth');
$router->get('/compte/parametres', 'AccountController@settings', 'account.settings')
    ->middleware('Auth');
$router->post('/compte/parametres', 'AccountController@updateSettings', 'account.settings.update')
    ->middleware('Auth');
$router->post('/compte/mot-de-passe', 'AccountController@updatePassword', 'account.password.update')
    ->middleware('Auth');
$router->post('/compte/avatar', 'AccountController@uploadAvatar', 'account.avatar.upload')
    ->middleware('Auth');

// 2FA
$router->get('/compte/2fa', 'TwoFactorController@show', 'account.2fa')
    ->middleware('Auth');
$router->post('/compte/2fa/activer', 'TwoFactorController@enable', 'account.2fa.enable')
    ->middleware('Auth');
$router->post('/compte/2fa/desactiver', 'TwoFactorController@disable', 'account.2fa.disable')
    ->middleware('Auth');

// Notifications
$router->get('/notifications', 'NotificationController@index', 'notifications.index')
    ->middleware('Auth');
$router->post('/notifications/{id}/lire', 'NotificationController@markAsRead', 'notifications.read')
    ->middleware('Auth');

// ==================== SELLER ROUTES ====================

// Seller onboarding
$router->get('/vendre', 'SellerController@onboarding', 'seller.onboarding');
$router->post('/vendre/devenir-vendeur', 'SellerController@become', 'seller.become')
    ->middleware('Auth');

// Seller dashboard
$router->get('/vendeur/tableau-de-bord', 'SellerController@dashboard', 'seller.dashboard')
    ->middleware(['Auth', 'Seller']);
$router->get('/vendeur/statistiques', 'SellerController@statistics', 'seller.statistics')
    ->middleware(['Auth', 'Seller']);

// Products management
$router->get('/vendeur/produits', 'SellerProductController@index', 'seller.products')
    ->middleware(['Auth', 'Seller']);
$router->get('/vendeur/produits/nouveau', 'SellerProductController@create', 'seller.products.create')
    ->middleware(['Auth', 'Seller']);
$router->post('/vendeur/produits', 'SellerProductController@store', 'seller.products.store')
    ->middleware(['Auth', 'Seller']);
$router->get('/vendeur/produits/{id}/modifier', 'SellerProductController@edit', 'seller.products.edit')
    ->middleware(['Auth', 'Seller']);
$router->post('/vendeur/produits/{id}', 'SellerProductController@update', 'seller.products.update')
    ->middleware(['Auth', 'Seller']);
$router->post('/vendeur/produits/{id}/supprimer', 'SellerProductController@destroy', 'seller.products.destroy')
    ->middleware(['Auth', 'Seller']);

// File upload
$router->post('/vendeur/upload-url', 'UploadController@getSignedUrl', 'upload.signed-url')
    ->middleware(['Auth', 'Seller']);
$router->post('/vendeur/upload-complete', 'UploadController@complete', 'upload.complete')
    ->middleware(['Auth', 'Seller']);

// Orders management
$router->get('/vendeur/commandes', 'SellerOrderController@index', 'seller.orders')
    ->middleware(['Auth', 'Seller']);
$router->get('/vendeur/commandes/{id}', 'SellerOrderController@show', 'seller.orders.show')
    ->middleware(['Auth', 'Seller']);

// Payouts
$router->get('/vendeur/paiements', 'PayoutController@index', 'seller.payouts')
    ->middleware(['Auth', 'Seller']);
$router->post('/vendeur/paiements/demander', 'PayoutController@request', 'seller.payouts.request')
    ->middleware(['Auth', 'Seller']);
$router->get('/vendeur/paiements/configurer', 'PayoutController@setupMethod', 'seller.payouts.setup')
    ->middleware(['Auth', 'Seller']);
$router->post('/vendeur/paiements/configurer', 'PayoutController@saveMethod', 'seller.payouts.save')
    ->middleware(['Auth', 'Seller']);

// Reviews
$router->get('/vendeur/avis', 'SellerReviewController@index', 'seller.reviews')
    ->middleware(['Auth', 'Seller']);

// ==================== CHECKOUT & PAYMENT ====================

$router->post('/panier/ajouter', 'CartController@add', 'cart.add');
$router->get('/panier', 'CartController@index', 'cart.index');
$router->post('/panier/supprimer', 'CartController@remove', 'cart.remove');

$router->get('/commander/{slug}', 'CheckoutController@show', 'checkout.show');
$router->post('/commander/creer', 'CheckoutController@create', 'checkout.create')
    ->middleware('Auth');
$router->post('/commander/stripe', 'CheckoutController@processStripe', 'checkout.stripe')
    ->middleware('Auth');
$router->post('/commander/paypal', 'CheckoutController@processPaypal', 'checkout.paypal')
    ->middleware('Auth');
$router->get('/commande/succes/{orderNumber}', 'CheckoutController@success', 'checkout.success')
    ->middleware('Auth');
$router->get('/commande/annulee', 'CheckoutController@cancelled', 'checkout.cancelled');

// Webhooks (no auth - verified by signature)
$router->post('/webhooks/stripe', 'WebhookController@stripe', 'webhooks.stripe');
$router->post('/webhooks/paypal', 'WebhookController@paypal', 'webhooks.paypal');

// Download
$router->get('/telecharger/{token}', 'DownloadController@download', 'download')
    ->middleware('Auth');
$router->get('/telecharger/{token}/stream', 'DownloadController@stream', 'download.stream')
    ->middleware('Auth');

// Reviews
$router->post('/produit/{id}/avis', 'ReviewController@store', 'review.store')
    ->middleware('Auth');

// ==================== ADMIN ROUTES ====================

$router->get('/admin', 'Admin\DashboardController@index', 'admin.dashboard')
    ->middleware(['Auth', 'Admin']);

// Users
$router->get('/admin/utilisateurs', 'Admin\UserController@index', 'admin.users')
    ->middleware(['Auth', 'Admin']);
$router->get('/admin/utilisateurs/{id}', 'Admin\UserController@show', 'admin.users.show')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/utilisateurs/{id}/suspendre', 'Admin\UserController@suspend', 'admin.users.suspend')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/utilisateurs/{id}/activer', 'Admin\UserController@activate', 'admin.users.activate')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/utilisateurs/{id}/role', 'Admin\UserController@updateRole', 'admin.users.role')
    ->middleware(['Auth', 'Admin']);

// Products
$router->get('/admin/produits', 'Admin\ProductController@index', 'admin.products')
    ->middleware(['Auth', 'Admin']);
$router->get('/admin/produits/{id}', 'Admin\ProductController@show', 'admin.products.show')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/produits/{id}/approuver', 'Admin\ProductController@approve', 'admin.products.approve')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/produits/{id}/rejeter', 'Admin\ProductController@reject', 'admin.products.reject')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/produits/{id}/featured', 'Admin\ProductController@toggleFeatured', 'admin.products.featured')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/produits/{id}/supprimer', 'Admin\ProductController@destroy', 'admin.products.destroy')
    ->middleware(['Auth', 'Admin']);

// Orders
$router->get('/admin/commandes', 'Admin\OrderController@index', 'admin.orders')
    ->middleware(['Auth', 'Admin']);
$router->get('/admin/commandes/{id}', 'Admin\OrderController@show', 'admin.orders.show')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/commandes/{id}/rembourser', 'Admin\OrderController@refund', 'admin.orders.refund')
    ->middleware(['Auth', 'Admin']);

// Payouts
$router->get('/admin/paiements', 'Admin\PayoutController@index', 'admin.payouts')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/paiements/{id}/approuver', 'Admin\PayoutController@approve', 'admin.payouts.approve')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/paiements/{id}/rejeter', 'Admin\PayoutController@reject', 'admin.payouts.reject')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/paiements/traiter', 'Admin\PayoutController@processBatch', 'admin.payouts.batch')
    ->middleware(['Auth', 'Admin']);

// Categories
$router->get('/admin/categories', 'Admin\CategoryController@index', 'admin.categories')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/categories', 'Admin\CategoryController@store', 'admin.categories.store')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/categories/{id}', 'Admin\CategoryController@update', 'admin.categories.update')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/categories/{id}/supprimer', 'Admin\CategoryController@destroy', 'admin.categories.destroy')
    ->middleware(['Auth', 'Admin']);

// Coupons
$router->get('/admin/coupons', 'Admin\CouponController@index', 'admin.coupons')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/coupons', 'Admin\CouponController@store', 'admin.coupons.store')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/coupons/{id}', 'Admin\CouponController@update', 'admin.coupons.update')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/coupons/{id}/supprimer', 'Admin\CouponController@destroy', 'admin.coupons.destroy')
    ->middleware(['Auth', 'Admin']);

// Reviews
$router->get('/admin/avis', 'Admin\ReviewController@index', 'admin.reviews')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/avis/{id}/approuver', 'Admin\ReviewController@approve', 'admin.reviews.approve')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/avis/{id}/rejeter', 'Admin\ReviewController@reject', 'admin.reviews.reject')
    ->middleware(['Auth', 'Admin']);

// Settings
$router->get('/admin/parametres', 'Admin\SettingsController@index', 'admin.settings')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/parametres', 'Admin\SettingsController@update', 'admin.settings.update')
    ->middleware(['Auth', 'Admin']);
$router->post('/admin/maintenance', 'Admin\SettingsController@toggleMaintenance', 'admin.maintenance')
    ->middleware(['Auth', 'Admin']);

// Reports
$router->get('/admin/rapports', 'Admin\ReportController@index', 'admin.reports')
    ->middleware(['Auth', 'Admin']);
$router->get('/admin/rapports/ventes', 'Admin\ReportController@sales', 'admin.reports.sales')
    ->middleware(['Auth', 'Admin']);
$router->get('/admin/rapports/revenus', 'Admin\ReportController@revenue', 'admin.reports.revenue')
    ->middleware(['Auth', 'Admin']);
$router->get('/admin/rapports/export', 'Admin\ReportController@export', 'admin.reports.export')
    ->middleware(['Auth', 'Admin']);

// Activity logs
$router->get('/admin/logs', 'Admin\LogController@index', 'admin.logs')
    ->middleware(['Auth', 'Admin']);

// ==================== API ROUTES ====================

$router->get('/api/products', 'Api\ProductController@index', 'api.products');
$router->get('/api/products/{id}', 'Api\ProductController@show', 'api.products.show');
$router->get('/api/categories', 'Api\CategoryController@index', 'api.categories');
$router->post('/api/coupon/validate', 'Api\CouponController@validate', 'api.coupon.validate');

// API with auth
$router->get('/api/account', 'Api\AccountController@show', 'api.account')
    ->middleware(['Auth', 'ApiAuth']);
$router->get('/api/orders', 'Api\OrderController@index', 'api.orders')
    ->middleware(['Auth', 'ApiAuth']);
