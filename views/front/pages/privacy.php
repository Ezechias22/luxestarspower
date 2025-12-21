<?php ob_start(); ?>

<div class="container" style="padding: 60px 20px; max-width: 900px; margin: 0 auto;">
    <h1 style="text-align: center; margin-bottom: 40px;">🔒 Politique de Confidentialité</h1>
    
    <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); line-height: 1.8; color: #555;">
        
        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">1. Collecte des données</h2>
            <p>Nous collectons les informations suivantes :</p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li><strong>Informations d'identification :</strong> nom, email, mot de passe (crypté)</li>
                <li><strong>Informations de paiement :</strong> traitées par nos partenaires sécurisés (Stripe, PayPal)</li>
                <li><strong>Données de navigation :</strong> adresse IP, type de navigateur, pages visitées</li>
                <li><strong>Produits consultés et achetés</strong></li>
            </ul>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">2. Utilisation des données</h2>
            <p>Vos données sont utilisées pour :</p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>Gérer votre compte et vos commandes</li>
                <li>Traiter les paiements de manière sécurisée</li>
                <li>Vous envoyer des notifications importantes</li>
                <li>Améliorer nos services et votre expérience</li>
                <li>Prévenir la fraude et assurer la sécurité</li>
            </ul>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">3. Partage des données</h2>
            <p>
                Nous ne vendons jamais vos données personnelles. Vos informations peuvent être partagées uniquement avec :
            </p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li><strong>Processeurs de paiement :</strong> Stripe et PayPal pour les transactions</li>
                <li><strong>Vendeurs :</strong> informations nécessaires à la livraison des produits achetés</li>
                <li><strong>Services cloud :</strong> pour l'hébergement sécurisé (Cloudinary, Railway)</li>
                <li><strong>Autorités légales :</strong> si requis par la loi</li>
            </ul>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">4. Sécurité des données</h2>
            <p>Nous mettons en œuvre des mesures de sécurité robustes :</p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>Chiffrement SSL/TLS pour toutes les communications</li>
                <li>Mots de passe cryptés avec algorithme Argon2ID</li>
                <li>Serveurs sécurisés et surveillés 24/7</li>
                <li>Audits de sécurité réguliers</li>
            </ul>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">5. Vos droits (RGPD)</h2>
            <p>Conformément au RGPD, vous avez le droit de :</p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li><strong>Accès :</strong> consulter vos données personnelles</li>
                <li><strong>Rectification :</strong> corriger vos informations</li>
                <li><strong>Suppression :</strong> demander l'effacement de vos données</li>
                <li><strong>Portabilité :</strong> récupérer vos données dans un format lisible</li>
                <li><strong>Opposition :</strong> refuser certains traitements</li>
            </ul>
            <p style="margin-top: 15px;">
                Pour exercer ces droits, contactez-nous à : <a href="/contact" style="color: #3498db;">notre page de contact</a>
            </p>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">6. Cookies</h2>
            <p>
                Nous utilisons des cookies essentiels pour le fonctionnement du site (authentification, panier). 
                Aucun cookie publicitaire tiers n'est utilisé.
            </p>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">7. Conservation des données</h2>
            <p>
                Vos données sont conservées tant que votre compte est actif. 
                Après suppression de votre compte, vos données sont effacées sous 30 jours, 
                sauf obligation légale de conservation.
            </p>
        </section>

        <section>
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">8. Modifications de la politique</h2>
            <p>
                Nous pouvons modifier cette politique de confidentialité. 
                Les changements importants vous seront notifiés par email.
            </p>
        </section>

        <div style="margin-top: 40px; padding: 20px; background: #f8f9fa; border-left: 4px solid #e74c3c; border-radius: 5px;">
            <p style="margin: 0; color: #666;">
                <strong>Dernière mise à jour :</strong> <?php echo date('d/m/Y'); ?>
            </p>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>