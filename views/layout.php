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

    <footer style="background: #2c3e50; color: white; padding: 60px 20px 20px; margin-top: 80px;">
        <div class="container" style="max-width: 1200px; margin: 0 auto;">
            <!-- Top Footer -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; margin-bottom: 40px;">
                
                <!-- About Section -->
                <div>
                    <h3 style="margin-bottom: 20px; font-size: 1.3rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        Luxe Stars Power
                    </h3>
                    <p style="color: #bdc3c7; line-height: 1.6; margin-bottom: 20px;">
                        Votre marketplace premium pour acheter et vendre des produits numériques de qualité. Ebooks, formations, vidéos et bien plus encore.
                    </p>
                    <div style="display: flex; gap: 15px; margin-top: 20px;">
                        <a href="#" style="color: white; font-size: 1.5rem; transition: color 0.3s;" title="Facebook">📘</a>
                        <a href="#" style="color: white; font-size: 1.5rem; transition: color 0.3s;" title="Twitter">🐦</a>
                        <a href="#" style="color: white; font-size: 1.5rem; transition: color 0.3s;" title="Instagram">📷</a>
                        <a href="#" style="color: white; font-size: 1.5rem; transition: color 0.3s;" title="LinkedIn">💼</a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 style="margin-bottom: 20px; font-size: 1.1rem; color: white;">Liens rapides</h4>
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
                            <a href="/a-propos" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                → À propos
                            </a>
                        </li>
                        <li style="margin-bottom: 12px;">
                            <a href="/contact" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                → Contact
                            </a>
                        </li>
                        <li style="margin-bottom: 12px;">
                            <a href="/faq" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                → FAQ
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h4 style="margin-bottom: 20px; font-size: 1.1rem; color: white;">Informations légales</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 12px;">
                            <a href="/conditions" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                → Conditions générales
                            </a>
                        </li>
                        <li style="margin-bottom: 12px;">
                            <a href="/confidentialite" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                → Politique de confidentialité
                            </a>
                        </li>
                        <li style="margin-bottom: 12px;">
                            <a href="/politique-remboursement" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                → Politique de remboursement
                            </a>
                        </li>
                        <li style="margin-bottom: 12px;">
                            <a href="#" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                → Mentions légales
                            </a>
                        </li>
                        <li style="margin-bottom: 12px;">
                            <a href="#" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                → Cookies
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h4 style="margin-bottom: 20px; font-size: 1.1rem; color: white;">Support</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 12px;">
                            <a href="/contact" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                📧 Nous contacter
                            </a>
                        </li>
                        <li style="margin-bottom: 12px;">
                            <a href="/faq" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                                ❓ Centre d'aide
                            </a>
                        </li>
                        <li style="margin-bottom: 12px; color: #bdc3c7;">
                            📞 +33 1 23 45 67 89
                        </li>
                        <li style="margin-bottom: 12px; color: #bdc3c7;">
                            ⏰ Lun-Ven: 9h-18h
                        </li>
                    </ul>
                    
                    <!-- Payment Methods -->
                    <div style="margin-top: 25px;">
                        <p style="color: #bdc3c7; font-size: 0.9rem; margin-bottom: 10px;">Paiements sécurisés :</p>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <span style="background: white; padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; color: #333;">💳 Visa</span>
                            <span style="background: white; padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; color: #333;">💳 Mastercard</span>
                            <span style="background: white; padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; color: #333;">🅿️ PayPal</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 30px; margin-top: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                    <p style="color: #95a5a6; font-size: 0.9rem; margin: 0;">
                        &copy; <?php echo date('Y'); ?> Luxe Stars Power. <?php echo __('all_rights_reserved'); ?>
                    </p>
                    <p style="color: #95a5a6; font-size: 0.9rem; margin: 0;">
                        Made with ❤️ by <a href="#" style="color: #3498db; text-decoration: none;">Luxe Stars Power Team</a>
                    </p>
                </div>
            </div>
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