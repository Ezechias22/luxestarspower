<?php ob_start(); ?>

<style>
.seller-subscription {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-card h3 {
    margin: 0 0 10px;
    color: #666;
    font-size: 0.9rem;
    text-transform: uppercase;
}

.stat-card .value {
    font-size: 2rem;
    font-weight: bold;
    color: #667eea;
}

.usage-bar {
    background: #e0e0e0;
    height: 10px;
    border-radius: 5px;
    overflow: hidden;
    margin-top: 10px;
}

.usage-bar-fill {
    background: linear-gradient(90deg, #667eea, #764ba2);
    height: 100%;
    transition: width 0.3s;
}

.benefits-section {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.benefits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.benefit-item {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    border-left: 4px solid #667eea;
}

.benefit-item h4 {
    margin: 0 0 10px;
    color: #333;
}

.benefit-item p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}
</style>

<div class="seller-subscription">
    <h1 style="margin-bottom: 30px;">💎 Mon Abonnement Vendeur</h1>
    
    <?php if ($subscription): ?>
        <!-- Statistiques d'utilisation -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>📦 Produits</h3>
                <div class="value">
                    <?php 
                    // Compte les produits du vendeur
                    $db = \App\Database::getInstance();
                    $productsCount = $db->fetchOne(
                        "SELECT COUNT(*) as count FROM products WHERE seller_id = ?",
                        [$_SESSION['user_id']]
                    );
                    $count = $productsCount['count'] ?? 0;
                    $max = $subscription['max_products'] == -1 ? '∞' : $subscription['max_products'];
                    echo $count . ' / ' . $max;
                    ?>
                </div>
                <?php if ($subscription['max_products'] != -1): ?>
                    <div class="usage-bar">
                        <div class="usage-bar-fill" style="width: <?php echo min(100, ($count / $subscription['max_products']) * 100); ?>%;"></div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="stat-card">
                <h3>💰 Commission</h3>
                <div class="value"><?php echo number_format($subscription['commission_rate'], 1); ?>%</div>
                <small style="color: #999;">sur chaque vente</small>
            </div>
            
            <div class="stat-card">
                <h3>📅 Statut</h3>
                <div class="value" style="font-size: 1.5rem;">
                    <?php 
                    $statusIcons = [
                        'trial' => '🎁 Essai',
                        'active' => '✅ Actif',
                        'cancelled' => '❌ Annulé'
                    ];
                    echo $statusIcons[$subscription['status']] ?? $subscription['status'];
                    ?>
                </div>
            </div>
            
            <div class="stat-card">
                <h3>⏰ Renouvellement</h3>
                <div class="value" style="font-size: 1.2rem;">
                    <?php 
                    if ($subscription['status'] === 'trial') {
                        echo date('d/m/Y', strtotime($subscription['trial_ends_at']));
                    } else {
                        echo date('d/m/Y', strtotime($subscription['current_period_end']));
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <!-- Avantages du plan actuel -->
        <div class="benefits-section">
            <h2>🎁 Vos avantages actuels</h2>
            <div class="benefits-grid">
                <?php 
                $features = json_decode($subscription['plan_features'] ?? '[]', true);
                if (empty($features)) {
                    // Charge les features du plan
                    $plan = null;
                    foreach ($plans as $p) {
                        if ($p['id'] == $subscription['plan_id']) {
                            $plan = $p;
                            break;
                        }
                    }
                    if ($plan) {
                        $features = json_decode($plan['features'], true);
                    }
                }
                
                foreach ($features as $feature): 
                ?>
                    <div class="benefit-item">
                        <h4>✓ <?php echo htmlspecialchars($feature); ?></h4>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Actions -->
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="/abonnement" class="btn btn-primary">
                ⚙️ Gérer mon abonnement
            </a>
            
            <?php 
            // Trouve un plan supérieur
            $currentPrice = 0;
            foreach ($plans as $p) {
                if ($p['id'] == $subscription['plan_id']) {
                    $currentPrice = $p['price'];
                    break;
                }
            }
            
            $hasUpgrade = false;
            foreach ($plans as $plan) {
                if ($plan['price'] > $currentPrice && $plan['slug'] !== 'trial') {
                    $hasUpgrade = true;
                    break;
                }
            }
            
            if ($hasUpgrade && $subscription['status'] !== 'cancelled'):
            ?>
                <a href="/tarifs" class="btn btn-success">
                    ⬆️ Passer à un plan supérieur
                </a>
            <?php endif; ?>
            
            <a href="/vendeur/tableau-de-bord" class="btn btn-secondary">
                📊 Retour au tableau de bord
            </a>
        </div>
        
    <?php else: ?>
        <!-- Pas d'abonnement -->
        <div style="background: white; padding: 60px 40px; border-radius: 15px; text-align: center; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 20px;">😔 Aucun abonnement actif</h2>
            <p style="color: #666; margin-bottom: 30px; font-size: 1.1rem;">
                Pour vendre sur Luxe Stars Power, vous devez choisir un plan d'abonnement.
            </p>
            <a href="/tarifs" class="btn btn-primary" style="font-size: 1.1rem; padding: 15px 30px;">
                💎 Choisir un plan
            </a>
        </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../seller-layout.php'; ?>