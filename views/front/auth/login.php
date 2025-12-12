<?php ob_start(); ?>

<div style="max-width: 400px; margin: 80px auto; padding: 40px; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h2 style="text-align: center; margin-bottom: 30px;">Connexion</h2>
    
    <?php if(isset($error)): ?>
        <div style="background: #fee; border: 1px solid #fcc; color: #c00; padding: 12px; border-radius: 5px; margin-bottom: 20px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="/connexion">
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Adresse e-mail</label>
            <input type="email" name="email" required 
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
        </div>
        
        <div style="margin-bottom: 25px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">Mot de passe</label>
            <input type="password" name="password" required 
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
        </div>
        
        <button type="submit" class="btn btn-primary" 
                style="width: 100%; padding: 14px; background: #667eea; color: white; border: none; border-radius: 5px; font-size: 1rem; font-weight: 600; cursor: pointer;">
            Se connecter
        </button>
    </form>
    
    <p style="text-align: center; margin-top: 25px; color: #666;">
        Pas encore de compte ? <a href="/inscription" style="color: #667eea; text-decoration: none; font-weight: 600;">S'inscrire</a>
    </p>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>