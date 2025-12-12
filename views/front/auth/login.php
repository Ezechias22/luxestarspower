<?php ob_start(); ?>

<div class="auth-container">
    <div class="auth-box">
        <h2><?= __('nav.login') ?></h2>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="/login">
            <div class="form-group">
                <label><?= __('auth.email') ?></label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label><?= __('auth.password') ?></label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block"><?= __('nav.login') ?></button>
        </form>
        
        <p class="auth-link"><?= __('auth.no_account') ?> <a href="/register"><?= __('nav.register') ?></a></p>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>
