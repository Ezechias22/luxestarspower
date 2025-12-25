<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>Mes Produits</h1>
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
                <div class="product-card">
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

                    <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                    <p style="color: #667eea; font-size: 0.875rem; text-transform: uppercase; font-weight: 600; margin: 8px 0;">
                        <?php echo htmlspecialchars($product['type'] ?? 'file'); ?>
                    </p>
                    <p class="price">$<?php echo number_format($product['price'], 2); ?></p>

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
                        
                        <?php if(!empty($product['is_featured'])): ?>
                            <span style="background: #fff3cd; color: #856404; padding: 5px 12px; border-radius: 20px; font-size: 0.875rem;">
                                ⭐ Vedette
                            </span>
                        <?php endif; ?>
                    </div>

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