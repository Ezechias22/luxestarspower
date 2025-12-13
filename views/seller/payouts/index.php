<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px;">
    <h1 style="margin-bottom: 30px;">Mes Paiements</h1>
    
    <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2 style="margin-bottom: 20px;">Solde disponible</h2>
        <p style="font-size: 3rem; color: #4caf50; font-weight: bold; margin-bottom: 20px;">0.00 €</p>
        
        <form method="POST" action="/vendeur/paiements/demander">
            <button type="submit" class="btn btn-primary">Demander un paiement</button>
        </form>
    </div>
    
    <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="margin-bottom: 20px;">Historique des paiements</h2>
        <p style="color: #666; text-align: center; padding: 40px 0;">Aucun paiement pour le moment</p>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>