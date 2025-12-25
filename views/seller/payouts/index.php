<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px;">
    <h1 style="margin-bottom: 30px;">💰 Mes Paiements</h1>

    <?php if(isset($_SESSION['flash_success'])): ?>
        <div style="background: #e8f5e9; border: 1px solid #4caf50; color: #2e7d32; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            ✅ <?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['flash_error'])): ?>
        <div style="background: #ffebee; border: 1px solid #f44336; color: #c62828; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            ❌ <?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <?php
    $currentBalance = $balance ?? 0;
    $totalRev = $totalRevenue ?? 0;
    $commission = $totalRev * 0.1;
    $minPayout = 50;
    ?>

    <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2 style="margin-bottom: 20px;">Solde disponible</h2>
        <p style="font-size: 3rem; color: #4caf50; font-weight: bold; margin-bottom: 20px;">
            $<?php echo number_format($currentBalance, 2); ?>
        </p>

        <p style="color: #666; margin-bottom: 10px;">
            💡 <strong>Revenus totaux :</strong> $<?php echo number_format($totalRev, 2); ?>
        </p>
        <p style="color: #666; margin-bottom: 20px;">
            💼 <strong>Commission plateforme (10%) :</strong> $<?php echo number_format($commission, 2); ?>
        </p>

        <p style="color: #999; margin-bottom: 15px;">
            ⚠️ Minimum $<?php echo number_format($minPayout, 2); ?> requis pour demander un paiement
        </p>

        <div style="display: flex; gap: 15px;">
            <form method="POST" action="/vendeur/paiements/demander">
                <button type="submit" class="btn btn-primary"
                        <?php echo $currentBalance < $minPayout ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''; ?>>
                    💸 Demander un paiement
                </button>
            </form>
            <a href="/vendeur/produits/nouveau" class="btn">➕ Ajouter des produits</a>
        </div>
    </div>

    <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="margin-bottom: 20px;">📋 Historique des paiements</h2>

        <?php if(empty($payouts)): ?>
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 4rem; margin-bottom: 20px;">💳</div>
                <p style="color: #666; font-size: 1.1rem;">Aucun paiement pour le moment</p>
                <p style="color: #999; margin-top: 10px;">
                    Vos demandes de paiement apparaîtront ici
                </p>
            </div>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #eee;">
                        <th style="padding: 15px; text-align: left;">Date</th>
                        <th style="padding: 15px; text-align: left;">Montant</th>
                        <th style="padding: 15px; text-align: left;">Statut</th>
                        <th style="padding: 15px; text-align: left;">Méthode</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($payouts as $payout): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 15px;"><?php echo date('d/m/Y H:i', strtotime($payout['created_at'])); ?></td>
                            <td style="padding: 15px; font-weight: 600;">$<?php echo number_format($payout['amount'], 2); ?></td>
                            <td style="padding: 15px;">
                                <?php
                                $statusColors = [
                                    'pending' => ['bg' => '#fff3cd', 'text' => '#856404', 'label' => '⏳ En attente'],
                                    'approved' => ['bg' => '#d1ecf1', 'text' => '#0c5460', 'label' => '✅ Approuvé'],
                                    'paid' => ['bg' => '#d4edda', 'text' => '#155724', 'label' => '💰 Payé'],
                                    'rejected' => ['bg' => '#f8d7da', 'text' => '#721c24', 'label' => '❌ Rejeté']
                                ];
                                $status = $statusColors[$payout['status']] ?? $statusColors['pending'];
                                ?>
                                <span style="padding: 5px 15px; background: <?php echo $status['bg']; ?>; color: <?php echo $status['text']; ?>; border-radius: 20px; font-size: 0.875rem;">
                                    <?php echo $status['label']; ?>
                                </span>
                            </td>
                            <td style="padding: 15px;"><?php echo htmlspecialchars($payout['method'] ?? 'Virement bancaire'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div style="background: #e3f2fd; padding: 20px; border-radius: 10px; margin-top: 30px; border-left: 4px solid #2196f3;">
        <h3 style="margin-bottom: 10px; color: #1976d2;">ℹ️ Informations sur les paiements</h3>
        <ul style="color: #0d47a1; line-height: 1.8;">
            <li>Les paiements sont traités sous 3-5 jours ouvrables</li>
            <li>Une commission de 10% est prélevée sur chaque vente</li>
            <li>Montant minimum de retrait : $50.00</li>
            <li>Les fonds sont versés par virement bancaire</li>
        </ul>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>