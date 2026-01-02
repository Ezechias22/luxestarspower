<?php

/**
 * Routes principales de l'application
 */

// ==================== SEO & STATIC ROUTES ====================

$router->get('/robots.txt', function() {
    header('Content-Type: text/plain; charset=UTF-8');
    if (file_exists(__DIR__ . '/../public/robots.txt')) {
        readfile(__DIR__ . '/../public/robots.txt');
    } else {
        echo "User-agent: *\nAllow: /\n";
    }
    exit;
});

$router->get('/sitemap.xml', function() {
    header('Location: /sitemap.php', true, 301);
    exit;
});

// ==================== PUBLIC ROUTES ====================

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

// Pricing & Subscriptions
$router->get('/tarifs', 'SubscriptionController@pricing', 'pricing');
$router->get('/abonnements', 'SubscriptionController@pricing', 'subscriptions.pricing'); // Alias

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

// Boutiques vendeurs
$router->get('/boutique/{slug}', 'ShopController@show', 'shop.show');

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

// ==================== SUBSCRIPTION ROUTES ====================

// Subscription management (authenticated)
$router->get('/abonnement', 'SubscriptionController@current', 'subscription.current');
$router->post('/abonnement/essai', 'SubscriptionController@startTrial', 'subscription.trial');
$router->get('/abonnement/paiement/{plan}', 'SubscriptionController@checkout', 'subscription.checkout');
$router->post('/abonnement/paiement', 'SubscriptionController@processPayment', 'subscription.payment');
$router->post('/abonnement/annuler', 'SubscriptionController@cancel', 'subscription.cancel');
$router->post('/abonnement/reactiver', 'SubscriptionController@resume', 'subscription.resume');
$router->post('/abonnement/changer/{plan}', 'SubscriptionController@change', 'subscription.change');
$router->get('/abonnement/factures', 'SubscriptionController@invoices', 'subscription.invoices');
$router->get('/abonnement/facture/{id}', 'SubscriptionController@downloadInvoice', 'subscription.invoice');

// Subscription webhook (Stripe)
$router->post('/webhooks/subscription', 'WebhookController@subscription', 'webhooks.subscription');

// ==================== CART ROUTES ====================

$router->get('/panier', 'CartController@index', 'cart.index');
$router->post('/panier/ajouter', 'CartController@add', 'cart.add');
$router->post('/panier/supprimer/{id}', 'CartController@remove', 'cart.remove');
$router->post('/panier/quantite', 'CartController@updateQuantity', 'cart.quantity');

// ==================== SELLER ROUTES ====================

// Seller onboarding
$router->get('/vendre', 'SellerController@onboarding', 'seller.onboarding');
$router->post('/vendre/devenir-vendeur', 'SellerController@become', 'seller.become');

// Seller dashboard et stats (ROUTES SPÉCIFIQUES EN PREMIER)
$router->get('/vendeur/tableau-de-bord', 'SellerController@dashboard', 'seller.dashboard');
$router->get('/vendeur/statistiques', 'SellerController@statistics', 'seller.statistics');
$router->get('/vendeur/boutique', 'SellerController@shopSettings', 'seller.shop.settings');
$router->post('/vendeur/boutique', 'SellerController@updateShop', 'seller.shop.update');

// Seller subscription management
$router->get('/vendeur/abonnement', 'SubscriptionController@sellerSubscription', 'seller.subscription');
$router->post('/vendeur/abonnement/upgrade', 'SubscriptionController@upgrade', 'seller.subscription.upgrade');

// Seller settings (ROUTES SPÉCIFIQUES)
$router->get('/vendeur/parametres', 'SellerController@settings', 'seller.settings');
$router->post('/vendeur/parametres/profil', 'SellerController@updateProfile', 'seller.settings.profile');
$router->post('/vendeur/parametres/boutique', 'SellerController@updateShopInfo', 'seller.settings.shop');
$router->post('/vendeur/parametres/reseaux-sociaux', 'SellerController@updateSocialLinks', 'seller.settings.social');
$router->post('/vendeur/parametres/images', 'SellerController@uploadShopImages', 'seller.settings.images');
$router->post('/vendeur/parametres/mot-de-passe', 'SellerController@updatePassword', 'seller.settings.password');

