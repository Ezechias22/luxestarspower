<?php ob_start(); ?>

<div class="container" style="padding: 60px 20px; max-width: 600px; margin: 0 auto; text-align: center;">
    <div style="background: white; border-radius: 10px; padding: 50px 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="font-size: 5rem; margin-bottom: 20px;">❌</div>
        
        <h1 style="color: #e74c3c; margin-bottom: 20px;">Paiement annulé</h1>
        
        <p style="font-size: 1.2rem; color: #666; margin-bottom: 30px;">
            Votre paiement a été annulé. Aucun montant n'a été débité.
        </p>
        
        <p style="color: #666; margin-bottom: 30px;">
            Vous pouvez retourner à votre panier et réessayer quand vous êtes prêt.
        </p>
        
        <div style="display: flex; gap: 15px; justify-content: center;">
            <a href="/panier" class="btn btn-primary" style="padding: 15px 30px;">
                Retour au panier
            </a>
            <a href="/produits" class="btn" style="padding: 15px 30px;">
                Continuer mes achats
            </a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>