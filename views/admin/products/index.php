<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px;">
    <h1 style="margin-bottom: 30px;">📦 Gestion des Produits</h1>
    
    <?php if(isset($_SESSION['flash_success'])): ?>
        <div style="background: #e8f5e9; border: 1px solid #4caf50; color: #2e7d32; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            ✅ <?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(empty($products)): ?>
        <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 10px;">
            <h2 style="color: #666; margin-bottom: 20px;">Aucun produit</h2>
            <p style="color: #999;">Les produits apparaîtront ici</p>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach($products as $product): ?>
                <div class="product-card">
                    <?php if(!empty($product['thumbnail_path'])): ?>
                        <img src="<?php echo htmlspecialchars($product['thumbnail_path']); ?>" 
                             alt="<?php echo htmlspecialchars($product['title']); ?>"
                             style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 15px;">
                    <?php else: ?>
                        <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                            📦
                        </div>
                    <?php endif; ?>
                    
                    <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                    <p style="color: #667eea; font-size: 0.875rem; text-transform: uppercase; font-weight: 600; margin: 8px 0;">
                        <?php echo htmlspecialchars($product['type'] ?? 'file'); ?>
                    </p>
                    <p class="price"><?php echo number_format($product['price'], 2); ?> €</p>
                    
                    <p style="font-size: 0.875rem; color: #666; margin: 10px 0;">
                        Vendeur ID: <?php echo $product['seller_id']; ?>
                    </p>
                    
                    <p style="margin: 10px 0;">
                        <?php if($product['is_active'] ?? 0): ?>
                            <span style="color: #4caf50; font-weight: 600;">✅ Actif</span>
                        <?php else: ?>
                            <span style="color: #f44336; font-weight: 600;">❌ Inactif</span>
                        <?php endif; ?>
                    </p>
                    
                    <div style="display: flex; gap: 5px; margin-top: 15px; flex-wrap: wrap;">
                        <?php if(!($product['is_active'] ?? 0)): ?>
                            <form method="POST" action="/admin/produits/<?php echo $product['id']; ?>/approuver" style="flex: 1;">
                                <button type="submit" class="btn" style="width: 100%; background: #4caf50;">
                                    ✓ Approuver
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="/admin/produits/<?php echo $product['id']; ?>/rejeter" style="flex: 1;">
                                <button type="submit" class="btn" style="width: 100%; background: #ff9800;">
                                    ✕ Désactiver
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <form method="POST" action="/admin/produits/<?php echo $product['id']; ?>/featured" style="flex: 1;">
                            <button type="submit" class="btn" style="width: 100%; background: #2196f3;">
                                ⭐ Featured
                            </button>
                        </form>
                        
                        <form method="POST" action="/admin/produits/<?php echo $product['id']; ?>/supprimer" style="flex: 1;">
                            <button type="submit" class="btn" style="width: 100%; background: #e74c3c;" 
                                    onclick="return confirm('Supprimer ce produit ?')">
                                🗑️ Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>