<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language'] ?? 'fr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO -->
    <title><?php echo htmlspecialchars($seo['title'] ?? $seller['shop_name'] . ' - Boutique'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($seo['description'] ?? ''); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($seo['keywords'] ?? ''); ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($seo['title'] ?? ''); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($seo['description'] ?? ''); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($seo['image'] ?? ''); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($seo['url'] ?? ''); ?>">
    <meta property="og:type" content="profile">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/main.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        /* Navbar boutique */
        .shop-navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .shop-navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .shop-navbar-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .shop-navbar-brand {
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
            text-decoration: none;
        }
        
        .shop-navbar-logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            background: white;
        }
        
        .shop-navbar-info h1 {
            margin: 0;
            font-size: 1.8rem;
            color: white;
        }
        
        .shop-navbar-info p {
            margin: 0;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.9);
        }
        
        .shop-navbar-stats {
            display: flex;
            gap: 30px;
            color: white;
        }
        
        .shop-navbar-stat {
            text-align: center;
        }
        
        .shop-navbar-stat-value {
            font-size: 1.8rem;
            font-weight: bold;
            display: block;
        }
        
        .shop-navbar-stat-label {
            font-size: 0.85rem;
            opacity: 0.9;
        }
        
        .shop-navbar-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.2);
        }
        
        .shop-navbar-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .shop-navbar-links a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 5px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .shop-navbar-links a:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .shop-navbar-links a.btn-primary {
            background: white;
            color: #667eea;
        }
        
        .shop-navbar-links a.btn-primary:hover {
            background: #f0f0f0;
        }
        
        .shop-navbar-social {
            display: flex;
            gap: 10px;
        }
        
        .shop-navbar-social a {
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 5px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
        }
        
        .shop-navbar-social a:hover {
            background: rgba(255,255,255,0.2);
        }
        
        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .product-card h3 {
            padding: 15px;
            font-size: 1.1rem;
            color: #2c3e50;
            min-height: 60px;
        }
        
        .product-card .price {
            padding: 0 15px;
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .product-card .btn {
            display: block;
            margin: 15px;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .product-card .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .shop-navbar-top {
                flex-direction: column;
                gap: 15px;
            }
            
            .shop-navbar-stats {
                gap: 20px;
            }
            
            .shop-navbar-bottom {
                flex-direction: column;
                gap: 15px;
            }
        }
        
        @media (max-width: 768px) {
            .shop-navbar-info h1 {
                font-size: 1.3rem;
            }
            
            .shop-navbar-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .shop-navbar-social {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<!-- NAVBAR PERSONNALISÉE BOUTIQUE -->
<nav class="shop-navbar">
    <div class="shop-navbar-container">
        <!-- Top: Logo + Stats -->
        <div class="shop-navbar-top">
            <a href="/boutique/<?php echo htmlspecialchars($seller['shop_slug']); ?>" class="shop-navbar-brand">
                <?php if(!empty($seller['shop_logo'])): ?>
                    <img src="<?php echo htmlspecialchars($seller['shop_logo']); ?>" 
                         alt="<?php echo htmlspecialchars($seller['shop_name']); ?>"
                         class="shop-navbar-logo">
                <?php else: ?>
                    <div class="shop-navbar-logo" style="display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                        🏪
                    </div>
                <?php endif; ?>
                
                <div class="shop-navbar-info">
                    <h1><?php echo htmlspecialchars($seller['shop_name']); ?></h1>
                    <p><?php echo htmlspecialchars($seller['shop_description'] ?? 'Boutique en ligne'); ?></p>
                </div>
            </a>
            
            <!-- Statistiques -->
            <div class="shop-navbar-stats">
                <div class="shop-navbar-stat">
                    <span class="shop-navbar-stat-value"><?php echo $stats['products_count']; ?></span>
                    <span class="shop-navbar-stat-label">Produits</span>
                </div>
                <div class="shop-navbar-stat">
                    <span class="shop-navbar-stat-value"><?php echo $stats['sales_count']; ?></span>
                    <span class="shop-navbar-stat-label">Ventes</span>
                </div>
                <?php if($stats['reviews_count'] > 0): ?>
                <div class="shop-navbar-stat">
                    <span class="shop-navbar-stat-value">⭐ <?php echo $stats['avg_rating']; ?></span>
                    <span class="shop-navbar-stat-label"><?php echo $stats['reviews_count']; ?> avis</span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Bottom: Navigation + Social -->
        <div class="shop-navbar-bottom">
            <!-- Navigation -->
            <div class="shop-navbar-links">
                <a href="/boutique/<?php echo htmlspecialchars($seller['shop_slug']); ?>">🏠 Accueil</a>
                <a href="#products">📦 Produits</a>
                <a href="/">🌐 Luxe Stars Power</a>
                
                <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $seller['id']): ?>
                    <a href="/vendeur/tableau-de-bord" class="btn-primary">📊 Mon Dashboard</a>
                <?php endif; ?>
            </div>
            
            <!-- Réseaux sociaux -->
            <div class="shop-navbar-social">
                <?php if(!empty($seller['facebook_url'])): ?>
                    <a href="<?php echo htmlspecialchars($seller['facebook_url']); ?>" target="_blank" rel="noopener" title="Facebook">
                        📘
                    </a>
                <?php endif; ?>
                
                <?php if(!empty($seller['twitter_url'])): ?>
                    <a href="<?php echo htmlspecialchars($seller['twitter_url']); ?>" target="_blank" rel="noopener" title="Twitter">
                        🐦
                    </a>
                <?php endif; ?>
                
                <?php if(!empty($seller['instagram_url'])): ?>
                    <a href="<?php echo htmlspecialchars($seller['instagram_url']); ?>" target="_blank" rel="noopener" title="Instagram">
                        📸
                    </a>
                <?php endif; ?>
                
                <?php if(!empty($seller['linkedin_url'])): ?>
                    <a href="<?php echo htmlspecialchars($seller['linkedin_url']); ?>" target="_blank" rel="noopener" title="LinkedIn">
                        💼
                    </a>
                <?php endif; ?>
                
                <?php if(!empty($seller['youtube_url'])): ?>
                    <a href="<?php echo htmlspecialchars($seller['youtube_url']); ?>" target="_blank" rel="noopener" title="YouTube">
                        📹
                    </a>
                <?php endif; ?>
                
                <?php if(!empty($seller['tiktok_url'])): ?>
                    <a href="<?php echo htmlspecialchars($seller['tiktok_url']); ?>" target="_blank" rel="noopener" title="TikTok">
                        🎵
                    </a>
                <?php endif; ?>
                
                <!-- Bouton partager -->
                <button onclick="copyShopLink()" 
                        style="background: rgba(255,255,255,0.2); color: white; padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 0.9rem;"
                        title="Copier le lien">
                    📋 Partager
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section avec bannière -->
<div style="position: relative; height: 400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); overflow: hidden;">
    <?php if(!empty($seller['shop_banner'])): ?>
        <img src="<?php echo htmlspecialchars($seller['shop_banner']); ?>" 
             alt="<?php echo htmlspecialchars($seller['shop_name']); ?>"
             style="width: 100%; height: 100%; object-fit: cover; opacity: 0.7;">
    <?php endif; ?>
    
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: white; max-width: 800px; padding: 0 20px;">
        <h2 style="font-size: 3rem; margin-bottom: 20px; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">
            Bienvenue chez <?php echo htmlspecialchars($seller['shop_name']); ?>
        </h2>
        <?php if(!empty($seller['shop_description'])): ?>
            <p style="font-size: 1.3rem; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">
                <?php echo htmlspecialchars($seller['shop_description']); ?>
            </p>
        <?php endif; ?>
        <div style="margin-top: 30px;">
            <a href="#products" style="background: white; color: #667eea; padding: 15px 40px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 1.1rem; display: inline-block; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                Découvrir les produits →
            </a>
        </div>
    </div>
</div>

<div class="container" style="padding: 60px 20px; max-width: 1200px; margin: 0 auto;">
    
    <!-- Produits de la boutique -->
    <div id="products">
        <h3 style="font-size: 2.5rem; margin-bottom: 40px; text-align: center; color: #2c3e50;">
            Nos Produits
        </h3>
        
        <?php if(!empty($products)): ?>
            <div class="products-grid">
                <?php foreach($products as $product): ?>
                    <div class="product-card" style="position: relative;">
                        
                        <!-- Badge PROMO -->
                        <?php if(!empty($product['is_on_sale']) && !empty($product['discount_percentage'])): ?>
                            <div style="position: absolute; top: 10px; right: 10px; background: #ff4444; color: white; padding: 8px 15px; border-radius: 20px; font-weight: bold; font-size: 0.85rem; z-index: 10; box-shadow: 0 2px 8px rgba(255,68,68,0.4);">
                                🔥 -<?php echo $product['discount_percentage']; ?>%
                            </div>
                        <?php endif; ?>
                        
                        <!-- Image -->
                        <?php if(!empty($product['thumbnail_path'])): ?>
                            <img src="<?php echo htmlspecialchars($product['thumbnail_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <?php else: ?>
                            <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 3rem;">📦</span>
                            </div>
                        <?php endif; ?>
                        
                        <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                        
                        <!-- Prix avec promotion -->
                        <?php if(!empty($product['is_on_sale']) && !empty($product['original_price'])): ?>
                            <div style="padding: 0 15px; margin: 10px 0;">
                                <span style="text-decoration: line-through; color: #999; font-size: 0.9rem;">
                                    $<?php echo number_format($product['original_price'], 2); ?>
                                </span>
                                <span class="price" style="color: #ff4444; margin-left: 8px; font-size: 1.5rem;">
                                    $<?php echo number_format($product['price'], 2); ?>
                                </span>
                            </div>
                        <?php else: ?>
                            <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                        <?php endif; ?>
                        
                        <!-- Barre de progression des ventes -->
                        <?php 
                            $currentSales = $product['sales'] ?? 0;
                            $goal = $product['sales_goal'] ?? 100;
                            $percentage = min(100, ($goal > 0 ? ($currentSales / $goal) * 100 : 0));
                        ?>
                        <?php if($currentSales > 0 || $goal > 0): ?>
                        <div style="padding: 0 15px; margin: 15px 0;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.85rem;">
                                <span style="color: #666;">📊 Ventes</span>
                                <span style="color: #333; font-weight: 600;">
                                    <?php echo $currentSales; ?> / <?php echo $goal; ?>
                                </span>
                            </div>
                            <div style="background: #e0e0e0; height: 8px; border-radius: 10px; overflow: hidden;">
                                <div style="background: linear-gradient(90deg, #667eea, #764ba2); height: 100%; width: <?php echo $percentage; ?>%; transition: width 0.3s;"></div>
                            </div>
                            <div style="text-align: right; font-size: 0.75rem; color: #666; margin-top: 3px;">
                                <?php echo round($percentage); ?>%
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <a href="/produit/<?php echo htmlspecialchars($product['slug']); ?>" class="btn">
                            Voir le produit
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #666; padding: 60px 20px; font-size: 1.2rem;">
                Aucun produit disponible pour le moment. Revenez bientôt !
            </p>
        <?php endif; ?>
    </div>
    
</div>

<!-- Footer simplifié -->
<footer style="background: #2c3e50; color: white; padding: 40px 20px; margin-top: 60px; text-align: center;">
    <p style="margin: 0 0 10px; font-size: 1.1rem;"><?php echo htmlspecialchars($seller['shop_name']); ?> • Propulsé par <strong>Luxe Stars Power</strong></p>
    <p style="margin: 0; opacity: 0.8; font-size: 0.9rem;">© 2025 Tous droits réservés</p>
    <div style="margin-top: 20px;">
        <a href="/boutique/<?php echo htmlspecialchars($seller['shop_slug']); ?>" style="color: white; margin: 0 10px; text-decoration: none;">🏠 Accueil</a>
        <a href="/" style="color: white; margin: 0 10px; text-decoration: none;">🌐 Luxe Stars Power</a>
        <a href="/conditions" style="color: white; margin: 0 10px; text-decoration: none;">📄 CGU</a>
        <a href="/contact" style="color: white; margin: 0 10px; text-decoration: none;">✉️ Contact</a>
    </div>
</footer>

<script>
// Scroll smooth vers les produits
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Copier le lien de la boutique
function copyShopLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert('✅ Lien copié dans le presse-papiers !');
    }).catch(err => {
        // Fallback
        const tempInput = document.createElement('input');
        tempInput.value = url;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        alert('✅ Lien copié !');
    });
}
</script>

</body>
</html>