// Products management (ROUTES TRÈS SPÉCIFIQUES EN PREMIER)
$router->get('/vendeur/produits/nouveau', 'SellerProductController@create', 'seller.products.create');
$router->post('/vendeur/produits', 'SellerProductController@store', 'seller.products.store');
$router->get('/vendeur/produits/{id}/modifier', 'SellerProductController@edit', 'seller.products.edit');
$router->post('/vendeur/produits/{id}/modifier', 'SellerProductController@update', 'seller.products.update');
$router->post('/vendeur/produits/{id}/supprimer', 'SellerProductController@destroy', 'seller.products.destroy');
$router->get('/vendeur/produits', 'SellerProductController@index', 'seller.products');

// File upload
$router->post('/vendeur/upload-url', 'UploadController@getSignedUrl', 'upload.signed-url');
$router->post('/vendeur/upload-complete', 'UploadController@complete', 'upload.complete');

// Orders management (ROUTES SPÉCIFIQUES EN PREMIER)
$router->get('/vendeur/commandes/{id}', 'SellerOrderController@show', 'seller.orders.show');
$router->get('/vendeur/commandes', 'SellerOrderController@index', 'seller.orders');

// Payouts (ROUTES SPÉCIFIQUES EN PREMIER)
$router->get('/vendeur/paiements/configurer', 'PayoutController@setupMethod', 'seller.payouts.setup');
$router->post('/vendeur/paiements/configurer', 'PayoutController@saveMethod', 'seller.payouts.save');
$router->post('/vendeur/paiements/demander', 'PayoutController@request', 'seller.payouts.request');
$router->get('/vendeur/paiements', 'PayoutController@index', 'seller.payouts');

// Reviews
$router->get('/vendeur/avis', 'SellerReviewController@index', 'seller.reviews');

// ==================== CHECKOUT & PAYMENT ====================

$router->get('/checkout', 'CheckoutController@show', 'checkout.show');
$router->post('/checkout/stripe', 'CheckoutController@processStripe', 'checkout.stripe');
$router->post('/checkout/paypal', 'CheckoutController@processPaypal', 'checkout.paypal');
$router->get('/checkout/success', 'CheckoutController@success', 'checkout.success');
$router->get('/checkout/cancelled', 'CheckoutController@cancelled', 'checkout.cancelled');

// Webhooks
$router->post('/webhooks/stripe', 'WebhookController@stripe', 'webhooks.stripe');
$router->post('/webhooks/paypal', 'WebhookController@paypal', 'webhooks.paypal');

// Download
$router->get('/telecharger/{token}', 'DownloadController@download', 'download');
$router->get('/telecharger/{token}/stream', 'DownloadController@stream', 'download.stream');
$router->get('/telecharger/produit/{id}', 'DownloadController@downloadProduct', 'download.product');

// Reviews
$router->post('/produit/{id}/avis', 'ReviewController@store', 'review.store');

// ==================== ADMIN ROUTES ====================

$router->get('/admin', 'Admin\DashboardController@index', 'admin.dashboard');

// Users
$router->get('/admin/utilisateurs/{id}', 'Admin\UserController@show', 'admin.users.show');
$router->post('/admin/utilisateurs/{id}/suspendre', 'Admin\UserController@suspend', 'admin.users.suspend');
$router->post('/admin/utilisateurs/{id}/activer', 'Admin\UserController@activate', 'admin.users.activate');
$router->post('/admin/utilisateurs/{id}/role', 'Admin\UserController@updateRole', 'admin.users.role');
$router->get('/admin/utilisateurs', 'Admin\UserController@index', 'admin.users');

// Products
$router->get('/admin/produits/{id}', 'Admin\ProductController@show', 'admin.products.show');
$router->post('/admin/produits/{id}/approuver', 'Admin\ProductController@approve', 'admin.products.approve');
$router->post('/admin/produits/{id}/rejeter', 'Admin\ProductController@reject', 'admin.products.reject');
$router->post('/admin/produits/{id}/featured', 'Admin\ProductController@toggleFeatured', 'admin.products.featured');
$router->post('/admin/produits/{id}/supprimer', 'Admin\ProductController@destroy', 'admin.products.destroy');
$router->get('/admin/produits', 'Admin\ProductController@index', 'admin.products');

// Orders
$router->get('/admin/commandes/{id}', 'Admin\OrderController@show', 'admin.orders.show');
$router->post('/admin/commandes/{id}/rembourser', 'Admin\OrderController@refund', 'admin.orders.refund');
$router->get('/admin/commandes', 'Admin\OrderController@index', 'admin.orders');

