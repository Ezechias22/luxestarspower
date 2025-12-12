<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>Mes Produits</h1>
        <a href="/vendeur/produits/nouveau" class="btn btn-primary">➕ Ajouter un produit</a>
    </div>
    
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
                    <?php endif; ?>
                    
                    <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                    <p class="price"><?php echo number_format($product['price'], 2); ?> €</p>
                    
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