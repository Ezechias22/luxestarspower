<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">📥 Mes Téléchargements</h1>

    <?php if (empty($downloads)): ?>
        <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 10px;">
            <div style="font-size: 5rem; margin-bottom: 20px;">📥</div>
            <h2 style="color: #666; margin-bottom: 20px;">Aucun téléchargement</h2>
            <p style="color: #999; margin-bottom: 30px;">Achetez des produits pour accéder à vos téléchargements.</p>
            <a href="/produits" class="btn btn-primary" style="padding: 15px 40px; font-size: 1.1rem;">
                Découvrir les produits
            </a>
        </div>
    <?php else: ?>
        <div style="background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <?php foreach($downloads as $download): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid #eee;">
                    <div>
                        <h3 style="margin-bottom: 10px;">
                            <?php echo htmlspecialchars($download['product_title']); ?>
                        </h3>
                        <p style="color: #666; font-size: 0.9rem;">
                            Type: <?php echo ucfirst($download['product_type']); ?> • 
                            Acheté le <?php echo date('d/m/Y', strtotime($download['purchased_at'])); ?>
                        </p>
                    </div>
                    <a href="/telecharger/produit/<?php echo $download['product_id']; ?>" 
                       class="btn btn-primary" 
                       style="padding: 12px 30px;">
                        📥 Télécharger
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>