<!DOCTYPE html>
<html lang="<?= \App\I18n::getLocale() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Luxe Stars Power' ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="/" class="logo">Luxe Stars Power</a>
            <div class="nav-links">
                <a href="/produits"><?= __('nav.products') ?></a>
                <a href="/vendre"><?= __('nav.sell') ?></a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="/compte"><?= __('nav.account') ?></a>
                    <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <a href="/admin/dashboard"><?= __('nav.admin') ?></a>
                    <?php endif; ?>
                    <a href="/logout"><?= __('nav.logout') ?></a>
                <?php else: ?>
                    <a href="/login"><?= __('nav.login') ?></a>
                    <a href="/register"><?= __('nav.register') ?></a>
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
        <?= $content ?? '' ?>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> Luxe Stars Power. All rights reserved.</p>
        </div>
    </footer>
    
    <script src="/assets/js/main.js"></script>
</body>
</html>
