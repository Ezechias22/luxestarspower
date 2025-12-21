<?php ob_start(); ?>

<div class="hero">
    <div class="container">
        <h1><?php echo __('discover_digital_products'); ?></h1>
        <p><?php echo __('marketplace_description'); ?></p>
        <div>
            <a href="/produits" class="btn btn-primary"><?php echo __('browse_products'); ?></a>
            <a href="/vendre" class="btn"><?php echo __('become_seller'); ?></a>
        </div>
    </div>
</div>

<section>
    <div class="container">
        <h2><?php echo __('featured_products'); ?></h2>
        <?php if(!empty($featuredProducts)): ?>
            <div class="products-grid">
                <?php foreach($featuredProducts as $product): ?>
                    <div class="product-card">
                        <?php if(!empty($product['thumbnail_path'])): ?>
                            <img src="<?php echo htmlspecialchars($product['thumbnail_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <?php else: ?>
                            <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; border-radius: 8px; margin-bottom: 15px;">
                                <span style="font-size: 3rem;">📦</span>
                            </div>
                        <?php endif; ?>
                        
                        <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                        <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                        <a href="/produit/<?php echo htmlspecialchars($product['slug']); ?>" class="btn btn-block">
                            <?php echo __('view'); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #666; padding: 40px 0;">
                <?php echo __('no_products_found'); ?>
            </p>
        <?php endif; ?>
    </div>
</section>

<section style="background: white;">
    <div class="container">
        <h2><?php echo __('latest_products'); ?></h2>
        <?php if(!empty($latestProducts)): ?>
            <div class="products-grid">
                <?php foreach($latestProducts as $product): ?>
                    <div class="product-card">
                        <?php if(!empty($product['thumbnail_path'])): ?>
                            <img src="<?php echo htmlspecialchars($product['thumbnail_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <?php else: ?>
                            <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; border-radius: 8px; margin-bottom: 15px;">
                                <span style="font-size: 3rem;">📦</span>
                            </div>
                        <?php endif; ?>
                        
                        <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                        <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                        <a href="/produit/<?php echo htmlspecialchars($product['slug']); ?>" class="btn btn-block">
                            <?php echo __('view'); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #666; padding: 40px 0;">
                <?php echo __('no_products_found'); ?>
            </p>
        <?php endif; ?>
    </div>
</section>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>