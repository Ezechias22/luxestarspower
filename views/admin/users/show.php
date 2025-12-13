<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 1000px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <a href="/admin/utilisateurs" style="color: #2196f3; text-decoration: none;">← Retour à la liste</a>
    </div>
    
    <h1 style="margin-bottom: 30px;">Détails de l'utilisateur</h1>
    
    <div style="background: white; padding: 40px; border-radius: 10px;">
        <h2 style="margin-bottom: 20px;"><?php echo htmlspecialchars($targetUser['name']); ?></h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div>
                <p style="color: #666; margin-bottom: 5px;">Email</p>
                <p style="font-weight: 600;"><?php echo htmlspecialchars($targetUser['email']); ?></p>
            </div>
            
            <div>
                <p style="color: #666; margin-bottom: 5px;">Rôle</p>
                <p style="font-weight: 600;"><?php echo htmlspecialchars($targetUser['role']); ?></p>
            </div>
            
            <div>
                <p style="color: #666; margin-bottom: 5px;">Inscrit le</p>
                <p style="font-weight: 600;"><?php echo date('d/m/Y', strtotime($targetUser['created_at'])); ?></p>
            </div>
            
            <div>
                <p style="color: #666; margin-bottom: 5px;">Statut</p>
                <p style="font-weight: 600; color: <?php echo ($targetUser['is_active'] ?? 1) ? '#4caf50' : '#f44336'; ?>;">
                    <?php echo ($targetUser['is_active'] ?? 1) ? 'Actif' : 'Suspendu'; ?>
                </p>
            </div>
        </div>
        
        <h3 style="margin: 30px 0 20px 0;">Actions</h3>
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <form method="POST" action="/admin/utilisateurs/<?php echo $targetUser['id']; ?>/role">
                <select name="role" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-right: 10px;">
                    <option value="buyer" <?php echo $targetUser['role'] === 'buyer' ? 'selected' : ''; ?>>Acheteur</option>
                    <option value="seller" <?php echo $targetUser['role'] === 'seller' ? 'selected' : ''; ?>>Vendeur</option>
                    <option value="admin" <?php echo $targetUser['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
                <button type="submit" class="btn">Changer le rôle</button>
            </form>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>