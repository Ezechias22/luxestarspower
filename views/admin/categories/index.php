<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>🏷️ Gestion des Catégories</h1>
        <button onclick="document.getElementById('addModal').style.display='flex'" class="btn btn-primary">
            ➕ Nouvelle catégorie
        </button>
    </div>
    
    <?php if(isset($_SESSION['flash_success'])): ?>
        <div style="background: #e8f5e9; border: 1px solid #4caf50; color: #2e7d32; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            ✅ <?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['flash_error'])): ?>
        <div style="background: #ffebee; border: 1px solid #f44336; color: #c62828; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            ❌ <?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>
    
    <div style="background: white; border-radius: 10px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f5;">
                <tr>
                    <th style="padding: 15px; text-align: left;">Icône</th>
                    <th style="padding: 15px; text-align: left;">Nom</th>
                    <th style="padding: 15px; text-align: left;">Slug</th>
                    <th style="padding: 15px; text-align: left;">Description</th>
                    <th style="padding: 15px; text-align: center;">Ordre</th>
                    <th style="padding: 15px; text-align: center;">Statut</th>
                    <th style="padding: 15px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($categories)): ?>
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: #999;">
                            Aucune catégorie
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($categories as $cat): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 15px; font-size: 2rem;"><?php echo $cat['icon'] ?? '📦'; ?></td>
                            <td style="padding: 15px; font-weight: 600;"><?php echo htmlspecialchars($cat['name']); ?></td>
                            <td style="padding: 15px; color: #666;"><?php echo htmlspecialchars($cat['slug']); ?></td>
                            <td style="padding: 15px; color: #666;"><?php echo htmlspecialchars($cat['description'] ?? '-'); ?></td>
                            <td style="padding: 15px; text-align: center;"><?php echo $cat['display_order']; ?></td>
                            <td style="padding: 15px; text-align: center;">
                                <?php if($cat['is_active']): ?>
                                    <span style="color: #4caf50;">✅ Actif</span>
                                <?php else: ?>
                                    <span style="color: #f44336;">❌ Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <div style="display: flex; gap: 5px; justify-content: center;">
                                    <button onclick="editCategory(<?php echo htmlspecialchars(json_encode($cat)); ?>)" 
                                            style="padding: 5px 10px; background: #2196f3; color: white; border: none; border-radius: 3px; cursor: pointer;">
                                        ✏️ Modifier
                                    </button>
                                    
                                    <form method="POST" action="/admin/categories/<?php echo $cat['id']; ?>/supprimer" style="display: inline;">
                                        <button type="submit" onclick="return confirm('Supprimer cette catégorie ?')"
                                                style="padding: 5px 10px; background: #e74c3c; color: white; border: none; border-radius: 3px; cursor: pointer;">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajout -->
<div id="addModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
    <div style="background: white; padding: 40px; border-radius: 10px; max-width: 500px; width: 90%;">
        <h2 style="margin-bottom: 20px;">Nouvelle catégorie</h2>
        <form method="POST" action="/admin/categories">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Nom</label>
                <input type="text" name="name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Description</label>
                <textarea name="description" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Icône (emoji)</label>
                <input type="text" name="icon" value="📦" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Ordre d'affichage</label>
                <input type="number" name="display_order" value="0" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Créer</button>
                <button type="button" onclick="document.getElementById('addModal').style.display='none'" class="btn" style="flex: 1;">Annuler</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Édition -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;">
    <div style="background: white; padding: 40px; border-radius: 10px; max-width: 500px; width: 90%;">
        <h2 style="margin-bottom: 20px;">Modifier la catégorie</h2>
        <form method="POST" id="editForm">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Nom</label>
                <input type="text" name="name" id="edit_name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Description</label>
                <textarea name="description" id="edit_description" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Icône (emoji)</label>
                <input type="text" name="icon" id="edit_icon" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Ordre d'affichage</label>
                <input type="number" name="display_order" id="edit_display_order" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="is_active" id="edit_is_active">
                    <span>Actif</span>
                </label>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Enregistrer</button>
                <button type="button" onclick="document.getElementById('editModal').style.display='none'" class="btn" style="flex: 1;">Annuler</button>
            </div>
        </form>
    </div>
</div>

<script>
function editCategory(cat) {
    document.getElementById('edit_name').value = cat.name;
    document.getElementById('edit_description').value = cat.description || '';
    document.getElementById('edit_icon').value = cat.icon || '📦';
    document.getElementById('edit_display_order').value = cat.display_order || 0;
    document.getElementById('edit_is_active').checked = cat.is_active == 1;
    document.getElementById('editForm').action = '/admin/categories/' + cat.id;
    document.getElementById('editModal').style.display = 'flex';
}
</script>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>