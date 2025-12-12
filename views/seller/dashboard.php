<?php ob_start(); ?>

<div class="seller-dashboard">
    <div class="container">
        <h1>Dashboard Vendeur</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Produits</h3>
                <p class="stat-number"><?= $stats['total_products'] ?? 0 ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Revenus totaux</h3>
                <p class="stat-number">$<?= number_format($stats['total_sales'] ?? 0, 2) ?></p>
            </div>
        </div>
        
        <div class="quick-actions">
            <a href="/vendeur/produit/nouveau" class="btn btn-primary">+ Ajouter un produit</a>
            <a href="/vendeur/produits" class="btn">Mes produits</a>
            <a href="/vendeur/commandes" class="btn">Commandes</a>
            <a href="/vendeur/payouts" class="btn">Paiements</a>
        </div>
        
        <h2>Commandes récentes</h2>
        <?php if (empty($stats['recent_orders'])): ?>
            <p>Aucune commande pour le moment.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>N° Commande</th>
                        <th>Acheteur</th>
                        <th>Montant</th>
                        <th>Vos revenus</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($stats['recent_orders'] as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order->order_number) ?></td>
                        <td><?= htmlspecialchars($order->buyer_name ?? 'N/A') ?></td>
                        <td>$<?= number_format($order->amount, 2) ?></td>
                        <td>$<?= number_format($order->seller_earnings, 2) ?></td>
                        <td><span class="badge badge-<?= $order->status ?>"><?= $order->status ?></span></td>
                        <td><?= date('d/m/Y H:i', strtotime($order->created_at)) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>
