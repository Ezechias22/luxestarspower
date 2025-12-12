<?php ob_start(); ?>

<div class="admin-dashboard">
    <div class="container">
        <h1>Dashboard Admin</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Utilisateurs totaux</h3>
                <p class="stat-number"><?= $stats['total_users'] ?? 0 ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Vendeurs</h3>
                <p class="stat-number"><?= $stats['total_sellers'] ?? 0 ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Revenus totaux</h3>
                <p class="stat-number">$<?= number_format($stats['total_revenue'] ?? 0, 2) ?></p>
            </div>
        </div>
        
        <div class="admin-nav">
            <a href="/admin/users" class="btn">Utilisateurs</a>
            <a href="/admin/products" class="btn">Produits</a>
            <a href="/admin/orders" class="btn">Commandes</a>
            <a href="/admin/payouts" class="btn">Paiements</a>
            <a href="/admin/settings" class="btn">Paramètres</a>
        </div>
        
        <h2>Commandes récentes</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Acheteur</th>
                    <th>Produit</th>
                    <th>Montant</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($stats['recent_orders'] ?? [] as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order->order_number) ?></td>
                    <td><?= htmlspecialchars($order->buyer_name ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($order->product_title ?? 'N/A') ?></td>
                    <td>$<?= number_format($order->amount, 2) ?></td>
                    <td><span class="badge badge-<?= $order->status ?>"><?= $order->status ?></span></td>
                    <td><?= date('d/m/Y', strtotime($order->created_at)) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin: 2rem 0;
}
.stat-card {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    border: 1px solid var(--border);
}
.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary);
    margin: 1rem 0 0;
}
.admin-nav {
    display: flex;
    gap: 1rem;
    margin: 2rem 0;
    flex-wrap: wrap;
}
.data-table {
    width: 100%;
    background: white;
    border-collapse: collapse;
    margin-top: 1rem;
}
.data-table th, .data-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--border);
}
.data-table th {
    background: var(--light);
    font-weight: 600;
}
.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.875rem;
}
.badge-paid { background: #d1fae5; color: #065f46; }
.badge-pending { background: #fef3c7; color: #92400e; }
.badge-failed { background: #fee2e2; color: #991b1b; }
</style>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>
