<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 800px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">⚙️ Paramètres du Site</h1>
    
    <div style="background: white; padding: 40px; border-radius: 10px;">
        <h2 style="margin-bottom: 20px;">Configuration générale</h2>
        
        <form method="POST" action="/admin/parametres">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Nom du site</label>
                <input type="text" name="site_name" value="Luxe Stars Power" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Email de contact</label>
                <input type="email" name="contact_email" value="contact@luxestarspower.com" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Commission plateforme (%)</label>
                <input type="number" name="commission" value="10" min="0" max="100" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="maintenance_mode">
                    <span>Mode maintenance</span>
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        </form>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>