<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 1400px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">🎛️ <?php echo __('admin_panel'); ?></h1>
    
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2 style="margin-bottom: 20px;">
            <?php echo __('welcome'); ?>, <?php echo ucwords(strtolower(htmlspecialchars($user['name']))); ?> !
        </h2>
        <p style="color: #666;">
            <strong><?php echo __('role'); ?> :</strong> 
            <span style="color: #e74c3c; font-weight: 600;">👑 <?php echo __('admin'); ?></span>
        </p>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px;">
            <h3 style="margin-bottom: 10px; font-size: 2.5rem;"><?php echo $stats['total_users']; ?></h3>
            <p style="opacity: 0.9;"><?php echo __('total_users'); ?></p>
        </div>
        
        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px; border-radius: 10px;">
            <h3 style="margin-bottom: 10px; font-size: 2.5rem;"><?php echo $stats['total_sellers']; ?></h3>
            <p style="opacity: 0.9;"><?php echo __('total_sellers'); ?></p>
        </div>
        
        <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 30px; border-radius: 10px;">
            <h3 style="margin-bottom: 10px; font-size: 2.5rem;"><?php echo $stats['total_products']; ?></h3>
            <p style="opacity: 0.9;"><?php echo __('total_products'); ?></p>
        </div>
        
        <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 30px; border-radius: 10px;">
            <h3 style="margin-bottom: 10px; font-size: 2.5rem;"><?php echo $stats['total_orders']; ?></h3>
            <p style="opacity: 0.9;"><?php echo __('total_orders'); ?></p>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <a href="/admin/utilisateurs" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">👥 <?php echo __('manage_users'); ?></h3>
            <p style="color: #666;"><?php echo __('manage_user_accounts'); ?></p>
        </a>
        
        <a href="/admin/produits" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">📦 <?php echo __('manage_products'); ?></h3>
            <p style="color: #666;"><?php echo __('moderate_products'); ?></p>
        </a>
        
        <a href="/admin/commandes" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">🛒 <?php echo __('manage_orders'); ?></h3>
            <p style="color: #666;"><?php echo __('view_all_orders'); ?></p>
        </a>
        
        <a href="/admin/paiements" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">💰 <?php echo __('seller_payouts'); ?></h3>
            <p style="color: #666;"><?php echo __('manage_payouts'); ?></p>
        </a>
        
        <a href="/admin/categories" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">🏷️ <?php echo __('manage_categories'); ?></h3>
            <p style="color: #666;"><?php echo __('manage_categories_desc'); ?></p>
        </a>
        
        <a href="/admin/avis" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">⭐ <?php echo __('reviews'); ?></h3>
            <p style="color: #666;"><?php echo __('moderate_reviews'); ?></p>
        </a>
        
        <a href="/admin/parametres" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">⚙️ <?php echo __('site_settings'); ?></h3>
            <p style="color: #666;"><?php echo __('site_configuration'); ?></p>
        </a>
        
        <a href="/admin/rapports" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">📊 <?php echo __('reports'); ?></h3>
            <p style="color: #666;"><?php echo __('detailed_statistics'); ?></p>
        </a>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>