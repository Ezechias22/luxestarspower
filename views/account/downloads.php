<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">Mes Téléchargements</h1>
    
    <?php if (empty($downloads) || !is_array($downloads)): ?>
        <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 10px;">
            <h2 style="color: #666; margin-bottom: 20px;">Aucun téléchargement disponible</h2>
            <p style="font-size: 1.1rem; color: #999; margin-bottom: 30px;">
                Vos fichiers achetés apparaîtront ici
            </p>
            <a href="/produits" class="btn btn-primary">Découvrir les produits</a>
        </div>
    <?php else: ?>
        <div style="display: grid; gap: 20px;">
            <?php foreach($downloads as $download): ?>
                <div style="background: white; padding: 20px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin-bottom: 5px;"><?php echo htmlspecialchars($download['product_title']); ?></h3>
                        <p style="color: #666; font-size: 0.9rem;">
                            Téléchargé <?php echo $download['download_count']; ?> fois
                        </p>
                    </div>
                    <a href="/telecharger/<?php echo htmlspecialchars($download['token']); ?>" class="btn">
                        ⬇️ Télécharger
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>