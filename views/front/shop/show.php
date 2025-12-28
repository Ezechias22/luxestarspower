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
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($seo['title'] ?? ''); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($seo['description'] ?? ''); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($seo['image'] ?? ''); ?>">
    
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
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .shop-navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .shop-navbar-brand {
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
            text-decoration: none;
        }
        
        .shop-navbar-logo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            background: white;
        }
        
        .shop-navbar-logo-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 3px solid white;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .shop-navbar-info h1 {
            margin: 0;
            font-size: 1.5rem;
            color: white;
        }
        
        .shop-navbar-info p {
            margin: 0;
            font-size: 0.85rem;
            color: rgba(255,255,255,0.9);
        }
        
        .shop-navbar-links {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .shop-navbar-links a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 5px;
            transition: all 0.3s;
            font-weight: 500;
            font-size: 0.95rem;
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
            transform: translateY(-2px);
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
        @media (max-width: 768px) {
            .shop-navbar-container {
                flex-direction: column;
                gap: 15px;
            }
            
            .shop-navbar-info h1 {
                font-size: 1.2rem;
            }
            
            .shop-navbar-links {
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
        <a href="/boutique/<?php echo htmlspecialchars($seller['shop_slug']); ?>" class="shop-navbar-brand">
            <?php if(!empty($seller['shop_logo'])): ?>
                <img src="<?php echo htmlspecialchars($seller['shop_logo']); ?>" 
                     alt="<?php echo htmlspecialchars($seller['shop_name']); ?>"
                     class="shop-navbar-logo">
            <?php else: ?>
                <div class="shop-navbar-logo-placeholder">
                    🏪
                </div>
            <?php endif; ?>
            
            <div class="shop-navbar-info">
                <h1><?php echo htmlspecialchars($seller['shop_name']); ?></h1>
                <p><?php echo $stats['products_count']; ?> produits • <?php echo $stats['sales_count']; ?> ventes</p>
            </div>
        </a>
        
        <div class="shop-navbar-links">
            <a href="/">🏠 Accueil</a>
            <a href="/produits">📦 Produits</a>
            
            <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $seller['id']): ?>
                <a href="/vendeur/tableau-de-bord" class="btn-primary">📊 Mon Dashboard</a>
            <?php else: ?>
                <a href="#products">🛍️ Parcourir</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Hero Section avec bannière -->
<div style="position: relative; height: 300px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); overflow: hidden;">
    <?php if(!empty($seller['shop_banner'])): ?>
        <img src="<?php echo htmlspecialchars($seller['shop_banner']); ?>" 
             alt="<?php echo htmlspecialchars($seller['shop_name']); ?>"
             style="width: 100%; height: 100%; object-fit: cover; opacity: 0.8;">
    <?php endif; ?>
    
    <div style="position: absolute; bottom: -50px; left: 50%; transform: translateX(-50%); text-align: center;">
        <?php if(!empty($seller['shop_logo'])): ?>
            <img src="<?php echo htmlspecialchars($seller['shop_logo']); ?>" 
                 alt="Logo <?php echo htmlspecialchars($seller['shop_name']); ?>"
                 style="width: 150px; height: 150px; border-radius: 50%; border: 5px solid white; box-shadow: 0 4px 15px rgba(0,0,0,0.2); object-fit: cover; background: white;">
        <?php else: ?>
            <div style="width: 150px; height: 150px; border-radius: 50%; border: 5px solid white; box-shadow: 0 4px 15px rgba(0,0,0,0.2); background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white;">
                🏪
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="container" style="padding: 80px 20px 60px; max-width: 1200px; margin: 0 auto;">
    
    <!-- En-tête boutique -->
    <div style="text-align: center; margin-bottom: 50px;">
        <h2 style="font-size: 2.5rem; margin-bottom: 15px; color: #2c3e50;">
            <?php echo htmlspecialchars($seller['shop_name']); ?>
        </h2>
        
        <?php if(!empty($seller['shop_description'])): ?>
            <p style="font-size: 1.2rem; color: #666; max-width: 800px; margin: 0 auto 30px;">
                <?php echo nl2br(htmlspecialchars($seller['shop_description'])); ?>
            </p>
        <?php endif; ?>
        
        <!-- Statistiques -->
        <div style="display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; margin-top: 30px;">
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: 600; color: #667eea;">
                    <?php echo $stats['products_count']; ?>
                </div>
                <div style="color: #666; font-size: 0.9rem;">
                    Produits
                </div>
            </div>
            
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: 600; color: #667eea;">
                    <?php echo $stats['sales_count']; ?>
                </div>
                <div style="color: #666; font-size: 0.9rem;">
                    Ventes totales
                </div>
            </div>
            
            <?php if($stats['reviews_count'] > 0): ?>
            <div style="text-align: center;">
                <div style="font-size: 2rem; font-weight: 600; color: #667eea;">
                    ⭐ <?php echo $stats['avg_rating']; ?>
                </div>
                <div style="color: #666; font-size: 0.9rem;">
                    <?php echo $stats['reviews_count']; ?> avis
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Boutons de partage social -->
        <div style="margin-top: 30px;">
            <p style="color: #666; margin-bottom: 10px; font-size: 0.9rem;">
                Partager cette boutique
            </p>
            <div style="display: flex; justify-content: center; gap: 10px; flex-wrap: wrap;">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($seo['url']); ?>" 
                   target="_blank" rel="noopener"
                   style="background: #3b5998; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">
                    Facebook
                </a>
                
                <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode('Découvrez ' . $seller['shop_name']); ?>&url=<?php echo urlencode($seo['url']); ?>" 
                   target="_blank" rel="noopener"
                   style="background: #1DA1F2; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">
                    Twitter
                </a>
                
                <a href="https://wa.me/?text=<?php echo urlencode('Découvrez ' . $seller['shop_name'] . ' : ' . $seo['url']); ?>" 
                   target="_blank" rel="noopener"
                   style="background: #25D366; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">
                    WhatsApp
                </a>
                
                <button onclick="copyShopLink()" 
                        style="background: #666; color: white; padding: 10px 20px; border-radius: 5px; border: none; cursor: pointer;">
                    📋 Copier le lien
                </button>
            </div>
        </div>
    </div>
    
    <!-- Produits de la boutique -->
    <div id="products">
        <h3 style="font-size: 2rem; margin-bottom: 30px; text-align: center;">
            Produits de la boutique
        </h3>
        
        <?php if(!empty($products)): ?>
            <div class="products-grid">
                <?php foreach($products as $product): ?>
                    <div class="product-card">
                        <?php if(!empty($product['thumbnail_path'])): ?>
                            <img src="<?php echo htmlspecialchars($product['thumbnail_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <?php else: ?>
                            <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 3rem;">📦</span>
                            </div>
                        <?php endif; ?>
                        
                        <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                        <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                        <a href="/produit/<?php echo htmlspecialchars($product['slug']); ?>" class="btn">
                            Voir le produit
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #666; padding: 60px 20px;">
                Aucun produit disponible pour le moment
            </p>
        <?php endif; ?>
    </div>
    
</div>

<!-- Footer simplifié -->
<footer style="background: #2c3e50; color: white; padding: 40px 20px; margin-top: 60px; text-align: center;">
    <p style="margin: 0 0 10px;">Propulsé par <strong>Luxe Stars Power</strong></p>
    <p style="margin: 0; opacity: 0.8; font-size: 0.9rem;">© 2025 Tous droits réservés</p>
    <div style="margin-top: 20px;">
        <a href="/" style="color: white; margin: 0 10px; text-decoration: none;">🏠 Accueil</a>
        <a href="/produits" style="color: white; margin: 0 10px; text-decoration: none;">📦 Produits</a>
        <a href="/conditions" style="color: white; margin: 0 10px; text-decoration: none;">📄 CGU</a>
        <a href="/contact" style="color: white; margin: 0 10px; text-decoration: none;">✉️ Contact</a>
    </div>
</footer>

<script>
function copyShopLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert('Lien copié dans le presse-papiers !');
    }).catch(err => {
        console.error('Erreur:', err);
        // Fallback pour navigateurs anciens
        const tempInput = document.createElement('input');
        tempInput.value = url;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        alert('Lien copié !');
    });
}
</script>

</body>
</html>