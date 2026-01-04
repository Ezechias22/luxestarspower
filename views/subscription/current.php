<?php ob_start(); ?>

<style>
/* Styles inchangés - gardez les mêmes */
.subscription-container {
    max-width: 1000px;
    margin: 50px auto;
    padding: 20px;
}

.subscription-card {
    background: white;
    border-radius: 15px;
    padding: 40px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.subscription-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.plan-badge {
    display: inline-block;
    padding: 10px 20px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 1.1rem;
}

.plan-badge.trial {
    background: #ffeaa7;
    color: #d63031;
}

.plan-badge.active {
    background: #d4edda;
    color: #155724;
}

.plan-badge.cancelled {
    background: #f8d7da;
    color: #721c24;
}

.subscription-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.info-box {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
}

.info-box h4 {
    margin: 0 0 10px;
    color: #666;
    font-size: 0.9rem;
    text-transform: uppercase;
}

.info-box p {
    margin: 0;
    font-size: 1.3rem;
    font-weight: bold;
    color: #333;
}

.action-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: transform 0.3s;
    border: none;
    cursor: pointer;
    display: inline-block;
}

.btn:hover {
    transform: translateY(-2px);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

.btn-success {
    background: #28a745;
    color: white;
}

.plans-comparison {
    margin-top: 50px;
}

.plans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.plan-card {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 25px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
}

.plan-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.plan-card.current {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%);
}

.plan-card h3 {
    margin: 0 0 15px;
    color: #333;
}

.plan-card .price {
    font-size: 2rem;
    font-weight: bold;
    color: #667eea;
    margin-bottom: 20px;
}

.features {
    list-style: none;
    padding: 0;
    margin: 20px 0;
    text-align: left;
}

.features li {
    padding: 8px 0;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.9rem;
}

