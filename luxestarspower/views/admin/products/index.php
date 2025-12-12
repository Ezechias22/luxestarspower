<?php ob_start(); ?>

<div class="admin-products">
    <div class="container">
        <h1>Gestion des produits</h1>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Vendeur</th>
                    <th>Type</th>
                    <th>Prix</th>
                    <th>Ventes</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $product): ?>
                <tr>
                    <td><?= $product->id ?></td>
                    <td><?= htmlspecialchars($product->title) ?></td>
                    <td><?= $product->seller_id ?></td>
                    <td><?= htmlspecialchars($product->type) ?></td>
                    <td>$<?= number_format($product->price, 2) ?></td>
                    <td><?= $product->sales ?></td>
                    <td>
                        <?php if($product->is_active): ?>
                            <span class="badge badge-active">Actif</span>
                        <?php else: ?>
                            <span class="badge badge-inactive">Inactif</span>
                        <?php endif; ?>
                        <?php if($product->is_featured): ?>
                            <span class="badge badge-featured">⭐ Vedette</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" action="/admin/product/<?= $product->id ?>/toggle" style="display:inline">
                            <button type="submit" class="btn btn-sm">
                                <?= $product->is_active ? 'Désactiver' : 'Activer' ?>
                            </button>
                        </form>
                        <form method="POST" action="/admin/product/<?= $product->id ?>/feature" style="display:inline">
                            <button type="submit" class="btn btn-sm">
                                <?= $product->is_featured ? 'Retirer vedette' : 'Mettre en vedette' ?>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.badge-featured { background: #fef3c7; color: #92400e; }
</style>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>
