<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>📦 Mes Produits</h1>
        <a href="/vendeur/produits/nouveau" class="btn btn-primary">➕ Ajouter un produit</a>
    </div>

    <?php if(isset($_SESSION['flash_success'])): ?>
        <div style="background: #e8f5e9; border: 1px solid #4caf50; color: #2e7d32; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            ✅ <?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['flash_error'])): ?>
        <div style="background: #ffebee; border: 1px solid #f44336; color: #c62828; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            ❌ <?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($products) || !is_array($products)): ?>
        <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 10px;">
            <h2 style="color: #666; margin-bottom: 20px;">Aucun produit pour le moment</h2>
            <p style="font-size: 1.1rem; color: #999; margin-bottom: 30px;">
                Commencez à vendre en ajoutant votre premier produit
            </p>
            <a href="/vendeur/produits/nouveau" class="btn btn-primary">Ajouter mon premier produit</a>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach($products as $product): ?>
                <div class="product-card" style="position: relative;">
                    
                    <!-- Badge PROMO si en promotion -->
                    <?php if(!empty($product['is_on_sale'])): ?>
                        <div style="position: absolute; top: 10px; right: 10px; background: #ff4444; color: white; padding: 8px 15px; border-radius: 20px; font-weight: bold; font-size: 0.85rem; z-index: 10; box-shadow: 0 2px 8px rgba(255,68,68,0.4);">
                            🔥 -<?php echo $product['discount_percentage']; ?>%
                        </div>
                    <?php endif; ?>
                    
                    <!-- Badge Vedette -->
                    <?php if(!empty($product['is_featured'])): ?>
                        <div style="position: absolute; top: 10px; left: 10px; background: #ffc107; color: #333; padding: 8px 15px; border-radius: 20px; font-weight: bold; font-size: 0.85rem; z-index: 10; box-shadow: 0 2px 8px rgba(255,193,7,0.4);">
                            ⭐ Vedette
                        </div>
                    <?php endif; ?>
                    
                    <!-- Image -->
                    <?php if(!empty($product['thumbnail_path'])): ?>
                        <img src="<?php echo htmlspecialchars($product['thumbnail_path']); ?>"
                             alt="<?php echo htmlspecialchars($product['title']); ?>">
                    <?php else: ?>
                        <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                            <?php
                            $typeEmojis = [
                                'ebook' => '📚',
                                'video' => '🎥',
                                'image' => '🖼️',
                                'course' => '🎓',
                                'file' => '📁'
                            ];
                            echo $typeEmojis[$product['type']] ?? '📦';
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Titre et type -->
                    <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                    <p style="color: #667eea; font-size: 0.875rem; text-transform: uppercase; font-weight: 600; margin: 8px 0;">
                        <?php echo htmlspecialchars($product['type'] ?? 'file'); ?>
                    </p>
                    
                    <!-- Prix avec promo -->
                    <?php if(!empty($product['is_on_sale']) && !empty($product['original_price'])): ?>
                        <div style="margin: 10px 0;">
                            <span style="text-decoration: line-through; color: #999; font-size: 0.9rem;">
                                $<?php echo number_format($product['original_price'], 2); ?>
                            </span>
                            <span class="price" style="color: #ff4444; margin-left: 8px;">
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
                    <div style="margin: 15px 0;">
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

                    <!-- Statut -->
                    <div style="margin: 10px 0;">
                        <?php if($product['is_active']): ?>
                            <span style="background: #e8f5e9; color: #2e7d32; padding: 5px 12px; border-radius: 20px; font-size: 0.875rem;">
                                ✅ Actif
                            </span>
                        <?php else: ?>
                            <span style="background: #ffebee; color: #c62828; padding: 5px 12px; border-radius: 20px; font-size: 0.875rem;">
                                ❌ Inactif
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Actions -->
                    <div style="display: flex; gap: 10px; margin-top: 15px;">
                        <a href="/vendeur/produits/<?php echo $product['id']; ?>/modifier" class="btn" style="flex: 1;">
                            ✏️ Modifier
                        </a>
                        <form method="POST" action="/vendeur/produits/<?php echo $product['id']; ?>/supprimer" style="flex: 1;">
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