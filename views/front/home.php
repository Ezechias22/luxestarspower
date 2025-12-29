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
                    <div class="product-card" style="position: relative;">
                        
                        <!-- Badge PROMO -->
                        <?php if(!empty($product['is_on_sale']) && !empty($product['discount_percentage'])): ?>
                            <div style="position: absolute; top: 10px; right: 10px; background: #ff4444; color: white; padding: 8px 15px; border-radius: 20px; font-weight: bold; font-size: 0.85rem; z-index: 10; box-shadow: 0 2px 8px rgba(255,68,68,0.4);">
                                🔥 -<?php echo $product['discount_percentage']; ?>%
                            </div>
                        <?php endif; ?>
                        
                        <!-- Image -->
                        <?php if(!empty($product['thumbnail_path'])): ?>
                            <img src="<?php echo htmlspecialchars($product['thumbnail_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <?php else: ?>
                            <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; border-radius: 8px; margin-bottom: 15px;">
                                <span style="font-size: 3rem;">📦</span>
                            </div>
                        <?php endif; ?>
                        
                        <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                        
                        <!-- Prix avec promotion -->
                        <?php if(!empty($product['is_on_sale']) && !empty($product['original_price'])): ?>
                            <div style="margin: 10px 0;">
                                <span style="text-decoration: line-through; color: #999; font-size: 0.9rem;">
                                    $<?php echo number_format($product['original_price'], 2); ?>
                                </span>
                                <span class="price" style="color: #ff4444; margin-left: 8px;">
                                    $<?php echo number_format($product['price'], 2); ?>
                                </span>
                            </div>
                        <?php else: ?>
                            <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                        <?php endif; ?>
                        
                        <!-- Barre de progression -->
                        <?php 
                            $currentSales = $product['sales'] ?? 0;
                            $goal = $product['sales_goal'] ?? 100;
                            $percentage = min(100, ($goal > 0 ? ($currentSales / $goal) * 100 : 0));
                        ?>
                        <?php if($currentSales > 0): ?>
                        <div style="margin: 15px 0;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.85rem;">
                                <span style="color: #666;">🔥 <?php echo $currentSales; ?> <?php echo __('sold'); ?></span>
                                <span style="color: #333; font-weight: 600;">
                                    <?php echo round($percentage); ?>%
                                </span>
                            </div>
                            <div style="background: #e0e0e0; height: 8px; border-radius: 10px; overflow: hidden;">
                                <div style="background: linear-gradient(90deg, #667eea, #764ba2); height: 100%; width: <?php echo $percentage; ?>%; transition: width 0.3s;"></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
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
                    <div class="product-card" style="position: relative;">
                        
                        <!-- Badge PROMO -->
                        <?php if(!empty($product['is_on_sale']) && !empty($product['discount_percentage'])): ?>
                            <div style="position: absolute; top: 10px; right: 10px; background: #ff4444; color: white; padding: 8px 15px; border-radius: 20px; font-weight: bold; font-size: 0.85rem; z-index: 10; box-shadow: 0 2px 8px rgba(255,68,68,0.4);">
                                🔥 -<?php echo $product['discount_percentage']; ?>%
                            </div>
                        <?php endif; ?>
                        
                        <!-- Image -->
                        <?php if(!empty($product['thumbnail_path'])): ?>
                            <img src="<?php echo htmlspecialchars($product['thumbnail_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <?php else: ?>
                            <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; border-radius: 8px; margin-bottom: 15px;">
                                <span style="font-size: 3rem;">📦</span>
                            </div>
                        <?php endif; ?>
                        
                        <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                        
                        <!-- Prix avec promotion -->
                        <?php if(!empty($product['is_on_sale']) && !empty($product['original_price'])): ?>
                            <div style="margin: 10px 0;">
                                <span style="text-decoration: line-through; color: #999; font-size: 0.9rem;">
                                    $<?php echo number_format($product['original_price'], 2); ?>
                                </span>
                                <span class="price" style="color: #ff4444; margin-left: 8px;">
                                    $<?php echo number_format($product['price'], 2); ?>
                                </span>
                            </div>
                        <?php else: ?>
                            <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                        <?php endif; ?>
                        
                        <!-- Barre de progression -->
                        <?php 
                            $currentSales = $product['sales'] ?? 0;
                            $goal = $product['sales_goal'] ?? 100;
                            $percentage = min(100, ($goal > 0 ? ($currentSales / $goal) * 100 : 0));
                        ?>
                        <?php if($currentSales > 0): ?>
                        <div style="margin: 15px 0;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.85rem;">
                                <span style="color: #666;">🔥 <?php echo $currentSales; ?> <?php echo __('sold'); ?></span>
                                <span style="color: #333; font-weight: 600;">
                                    <?php echo round($percentage); ?>%
                                </span>
                            </div>
                            <div style="background: #e0e0e0; height: 8px; border-radius: 10px; overflow: hidden;">
                                <div style="background: linear-gradient(90deg, #667eea, #764ba2); height: 100%; width: <?php echo $percentage; ?>%; transition: width 0.3s;"></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
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