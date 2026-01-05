<?php ob_start(); ?>

<style>
.admin-subscriptions {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.filters-bar {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.filter-tabs {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.filter-tab {
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    border: 2px solid #e0e0e0;
    color: #666;
}

.filter-tab:hover {
    border-color: #667eea;
    color: #667eea;
}

.filter-tab.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
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

.subscriptions-table {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    background: #f8f9fa;
}

th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #e0e0e0;
}

td {
    padding: 15px;
    border-bottom: 1px solid #f0f0f0;
}

tr:hover {
    background: #f8f9fa;
}

.status-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-badge.trial {
    background: #ffeaa7;
    color: #d63031;
}

.status-badge.active {
    background: #d4edda;
    color: #155724;
}

.status-badge.cancelled {
    background: #f8d7da;
    color: #721c24;
}

.status-badge.expired {
    background: #f0f0f0;
    color: #666;
}

.action-btn {
    padding: 8px 15px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.3s;
}

.action-btn.primary {
    background: #667eea;
    color: white;
}

.action-btn.primary:hover {
    background: #5568d3;
}

.search-box {
    display: flex;
    gap: 10px;
    align-items: center;
}

.search-box input {
    flex: 1;
    padding: 10px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
}

.search-box button {
    padding: 10px 20px;
    background: #667eea;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}
</style>

<div class="admin-subscriptions">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>💎 Gestion des Abonnements</h1>
        <a href="/admin/abonnements/statistiques" class="action-btn primary">
            📊 Statistiques détaillées
        </a>
    </div>

    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3><?php echo $stats['total']; ?></h3>
            <p>Total abonnements</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $stats['active']; ?></h3>
            <p>Actifs</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $stats['trial']; ?></h3>
            <p>Essais gratuits</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $stats['cancelled']; ?></h3>
            <p>Annulés</p>
        </div>
        <div class="stat-card">
            <h3>$<?php echo number_format($stats['month_revenue'], 2); ?></h3>
            <p>Revenus ce mois</p>
        </div>
        <div class="stat-card">
            <h3>$<?php echo number_format($stats['total_revenue'], 2); ?></h3>
            <p>Revenus total</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="filters-bar">
        <div class="filter-tabs">
            <a href="/admin/abonnements?status=all" 
               class="filter-tab <?php echo $currentStatus === 'all' ? 'active' : ''; ?>">
                📋 Tous (<?php echo $stats['total']; ?>)
            </a>
            <a href="/admin/abonnements?status=trial" 
               class="filter-tab <?php echo $currentStatus === 'trial' ? 'active' : ''; ?>">
                🎁 Essais (<?php echo $stats['trial']; ?>)
            </a>
            <a href="/admin/abonnements?status=active" 
               class="filter-tab <?php echo $currentStatus === 'active' ? 'active' : ''; ?>">
                ✅ Actifs (<?php echo $stats['active']; ?>)
            </a>
            <a href="/admin/abonnements?status=cancelled" 
               class="filter-tab <?php echo $currentStatus === 'cancelled' ? 'active' : ''; ?>">
                ❌ Annulés (<?php echo $stats['cancelled']; ?>)
            </a>
        </div>

        <!-- Recherche -->
        <form method="GET" action="/admin/abonnements" class="search-box">
            <input type="hidden" name="status" value="<?php echo htmlspecialchars($currentStatus); ?>">
            <input type="text" 
                   name="search" 
                   placeholder="Rechercher par nom ou email..." 
                   value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit">🔍 Rechercher</button>
        </form>
    </div>

    <!-- Tableau -->
    <div class="subscriptions-table">
        <?php if (empty($subscriptions)): ?>
            <div style="padding: 60px; text-align: center; color: #999;">
                <h2>😔 Aucun abonnement trouvé</h2>
                <p>Aucun résultat ne correspond à vos critères de recherche.</p>
            </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th>Fin de période</th>
                    <th>Créé le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subscriptions as $sub): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($sub['user_name']); ?></strong>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($sub['user_email']); ?>
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($sub['plan_name']); ?></strong>
                        <br>
                        <small style="color: #999;">
                            $<?php echo number_format($sub['plan_price'], 2); ?>/<?php echo $sub['billing_period'] === 'monthly' ? 'mois' : 'an'; ?>
                        </small>
                    </td>
                    <td>
                        <span class="status-badge <?php echo $sub['status']; ?>">
                            <?php 
                            $statusLabels = [
                                'trial' => '🎁 Essai',
                                'active' => '✅ Actif',
                                'cancelled' => '❌ Annulé',
                                'expired' => '⏰ Expiré'
                            ];
                            echo $statusLabels[$sub['status']] ?? $sub['status'];
                            ?>
                        </span>
                        <?php if ($sub['cancel_at_period_end']): ?>
                            <br><small style="color: #dc3545;">⚠️ Annulation programmée</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php 
                        $endDate = $sub['status'] === 'trial' ? $sub['trial_ends_at'] : $sub['current_period_end'];
                        echo date('d/m/Y', strtotime($endDate));
                        ?>
                    </td>
                    <td>
                        <?php echo date('d/m/Y', strtotime($sub['created_at'])); ?>
                    </td>
                    <td>
                        <a href="/admin/abonnements/<?php echo $sub['id']; ?>" class="action-btn primary">
                            👁️ Détails
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>