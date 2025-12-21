<?php ob_start(); ?>

<div class="container" style="padding: 60px 20px; max-width: 900px; margin: 0 auto;">
    <h1 style="text-align: center; margin-bottom: 40px;">❓ Questions Fréquentes</h1>
    
    <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <div style="margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid #eee;">
            <h3 style="color: #2c3e50; margin-bottom: 10px;">Comment acheter un produit ?</h3>
            <p style="color: #666; line-height: 1.8;">
                Parcourez notre catalogue, ajoutez les produits à votre panier, puis procédez au paiement sécurisé via Stripe ou PayPal. Vous recevrez instantanément vos liens de téléchargement par email.
            </p>
        </div>
        
        <div style="margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid #eee;">
            <h3 style="color: #2c3e50; margin-bottom: 10px;">Comment devenir vendeur ?</h3>
            <p style="color: #666; line-height: 1.8;">
                Cliquez sur "Vendre" dans le menu, créez votre compte vendeur et commencez à publier vos produits numériques. Vous recevez vos paiements directement sur votre compte.
            </p>
        </div>
        
        <div style="margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid #eee;">
            <h3 style="color: #2c3e50; margin-bottom: 10px;">Quels sont les moyens de paiement acceptés ?</h3>
            <p style="color: #666; line-height: 1.8;">
                Nous acceptons les cartes Visa, Mastercard, American Express via Stripe, ainsi que PayPal pour des paiements 100% sécurisés.
            </p>
        </div>
        
        <div style="margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid #eee;">
            <h3 style="color: #2c3e50; margin-bottom: 10px;">Puis-je obtenir un remboursement ?</h3>
            <p style="color: #666; line-height: 1.8;">
                Oui, consultez notre <a href="/politique-remboursement" style="color: #3498db;">politique de remboursement</a> pour connaître les conditions.
            </p>
        </div>
        
        <div>
            <h3 style="color: #2c3e50; margin-bottom: 10px;">Comment télécharger mes produits achetés ?</h3>
            <p style="color: #666; line-height: 1.8;">
                Après l'achat, vous recevez un email avec vos liens de téléchargement. Vous pouvez aussi accéder à vos achats depuis votre espace "Mon Compte" → "Mes achats".
            </p>
        </div>
        
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>