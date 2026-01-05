<?php ob_start(); ?>

<style>
.subscription-details {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.detail-card {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.detail-item {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.detail-item label {
    display: block;
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 5px;
    text-transform: uppercase;
}

.detail-item .value {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
}

.action-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 20px;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-primary {
    background: #667eea;
    color: white;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal.active {
    display: flex;
}

.modal-content {
    background: white;
    padding: 30px;
    border-radius: 10px;
    max-width: 500px;
    width: 90%;
}

.payments-table {
    background: white;
    border-radius: 10px;
    overflow: hidden;
}

.payments-table table {
    width: 100%;
    border-collapse: collapse;
}

.payments-table th {
    background: #f8f9fa;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    border-bottom: 2px solid #e0e0e0;
}

.payments-table td {
    padding: 12px;
    border-bottom: 1px solid #f0f0f0;
}
</style>

<div class="subscription-details">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>💎 Détails de l'Abonnement</h1>
        <a href="/admin/abonnements" class="btn btn-secondary">← Retour</a>
    </div>

    <!-- Informations Utilisateur -->
    <div class="detail-card">
        <h2 style="margin-bottom: 20px;">👤 Informations Utilisateur</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Nom</label>
                <div class="value"><?php echo htmlspecialchars($subscription['user_name']); ?></div>
            </div>
            <div class="detail-item">
                <label>Email</label>
                <div class="value"><?php echo htmlspecialchars($subscription['user_email']); ?></div>
            </div>
            <div class="detail-item">
                <label>Rôle</label>
                <div class="value"><?php echo htmlspecialchars($subscription['user_role']); ?></div>
            </div>
            <div class="detail-item">
                <label>Produits publiés</label>
                <div class="value">
                    <?php echo $productsCount; ?> / 
                    <?php echo $subscription['max_products'] == -1 ? '∞' : $subscription['max_products']; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations Abonnement -->
    <div class="detail-card">
        <h2 style="margin-bottom: 20px;">💎 Abonnement Actuel</h2>
        <div class="detail-grid">
            <div class="detail-item">
                <label>Plan</label>
                <div class="value"><?php echo htmlspecialchars($subscription['plan_name']); ?></div>
            </div>
            <div class="detail-item">
                <label>Prix</label>
                <div class="value">
                    $<?php echo number_format($subscription['plan_price'], 2); ?>/<?php echo $subscription['billing_period'] === 'monthly' ? 'mois' : 'an'; ?>
                </div>
            </div>
            <div class="detail-item">
                <label>Commission</label>
                <div class="value"><?php echo number_format($subscription['commission_rate'], 1); ?>%</div>
            </div>
            <div class="detail-item">
                <label>Status</label>
                <div class="value">
                    <span class="status-badge <?php echo $subscription['status']; ?>">
                        <?php 
                        $statusLabels = [
                            'trial' => '🎁 Essai Gratuit',
                            'active' => '✅ Actif',
                            'cancelled' => '❌ Annulé',
                            'expired' => '⏰ Expiré'
                        ];
                        echo $statusLabels[$subscription['status']] ?? $subscription['status'];
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="detail-grid" style="margin-top: 20px;">
            <div class="detail-item">
                <label>Date de création</label>
                <div class="value"><?php echo date('d/m/Y H:i', strtotime($subscription['created_at'])); ?></div>
            </div>
            <div class="detail-item">
                <label>Début période actuelle</label>
                <div class="value"><?php echo date('d/m/Y', strtotime($subscription['current_period_start'])); ?></div>
            </div>
            <div class="detail-item">
                <label>Fin période actuelle</label>
                <div class="value"><?php echo date('d/m/Y', strtotime($subscription['current_period_end'])); ?></div>
            </div>
            <?php if ($subscription['status'] === 'trial'): ?>
            <div class="detail-item">
                <label>Fin de l'essai</label>
                <div class="value"><?php echo date('d/m/Y', strtotime($subscription['trial_ends_at'])); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($subscription['stripe_subscription_id']): ?>
        <div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border-radius: 8px;">
            <strong>🔗 Stripe Subscription ID:</strong> 
            <code style="background: white; padding: 5px 10px; border-radius: 4px; margin-left: 10px;">
                <?php echo htmlspecialchars($subscription['stripe_subscription_id']); ?>
            </code>
        </div>
        <?php endif; ?>

        <!-- Actions Admin -->
        <div class="action-buttons">
            <button onclick="openChangePlanModal()" class="btn btn-primary">
                🔄 Changer de Plan
            </button>
            
            <button onclick="openExtendModal()" class="btn btn-success">
                ⏰ Prolonger la Période
            </button>

            <?php if ($subscription['status'] === 'active' || $subscription['status'] === 'trial'): ?>
            <form method="POST" action="/admin/abonnements/annuler" style="margin: 0;" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cet abonnement ?');">
                <input type="hidden" name="subscription_id" value="<?php echo $subscription['id']; ?>">
                <button type="submit" class="btn btn-danger">
                    ❌ Annuler l'Abonnement
                </button>
            </form>
            <?php elseif ($subscription['status'] === 'cancelled'): ?>
            <form method="POST" action="/admin/abonnements/reactiver" style="margin: 0;">
                <input type="hidden" name="subscription_id" value="<?php echo $subscription['id']; ?>">
                <button type="submit" class="btn btn-success">
                    ♻️ Réactiver l'Abonnement
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Historique des Paiements -->
    <?php if (!empty($payments)): ?>
    <div class="detail-card">
        <h2 style="margin-bottom: 20px;">💳 Historique des Paiements</h2>
        <div class="payments-table">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Montant</th>
                        <th>Devise</th>
                        <th>Status</th>
                        <th>Stripe Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($payment['created_at'])); ?></td>
                        <td><strong>$<?php echo number_format($payment['amount'], 2); ?></strong></td>
                        <td><?php echo htmlspecialchars($payment['currency']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $payment['status'] === 'succeeded' ? 'active' : 'cancelled'; ?>">
                                <?php echo $payment['status'] === 'succeeded' ? '✅ Réussi' : '❌ Échoué'; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($payment['stripe_invoice_id']): ?>
                                <code style="font-size: 0.85rem;"><?php echo htmlspecialchars($payment['stripe_invoice_id']); ?></code>
                            <?php else: ?>
                                <span style="color: #999;">N/A</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Changer de Plan -->
<div id="changePlanModal" class="modal">
    <div class="modal-content">
        <h2 style="margin-bottom: 20px;">🔄 Changer de Plan</h2>
        <form method="POST" action="/admin/abonnements/changer-plan">
            <input type="hidden" name="subscription_id" value="<?php echo $subscription['id']; ?>">
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Nouveau Plan</label>
                <select name="new_plan_id" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;">
                    <?php foreach ($plans as $plan): ?>
                        <option value="<?php echo $plan['id']; ?>" <?php echo $plan['id'] == $subscription['plan_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($plan['name']); ?> - 
                            $<?php echo number_format($plan['price'], 2); ?>/<?php echo $plan['billing_period'] === 'monthly' ? 'mois' : 'an'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    ✅ Confirmer
                </button>
                <button type="button" onclick="closeChangePlanModal()" class="btn btn-secondary" style="flex: 1;">
                    ❌ Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Prolonger -->
<div id="extendModal" class="modal">
    <div class="modal-content">
        <h2 style="margin-bottom: 20px;">⏰ Prolonger la Période</h2>
        <form method="POST" action="/admin/abonnements/prolonger">
            <input type="hidden" name="subscription_id" value="<?php echo $subscription['id']; ?>">
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Nombre de jours</label>
                <input type="number" name="days" min="1" max="365" value="30" required 
                       style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;">
                <small style="color: #666; display: block; margin-top: 5px;">
                    La période sera prolongée du nombre de jours indiqué
                </small>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-success" style="flex: 1;">
                    ✅ Prolonger
                </button>
                <button type="button" onclick="closeExtendModal()" class="btn btn-secondary" style="flex: 1;">
                    ❌ Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openChangePlanModal() {
    document.getElementById('changePlanModal').classList.add('active');
}

function closeChangePlanModal() {
    document.getElementById('changePlanModal').classList.remove('active');
}

function openExtendModal() {
    document.getElementById('extendModal').classList.add('active');
}

function closeExtendModal() {
    document.getElementById('extendModal').classList.remove('active');
}

// Fermer les modals en cliquant à l'extérieur
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});
</script>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>