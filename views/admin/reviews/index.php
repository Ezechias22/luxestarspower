<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px;">
    <h1 style="margin-bottom: 30px;">⭐ Gestion des Avis</h1>
    
    <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 10px;">
        <h2 style="color: #666; margin-bottom: 20px;">Aucun avis</h2>
        <p style="color: #999;">Les avis clients apparaîtront ici</p>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>