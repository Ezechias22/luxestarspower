<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;"><?php echo __('dashboard'); ?></h1>
    
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2 style="margin-bottom: 20px;">
            <?php echo __('welcome'); ?>, <?php echo ucwords(strtolower(htmlspecialchars($user['name']))); ?> !
        </h2>
        <p style="color: #666; margin-bottom: 10px;">
            <strong><?php echo __('email'); ?> :</strong> <?php echo htmlspecialchars($user['email']); ?>
        </p>
        <p style="color: #666;">
            <strong><?php echo __('role'); ?> :</strong> <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
        </p>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px;">
            <h3 style="margin-bottom: 10px; font-size: 2rem;"><?php echo $totalOrders; ?></h3>
            <p><?php echo __('my_orders'); ?></p>
        </div>
        
        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px; border-radius: 10px;">
            <h3 style="margin-bottom: 10px; font-size: 2rem;">$<?php echo number_format($totalSpent, 2); ?></h3>
            <p><?php echo __('total_spent'); ?></p>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <a href="/compte/achats" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">📦 <?php echo __('my_purchases'); ?></h3>
            <p style="color: #666;"><?php echo __('view_order_history'); ?></p>
        </a>
        
        <a href="/compte/telechargements" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">⬇️ <?php echo __('downloads'); ?></h3>
            <p style="color: #666;"><?php echo __('access_your_files'); ?></p>
        </a>
        
        <a href="/compte/parametres" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">⚙️ <?php echo __('account_settings'); ?></h3>
            <p style="color: #666;"><?php echo __('manage_your_account'); ?></p>
        </a>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>