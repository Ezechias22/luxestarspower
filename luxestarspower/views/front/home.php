<?php ob_start(); ?>

<div class="hero">
    <div class="container">
        <h1><?= __('nav.home') ?> - Marketplace Premium</h1>
        <p>Achetez et vendez des produits numériques de qualité</p>
        <a href="/produits" class="btn btn-primary">Découvrir</a>
    </div>
</div>

<section class="featured">
    <div class="container">
        <h2>Produits en vedette</h2>
        <div class="products-grid">
            <?php foreach($featured as $product): ?>
                <div class="product-card">
                    <?php if($product->thumbnail_path): ?>
                        <img src="<?= $product->thumbnail_path ?>" alt="<?= htmlspecialchars($product->title) ?>">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($product->title) ?></h3>
                    <p class="price"><?= $product->formatPrice() ?></p>
                    <a href="/produit/<?= $product->slug ?>" class="btn">Voir</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="recent">
    <div class="container">
        <h2>Récemment ajoutés</h2>
        <div class="products-grid">
            <?php foreach($recent as $product): ?>
                <div class="product-card">
                    <h3><?= htmlspecialchars($product->title) ?></h3>
                    <p class="price"><?= $product->formatPrice() ?></p>
                    <a href="/produit/<?= $product->slug ?>" class="btn">Voir</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>
