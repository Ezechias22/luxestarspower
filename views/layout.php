<?php \App\I18n::init(); ?>
<!DOCTYPE html>
<html lang="<?php echo \App\I18n::getLocale(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-2LFWSK2TNG"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-2LFWSK2TNG');
    </script>

    <?php
    if (file_exists(__DIR__ . '/components/seo-meta.php')) {
        include __DIR__ . '/components/seo-meta.php';
    } else {
        echo '<title>' . htmlspecialchars($title ?? 'Luxe Stars Power - Marketplace Premium de Produits Numériques') . '</title>';
        echo '<meta name="description" content="Achetez et vendez des produits numériques de qualité : ebooks, formations, vidéos et plus.">';
    }
    ?>

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://res.cloudinary.com">
    <link rel="dns-prefetch" href="https://luxestarspower.b-cdn.net">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
    <link rel="apple-touch-icon" href="/assets/images/apple-touch-icon.png">

    <!-- Styles -->
    <link rel="stylesheet" href="/assets/css/main.css">
    
    <style>
        /* ==================== NAVBAR ==================== */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .navbar .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 15px 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
            flex-shrink: 0;
            transition: transform 0.3s;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .site-logo {
            height: 40px;
            width: auto;
            /* Logo garde ses couleurs originales */
        }

        .logo-text {
            color: white;
        }

        /* ==================== SEARCH BAR ==================== */
        .search-container {
            position: relative;
            width: 100%;
            max-width: 320px;
            margin: 0 auto;
        }

        .search-form {
            position: relative;
            width: 100%;
        }

        .search-input {
            width: 100%;
            padding: 10px 75px 10px 40px;
            border: none;
            border-radius: 25px;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            outline: none;
            background: white;
            color: #333;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .search-input:focus {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-1px);
        }

        .search-input::placeholder {
            color: #999;
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            font-size: 1.1rem;
            pointer-events: none;
        }

        .search-btn {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .search-btn:hover {
            transform: translateY(-50%) scale(1.05);
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
        }

        .search-btn:active {
            transform: translateY(-50%) scale(0.98);
        }

        /* Affichage conditionnel */
        .search-desktop {
            display: block;
        }

        .search-mobile {
            display: none;
        }

        /* ==================== SEARCH RESULTS ==================== */
        .search-results {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            max-height: 380px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .search-results.show {
            display: block;
            animation: slideDown 0.2s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .search-result-item {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }

        .search-result-item:hover {
            background: #f8f9fa;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            flex-shrink: 0;
        }

        .search-result-info {
            flex: 1;
            min-width: 0;
        }

        .search-result-title {
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 3px;
            color: #333;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .search-result-type {
            font-size: 0.7rem;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .search-result-price {
            color: #667eea;
            font-weight: 700;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .no-results {
            padding: 30px;
            text-align: center;
            color: #999;
            font-size: 0.875rem;
        }

        /* ==================== NAV LINKS ==================== */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-shrink: 0;
        }

        .nav-links a {
            text-decoration: none;
            color: white;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s;
            white-space: nowrap;
            position: relative;
            padding: 8px 12px;
            border-radius: 6px;
        }

        .nav-links a:hover {
            background: rgba(255,255,255,0.15);
        }

        .cart-link {
            position: relative;
        }

        .cart-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #ff4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            border: 2px solid #667eea;
        }

        /* ==================== LANGUAGE DROPDOWN ==================== */
        .lang-dropdown {
            position: relative;
        }

        .lang-btn {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            color: white;
            transition: all 0.2s;
        }

        .lang-btn:hover {
            background: rgba(255,255,255,0.25);
            border-color: white;
        }

        .lang-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            min-width: 150px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s;
            z-index: 1000;
        }

        .lang-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .lang-menu a {
            display: block;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
            transition: background 0.2s;
            font-size: 0.875rem;
        }

        .lang-menu a:first-child {
            border-radius: 8px 8px 0 0;
        }

        .lang-menu a:last-child {
            border-radius: 0 0 8px 8px;
        }

        .lang-menu a:hover {
            background: #f8f9fa;
        }

        /* ==================== BURGER MENU ==================== */
        .burger-menu {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 8px;
            z-index: 1001;
            background: rgba(255,255,255,0.1);
            border-radius: 6px;
            transition: background 0.3s;
        }

        .burger-menu:hover {
            background: rgba(255,255,255,0.2);
        }

        .burger-menu span {
            width: 25px;
            height: 3px;
            background: white;
            transition: all 0.3s ease;
            border-radius: 3px;
        }

        /* Close icon (X) */
        .burger-menu.active {
            background: rgba(255,255,255,0.2);
        }

        .burger-menu.active span:nth-child(1) {
            transform: rotate(45deg) translate(8px, 8px);
        }

        .burger-menu.active span:nth-child(2) {
            opacity: 0;
            transform: translateX(-20px);
        }

        .burger-menu.active span:nth-child(3) {
            transform: rotate(-45deg) translate(8px, -8px);
        }

        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 1100px) {
            .search-container {
                max-width: 250px;
            }

            .search-input {
                padding: 8px 70px 8px 35px;
                font-size: 0.8rem;
            }

            .search-btn {
                font-size: 0.75rem;
                padding: 5px 14px;
            }

            .nav-links {
                gap: 15px;
            }

            .nav-links a {
                font-size: 0.9rem;
                padding: 6px 10px;
            }
        }

        @media (max-width: 900px) {
            .search-desktop {
                display: none;
            }

            .logo-text {
                display: inline;
            }
        }

        @media (max-width: 768px) {
            .burger-menu {
                display: flex;
            }

            .logo-text {
                display: none;
            }

            .navbar .container {
                padding: 12px 15px;
            }

            .site-logo {
                height: 35px;
            }

            /* Menu mobile avec overlay */
            body.menu-open {
                overflow: hidden;
            }

            body.menu-open::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 998;
                animation: fadeIn 0.3s;
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            .nav-links {
                position: fixed;
                top: 0;
                right: -100%;
                width: 300px;
                height: 100vh;
                background: white;
                flex-direction: column;
                align-items: stretch;
                gap: 0;
                padding: 80px 20px 20px;
                box-shadow: -4px 0 20px rgba(0,0,0,0.15);
                transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                overflow-y: auto;
                z-index: 999;
            }

            .nav-links.active {
                right: 0;
            }

            .nav-links a {
                padding: 15px 10px;
                border-bottom: 1px solid #f0f0f0;
                font-size: 1rem;
                color: #333;
                border-radius: 0;
            }

            .nav-links a:hover {
                background: #f8f9fa;
                color: #667eea;
            }

            /* Recherche mobile */
            .search-mobile {
                display: block;
                max-width: 100%;
                margin: 0 0 20px 0;
                order: -1;
            }

            .search-mobile .search-input {
                width: 100%;
                padding: 12px 80px 12px 40px;
                font-size: 0.95rem;
            }

            .search-mobile .search-btn {
                font-size: 0.85rem;
                padding: 7px 16px;
            }

            .search-mobile .search-icon {
                font-size: 1.1rem;
            }

            .lang-dropdown {
                width: 100%;
                border-top: 1px solid #f0f0f0;
                padding-top: 15px;
                margin-top: 15px;
            }

            .lang-btn {
                width: 100%;
                text-align: left;
                background: #f8f9fa;
                border-color: #e0e0e0;
                color: #333;
                padding: 12px 14px;
            }

            .lang-btn:hover {
                background: #e9ecef;
            }

            .lang-menu {
                position: static;
                box-shadow: none;
                border: 1px solid #e0e0e0;
                margin-top: 8px;
            }
        }

        @media (max-width: 480px) {
            .navbar .container {
                padding: 10px 12px;
            }

            .site-logo {
                height: 32px;
            }

            .nav-links {
                width: 85%;
            }
        }
    </style>
</head>
<body>
    <!-- ==================== NAVBAR ==================== -->
    <nav class="navbar">
        <div class="container">
            <a href="/" class="logo">
                <img src="/assets/images/logo.png" alt="Luxe Stars Power" class="site-logo">
                <span class="logo-text">Luxe Stars Power</span>
            </a>

            <!-- SEARCH BAR (Desktop/Tablet uniquement) -->
            <div class="search-container search-desktop">
                <form class="search-form" action="/produits" method="GET">
                    <span class="search-icon">🔍</span>
                    <input 
                        type="text" 
                        name="q" 
                        class="search-input" 
                        placeholder="<?php echo __('search'); ?>..."
                        autocomplete="off"
                        id="searchInput"
                        aria-label="<?php echo __('search_products'); ?>"
                    >
                    <button type="submit" class="search-btn" aria-label="<?php echo __('search'); ?>">
                        <?php echo __('search'); ?>
                    </button>
                </form>
                <div class="search-results" id="searchResults" role="listbox"></div>
            </div>

            <!-- BURGER MENU -->
            <div class="burger-menu" aria-label="Menu" role="button" tabindex="0">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <!-- NAVIGATION LINKS -->
            <div class="nav-links">
                <!-- SEARCH BAR (Mobile uniquement - dans le menu) -->
                <div class="search-container search-mobile">
                    <form class="search-form" action="/produits" method="GET">
                        <span class="search-icon">🔍</span>
                        <input 
                            type="text" 
                            name="q" 
                            class="search-input" 
                            placeholder="<?php echo __('search'); ?>..."
                            autocomplete="off"
                        >
                        <button type="submit" class="search-btn">
                            <?php echo __('search'); ?>
                        </button>
                    </form>
                </div>

                <a href="/produits"><?php echo __('products'); ?></a>
                <a href="/vendre"><?php echo __('sell'); ?></a>

                <?php if(isset($_SESSION['user_id'])): ?>
                    <!-- CART -->
                    <?php
                    $cartCount = 0;
                    if (class_exists('App\Repositories\CartRepository')) {
                        try {
                            $cartRepo = new \App\Repositories\CartRepository();
                            $cartCount = $cartRepo->getCartCount($_SESSION['user_id']);
                        } catch (\Exception $e) {
                            $cartCount = 0;
                        }
                    }
                    ?>
                    <a href="/panier" class="cart-link">
                        🛒 <?php echo __('cart'); ?>
                        <?php if($cartCount > 0): ?>
                            <span class="cart-badge"><?php echo $cartCount; ?></span>
                        <?php endif; ?>
                    </a>

                    <!-- ACCOUNT -->
                    <?php if(isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'seller' || $_SESSION['user_role'] === 'admin')): ?>
                        <a href="/vendeur/tableau-de-bord"><?php echo __('my_account'); ?></a>
                    <?php else: ?>
                        <a href="/compte"><?php echo __('my_account'); ?></a>
                    <?php endif; ?>

                    <!-- ADMIN -->
                    <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <a href="/admin"><?php echo __('admin'); ?></a>
                    <?php endif; ?>

                    <a href="/deconnexion"><?php echo __('logout'); ?></a>
                <?php else: ?>
                    <a href="/connexion"><?php echo __('login'); ?></a>
                    <a href="/inscription"><?php echo __('register'); ?></a>
                <?php endif; ?>

                <!-- LANGUAGE DROPDOWN -->
                <div class="lang-dropdown">
                    <button class="lang-btn" aria-haspopup="true" aria-expanded="false">
                        <?php echo strtoupper(\App\I18n::getLocale()); ?> ▼
                    </button>
                    <div class="lang-menu" role="menu">
                        <a href="/langue/fr" role="menuitem">🇫🇷 Français</a>
                        <a href="/langue/en" role="menuitem">🇬🇧 English</a>
                        <a href="/langue/es" role="menuitem">🇪🇸 Español</a>
                        <a href="/langue/de" role="menuitem">🇩🇪 Deutsch</a>
                        <a href="/langue/it" role="menuitem">🇮🇹 Italiano</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- ==================== MAIN CONTENT ==================== -->
    <main role="main">
        <?php echo $content ?? ''; ?>
    </main>

    <!-- ==================== FOOTER ==================== -->
    <footer style="background: #2c3e50; color: white; padding: 60px 20px 20px; margin-top: 80px;">
        <div class="container" style="max-width: 1200px; margin: 0 auto;">
            <!-- Top Footer -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; margin-bottom: 40px;">
                <!-- About -->
                <div>
                    <h3 style="margin-bottom: 20px; font-size: 1.3rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        Luxe Stars Power
                    </h3>
                    <p style="color: #bdc3c7; line-height: 1.6; margin-bottom: 20px;">
                        <?php echo __('marketplace_premium_description'); ?>
                    </p>
                    <div style="display: flex; gap: 15px; margin-top: 20px;">
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" style="color: #3b5998; font-size: 1.8rem; transition: transform 0.3s;" title="Facebook" aria-label="Facebook">
                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" style="color: #1DA1F2; font-size: 1.8rem; transition: transform 0.3s;" title="Twitter" aria-label="Twitter">
                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" style="color: #E1306C; font-size: 1.8rem; transition: transform 0.3s;" title="Instagram" aria-label="Instagram">
                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                        </a>
                        <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" style="color: #0077b5; font-size: 1.8rem; transition: transform 0.3s;" title="LinkedIn" aria-label="LinkedIn">
                            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 style="margin-bottom: 20px; font-size: 1.1rem; color: white;"><?php echo __('quick_links'); ?></h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 12px;">
                            <a href="/produits" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                → <?php echo __('products'); ?>
                            </a>
                        </li>
                        <li style="margin-bottom: 12px;">
                            <a href="/vendre" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                → <?php echo __('sell'); ?>
                            </a>
                        </li>
                        <li style="margin-bottom: 12px;">
                            <a href="/contact" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                → <?php echo __('contact'); ?>
                            </a>
                        </li>
                        <li style="margin-bottom: 12px;">
                            <a href="/faq" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                → <?php echo __('faq'); ?>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h4 style="margin-bottom: 20px; font-size: 1.1rem; color: white;"><?php echo __('legal_information'); ?></h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 12px;">
                            <a href="/conditions" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                → <?php echo __('terms_conditions'); ?>
                            </a>
                        </li>
                        <li style="margin-bottom: 12px;">
                            <a href="/confidentialite" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                → <?php echo __('privacy_policy'); ?>
                            </a>
                        </li>
                        <li style="margin-bottom: 12px;">
                            <a href="/politique-remboursement" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                → <?php echo __('refund_policy'); ?>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h4 style="margin-bottom: 20px; font-size: 1.1rem; color: white;"><?php echo __('support'); ?></h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 12px;">
                            <a href="/contact" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                📧 <?php echo __('contact_us'); ?>
                            </a>
                        </li>
                        <li style="margin-bottom: 12px;">
                            <a href="/faq" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                ❓ <?php echo __('help_center'); ?>
                            </a>
                        </li>
                        <li style="margin-bottom: 12px; color: #bdc3c7;">
                            📞 +55 54 99302-4286
                        </li>
                        <li style="margin-bottom: 12px; color: #bdc3c7;">
                            ⏰ <?php echo __('available_24_7'); ?>
                        </li>
                    </ul>

                    <!-- Payment Methods -->
                    <div style="margin-top: 25px;">
                        <p style="color: #bdc3c7; font-size: 0.9rem; margin-bottom: 10px;"><?php echo __('secure_payments'); ?> :</p>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <span style="background: white; padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; color: #333;">💳 Visa</span>
                            <span style="background: white; padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; color: #333;">💳 Mastercard</span>
                            <span style="background: white; padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; color: #333;">🅿️ PayPal</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 30px; margin-top: 30px; text-align: center;">
                <p style="color: #95a5a6; font-size: 0.9rem; margin: 0;">
                    &copy; <?php echo date('Y'); ?> Luxe Stars Power. <?php echo __('all_rights_reserved'); ?>
                </p>
            </div>
        </div>
    </footer>

    <!-- ==================== SCRIPTS ==================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ========== LIVE SEARCH ==========
            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');
            let searchTimeout;

            if (searchInput && searchResults) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();

                    if (query.length < 2) {
                        searchResults.classList.remove('show');
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        fetch(`/api/search?q=${encodeURIComponent(query)}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.products && data.products.length > 0) {
                                    let html = '';
                                    data.products.forEach(product => {
                                        const thumb = product.thumbnail_path || '/assets/images/placeholder.png';
                                        const title = product.title.length > 40 ? product.title.substring(0, 40) + '...' : product.title;
                                        html += `
                                            <a href="/produits/${product.slug}" class="search-result-item" role="option">
                                                <img src="${thumb}" alt="${product.title}" class="search-result-thumb">
                                                <div class="search-result-info">
                                                    <div class="search-result-title">${title}</div>
                                                    <div class="search-result-type">${product.type}</div>
                                                </div>
                                                <div class="search-result-price">$${parseFloat(product.price).toFixed(2)}</div>
                                            </a>
                                        `;
                                    });
                                    searchResults.innerHTML = html;
                                    searchResults.classList.add('show');
                                } else {
                                    searchResults.innerHTML = '<div class="no-results">Aucun produit trouvé 😔</div>';
                                    searchResults.classList.add('show');
                                }
                            })
                            .catch(() => searchResults.classList.remove('show'));
                    }, 300);
                });

                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.classList.remove('show');
                    }
                });

                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        searchResults.classList.remove('show');
                    }
                });
            }

            // ========== LANGUAGE DROPDOWN ==========
            const langBtn = document.querySelector('.lang-btn');
            const langMenu = document.querySelector('.lang-menu');

            if (langBtn && langMenu) {
                langBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    langMenu.classList.toggle('show');
                    this.setAttribute('aria-expanded', langMenu.classList.contains('show'));
                });

                window.addEventListener('click', function(e) {
                    if (!e.target.matches('.lang-btn')) {
                        langMenu.classList.remove('show');
                        if (langBtn) langBtn.setAttribute('aria-expanded', 'false');
                    }
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && langMenu.classList.contains('show')) {
                        langMenu.classList.remove('show');
                        langBtn.setAttribute('aria-expanded', 'false');
                        langBtn.focus();
                    }
                });
            }

            // ========== BURGER MENU ==========
            const burger = document.querySelector('.burger-menu');
            const navLinks = document.querySelector('.nav-links');

            if (burger && navLinks) {
                burger.addEventListener('click', function() {
                    const isActive = navLinks.classList.toggle('active');
                    this.classList.toggle('active');
                    this.setAttribute('aria-expanded', isActive);
                    
                    // Toggle body class for overlay
                    if (isActive) {
                        document.body.classList.add('menu-open');
                    } else {
                        document.body.classList.remove('menu-open');
                    }
                });

                // Close menu on link click
                navLinks.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 768) {
                            navLinks.classList.remove('active');
                            burger.classList.remove('active');
                            burger.setAttribute('aria-expanded', 'false');
                            document.body.classList.remove('menu-open');
                        }
                    });
                });

                // Close on overlay click
                document.body.addEventListener('click', function(e) {
                    if (e.target === document.body && navLinks.classList.contains('active')) {
                        navLinks.classList.remove('active');
                        burger.classList.remove('active');
                        burger.setAttribute('aria-expanded', 'false');
                        document.body.classList.remove('menu-open');
                    }
                });

                // Close on escape
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && navLinks.classList.contains('active')) {
                        navLinks.classList.remove('active');
                        burger.classList.remove('active');
                        burger.setAttribute('aria-expanded', 'false');
                        document.body.classList.remove('menu-open');
                    }
                });
            }
        });
    </script>
</body>
</html>