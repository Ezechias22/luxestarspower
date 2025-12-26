<?php \App\I18n::init(); ?>
<!DOCTYPE html>
<html lang="<?php echo \App\I18n::getLocale(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Google tag (gtag.js) -->
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
    }
    ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://res.cloudinary.com">
    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
    <link rel="apple-touch-icon" href="/assets/images/apple-touch-icon.png">
    <link rel="stylesheet" href="/assets/css/main.css">
    
    <style>
        /* Container navbar responsive */
        .navbar .container {
            display: flex;
            align-items: center;
            gap: 15px;
            position: relative;
        }

        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            white-space: nowrap;
        }

        /* Barre de recherche - Desktop */
        .search-container {
            position: relative;
            flex: 1;
            max-width: 400px;
        }

        .search-form {
            position: relative;
            width: 100%;
        }

        .search-input {
            width: 100%;
            padding: 8px 80px 8px 35px;
            border: 2px solid #e0e0e0;
            border-radius: 20px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .search-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 1rem;
        }

        .search-btn {
            position: absolute;
            right: 3px;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 17px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: transform 0.2s;
        }

        .search-btn:hover {
            transform: translateY(-50%) scale(1.05);
        }

        /* Résultats de recherche */
        .search-results {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .search-results.show {
            display: block;
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
            border-radius: 6px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            flex-shrink: 0;
        }

        .search-result-info {
            flex: 1;
            min-width: 0;
        }

        .search-result-title {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 3px;
            color: #333;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .search-result-type {
            font-size: 0.75rem;
            color: #999;
            text-transform: uppercase;
        }

        .search-result-price {
            color: #667eea;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .no-results {
            padding: 30px;
            text-align: center;
            color: #999;
            font-size: 0.9rem;
        }

        /* Navigation links - Desktop */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Mobile */
        @media (max-width: 968px) {
            .navbar .container {
                flex-wrap: wrap;
            }

            /* Barre de recherche sous le menu sur mobile */
            .search-container {
                order: 3;
                width: 100%;
                max-width: 100%;
                margin-top: 10px;
            }

            .nav-links {
                order: 2;
            }

            .search-input {
                font-size: 0.85rem;
                padding: 8px 75px 8px 32px;
            }

            .search-btn {
                font-size: 0.8rem;
                padding: 5px 12px;
            }
        }

        @media (max-width: 768px) {
            .search-container {
                display: none;
            }

            /* Version mobile simplifiée */
            .nav-links.active .search-container {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="/" class="logo">
                <img src="/assets/images/logo.png" alt="Luxe Stars Power" class="site-logo">
                <span class="logo-text">Luxe Stars Power</span>
            </a>

            <!-- BARRE DE RECHERCHE - Seulement sur desktop/tablette -->
            <div class="search-container">
                <form class="search-form" action="/produits" method="GET">
                    <span class="search-icon">🔍</span>
                    <input 
                        type="text" 
                        name="q" 
                        class="search-input" 
                        placeholder="<?php echo __('search'); ?>..."
                        autocomplete="off"
                        id="searchInput"
                    >
                    <button type="submit" class="search-btn">
                        <?php echo __('search'); ?>
                    </button>
                </form>
                <div class="search-results" id="searchResults"></div>
            </div>

            <!-- Burger Menu -->
            <div class="burger-menu">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <div class="nav-links">
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

                <div class="lang-dropdown">
                    <button class="lang-btn"><?php echo strtoupper(\App\I18n::getLocale()); ?> ▼</button>
                    <div class="lang-menu">
                        <a href="/langue/fr">🇫🇷 Français</a>
                        <a href="/langue/en">🇬🇧 English</a>
                        <a href="/langue/es">🇪🇸 Español</a>
                        <a href="/langue/de">🇩🇪 Deutsch</a>
                        <a href="/langue/it">🇮🇹 Italiano</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main>
        <?php echo $content ?? ''; ?>
    </main>

    <!-- FOOTER reste identique -->
    <footer style="background: #2c3e50; color: white; padding: 60px 20px 20px; margin-top: 80px;">
        <!-- ... Le footer complet que tu as déjà ... -->
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Recherche en temps réel
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
                                        html += `
                                            <a href="/produits/${product.slug}" class="search-result-item">
                                                <img src="${thumb}" alt="${product.title}" class="search-result-thumb">
                                                <div class="search-result-info">
                                                    <div class="search-result-title">${product.title}</div>
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
            }

            // Dropdown langue
            var langBtn = document.querySelector('.lang-btn');
            if (langBtn) {
                langBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    this.nextElementSibling.classList.toggle('show');
                });
            }

            window.addEventListener('click', function(e) {
                if (!e.target.matches('.lang-btn')) {
                    var dropdowns = document.getElementsByClassName('lang-menu');
                    for (var i = 0; i < dropdowns.length; i++) {
                        dropdowns[i].classList.remove('show');
                    }
                }
            });

            // Burger menu
            var burger = document.querySelector('.burger-menu');
            var navLinks = document.querySelector('.nav-links');

            if (burger && navLinks) {
                burger.addEventListener('click', function() {
                    navLinks.classList.toggle('active');
                    var spans = this.querySelectorAll('span');
                    if (navLinks.classList.contains('active')) {
                        spans[0].style.transform = 'rotate(45deg) translate(6px, 6px)';
                        spans[1].style.opacity = '0';
                        spans[2].style.transform = 'rotate(-45deg) translate(6px, -6px)';
                    } else {
                        spans[0].style.transform = 'none';
                        spans[1].style.opacity = '1';
                        spans[2].style.transform = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>