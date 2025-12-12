<?php ob_start(); ?>

<div class="products-page">
    <div class="container">
        <h1>Catalogue de produits</h1>
        
        <div class="filters-bar">
            <form method="GET" action="/produits">
                <select name="type" onchange="this.form.submit()">
                    <option value="">Tous les types</option>
                    <option value="ebook" <?= ($_GET['type'] ?? '') === 'ebook' ? 'selected' : '' ?>>Ebooks</option>
                    <option value="video" <?= ($_GET['type'] ?? '') === 'video' ? 'selected' : '' ?>>Vidéos</option>
                    <option value="image" <?= ($_GET['type'] ?? '') === 'image' ? 'selected' : '' ?>>Images</option>
                    <option value="course" <?= ($_GET['type'] ?? '') === 'course' ? 'selected' : '' ?>>Formations</option>
                    <option value="file" <?= ($_GET['type'] ?? '') === 'file' ? 'selected' : '' ?>>Fichiers</option>
                </select>
            </form>
        </div>
        
        <?php if (empty($products)): ?>
            <div class="empty-state">
                <p>Aucun produit trouvé.</p>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach($products as $product): ?>
                    <div class="product-card">
                        <?php if($product->thumbnail_path): ?>
                            <img src="<?= htmlspecialchars($product->thumbnail_path) ?>" alt="<?= htmlspecialchars($product->title) ?>">
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($product->title) ?></h3>
                        <p class="product-type"><?= htmlspecialchars($product->type) ?></p>
                        <p class="price"><?= $product->formatPrice() ?></p>
                        <a href="/produit/<?= htmlspecialchars($product->slug) ?>" class="btn">Voir détails</a>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if($page > 1 || count($products) >= 20): ?>
                <div class="pagination">
                    <?php if($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?><?= isset($filters['type']) ? '&type=' . $filters['type'] : '' ?>" class="btn">← Précédent</a>
                    <?php endif; ?>
                    <?php if(count($products) >= 20): ?>
                        <a href="?page=<?= $page + 1 ?><?= isset($filters['type']) ? '&type=' . $filters['type'] : '' ?>" class="btn">Suivant →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.filters-bar { margin: 2rem 0; }
.filters-bar select { padding: 0.75rem; border: 1px solid var(--border); border-radius: 6px; }
.product-type { color: #666; font-size: 0.875rem; text-transform: uppercase; }
.pagination { display: flex; gap: 1rem; justify-content: center; margin: 3rem 0; }
</style>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>
