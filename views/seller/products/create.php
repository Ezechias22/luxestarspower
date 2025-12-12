<?php ob_start(); ?>

<div class="seller-products">
    <div class="container">
        <h1>Ajouter un produit</h1>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="/vendeur/produit/nouveau" enctype="multipart/form-data" class="product-form">
            <div class="form-group">
                <label>Titre *</label>
                <input type="text" name="title" required>
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="5"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Type *</label>
                    <select name="type" required>
                        <option value="ebook">Ebook</option>
                        <option value="video">Vidéo</option>
                        <option value="image">Image</option>
                        <option value="course">Formation</option>
                        <option value="file">Fichier</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Prix *</label>
                    <input type="number" name="price" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label>Devise</label>
                    <select name="currency">
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                        <option value="GBP">GBP</option>
                        <option value="CAD">CAD</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>Fichier principal *</label>
                <input type="file" name="file" required>
                <small>PDF, ZIP, MP4, JPG, PNG acceptés. Max 500MB</small>
            </div>
            
            <div class="form-group">
                <label>Miniature (optionnel)</label>
                <input type="file" name="thumbnail" accept="image/*">
                <small>Image d'aperçu du produit</small>
            </div>
            
            <button type="submit" class="btn btn-primary btn-large">Publier le produit</button>
        </form>
    </div>
</div>

<style>
.product-form { max-width: 800px; margin: 2rem 0; }
.form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
small { display: block; margin-top: 0.25rem; color: #666; font-size: 0.875rem; }
</style>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>
