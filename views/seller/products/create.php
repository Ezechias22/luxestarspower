<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 800px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">Ajouter un Produit</h1>

    <?php if(isset($_SESSION['flash_error'])): ?>
        <div style="background: #ffebee; border: 1px solid #f44336; color: #c62828; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            ❌ <?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <form method="POST" action="/vendeur/produits" enctype="multipart/form-data">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Titre du produit *</label>
                <input type="text" name="title" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Description *</label>
                <textarea name="description" required rows="5" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Prix ($) *</label>
                <input type="number" name="price" required step="0.01" min="0" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Type de produit *</label>
                <select name="type" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                    <option value="">Sélectionnez un type</option>
                    <option value="ebook">📚 Ebook</option>
                    <option value="video">🎥 Vidéo</option>
                    <option value="image">🖼️ Image</option>
                    <option value="course">🎓 Formation</option>
                    <option value="file">📁 Fichier</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Image de présentation (optionnel)</label>
                <input type="file" name="thumbnail" accept="image/*" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                <small style="color: #666; font-size: 0.875rem; display: block; margin-top: 5px;">
                    Une image par défaut sera utilisée si vous n'en uploadez pas.
                </small>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Fichier du produit *</label>
                <input type="file" name="file" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                <small style="color: #666; font-size: 0.875rem; display: block; margin-top: 5px;">
                    PDF, ZIP, MP4, ou tout autre fichier numérique (Max 100MB)
                </small>
            </div>

            <div style="margin-bottom: 20px; background: #f8f9fa; padding: 20px; border-radius: 8px; border: 2px solid #e9ecef;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 15px;">
                    <input type="checkbox" name="is_featured" value="1"
                           style="width: 20px; height: 20px; cursor: pointer;">
                    <span style="font-weight: 600; font-size: 1.1rem;">🌟 Produit en vedette</span>
                </label>
                <p style="color: #666; font-size: 0.9rem; margin: 0;">
                    Les produits en vedette apparaissent en priorité sur la page d'accueil et dans les recherches.
                </p>
            </div>

            <div style="display: flex; gap: 15px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    Publier le produit
                </button>
                <a href="/vendeur/produits" class="btn" style="flex: 1; text-align: center;">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>