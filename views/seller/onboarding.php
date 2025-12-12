<?php ob_start(); ?>

<div class="container" style="padding: 60px 20px; max-width: 800px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-size: 2.5rem; margin-bottom: 20px;">Devenez Vendeur</h1>
        <p style="font-size: 1.2rem; color: #666;">Commencez à vendre vos produits numériques sur LuxeStarsPower</p>
    </div>
    
    <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="margin-bottom: 20px;">Avantages de vendre sur notre plateforme :</h2>
        
        <ul style="list-style: none; padding: 0;">
            <li style="padding: 15px 0; border-bottom: 1px solid #eee;">
                ✅ <strong>Commission basse</strong> - Gardez plus de profits
            </li>
            <li style="padding: 15px 0; border-bottom: 1px solid #eee;">
                ✅ <strong>Paiements sécurisés</strong> - Stripe et PayPal intégrés
            </li>
            <li style="padding: 15px 0; border-bottom: 1px solid #eee;">
                ✅ <strong>Tableau de bord complet</strong> - Suivez vos ventes en temps réel
            </li>
            <li style="padding: 15px 0; border-bottom: 1px solid #eee;">
                ✅ <strong>Support client</strong> - Notre équipe vous accompagne
            </li>
            <li style="padding: 15px 0;">
                ✅ <strong>Marketplace établie</strong> - Accédez à notre base de clients
            </li>
        </ul>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <form method="POST" action="/vendre/devenir-vendeur" style="margin-top: 30px; text-align: center;">
                <button type="submit" class="btn btn-primary" style="font-size: 1.2rem; padding: 15px 40px;">
                    Devenir Vendeur Maintenant
                </button>
            </form>
        <?php else: ?>
            <div style="margin-top: 30px; text-align: center;">
                <p style="margin-bottom: 20px;">Vous devez avoir un compte pour devenir vendeur</p>
                <a href="/inscription" class="btn btn-primary" style="font-size: 1.2rem; padding: 15px 40px; text-decoration: none; display: inline-block;">
                    Créer un Compte
                </a>
                <p style="margin-top: 15px;">
                    Déjà un compte ? <a href="/connexion">Se connecter</a>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>