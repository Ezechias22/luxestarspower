<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px;">
    <h1 style="margin-bottom: 30px;">👥 Gestion des Utilisateurs</h1>
    
    <?php if(isset($_SESSION['flash_success'])): ?>
        <div style="background: #e8f5e9; border: 1px solid #4caf50; color: #2e7d32; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            ✅ <?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>
    
    <!-- Filtres -->
    <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
        <form method="GET" action="/admin/utilisateurs" style="display: flex; gap: 15px; flex-wrap: wrap;">
            <input type="text" name="search" placeholder="Rechercher..." 
                   value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>"
                   style="flex: 1; min-width: 200px; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            
            <select name="role" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                <option value="">Tous les rôles</option>
                <option value="buyer" <?php echo ($filters['role'] ?? '') === 'buyer' ? 'selected' : ''; ?>>Acheteurs</option>
                <option value="seller" <?php echo ($filters['role'] ?? '') === 'seller' ? 'selected' : ''; ?>>Vendeurs</option>
                <option value="admin" <?php echo ($filters['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admins</option>
            </select>
            
            <button type="submit" class="btn">Filtrer</button>
        </form>
    </div>
    
    <!-- Tableau des utilisateurs -->
    <div style="background: white; border-radius: 10px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f5;">
                <tr>
                    <th style="padding: 15px; text-align: left;">ID</th>
                    <th style="padding: 15px; text-align: left;">Nom</th>
                    <th style="padding: 15px; text-align: left;">Email</th>
                    <th style="padding: 15px; text-align: left;">Rôle</th>
                    <th style="padding: 15px; text-align: left;">Statut</th>
                    <th style="padding: 15px; text-align: left;">Inscrit le</th>
                    <th style="padding: 15px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($users)): ?>
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: #999;">
                            Aucun utilisateur trouvé
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($users as $u): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 15px;">#<?php echo $u['id']; ?></td>
                            <td style="padding: 15px;"><?php echo htmlspecialchars($u['name']); ?></td>
                            <td style="padding: 15px;"><?php echo htmlspecialchars($u['email']); ?></td>
                            <td style="padding: 15px;">
                                <span style="padding: 5px 10px; background: #e3f2fd; color: #1976d2; border-radius: 15px; font-size: 0.875rem;">
                                    <?php echo htmlspecialchars($u['role']); ?>
                                </span>
                            </td>
                            <td style="padding: 15px;">
                                <?php if($u['is_active'] ?? 1): ?>
                                    <span style="color: #4caf50;">✅ Actif</span>
                                <?php else: ?>
                                    <span style="color: #f44336;">❌ Suspendu</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 15px;">
                                <?php echo date('d/m/Y', strtotime($u['created_at'])); ?>
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <div style="display: flex; gap: 5px; justify-content: center;">
                                    <a href="/admin/utilisateurs/<?php echo $u['id']; ?>" 
                                       style="padding: 5px 10px; background: #2196f3; color: white; text-decoration: none; border-radius: 3px; font-size: 0.875rem;">
                                        Voir
                                    </a>
                                    
                                    <?php if($u['is_active'] ?? 1): ?>
                                        <form method="POST" action="/admin/utilisateurs/<?php echo $u['id']; ?>/suspendre" style="display: inline;">
                                            <button type="submit" style="padding: 5px 10px; background: #ff9800; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 0.875rem;">
                                                Suspendre
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="/admin/utilisateurs/<?php echo $u['id']; ?>/activer" style="display: inline;">
                                            <button type="submit" style="padding: 5px 10px; background: #4caf50; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 0.875rem;">
                                                Activer
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>