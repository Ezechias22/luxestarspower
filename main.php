<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo e($title ?? 'LuxeStarsPower - Marketplace Premium'); ?></title>
    <meta name="description" content="<?php echo e($description ?? 'Marketplace premium pour contenus numériques'); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo asset('images/favicon.png'); ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo asset('css/app.css'); ?>">
    
    <?php if (isset($extraCss)): ?>
        <?php echo $extraCss; ?>
    <?php endif; ?>
</head>
<body class="<?php echo $bodyClass ?? ''; ?>">
    <!-- Header -->
    <header class="main-header">
        <nav class="navbar">
            <div class="container">
                <a href="<?php echo url('/'); ?>" class="logo">
                    <img src="<?php echo asset('images/logo.svg'); ?>" alt="LuxeStarsPower">
                </a>
                
                <!-- Search Bar -->
                <div class="search-bar">
                    <form action="<?php echo url('/recherche'); ?>" method="GET">
                        <input type="text" name="q" placeholder="Rechercher des produits..." value="<?php echo e($_GET['q'] ?? ''); ?>">
                        <button type="submit">
                            <svg width="20" height="20" fill="none" stroke="currentColor">
                                <circle cx="8" cy="8" r="7"/>
                                <path d="M13 13l6 6"/>
                            </svg>
                        </button>
                    </form>
                </div>
                
                <!-- Navigation -->
                <ul class="nav-links">
                    <li><a href="<?php echo url('/produits'); ?>">Catalogue</a></li>
                    <li><a href="<?php echo url('/vendre'); ?>">Vendre</a></li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="dropdown">
                            <a href="#" class="user-menu">
                                <?php if (!empty($_SESSION['user_avatar'])): ?>
                                    <img src="<?php echo e($_SESSION['user_avatar']); ?>" alt="Avatar" class="avatar">
                                <?php endif; ?>
                                <span><?php echo e($_SESSION['user_name'] ?? 'Mon compte'); ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo url('/compte'); ?>">Dashboard</a></li>
                                <li><a href="<?php echo url('/compte/achats'); ?>">Mes achats</a></li>
                                <?php if ($_SESSION['user_role'] === 'seller' || $_SESSION['user_role'] === 'admin'): ?>
                                    <li><a href="<?php echo url('/vendeur/produits'); ?>">Mes produits</a></li>
                                <?php endif; ?>
                                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                    <li><hr></li>
                                    <li><a href="<?php echo url('/admin'); ?>">Administration</a></li>
                                <?php endif; ?>
                                <li><hr></li>
                                <li><a href="<?php echo url('/deconnexion'); ?>">Déconnexion</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li><a href="<?php echo url('/connexion'); ?>" class="btn-secondary">Connexion</a></li>
                        <li><a href="<?php echo url('/inscription'); ?>" class="btn-primary">S'inscrire</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>
    
    <!-- Flash Messages -->
    <?php if ($success = get_flash('success')): ?>
        <div class="alert alert-success">
            <?php echo e($success); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error = get_flash('error')): ?>
        <div class="alert alert-error">
            <?php echo e($error); ?>
        </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="main-content">
        <?php echo $content; ?>
    </main>
    
    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>LuxeStarsPower</h4>
                    <p>Marketplace premium pour contenus numériques de qualité.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Twitter">
                            <svg width="24" height="24" fill="currentColor"><!-- Icon --></svg>
                        </a>
                        <a href="#" aria-label="Facebook">
                            <svg width="24" height="24" fill="currentColor"><!-- Icon --></svg>
                        </a>
                        <a href="#" aria-label="Instagram">
                            <svg width="24" height="24" fill="currentColor"><!-- Icon --></svg>
                        </a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h5>Marketplace</h5>
                    <ul>
                        <li><a href="<?php echo url('/produits'); ?>">Catalogue</a></li>
                        <li><a href="<?php echo url('/vendre'); ?>">Devenir vendeur</a></li>
                        <li><a href="<?php echo url('/a-propos'); ?>">À propos</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h5>Support</h5>
                    <ul>
                        <li><a href="<?php echo url('/faq'); ?>">FAQ</a></li>
                        <li><a href="<?php echo url('/contact'); ?>">Contact</a></li>
                        <li><a href="<?php echo url('/termes'); ?>">CGV</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h5>Légal</h5>
                    <ul>
                        <li><a href="<?php echo url('/confidentialite'); ?>">Confidentialité</a></li>
                        <li><a href="<?php echo url('/termes'); ?>">Conditions d'utilisation</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> LuxeStarsPower. Tous droits réservés.</p>
                <div class="payment-methods">
                    <img src="<?php echo asset('images/stripe.svg'); ?>" alt="Stripe">
                    <img src="<?php echo asset('images/paypal.svg'); ?>" alt="PayPal">
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="<?php echo asset('js/app.js'); ?>"></script>
    
    <?php if (isset($extraJs)): ?>
        <?php echo $extraJs; ?>
    <?php endif; ?>
</body>
</html>
