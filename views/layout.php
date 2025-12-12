<!DOCTYPE html>
<html lang="<?php echo \App\I18n::getLocale(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Luxe Stars Power'; ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="/" class="logo">Luxe Stars Power</a>
            <div class="nav-links">
                <a href="/produits">Produits</a>
                <a href="/vendre">Vendre</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="/compte">Mon Compte</a>
                    <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <a href="/admin">Admin</a>
                    <?php endif; ?>
                    <a href="/deconnexion">Déconnexion</a>
                <?php else: ?>
                    <a href="/connexion">Connexion</a>
                    <a href="/inscription">Inscription</a>
                <?php endif; ?>
            </div>
            <div class="lang-switch">
                <a href="?lang=fr">FR</a>
                <a href="?lang=en">EN</a>
                <a href="?lang=es">ES</a>
                <a href="?lang=de">DE</a>
                <a href="?lang=it">IT</a>
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
    
    <script src="/assets/js/main.js"></script>
</body>
</html>