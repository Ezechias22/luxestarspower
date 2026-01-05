<?php ob_start(); ?>

<style>
.stats-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-card h3 {
    font-size: 2.5rem;
    margin: 0 0 10px;
    color: #667eea;
}

.stat-card p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.chart-card {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #f8f9fa;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    border-bottom: 2px solid #e0e0e0;
}

td {
    padding: 12px;
    border-bottom: 1px solid #f0f0f0;
}

.btn-secondary {
    padding: 10px 20px;
    background: #6c757d;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
}
</style>

<div class="stats-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>📊 Statistiques des Abonnements</h1>
        <a href="/admin/abonnements" class="btn-secondary">← Retour à la liste</a>
    </div>

    <!-- Statistiques globales -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3><?php echo $stats['total']; ?></h3>
            <p>Total Abonnements</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $stats['active']; ?></h3>
            <p>Abonnements Actifs</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $stats['trial']; ?></h3>
            <p>Essais Gratuits</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $stats['cancelled']; ?></h3>
            <p>Annulés</p>
        </div>
    </div>

    <!-- Revenus -->
    <div class="stats-grid">
        <div class="stat-card" style="grid-column: span 2;">
            <h3>$<?php echo number_format($stats['total_revenue'], 2); ?></h3>
            <p>Revenus Total</p>
        </div>
        <div class="stat-card" style="grid-column: span 2;">
            <h3>$<?php echo number_format($stats['month_revenue'], 2); ?></h3>
            <p>Revenus ce Mois</p>
        </div>
    </div>

    <!-- Taux de conversion -->
    <div class="chart-card">
        <h2 style="margin-bottom: 20px;">📈 Taux de Conversion</h2>
        <div style="text-align: center; padding: 40px;">
            <h3 style="font-size: 3rem; color: #667eea; margin: 0;">
                <?php echo $stats['conversion_rate']; ?>%
            </h3>
            <p style="color: #666; margin-top: 10px;">
                Taux de conversion des essais gratuits vers plans payants
            </p>
        </div>
    </div>

    <!-- Revenus mensuels -->
    <?php if (!empty($monthlyRevenue)): ?>
    <div class="chart-card">
        <h2 style="margin-bottom: 20px;">💰 Revenus des 6 Derniers Mois</h2>
        <table>
            <thead>
                <tr>
                    <th>Mois</th>
                    <th>Revenus</th>
                    <th>Nombre de Paiements</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($monthlyRevenue as $month): ?>
                <tr>
                    <td>
                        <strong>
                            <?php 
                            $date = new DateTime($month['month'] . '-01');
                            echo strftime('%B %Y', $date->getTimestamp());
                            ?>
                        </strong>
                    </td>
                    <td>
                        <strong style="color: #28a745;">
                            $<?php echo number_format($month['revenue'], 2); ?>
                        </strong>
                    </td>
                    <td>
                        <?php echo $month['payments']; ?> paiements
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Actions rapides -->
    <div style="text-align: center; margin-top: 40px;">
        <a href="/admin/abonnements" class="btn-secondary">
            📋 Voir tous les abonnements
        </a>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>