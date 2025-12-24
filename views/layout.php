<?php \App\I18n::init(); // Initialise la langue ?>
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
    // Charge les meta tags SEO
    if (file_exists(__DIR__ . '/components/seo-meta.php')) {
        include __DIR__ . '/components/seo-meta.php';
    } else {
        // Fallback si le composant SEO n'existe pas encore
        echo '<title>' . htmlspecialchars($title ?? 'Luxe Stars Power - Marketplace Premium de Produits Numériques') . '</title>';
    }
    ?>
    
    <!-- Preconnect pour performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://res.cloudinary.com">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
    <link rel="apple-touch-icon" href="/assets/images/apple-touch-icon.png">
    
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="/" class="logo">
    <img src="/assets/images/logo.png" alt="Luxe Stars Power" class="site-logo">
    <span class="logo-text"></span>Luxe Stars Power</span>
</a>
            
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
                    <?php echo __('marketplace_premium_description'); ?>
                </p>
                <div style="display: flex; gap: 15px; margin-top: 20px;">
                    <a href="https://facebook.com" target="_blank" rel="noopener" style="color: #3b5998; font-size: 1.8rem; transition: transform 0.3s;" title="Facebook" aria-label="Facebook">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="https://twitter.com" target="_blank" rel="noopener" style="color: #1DA1F2; font-size: 1.8rem; transition: transform 0.3s;" title="Twitter" aria-label="Twitter">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                    <a href="https://instagram.com" target="_blank" rel="noopener" style="color: #E1306C; font-size: 1.8rem; transition: transform 0.3s;" title="Instagram" aria-label="Instagram">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                    </a>
                    <a href="https://linkedin.com" target="_blank" rel="noopener" style="color: #0077b5; font-size: 1.8rem; transition: transform 0.3s;" title="LinkedIn" aria-label="LinkedIn">
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