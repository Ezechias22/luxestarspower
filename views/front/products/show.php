<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 1000px; margin: 0 auto;">
    <div style="background: white; border-radius: 10px; padding: 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px;">
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
                
                <p class="price" style="font-size: 2.5rem; color: #e74c3c; font-weight: bold; margin: 20px 0;">
                    $<?php echo number_format($product['price'], 2); ?>
                </p>
                
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

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>