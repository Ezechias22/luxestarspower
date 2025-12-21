<?php ob_start(); ?>

<div class="container" style="padding: 60px 20px; max-width: 900px; margin: 0 auto;">
    <h1 style="text-align: center; margin-bottom: 40px;">📜 Conditions Générales d'Utilisation</h1>
    
    <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); line-height: 1.8; color: #555;">
        
        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">1. Acceptation des conditions</h2>
            <p>
                En accédant et en utilisant Luxe Stars Power, vous acceptez d'être lié par ces conditions générales d'utilisation. 
                Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser notre plateforme.
            </p>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">2. Description du service</h2>
            <p>
                Luxe Stars Power est une marketplace permettant aux vendeurs de proposer des produits numériques (ebooks, formations, vidéos, etc.) 
                et aux acheteurs de les acquérir de manière sécurisée.
            </p>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">3. Compte utilisateur</h2>
            <p>
                Pour utiliser certaines fonctionnalités, vous devez créer un compte. Vous êtes responsable de :
            </p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>La confidentialité de vos identifiants</li>
                <li>Toutes les activités effectuées depuis votre compte</li>
                <li>La véracité des informations fournies</li>
            </ul>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">4. Obligations des vendeurs</h2>
            <p>Les vendeurs s'engagent à :</p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>Proposer uniquement du contenu légal et dont ils détiennent les droits</li>
                <li>Fournir des descriptions précises de leurs produits</li>
                <li>Respecter les délais de livraison des fichiers numériques</li>
                <li>Répondre aux demandes des acheteurs dans un délai raisonnable</li>
            </ul>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">5. Obligations des acheteurs</h2>
            <p>Les acheteurs s'engagent à :</p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>Utiliser les produits achetés conformément aux droits accordés</li>
                <li>Ne pas partager ou revendre les produits sans autorisation</li>
                <li>Effectuer les paiements de manière légale</li>
            </ul>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">6. Propriété intellectuelle</h2>
            <p>
                Tous les contenus présents sur Luxe Stars Power (logo, design, textes) sont protégés par le droit d'auteur. 
                Les produits vendus restent la propriété intellectuelle de leurs créateurs respectifs.
            </p>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">7. Limitation de responsabilité</h2>
            <p>
                Luxe Stars Power agit comme intermédiaire entre vendeurs et acheteurs. Nous ne sommes pas responsables :
            </p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>De la qualité des produits vendus</li>
                <li>Des litiges entre vendeurs et acheteurs</li>
                <li>Des pertes de données ou interruptions de service</li>
            </ul>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">8. Modifications des conditions</h2>
            <p>
                Nous nous réservons le droit de modifier ces conditions à tout moment. 
                Les modifications entrent en vigueur dès leur publication sur le site.
            </p>
        </section>

        <section>
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">9. Contact</h2>
            <p>
                Pour toute question concernant ces conditions, contactez-nous à : 
                <a href="/contact" style="color: #3498db; text-decoration: none;">notre page de contact</a>
            </p>
        </section>

        <div style="margin-top: 40px; padding: 20px; background: #f8f9fa; border-left: 4px solid #3498db; border-radius: 5px;">
            <p style="margin: 0; color: #666;">
                <strong>Dernière mise à jour :</strong> <?php echo date('d/m/Y'); ?>
            </p>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>