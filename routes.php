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
$router->get('/compte', 'AccountController@dashboard', 'account.dashboard');
$router->get('/compte/achats', 'AccountController@purchases', 'account.purchases');
$router->get('/compte/telechargements', 'AccountController@downloads', 'account.downloads');
$router->get('/compte/parametres', 'AccountController@settings', 'account.settings');
$router->post('/compte/parametres', 'AccountController@updateSettings', 'account.settings.update');
$router->post('/compte/mot-de-passe', 'AccountController@updatePassword', 'account.password.update');
$router->post('/compte/avatar', 'AccountController@uploadAvatar', 'account.avatar.upload');

// 2FA
$router->get('/compte/2fa', 'TwoFactorController@show', 'account.2fa');
$router->post('/compte/2fa/activer', 'TwoFactorController@enable', 'account.2fa.enable');
$router->post('/compte/2fa/desactiver', 'TwoFactorController@disable', 'account.2fa.disable');

// Notifications
$router->get('/notifications', 'NotificationController@index', 'notifications.index');
$router->post('/notifications/{id}/lire', 'NotificationController@markAsRead', 'notifications.read');

// ==================== CART ROUTES ====================

$router->get('/panier', 'CartController@index', 'cart.index');
$router->post('/panier/ajouter', 'CartController@add', 'cart.add');
$router->post('/panier/supprimer/{id}', 'CartController@remove', 'cart.remove');
$router->post('/panier/quantite', 'CartController@updateQuantity', 'cart.quantity');

// ==================== SELLER ROUTES ====================

// Seller onboarding
$router->get('/vendre', 'SellerController@onboarding', 'seller.onboarding');
$router->post('/vendre/devenir-vendeur', 'SellerController@become', 'seller.become');

// Seller dashboard
$router->get('/vendeur/tableau-de-bord', 'SellerController@dashboard', 'seller.dashboard');
$router->get('/vendeur/statistiques', 'SellerController@statistics', 'seller.statistics');

// Products management
$router->get('/vendeur/produits', 'SellerProductController@index', 'seller.products');
$router->get('/vendeur/produits/nouveau', 'SellerProductController@create', 'seller.products.create');
$router->post('/vendeur/produits', 'SellerProductController@store', 'seller.products.store');
$router->get('/vendeur/produits/{id}/modifier', 'SellerProductController@edit', 'seller.products.edit');
$router->post('/vendeur/produits/{id}/modifier', 'SellerProductController@update', 'seller.products.update');
$router->post('/vendeur/produits/{id}/supprimer', 'SellerProductController@destroy', 'seller.products.destroy');

// File upload
$router->post('/vendeur/upload-url', 'UploadController@getSignedUrl', 'upload.signed-url');
$router->post('/vendeur/upload-complete', 'UploadController@complete', 'upload.complete');

// Orders management
$router->get('/vendeur/commandes', 'SellerOrderController@index', 'seller.orders');
$router->get('/vendeur/commandes/{id}', 'SellerOrderController@show', 'seller.orders.show');

// Payouts
$router->get('/vendeur/paiements', 'PayoutController@index', 'seller.payouts');
$router->post('/vendeur/paiements/demander', 'PayoutController@request', 'seller.payouts.request');
$router->get('/vendeur/paiements/configurer', 'PayoutController@setupMethod', 'seller.payouts.setup');
$router->post('/vendeur/paiements/configurer', 'PayoutController@saveMethod', 'seller.payouts.save');

// Reviews
$router->get('/vendeur/avis', 'SellerReviewController@index', 'seller.reviews');

// ==================== CHECKOUT & PAYMENT ====================

$router->get('/checkout', 'CheckoutController@show', 'checkout.show');
$router->post('/checkout/stripe', 'CheckoutController@processStripe', 'checkout.stripe');
$router->post('/checkout/paypal', 'CheckoutController@processPaypal', 'checkout.paypal');
$router->get('/checkout/success', 'CheckoutController@success', 'checkout.success');
$router->get('/checkout/cancelled', 'CheckoutController@cancelled', 'checkout.cancelled');

// Webhooks (no auth - verified by signature)
$router->post('/webhooks/stripe', 'WebhookController@stripe', 'webhooks.stripe');
$router->post('/webhooks/paypal', 'WebhookController@paypal', 'webhooks.paypal');

// Download
$router->get('/telecharger/{token}', 'DownloadController@download', 'download');
$router->get('/telecharger/{token}/stream', 'DownloadController@stream', 'download.stream');

// Reviews
$router->post('/produit/{id}/avis', 'ReviewController@store', 'review.store');

// ==================== ADMIN ROUTES ====================

