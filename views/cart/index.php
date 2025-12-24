<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">🛒 <?php echo __('shopping_cart'); ?></h1>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            ✅ <?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            ❌ <?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
        <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 10px;">
            <div style="font-size: 5rem; margin-bottom: 20px;">🛒</div>
            <h2 style="color: #666; margin-bottom: 20px;"><?php echo __('cart_empty'); ?></h2>
            <a href="/produits" class="btn btn-primary" style="margin-top: 20px; padding: 15px 40px; font-size: 1.1rem;">
                <?php echo __('continue_shopping'); ?>
            </a>
        </div>
    <?php else: ?>
        <div style="background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <?php foreach($cartItems as $item): ?>
                <div class="cart-item" style="display: flex; gap: 20px; padding: 20px; border-bottom: 1px solid #eee; align-items: center;">
                    <div style="flex-shrink: 0;">
                        <?php if(!empty($item['thumbnail_path'])): ?>
                            <img src="<?php echo htmlspecialchars($item['thumbnail_path']); ?>"
                                 alt="<?php echo htmlspecialchars($item['title']); ?>"
                                 style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px;">
                        <?php else: ?>
                            <div style="width: 120px; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                                📦
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="flex: 1;">
                        <h3 style="margin-bottom: 10px;">
                            <a href="/produit/<?php echo htmlspecialchars($item['slug']); ?>" style="color: #333; text-decoration: none;">
                                <?php echo htmlspecialchars($item['title']); ?>
                            </a>
                        </h3>
                        <p style="color: #666; margin-bottom: 5px;">
                            <?php echo __('seller'); ?>: <?php echo htmlspecialchars($item['seller_name']); ?>
                        </p>
                        <p style="font-size: 1.5rem; color: #e74c3c; font-weight: bold; margin: 10px 0;">
                            $<?php echo number_format($item['price'], 2); ?>
                        </p>
                    </div>

                    <form method="POST" action="/panier/supprimer/<?php echo $item['product_id']; ?>">
                        <button type="submit" class="btn" style="background: #e74c3c; color: white; padding: 10px 20px;">
                            🗑️ <?php echo __('remove'); ?>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>

            <div style="text-align: right; padding: 30px 20px; border-top: 3px solid #333; margin-top: 20px;">
                <h2 style="font-size: 2rem; margin-bottom: 20px;">
                    <?php echo __('total'); ?>: <span style="color: #e74c3c;">$<?php echo number_format($total, 2); ?></span>
                </h2>
                <div class="cart-actions" style="display: flex; gap: 15px; justify-content: flex-end;">
                    <a href="/produits" class="btn" style="padding: 15px 30px; font-size: 1.1rem;">
                        ← <?php echo __('continue_shopping'); ?>
                    </a>
                    <a href="/checkout" class="btn btn-primary" style="padding: 15px 40px; font-size: 1.1rem;">
                        <?php echo __('proceed_to_checkout'); ?> →
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>