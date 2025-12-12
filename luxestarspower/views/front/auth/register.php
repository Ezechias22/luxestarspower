<?php ob_start(); ?>

<div class="auth-container">
    <div class="auth-box">
        <h2><?= __('nav.register') ?></h2>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="/register">
            <div class="form-group">
                <label><?= __('auth.name') ?></label>
                <input type="text" name="name" required>
            </div>
            
            <div class="form-group">
                <label><?= __('auth.email') ?></label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label><?= __('auth.password') ?></label>
                <input type="password" name="password" required minlength="8">
                <small>Minimum 8 caractères</small>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block"><?= __('nav.register') ?></button>
        </form>
        
        <p class="auth-link"><?= __('auth.has_account') ?> <a href="/login"><?= __('nav.login') ?></a></p>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>
