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
            <div class="nav-links">
                <a href="/produits">Produits</a>
                <a href="/vendre">Vendre</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if(isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'seller' || $_SESSION['user_role'] === 'admin')): ?>
                        <a href="/vendeur/tableau-de-bord">Mon Compte</a>
                    <?php else: ?>
                        <a href="/compte">Mon Compte</a>
                    <?php endif; ?>
                    
                    <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <a href="/admin">Admin</a>
                    <?php endif; ?>
                    <a href="/deconnexion">Déconnexion</a>
                <?php else: ?>
                    <a href="/connexion">Connexion</a>
                    <a href="/inscription">Inscription</a>
                <?php endif; ?>
                
                <!-- Dropdown langue -->
                <div class="lang-dropdown">
                    <button class="lang-btn"><?php echo strtoupper(\App\I18n::getLocale()); ?></button>
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
            <p>&copy; <?php echo date('Y'); ?> Luxe Stars Power. Tous droits réservés.</p>
        </div>
    </footer>
    
    <script>
        // Toggle dropdown
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
</body>
</html>