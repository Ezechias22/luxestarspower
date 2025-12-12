<?php ob_start(); ?>

<div class="hero">
    <div class="container">
        <h1>LuxeStarsPower - Marketplace Premium</h1>
        <p>Achetez et vendez des produits numériques de qualité</p>
        <a href="/produits" class="btn btn-primary">Découvrir</a>
    </div>
</div>

<section class="featured">
    <div class="container">
        <h2>Produits en vedette</h2>
        <div class="products-grid">
            <?php if (!empty($featuredProducts)): ?>
                <?php foreach($featuredProducts as $product): ?>
                    <div class="product-card">
                        <?php if(!empty($product['thumbnail_path'])): ?>
                            <img src="<?php echo htmlspecialchars($product['thumbnail_path']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                        <p class="price"><?php echo number_format($product['price'], 2); ?> €</p>
                        <a href="/produit/<?php echo htmlspecialchars($product['slug']); ?>" class="btn">Voir</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun produit en vedette pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="recent">
    <div class="container">
        <h2>Récemment ajoutés</h2>
        <div class="products-grid">
            <?php if (!empty($latestProducts)): ?>
                <?php foreach($latestProducts as $product): ?>
                    <div class="product-card">
                        <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                        <p class="price"><?php echo number_format($product['price'], 2); ?> €</p>
                        <a href="/produit/<?php echo htmlspecialchars($product['slug']); ?>" class="btn">Voir</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun produit récent pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>