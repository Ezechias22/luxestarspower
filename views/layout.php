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
</head>
<body>
    <!-- ==================== NAVBAR ==================== -->
    <nav class="navbar">
        <div class="container">
            <a href="/" class="logo">
                <img src="/assets/images/logo.png" alt="Luxe Stars Power" class="site-logo">
                <span class="logo-text">Luxe Stars Power</span>
            </a>

            <!-- SEARCH BAR (Desktop/Tablet - Icône cliquable) -->
            <div class="search-container search-desktop">
                <button type="button" class="search-toggle" id="searchToggle" aria-label="Rechercher">
                    🔍
                </button>
                <div class="search-form" id="searchForm">
                    <form class="search-form-inner" action="/produits" method="GET">
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
            </div>

            <!-- BURGER MENU -->
            <div class="burger-menu" aria-label="Menu" role="button" tabindex="0">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <!-- NAVIGATION LINKS -->
            <div class="nav-links">
                <!-- SEARCH BAR (Mobile - Toujours visible dans le menu) -->
                <div class="search-container search-mobile">
                    <div class="search-form active">
                        <form class="search-form-inner" action="/produits" method="GET">
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
                </div>

                <a href="/produits"><?php echo __('products'); ?></a>
                <a href="/vendre"><?php echo __('sell'); ?></a>

                <?php if(isset($_SESSION['user_id'])): ?>
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

                    <?php if(isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'seller' || $_SESSION['user_role'] === 'admin')): ?>
                        <a href="/vendeur/tableau-de-bord"><?php echo __('my_account'); ?></a>
                    <?php else: ?>
                        <a href="/compte"><?php echo __('my_account'); ?></a>
                    <?php endif; ?>

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

    <!-- ==================== FOOTER (garde le footer existant) ==================== -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <!-- ==================== SCRIPTS ==================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search toggle
            const searchToggle = document.getElementById('searchToggle');
            const searchForm = document.getElementById('searchForm');
            const searchInputDesktop = document.getElementById('searchInput');

            if (searchToggle && searchForm) {
                searchToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isActive = searchForm.classList.toggle('active');
                    if (isActive) setTimeout(() => searchInputDesktop.focus(), 300);
                });

                document.addEventListener('click', function(e) {
                    if (!searchForm.contains(e.target) && !searchToggle.contains(e.target)) {
                        searchForm.classList.remove('active');
                    }
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && searchForm.classList.contains('active')) {
                        searchForm.classList.remove('active');
                    }
                });
            }

            // Live search
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
                                            <a href="/produits/${product.slug}" class="search-result-item">
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
            }

            // Language dropdown
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
            }

            // Burger menu
            const burger = document.querySelector('.burger-menu');
            const navLinks = document.querySelector('.nav-links');

            if (burger && navLinks) {
                burger.addEventListener('click', function() {
                    const isActive = navLinks.classList.toggle('active');
                    this.classList.toggle('active');
                    this.setAttribute('aria-expanded', isActive);
                    document.body.classList.toggle('menu-open', isActive);
                });

                navLinks.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 768) {
                            navLinks.classList.remove('active');
                            burger.classList.remove('active');
                            document.body.classList.remove('menu-open');
                        }
                    });
                });

                document.body.addEventListener('click', function(e) {
                    if (navLinks.classList.contains('active') && !navLinks.contains(e.target) && !burger.contains(e.target)) {
                        navLinks.classList.remove('active');
                        burger.classList.remove('active');
                        document.body.classList.remove('menu-open');
                    }
                });
            }
        });
    </script>
</body>
</html>