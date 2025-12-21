<?php ob_start(); ?>

<div class="container" style="padding: 60px 20px; max-width: 800px; margin: 0 auto;">
    <h1 style="text-align: center; margin-bottom: 40px;">📧 Contactez-nous</h1>
    
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
    
    <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <form method="POST" action="/contact">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Nom complet</label>
                <input type="text" name="name" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Email</label>
                <input type="email" name="email" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Message</label>
                <textarea name="message" required rows="6" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">
                Envoyer le message
            </button>
        </form>
    </div>
    
    <div style="margin-top: 40px; text-align: center;">
        <p style="color: #666; margin-bottom: 10px;">Vous pouvez aussi nous contacter directement :</p>
        <p style="font-size: 1.1rem;">📞 <strong>+55 54 99302-4286</strong></p>
        <p style="color: #666;">Disponible 24/7</p>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>