$router->get('/admin', 'Admin\DashboardController@index', 'admin.dashboard');

// Users
$router->get('/admin/utilisateurs', 'Admin\UserController@index', 'admin.users');
$router->get('/admin/utilisateurs/{id}', 'Admin\UserController@show', 'admin.users.show');
$router->post('/admin/utilisateurs/{id}/suspendre', 'Admin\UserController@suspend', 'admin.users.suspend');
$router->post('/admin/utilisateurs/{id}/activer', 'Admin\UserController@activate', 'admin.users.activate');
$router->post('/admin/utilisateurs/{id}/role', 'Admin\UserController@updateRole', 'admin.users.role');

// Products
$router->get('/admin/produits', 'Admin\ProductController@index', 'admin.products');
$router->get('/admin/produits/{id}', 'Admin\ProductController@show', 'admin.products.show');
$router->post('/admin/produits/{id}/approuver', 'Admin\ProductController@approve', 'admin.products.approve');
$router->post('/admin/produits/{id}/rejeter', 'Admin\ProductController@reject', 'admin.products.reject');
$router->post('/admin/produits/{id}/featured', 'Admin\ProductController@toggleFeatured', 'admin.products.featured');
$router->post('/admin/produits/{id}/supprimer', 'Admin\ProductController@destroy', 'admin.products.destroy');

// Orders
$router->get('/admin/commandes', 'Admin\OrderController@index', 'admin.orders');
$router->get('/admin/commandes/{id}', 'Admin\OrderController@show', 'admin.orders.show');
$router->post('/admin/commandes/{id}/rembourser', 'Admin\OrderController@refund', 'admin.orders.refund');

// Payouts
$router->get('/admin/paiements', 'Admin\PayoutController@index', 'admin.payouts');
$router->post('/admin/paiements/{id}/approuver', 'Admin\PayoutController@approve', 'admin.payouts.approve');
$router->post('/admin/paiements/{id}/rejeter', 'Admin\PayoutController@reject', 'admin.payouts.reject');
$router->post('/admin/paiements/traiter', 'Admin\PayoutController@processBatch', 'admin.payouts.batch');

// Categories
$router->get('/admin/categories', 'Admin\CategoryController@index', 'admin.categories');
$router->post('/admin/categories', 'Admin\CategoryController@store', 'admin.categories.store');
$router->post('/admin/categories/{id}', 'Admin\CategoryController@update', 'admin.categories.update');
$router->post('/admin/categories/{id}/supprimer', 'Admin\CategoryController@destroy', 'admin.categories.destroy');

// Coupons
$router->get('/admin/coupons', 'Admin\CouponController@index', 'admin.coupons');
$router->post('/admin/coupons', 'Admin\CouponController@store', 'admin.coupons.store');
$router->post('/admin/coupons/{id}', 'Admin\CouponController@update', 'admin.coupons.update');
$router->post('/admin/coupons/{id}/supprimer', 'Admin\CouponController@destroy', 'admin.coupons.destroy');

// Reviews
$router->get('/admin/avis', 'Admin\ReviewController@index', 'admin.reviews');
$router->post('/admin/avis/{id}/approuver', 'Admin\ReviewController@approve', 'admin.reviews.approve');
$router->post('/admin/avis/{id}/rejeter', 'Admin\ReviewController@reject', 'admin.reviews.reject');

// Settings
$router->get('/admin/parametres', 'Admin\SettingsController@index', 'admin.settings');
$router->post('/admin/parametres', 'Admin\SettingsController@update', 'admin.settings.update');
$router->post('/admin/maintenance', 'Admin\SettingsController@toggleMaintenance', 'admin.maintenance');

// Reports
$router->get('/admin/rapports', 'Admin\ReportController@index', 'admin.reports');
$router->get('/admin/rapports/ventes', 'Admin\ReportController@sales', 'admin.reports.sales');
$router->get('/admin/rapports/revenus', 'Admin\ReportController@revenue', 'admin.reports.revenue');
$router->get('/admin/rapports/export', 'Admin\ReportController@export', 'admin.reports.export');

// Activity logs
$router->get('/admin/logs', 'Admin\LogController@index', 'admin.logs');

// ==================== API ROUTES ====================

$router->get('/api/products', 'Api\ProductController@index', 'api.products');
$router->get('/api/products/{id}', 'Api\ProductController@show', 'api.products.show');
$router->get('/api/categories', 'Api\CategoryController@index', 'api.categories');
$router->post('/api/coupon/validate', 'Api\CouponController@validate', 'api.coupon.validate');

// API with auth
$router->get('/api/account', 'Api\AccountController@show', 'api.account');
$router->get('/api/orders', 'Api\OrderController@index', 'api.orders');