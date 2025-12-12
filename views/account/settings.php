<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 800px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">Paramètres du compte</h1>
    
    <?php if(isset($_GET['success'])): ?>
        <div style="background: #e8f5e9; border: 1px solid #4caf50; color: #2e7d32; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            ✅ Paramètres mis à jour avec succès !
        </div>
    <?php endif; ?>
    
    <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <form method="POST" action="/compte/parametres">
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Nom complet</label>
                <input type="text" name="name" required 
                       value="<?php echo htmlspecialchars($user['name']); ?>"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
            </div>
            
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Email</label>
                <input type="email" 
                       value="<?php echo htmlspecialchars($user['email']); ?>"
                       disabled
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; background: #f5f5f5; color: #999;">
                <small style="color: #666; font-size: 0.875rem;">L'email ne peut pas être modifié</small>
            </div>
            
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Rôle</label>
                <input type="text" 
                       value="<?php echo htmlspecialchars(ucfirst($user['role'])); ?>"
                       disabled
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; background: #f5f5f5; color: #999;">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px;">
                Enregistrer les modifications
            </button>
        </form>
        
        <hr style="margin: 40px 0; border: none; border-top: 1px solid #eee;">
        
        <div style="text-align: center;">
            <a href="/deconnexion" style="color: #f44336; text-decoration: none; font-weight: 600;">
                🚪 Se déconnecter
            </a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>