// Payouts
$router->post('/admin/paiements/{id}/approuver', 'Admin\PayoutController@approve', 'admin.payouts.approve');
$router->post('/admin/paiements/{id}/rejeter', 'Admin\PayoutController@reject', 'admin.payouts.reject');
$router->post('/admin/paiements/traiter', 'Admin\PayoutController@processBatch', 'admin.payouts.batch');
$router->get('/admin/paiements', 'Admin\PayoutController@index', 'admin.payouts');

// Subscriptions Admin
$router->get('/admin/abonnements', 'Admin\SubscriptionController@index', 'admin.subscriptions');
$router->get('/admin/abonnements/{id}', 'Admin\SubscriptionController@show', 'admin.subscriptions.show');
$router->post('/admin/abonnements/{id}/annuler', 'Admin\SubscriptionController@cancel', 'admin.subscriptions.cancel');
$router->get('/admin/abonnements/statistiques', 'Admin\SubscriptionController@stats', 'admin.subscriptions.stats');

// Subscription Plans Admin
$router->get('/admin/plans', 'Admin\PlanController@index', 'admin.plans');
$router->post('/admin/plans', 'Admin\PlanController@store', 'admin.plans.store');
$router->post('/admin/plans/{id}', 'Admin\PlanController@update', 'admin.plans.update');
$router->post('/admin/plans/{id}/toggle', 'Admin\PlanController@toggleActive', 'admin.plans.toggle');

// Categories
$router->post('/admin/categories/{id}', 'Admin\CategoryController@update', 'admin.categories.update');
$router->post('/admin/categories/{id}/supprimer', 'Admin\CategoryController@destroy', 'admin.categories.destroy');
$router->post('/admin/categories', 'Admin\CategoryController@store', 'admin.categories.store');
$router->get('/admin/categories', 'Admin\CategoryController@index', 'admin.categories');

// Coupons
$router->post('/admin/coupons/{id}', 'Admin\CouponController@update', 'admin.coupons.update');
$router->post('/admin/coupons/{id}/supprimer', 'Admin\CouponController@destroy', 'admin.coupons.destroy');
$router->post('/admin/coupons', 'Admin\CouponController@store', 'admin.coupons.store');
$router->get('/admin/coupons', 'Admin\CouponController@index', 'admin.coupons');

// Reviews
$router->post('/admin/avis/{id}/approuver', 'Admin\ReviewController@approve', 'admin.reviews.approve');
$router->post('/admin/avis/{id}/rejeter', 'Admin\ReviewController@reject', 'admin.reviews.reject');
$router->get('/admin/avis', 'Admin\ReviewController@index', 'admin.reviews');

// Settings
$router->post('/admin/parametres', 'Admin\SettingsController@update', 'admin.settings.update');
$router->post('/admin/maintenance', 'Admin\SettingsController@toggleMaintenance', 'admin.maintenance');
$router->get('/admin/parametres', 'Admin\SettingsController@index', 'admin.settings');

// Reports
$router->get('/admin/rapports/ventes', 'Admin\ReportController@sales', 'admin.reports.sales');
$router->get('/admin/rapports/revenus', 'Admin\ReportController@revenue', 'admin.reports.revenue');
$router->get('/admin/rapports/abonnements', 'Admin\ReportController@subscriptions', 'admin.reports.subscriptions');
$router->get('/admin/rapports/export', 'Admin\ReportController@export', 'admin.reports.export');
$router->get('/admin/rapports', 'Admin\ReportController@index', 'admin.reports');

// Activity logs
$router->get('/admin/logs', 'Admin\LogController@index', 'admin.logs');

// ==================== API ROUTES ====================

$router->get('/api/products/{id}', 'Api\ProductController@show', 'api.products.show');
$router->get('/api/products', 'Api\ProductController@index', 'api.products');
$router->get('/api/categories', 'Api\CategoryController@index', 'api.categories');
$router->post('/api/coupon/validate', 'Api\CouponController@validate', 'api.coupon.validate');

// API Subscriptions
$router->get('/api/plans', 'Api\SubscriptionController@plans', 'api.plans');
$router->get('/api/subscription/current', 'Api\SubscriptionController@current', 'api.subscription.current');

// API with auth
$router->get('/api/account', 'Api\AccountController@show', 'api.account');
$router->get('/api/orders', 'Api\OrderController@index', 'api.orders');