<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px;">
    <h1 style="margin-bottom: 30px;">Mes Paiements</h1>
    
    <?php if(isset($_SESSION['flash_success'])): ?>
        <div style="background: #e8f5e9; border: 1px solid #4caf50; color: #2e7d32; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            ✅ <?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>
    
    <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2 style="margin-bottom: 20px;">Solde disponible</h2>
        <p style="font-size: 3rem; color: #4caf50; font-weight: bold; margin-bottom: 20px;">
            <?php echo number_format($balance ?? 0, 2); ?> €
        </p>
        
        <?php if(($balance ?? 0) >= 50): ?>
            <form method="POST" action="/vendeur/paiements/demander">
                <button type="submit" class="btn btn-primary">Demander un paiement</button>
            </form>
        <?php else: ?>
            <p style="color: #999; margin-bottom: 15px;">
                Minimum 50€ requis pour demander un paiement
            </p>
            <a href="/vendeur/produits/nouveau" class="btn">Ajouter des produits</a>
        <?php endif; ?>
    </div>
    
    <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="margin-bottom: 20px;">Historique des paiements</h2>
        
        <?php if(empty($payouts)): ?>
            <p style="color: #666; text-align: center; padding: 40px 0;">Aucun paiement pour le moment</p>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #eee;">
                        <th style="padding: 15px; text-align: left;">Date</th>
                        <th style="padding: 15px; text-align: left;">Montant</th>
                        <th style="padding: 15px; text-align: left;">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($payouts as $payout): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 15px;"><?php echo date('d/m/Y', strtotime($payout['created_at'])); ?></td>
                            <td style="padding: 15px;"><?php echo number_format($payout['amount'], 2); ?> €</td>
                            <td style="padding: 15px;">
                                <span style="padding: 5px 15px; background: #e8f5e9; color: #2e7d32; border-radius: 20px; font-size: 0.875rem;">
                                    <?php echo htmlspecialchars($payout['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>