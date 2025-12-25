<?php ob_start(); ?>

<div class="container" style="padding: 60px 20px; max-width: 700px; margin: 0 auto;">
    <div style="background: white; border-radius: 10px; padding: 50px 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="text-align: center;">
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
        </div>

        <?php
        // Récupère les produits de la commande
        $orderRepo = new \App\Repositories\OrderRepository();
        $items = $orderRepo->getOrderItems($order['id']);
        ?>

        <?php if (!empty($items)): ?>
            <div style="margin: 40px 0; text-align: left;">
                <h2 style="font-size: 1.5rem; margin-bottom: 20px; text-align: center;">
                    📥 Vos téléchargements
                </h2>
                
                <?php foreach ($items as $item): ?>
                    <div style="background: #f0f8ff; border: 2px solid #2196f3; border-radius: 10px; padding: 20px; margin-bottom: 15px;">
                        <div style="display: flex; align-items: center; gap: 20px;">
                            <div style="font-size: 3rem;">
                                <?php
                                $typeEmojis = [
                                    'ebook' => '📚',
                                    'video' => '🎥',
                                    'image' => '🖼️',
                                    'course' => '🎓',
                                    'file' => '📁'
                                ];
                                echo $typeEmojis[$item['type']] ?? '📄';
                                ?>
                            </div>
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 10px 0; font-size: 1.2rem; color: #333;">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </h3>
                                <p style="margin: 0; color: #666; font-size: 0.9rem;">
                                    Type: <?php echo ucfirst($item['type']); ?>
                                    <?php if ($item['file_storage_path']): ?>
                                        • Format: <?php echo strtoupper(pathinfo($item['file_storage_path'], PATHINFO_EXTENSION)); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div>
                                <?php if (!empty($item['file_storage_path'])): ?>
                                    <a href="<?php echo htmlspecialchars($item['file_storage_path']); ?>" 
                                       target="_blank"
                                       download
                                       class="btn btn-primary" 
                                       style="padding: 12px 25px; white-space: nowrap; text-decoration: none;">
                                        📥 Télécharger
                                    </a>
                                <?php else: ?>
                                    <span style="color: #999; font-size: 0.9rem;">Fichier non disponible</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; border-left: 4px solid #4caf50; margin-top: 20px;">
                    <p style="margin: 0; color: #2e7d32; font-size: 0.95rem;">
                        💡 <strong>Astuce :</strong> Vous pouvez retrouver vos téléchargements à tout moment dans 
                        <a href="/compte/telechargements" style="color: #2e7d32; font-weight: 600;">votre compte</a>.
                    </p>
                </div>
            </div>
        <?php endif; ?>
        
        <div style="display: flex; gap: 15px; justify-content: center; margin-top: 40px;">
            <a href="/compte/telechargements" class="btn btn-primary" style="padding: 15px 30px;">
                📥 Mes téléchargements
            </a>
            <a href="/compte/achats" class="btn" style="padding: 15px 30px;">
                📦 Mes achats
            </a>
            <a href="/produits" class="btn" style="padding: 15px 30px;">
                🛍️ Continuer mes achats
            </a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>