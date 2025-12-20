<?php ob_start(); ?>

<div class="container" style="padding: 60px 20px; max-width: 600px; margin: 0 auto; text-align: center;">
    <div style="background: white; border-radius: 10px; padding: 50px 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="font-size: 5rem; margin-bottom: 20px;">✅</div>
        
        <h1 style="color: #27ae60; margin-bottom: 20px;">Paiement réussi !</h1>
        
        <p style="font-size: 1.2rem; color: #666; margin-bottom: 30px;">
            Votre commande <strong>#<?php echo $orderNumber; ?></strong> a été confirmée.
        </p>
        
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 30px 0;">
            <p style="margin-bottom: 10px;">
                <strong>Total payé :</strong> 
                <span style="font-size: 1.5rem; color: #e74c3c;">$<?php echo number_format($order['total_amount'], 2); ?></span>
            </p>
        </div>
        
        <p style="color: #666; margin-bottom: 30px;">
            Un email de confirmation vous a été envoyé avec les détails de votre commande et vos liens de téléchargement.
        </p>
        
        <div style="display: flex; gap: 15px; justify-content: center;">
            <a href="/compte/achats" class="btn btn-primary" style="padding: 15px 30px;">
                Voir mes achats
            </a>
            <a href="/produits" class="btn" style="padding: 15px 30px;">
                Continuer mes achats
            </a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>