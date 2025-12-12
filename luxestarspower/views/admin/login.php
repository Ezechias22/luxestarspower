<?php ob_start(); ?>

<div class="auth-container">
    <div class="auth-box">
        <h2>Administration - Connexion</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="/admin/login">
            <div class="form-group">
                <label>Email administrateur</label>
                <input type="email" name="email" required autofocus>
            </div>
            
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
        </form>
        
        <p style="text-align:center; margin-top:1rem; color:#666;">
            <small>Accès réservé aux administrateurs</small>
        </p>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>
