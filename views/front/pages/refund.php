<?php ob_start(); ?>

<div class="container" style="padding: 60px 20px; max-width: 900px; margin: 0 auto;">
    <h1 style="text-align: center; margin-bottom: 40px;">💰 Politique de Remboursement</h1>
    
    <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); line-height: 1.8; color: #555;">
        
        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin-bottom: 30px; border-radius: 5px;">
            <p style="margin: 0; color: #856404;">
                <strong>⚠️ Important :</strong> En raison de la nature numérique de nos produits, 
                les remboursements sont traités au cas par cas. Lisez attentivement cette politique.
            </p>
        </div>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">1. Conditions de remboursement</h2>
            <p>Vous pouvez demander un remboursement dans les cas suivants :</p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li><strong>Produit non conforme :</strong> le contenu ne correspond pas à la description</li>
                <li><strong>Fichier corrompu :</strong> impossible de télécharger ou d'ouvrir le fichier</li>
                <li><strong>Charge double :</strong> vous avez été facturé plusieurs fois par erreur</li>
                <li><strong>Problème technique :</strong> empêchant l'accès au produit acheté</li>
            </ul>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">2. Délai de demande</h2>
            <p>
                Les demandes de remboursement doivent être effectuées dans les <strong>14 jours</strong> 
                suivant l'achat. Passé ce délai, aucun remboursement ne sera accepté, 
                sauf en cas de problème technique avéré.
            </p>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">3. Cas de refus de remboursement</h2>
            <p>Nous ne remboursons <strong>PAS</strong> dans les cas suivants :</p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>Changement d'avis après téléchargement du produit</li>
                <li>Incompatibilité avec votre matériel (spécifications mentionnées dans la description)</li>
                <li>Manque de compétences pour utiliser le produit</li>
                <li>Attentes non réalistes par rapport au produit</li>
                <li>Demande après les 14 jours sans raison valable</li>
            </ul>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">4. Procédure de demande</h2>
            <p>Pour demander un remboursement :</p>
            <ol style="margin-left: 20px; margin-top: 10px;">
                <li>Contactez-nous via <a href="/contact" style="color: #3498db;">notre page de contact</a></li>
                <li>Fournissez votre numéro de commande</li>
                <li>Expliquez la raison de votre demande</li>
                <li>Joignez des preuves si nécessaire (captures d'écran, messages d'erreur)</li>
            </ol>
            <p style="margin-top: 15px;">
                Notre équipe examinera votre demande sous <strong>48 heures ouvrées</strong>.
            </p>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">5. Traitement du remboursement</h2>
            <p>Si votre demande est acceptée :</p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>Le remboursement est effectué sur le moyen de paiement original</li>
                <li>Délai de traitement : 5 à 10 jours ouvrés</li>
                <li>Vous recevrez un email de confirmation</li>
                <li>Votre accès au produit sera révoqué</li>
            </ul>
        </section>

        <section style="margin-bottom: 40px;">
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">6. Alternative au remboursement</h2>
            <p>
                Dans certains cas, nous pouvons proposer une <strong>solution alternative</strong> :
            </p>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>Remplacement par un produit similaire</li>
                <li>Crédit store pour un futur achat</li>
                <li>Assistance technique pour résoudre le problème</li>
            </ul>
        </section>

        <section>
            <h2 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.5rem;">7. Litiges</h2>
            <p>
                En cas de désaccord sur une décision de remboursement, 
                vous pouvez contacter notre service client pour une révision. 
                Si le litige persiste, vous pouvez faire appel à un médiateur de consommation.
            </p>
        </section>

        <div style="margin-top: 40px; padding: 20px; background: #d4edda; border-left: 4px solid #28a745; border-radius: 5px;">
            <p style="margin: 0; color: #155724;">
                <strong>💡 Notre engagement :</strong> Nous traitons chaque demande de remboursement 
                avec équité et transparence. Votre satisfaction est notre priorité.
            </p>
        </div>

        <div style="margin-top: 20px; padding: 20px; background: #f8f9fa; border-left: 4px solid #3498db; border-radius: 5px;">
            <p style="margin: 0; color: #666;">
                <strong>Dernière mise à jour :</strong> <?php echo date('d/m/Y'); ?>
            </p>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>