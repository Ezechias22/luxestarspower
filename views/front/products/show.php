<?php ob_start(); ?>

<div class="product-detail">
    <div class="container">
        <div class="product-content">
            <?php if($product->thumbnail_path): ?>
                <div class="product-image">
                    <img src="<?= $product->thumbnail_path ?>" alt="<?= htmlspecialchars($product->title) ?>">
                </div>
            <?php endif; ?>
            
            <div class="product-info">
                <h1><?= htmlspecialchars($product->title) ?></h1>
                <p class="seller">Par: <?= htmlspecialchars($seller->name) ?></p>
                <p class="type"><?= __('product.types.' . $product->type) ?></p>
                
                <div class="price-section">
                    <p class="price"><?= $product->formatPrice() ?></p>
                </div>
                
                <form method="POST" action="/checkout" id="checkout-form">
                    <input type="hidden" name="product_id" value="<?= $product->id ?>">
                    <input type="hidden" name="payment_method" value="stripe">
                    <button type="submit" class="btn btn-primary btn-large"><?= __('product.buy_now') ?></button>
                </form>
                
                <div class="product-description">
                    <h3><?= __('product.description') ?></h3>
                    <p><?= nl2br(htmlspecialchars($product->description)) ?></p>
                </div>
                
                <div class="product-stats">
                    <span><?= $product->views ?> vues</span>
                    <span><?= $product->sales ?> ventes</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('checkout-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    
    const response = await fetch('/checkout', {
        method: 'POST',
        body: formData
    });
    
    const data = await response.json();
    
    if (data.client_secret) {
        const stripe = Stripe('<?= $config['payment']['stripe']['public_key'] ?? '' ?>');
        const {error} = await stripe.confirmCardPayment(data.client_secret);
        
        if (error) {
            alert(error.message);
        } else {
            await fetch('/checkout/complete', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    order_id: data.order_id,
                    payment_reference: data.client_secret
                })
            });
            window.location.href = '/compte/telechargements';
        }
    }
});
</script>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>
