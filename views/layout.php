<?php \App\I18n::init(); // Initialise la langue ?>
<!DOCTYPE html>
<html lang="<?php echo \App\I18n::getLocale(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Luxe Stars Power'; ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="/" class="logo">Luxe Stars Power</a>
            
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
                    <!-- Panier avec badge -->
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

                <!-- Dropdown langue -->
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

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Luxe Stars Power. <?php echo __('all_rights_reserved'); ?></p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle dropdown langue
            var langBtn = document.querySelector('.lang-btn');
            if (langBtn) {
                langBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    this.nextElementSibling.classList.toggle('show');
                });
            }

            // Close dropdown when clicking outside
            window.addEventListener('click', function(e) {
                if (!e.target.matches('.lang-btn')) {
                    var dropdowns = document.getElementsByClassName('lang-menu');
                    for (var i = 0; i < dropdowns.length; i++) {
                        dropdowns[i].classList.remove('show');
                    }
                }
            });
            
            // Burger menu mobile
            var burger = document.querySelector('.burger-menu');
            var navLinks = document.querySelector('.nav-links');
            
            if (burger && navLinks) {
                burger.addEventListener('click', function() {
                    navLinks.classList.toggle('active');
                    
                    // Animation du burger
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