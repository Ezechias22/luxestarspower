<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 800px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">✏️ <?php echo __('edit_product'); ?></h1>
    
    <?php if(isset($_SESSION['error'])): ?>
        <div style="background: #ffebee; border: 1px solid #f44336; color: #c62828; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            ❌ <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <form method="POST" action="/vendeur/produits/<?php echo $product['id']; ?>/modifier" enctype="multipart/form-data">
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                    <?php echo __('product_title'); ?> *
                </label>
                <input type="text" name="title" required 
                       value="<?php echo htmlspecialchars($product['title']); ?>"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                    <?php echo __('description'); ?> *
                </label>
                <textarea name="description" required rows="6"
                          style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                        <?php echo __('price'); ?> ($) *
                    </label>
                    <input type="number" name="price" step="0.01" min="0" required 
                           value="<?php echo htmlspecialchars($product['price']); ?>"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                        <?php echo __('product_type'); ?> *
                    </label>
                    <select name="type" required 
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
                        <option value="ebook" <?php echo $product['type'] === 'ebook' ? 'selected' : ''; ?>>
                            📚 <?php echo __('ebook'); ?>
                        </option>
                        <option value="video" <?php echo $product['type'] === 'video' ? 'selected' : ''; ?>>
                            🎥 <?php echo __('video'); ?>
                        </option>
                        <option value="image" <?php echo $product['type'] === 'image' ? 'selected' : ''; ?>>
                            🖼️ <?php echo __('image'); ?>
                        </option>
                        <option value="course" <?php echo $product['type'] === 'course' ? 'selected' : ''; ?>>
                            🎓 <?php echo __('course'); ?>
                        </option>
                        <option value="file" <?php echo $product['type'] === 'file' ? 'selected' : ''; ?>>
                            📁 <?php echo __('file'); ?>
                        </option>
                    </select>
                </div>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                    <?php echo __('product_thumbnail'); ?>
                </label>
                
                <?php if(!empty($product['thumbnail_path'])): ?>
                    <div style="margin-bottom: 15px;">
                        <img src="<?php echo htmlspecialchars($product['thumbnail_path']); ?>" 
                             alt="Current thumbnail"
                             style="max-width: 200px; border-radius: 8px; border: 2px solid #ddd;">
                        <p style="color: #666; font-size: 0.875rem; margin-top: 8px;">
                            <?php echo __('current_image'); ?>
                        </p>
                    </div>
                <?php endif; ?>
                
                <input type="file" name="thumbnail" accept="image/*"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                <small style="color: #666; font-size: 0.875rem;">
                    <?php echo __('leave_empty_keep_current'); ?>
                </small>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" 
                           <?php echo $product['is_active'] ? 'checked' : ''; ?>
                           style="width: 20px; height: 20px; cursor: pointer;">
                    <span style="font-weight: 600;"><?php echo __('product_active'); ?></span>
                </label>
            </div>
            
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <?php echo __('save'); ?>
                </button>
                <a href="/vendeur/produits" class="btn" style="flex: 1; text-align: center;">
                    <?php echo __('cancel'); ?>
                </a>
            </div>
        </form>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>