.features li::before {
    content: "✓";
    color: #28a745;
    font-weight: bold;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.alert-warning {
    background: #fff3cd;
    border: 1px solid #ffc107;
    color: #856404;
}
</style>

<div class="subscription-container">
    <h1 style="margin-bottom: 30px;">💎 Mon Abonnement</h1>
    
    <?php if ($subscription): ?>
        <!-- Abonnement actif -->
        <div class="subscription-card">
            <div class="subscription-header">
                <div>
                    <h2 style="margin: 0 0 10px;"><?php echo htmlspecialchars($subscription['plan_name']); ?></h2>
                    <span class="plan-badge <?php echo $subscription['status']; ?>">
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
            
            <?php if ($subscription['cancel_at_period_end']): ?>
                <div class="alert alert-warning">
                    ⚠️ Votre abonnement sera annulé le <?php echo date('d/m/Y', strtotime($subscription['current_period_end'])); ?>. 
                    Vous pouvez le réactiver à tout moment avant cette date.
                </div>
            <?php endif; ?>
            
            <div class="subscription-info">
                <div class="info-box">
                    <h4>Prix</h4>
                    <p>
                        <?php 
                        $plan = null;
                        foreach ($plans as $p) {
                            if ($p['id'] == $subscription['plan_id']) {
                                $plan = $p;
                                break;
                            }
                        }
                        echo $plan ? '$' . number_format($plan['price'], 2) : 'N/A';
                        ?>
                    </p>
                </div>
                
                <div class="info-box">
                    <h4>Commission</h4>
                    <p><?php echo number_format($subscription['commission_rate'], 1); ?>%</p>
                </div>
                
                <div class="info-box">
                    <h4>Produits</h4>
                    <p><?php echo $subscription['max_products'] == -1 ? 'Illimité' : $subscription['max_products']; ?></p>
                </div>
                
                <?php if ($subscription['status'] === 'trial'): ?>
                    <div class="info-box">
                        <h4>Fin de l'essai</h4>
                        <p><?php echo date('d/m/Y', strtotime($subscription['trial_ends_at'])); ?></p>
                    </div>
                <?php else: ?>
                    <div class="info-box">
                        <h4>Prochain paiement</h4>
                        <p><?php echo date('d/m/Y', strtotime($subscription['current_period_end'])); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="action-buttons">
                <?php if ($subscription['cancel_at_period_end']): ?>
                    <form method="POST" action="/abonnement/reactiver" style="margin: 0;">
                        <button type="submit" class="btn btn-success">
                            ♻️ Réactiver l'abonnement
                        </button>
                    </form>
                <?php else: ?>
                    <?php if ($subscription['status'] !== 'cancelled'): ?>
                        <form method="POST" action="/abonnement/annuler" style="margin: 0;" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler votre abonnement ?');">
                            <button type="submit" class="btn btn-danger">
                                ❌ Annuler l'abonnement
                            </button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
                
                <a href="/vendeur/tableau-de-bord" class="btn btn-secondary">
                    📊 Tableau de bord
                </a>
            </div>
        </div>
        
    <?php else: ?>
        <!-- Pas d'abonnement -->
        <div class="subscription-card" style="text-align: center; padding: 60px 40px;">
            <h2 style="margin-bottom: 20px;">😔 Vous n'avez pas d'abonnement actif</h2>
            <p style="color: #666; margin-bottom: 30px;">
                Choisissez un plan pour commencer à vendre vos produits sur Luxe Stars Power !
            </p>
            <a href="/tarifs" class="btn btn-primary">
                💎 Voir les plans
            </a>
        </div>
    <?php endif; ?>
    
    <!-- Autres plans disponibles -->
    <?php if ($subscription && $subscription['status'] !== 'cancelled'): ?>
        <div class="plans-comparison">
            <h2>📈 Passer à un plan supérieur</h2>
            <div class="plans-grid">
                <?php foreach ($plans as $plan): ?>
                    <?php 
                    $isCurrent = $subscription && $plan['id'] == $subscription['plan_id'];
                    $isUpgrade = $subscription && $plan['price'] > ($plan['price'] ?? 0);
                    ?>
                    <div class="plan-card <?php echo $isCurrent ? 'current' : ''; ?>">
                        <h3><?php echo htmlspecialchars($plan['name']); ?></h3>
                        <div class="price">
                            $<?php echo number_format($plan['price'], 2); ?>
                            <span style="font-size: 0.9rem; color: #999;">
                                /<?php echo $plan['billing_period'] === 'monthly' ? 'mois' : ($plan['billing_period'] === 'yearly' ? 'an' : '14 jours'); ?>
                            </span>
                        </div>
                        
                        <?php 
                        $features = json_decode($plan['features'], true);
                        if ($features): 
                        ?>
                        <ul class="features">
                            <?php foreach (array_slice($features, 0, 4) as $feature): ?>
                                <li><?php echo htmlspecialchars($feature); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                        
                        <?php if ($isCurrent): ?>
                            <button class="btn btn-secondary" disabled style="width: 100%;">
                                ✓ Plan actuel
                            </button>
                        <?php elseif ($plan['slug'] === 'trial'): ?>
                            <!-- Ne pas afficher le plan trial si déjà abonné -->
                        <?php else: ?>
                            <?php if ($plan['price'] > 0): ?>
                                <!-- Plan payant : rediriger vers checkout -->
                                <a href="/abonnement/paiement/<?php echo $plan['slug']; ?>" class="btn btn-primary" style="width: 100%; text-align: center;">
                                    💳 Passer à ce plan
                                </a>
                            <?php else: ?>
                                <!-- Plan gratuit : formulaire POST (downgrade) -->
                                <form method="POST" action="/abonnement/changer/<?php echo $plan['slug']; ?>" style="margin: 0;">
                                    <button type="submit" class="btn btn-primary" style="width: 100%;" onclick="return confirm('Changer pour ce plan ?');">
                                        ⬇️ Passer à ce plan
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>