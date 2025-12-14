<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px;">
    <h1 style="margin-bottom: 30px;"><?php echo __('all_products'); ?></h1>
    
    <div style="margin: 30px 0;">
        <form method="GET" action="/produits">
            <select name="type" onchange="this.form.submit()" 
                    style="padding: 12px 20px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
                <option value=""><?php echo __('all_types'); ?></option>
                <option value="ebook" <?php echo ($_GET['type'] ?? '') === 'ebook' ? 'selected' : ''; ?>>
                    📚 <?php echo __('ebook'); ?>
                </option>
                <option value="video" <?php echo ($_GET['type'] ?? '') === 'video' ? 'selected' : ''; ?>>
                    🎥 <?php echo __('video'); ?>
                </option>
                <option value="image" <?php echo ($_GET['type'] ?? '') === 'image' ? 'selected' : ''; ?>>
                    🖼️ <?php echo __('image'); ?>
                </option>
                <option value="course" <?php echo ($_GET['type'] ?? '') === 'course' ? 'selected' : ''; ?>>
                    🎓 <?php echo __('course'); ?>
                </option>
                <option value="file" <?php echo ($_GET['type'] ?? '') === 'file' ? 'selected' : ''; ?>>
                    📁 <?php echo __('file'); ?>
                </option>
            </select>
        </form>
    </div>
    
    <?php if (empty($products) || !is_array($products)): ?>
        <div style="text-align: center; padding: 80px 20px;">
            <h2 style="color: #666; margin-bottom: 20px;"><?php echo __('no_products_found'); ?></h2>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach($products as $product): ?>
                <div class="product-card">
                    <?php if(!empty($product['thumbnail_path'])): ?>
                        <img src="<?php echo htmlspecialchars($product['thumbnail_path']); ?>" 
                             alt="<?php echo htmlspecialchars($product['title']); ?>"
                             style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 15px;">
                    <?php else: ?>
                        <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                            📦
                        </div>
                    <?php endif; ?>
                    
                    <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                    
                    <?php if(!empty($product['type'])): ?>
                        <p style="color: #667eea; font-size: 0.875rem; text-transform: uppercase; font-weight: 600; margin: 8px 0;">
                            <?php echo __($product['type']); ?>
                        </p>
                    <?php endif; ?>
                    
                    <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                    
                    <a href="/produit/<?php echo htmlspecialchars($product['slug']); ?>" class="btn">
                        <?php echo __('view'); ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if($page > 1 || count($products) >= 20): ?>
            <div style="display: flex; gap: 20px; justify-content: center; margin: 50px 0;">
                <?php if($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo isset($filters['type']) ? '&type=' . $filters['type'] : ''; ?>" class="btn">
                        ← <?php echo __('previous'); ?>
                    </a>
                <?php endif; ?>
                
                <?php if(count($products) >= 20): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo isset($filters['type']) ? '&type=' . $filters['type'] : ''; ?>" class="btn">
                        <?php echo __('next'); ?> →
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>