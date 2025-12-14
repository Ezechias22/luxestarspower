<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 800px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">💳 <?php echo __('checkout'); ?></h1>
    
    <?php if (isset($_SESSION['flash_error'])): ?>
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            ❌ <?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>
    
    <div style="background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2 style="margin-bottom: 20px;"><?php echo __('order_summary'); ?></h2>
        
        <?php foreach($items as $item): ?>
            <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #eee;">
                <div>
                    <h3 style="margin-bottom: 5px;"><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p style="color: #666; font-size: 0.9rem;">
                        <?php echo __($item['type'] ?? 'file'); ?>
                    </p>
                </div>
                <p style="font-size: 1.2rem; font-weight: bold; color: #e74c3c;">
                    $<?php echo number_format($item['price'], 2); ?>
                </p>
            </div>
        <?php endforeach; ?>
        
        <div style="display: flex; justify-content: space-between; padding: 20px 0; border-top: 3px solid #333; margin-top: 20px;">
            <h2><?php echo __('total'); ?></h2>
            <h2 style="color: #e74c3c;">$<?php echo number_format($total, 2); ?></h2>
        </div>
    </div>
    
    <div style="background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2 style="margin-bottom: 20px;"><?php echo __('payment_method'); ?></h2>
        
        <div style="display: grid; gap: 15px;">
            <form method="POST" action="/checkout/stripe">
                <button type="submit" style="width: 100%; padding: 20px; background: #635bff; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <span style="font-size: 1.5rem;">💳</span>
                    Payer avec Stripe
                </button>
            </form>
            
            <form method="POST" action="/checkout/paypal">
                <button type="submit" style="width: 100%; padding: 20px; background: #0070ba; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <span style="font-size: 1.5rem;">🅿️</span>
                    Payer avec PayPal
                </button>
            </form>
        </div>
        
        <p style="text-align: center; margin-top: 20px; color: #666; font-size: 0.9rem;">
            🔒 <?php echo __('secure_payment'); ?>
        </p>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>