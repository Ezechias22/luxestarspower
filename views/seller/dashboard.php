<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;"><?php echo __('seller_dashboard'); ?></h1>
    
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2 style="margin-bottom: 20px;">
            <?php echo __('welcome'); ?>, <?php echo ucwords(strtolower(htmlspecialchars($user['name']))); ?> !
        </h2>
        <p style="color: #666;">
            <strong><?php echo __('status'); ?> :</strong> 
            <span style="color: #4caf50; font-weight: 600;">✅ <?php echo __('active_seller_account'); ?></span>
        </p>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px;">
            <h3 style="margin-bottom: 10px; font-size: 2rem;"><?php echo $totalProducts; ?></h3>
            <p><?php echo __('products_for_sale'); ?></p>
        </div>
        
        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px; border-radius: 10px;">
            <h3 style="margin-bottom: 10px; font-size: 2rem;"><?php echo $totalSales; ?></h3>
            <p><?php echo __('total_sales'); ?></p>
        </div>
        
        <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 30px; border-radius: 10px;">
            <h3 style="margin-bottom: 10px; font-size: 2rem;">$<?php echo number_format($totalRevenue, 2); ?></h3>
            <p><?php echo __('total_revenue'); ?></p>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <a href="/vendeur/produits" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333; transition: transform 0.3s;">
            <h3 style="margin-bottom: 10px;">📦 <?php echo __('my_products'); ?></h3>
            <p style="color: #666;"><?php echo __('manage_your_catalog'); ?></p>
        </a>
        
        <a href="/vendeur/produits/nouveau" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333; transition: transform 0.3s;">
            <h3 style="margin-bottom: 10px;">➕ <?php echo __('add_new_product'); ?></h3>
            <p style="color: #666;"><?php echo __('list_new_product'); ?></p>
        </a>
        
        <a href="/vendeur/commandes" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333; transition: transform 0.3s;">
            <h3 style="margin-bottom: 10px;">🛒 <?php echo __('my_orders'); ?></h3>
            <p style="color: #666;"><?php echo __('view_received_orders'); ?></p>
        </a>
        
        <a href="/vendeur/paiements" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333; transition: transform 0.3s;">
            <h3 style="margin-bottom: 10px;">💰 <?php echo __('payouts'); ?></h3>
            <p style="color: #666;"><?php echo __('manage_your_payouts'); ?></p>
        </a>
        
        <a href="/vendeur/statistiques" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333; transition: transform 0.3s;">
            <h3 style="margin-bottom: 10px;">📊 <?php echo __('statistics'); ?></h3>
            <p style="color: #666;"><?php echo __('analyze_performance'); ?></p>
        </a>
        
        <a href="/vendeur/avis" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333; transition: transform 0.3s;">
            <h3 style="margin-bottom: 10px;">⭐ <?php echo __('reviews'); ?></h3>
            <p style="color: #666;"><?php echo __('view_customer_reviews'); ?></p>
        </a>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>