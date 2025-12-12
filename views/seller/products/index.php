<?php ob_start(); ?>

<div class="seller-products">
    <div class="container">
        <div class="page-header">
            <h1>Mes produits</h1>
            <a href="/vendeur/produit/nouveau" class="btn btn-primary">+ Ajouter</a>
        </div>
        
        <?php if (empty($products)): ?>
            <div class="empty-state">
                <p>Vous n'avez pas encore de produits.</p>
                <a href="/vendeur/produit/nouveau" class="btn btn-primary">Ajouter votre premier produit</a>
            </div>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Type</th>
                        <th>Prix</th>
                        <th>Ventes</th>
                        <th>Vues</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $product): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($product->title) ?></strong><br>
                            <small><?= htmlspecialchars($product->slug) ?></small>
                        </td>
                        <td><?= htmlspecialchars($product->type) ?></td>
                        <td>$<?= number_format($product->price, 2) ?></td>
                        <td><?= $product->sales ?></td>
                        <td><?= $product->views ?></td>
                        <td>
                            <?php if($product->is_active): ?>
                                <span class="badge badge-active">Actif</span>
                            <?php else: ?>
                                <span class="badge badge-inactive">Inactif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/vendeur/produit/<?= $product->id ?>/edit" class="btn btn-sm">Modifier</a>
                            <a href="/produit/<?= $product->slug ?>" class="btn btn-sm" target="_blank">Voir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<style>
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.empty-state { text-align: center; padding: 4rem 2rem; }
.btn-sm { padding: 0.5rem 1rem; font-size: 0.875rem; }
.badge-active { background: #d1fae5; color: #065f46; }
.badge-inactive { background: #e5e7eb; color: #374151; }
</style>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>
