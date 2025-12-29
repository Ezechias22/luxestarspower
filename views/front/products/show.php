<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 1000px; margin: 0 auto;">
    <div style="background: white; border-radius: 10px; padding: 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: relative;">

        <!-- Badge PROMO en haut à droite -->
        <?php if(!empty($product['is_on_sale']) && !empty($product['discount_percentage'])): ?>
            <div style="position: absolute; top: 20px; right: 20px; background: #ff4444; color: white; padding: 12px 20px; border-radius: 30px; font-weight: bold; font-size: 1.2rem; z-index: 10; box-shadow: 0 4px 12px rgba(255,68,68,0.4);">
                🔥 -<?php echo $product['discount_percentage']; ?>% DE RÉDUCTION
            </div>
        <?php endif; ?>

        <div class="product-layout" style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px;">
            <!-- Image produit -->
            <div>
                <?php if(!empty($product['thumbnail_path'])): ?>
                    <img src="<?php echo htmlspecialchars($product['thumbnail_path']); ?>"
                         alt="<?php echo htmlspecialchars($product['title']); ?>"
                         style="width: 100%; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div style="width: 100%; height: 400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: none; align-items: center; justify-content: center; color: white; font-size: 5rem;">
                        📦
                    </div>
                <?php else: ?>
                    <div style="width: 100%; height: 400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 5rem;">
                        📦
                    </div>
                <?php endif; ?>
            </div>

            <!-- Détails produit -->
            <div>
                <h1 style="margin-bottom: 15px; font-size: 2rem;">
                    <?php echo htmlspecialchars($product['title']); ?>
                </h1>

                <p style="color: #667eea; font-size: 0.875rem; text-transform: uppercase; font-weight: 600; margin-bottom: 20px;">
                    <?php echo __($product['type'] ?? 'file'); ?>
                </p>

                <!-- Prix avec promotion -->
                <?php if(!empty($product['is_on_sale']) && !empty($product['original_price'])): ?>
                    <div style="margin: 20px 0;">
                        <p style="text-decoration: line-through; color: #999; font-size: 1.3rem; margin-bottom: 5px;">
                            Prix normal: $<?php echo number_format($product['original_price'], 2); ?>
                        </p>
                        <p class="price" style="font-size: 3rem; color: #ff4444; font-weight: bold; margin: 10px 0;">
                            $<?php echo number_format($product['price'], 2); ?>
                        </p>
                        <p style="background: #fff3cd; color: #856404; padding: 10px 15px; border-radius: 8px; font-weight: 600; display: inline-block;">
                            💰 Économisez $<?php echo number_format($product['original_price'] - $product['price'], 2); ?> !
                        </p>
                    </div>
                    
                    <!-- Dates de promo -->
                    <?php if(!empty($product['sale_ends_at'])): ?>
                        <div style="background: #ffebee; border-left: 4px solid #f44336; padding: 15px; border-radius: 5px; margin: 20px 0;">
                            <p style="margin: 0; color: #c62828; font-weight: 600;">
                                ⏰ Offre limitée ! Se termine le <?php echo date('d/m/Y à H:i', strtotime($product['sale_ends_at'])); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="price" style="font-size: 2.5rem; color: #e74c3c; font-weight: bold; margin: 20px 0;">
                        $<?php echo number_format($product['price'], 2); ?>
                    </p>
                <?php endif; ?>

                <!-- Barre de progression des ventes -->
                <?php 
                    $currentSales = $product['sales'] ?? 0;
                    $goal = $product['sales_goal'] ?? 100;
                    $percentage = min(100, ($goal > 0 ? ($currentSales / $goal) * 100 : 0));
                ?>
                <?php if($currentSales > 0 || $goal > 0): ?>
                <div style="margin: 25px 0; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span style="color: #333; font-weight: 600; font-size: 1.1rem;">📊 Popularité</span>
                        <span style="color: #667eea; font-weight: bold; font-size: 1.1rem;">
                            <?php echo $currentSales; ?> / <?php echo $goal; ?> vendus
                        </span>
                    </div>
                    <div style="background: #e0e0e0; height: 12px; border-radius: 10px; overflow: hidden;">
                        <div style="background: linear-gradient(90deg, #667eea, #764ba2); height: 100%; width: <?php echo $percentage; ?>%; transition: width 0.3s;"></div>
                    </div>
                    <div style="text-align: right; font-size: 0.9rem; color: #666; margin-top: 5px;">
                        <?php echo round($percentage); ?>% de l'objectif atteint
                    </div>
                </div>
                <?php endif; ?>

                <div style="margin: 30px 0;">
                    <h3 style="margin-bottom: 15px;"><?php echo __('description'); ?></h3>
                    <p style="line-height: 1.8; color: #555;">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </p>
                </div>

                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <form method="POST" action="/panier/ajouter" style="flex: 1;">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px 30px; font-size: 1.1rem;">
                            🛒 <?php echo __('add_to_cart'); ?>
                        </button>
                    </form>

                    <a href="/checkout?product=<?php echo $product['id']; ?>" class="btn" style="flex: 1; padding: 15px 30px; font-size: 1.1rem; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        ⚡ <?php echo __('buy_now'); ?>
                    </a>
                </div>

                <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <p style="margin-bottom: 10px;">
                        <strong><?php echo __('seller'); ?> :</strong>
                        <?php echo htmlspecialchars($product['seller_name'] ?? 'Vendeur'); ?>
                    </p>
                    <p>
                        <strong><?php echo __('type'); ?> :</strong>
                        <?php echo __($product['type'] ?? 'file'); ?>
                    </p>
                </div>
            </div>
        </div>

        <a href="/produits" class="btn" style="display: inline-block; margin-top: 20px;">
            ← <?php echo __('back_to_products'); ?>
        </a>
    </div>
</div>

<!-- Style responsive pour mobile -->
<style>
@media (max-width: 768px) {
    .product-layout {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>