<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">Mes Achats</h1>
    
    <?php if (empty($orders) || !is_array($orders)): ?>
        <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 10px;">
            <h2 style="color: #666; margin-bottom: 20px;">Aucun achat pour le moment</h2>
            <p style="font-size: 1.1rem; color: #999; margin-bottom: 30px;">
                Découvrez notre catalogue de produits
            </p>
            <a href="/produits" class="btn btn-primary">Parcourir les produits</a>
        </div>
    <?php else: ?>
        <div style="background: white; border-radius: 10px; overflow: hidden;">
            <?php foreach($orders as $order): ?>
                <div style="padding: 20px; border-bottom: 1px solid #eee;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="margin-bottom: 5px;">Commande #<?php echo htmlspecialchars($order['order_number'] ?? $order['id']); ?></h3>
                            <p style="color: #666;">
                                <?php echo date('d/m/Y', strtotime($order['created_at'])); ?>
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-size: 1.5rem; font-weight: bold; color: #667eea;">
                                <?php echo number_format($order['amount'], 2); ?> €
                            </p>
                            <span style="padding: 5px 15px; background: #e8f5e9; color: #2e7d32; border-radius: 20px; font-size: 0.875rem;">
                                <?php echo htmlspecialchars($order['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>