<?php ob_start(); ?>

<div style="max-width: 600px; margin: 80px auto; padding: 40px; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h2 style="text-align: center; margin-bottom: 30px;"><?php echo __('register'); ?></h2>
    
    <?php if(isset($error)): ?>
        <div style="background: #fee; border: 1px solid #fcc; color: #c00; padding: 12px; border-radius: 5px; margin-bottom: 20px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="/inscription">
        
        <!-- Nom complet -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                <?php echo __('full_name'); ?> *
            </label>
            <input type="text" name="name" required 
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
        </div>
        
        <!-- Email -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                <?php echo __('email'); ?> *
            </label>
            <input type="email" name="email" required 
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
        </div>
        
        <!-- Mot de passe -->
        <div style="margin-bottom: 25px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                <?php echo __('password'); ?> *
            </label>
            <input type="password" name="password" required minlength="8" 
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
            <small style="color: #666; font-size: 0.875rem;">
                <?php echo __('password_min_8'); ?>
            </small>
        </div>
        
        <!-- Type de compte -->
        <div style="margin-bottom: 25px; background: #f8f9fa; padding: 20px; border-radius: 8px; border: 2px solid #e9ecef;">
            <label style="display: block; margin-bottom: 15px; font-weight: 600; font-size: 1.1rem;">
                <?php echo __('account_type'); ?> *
            </label>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <label style="cursor: pointer; padding: 15px; border: 2px solid #ddd; border-radius: 8px; transition: all 0.3s;">
                    <input type="radio" name="role" value="buyer" checked 
                           onclick="document.getElementById('shop-fields').style.display='none'; this.parentElement.style.borderColor='#667eea'; this.parentElement.style.background='#f0f4ff';"
                           style="margin-right: 8px;">
                    <div>
                        <div style="font-size: 1.5rem; margin-bottom: 5px;">🛒</div>
                        <span style="font-weight: 600; display: block; margin-bottom: 5px;">
                            <?php echo __('buyer_account'); ?>
                        </span>
                        <small style="color: #666; font-size: 0.85rem;">
                            <?php echo __('buyer_description'); ?>
                        </small>
                    </div>
                </label>
                
                <label style="cursor: pointer; padding: 15px; border: 2px solid #ddd; border-radius: 8px; transition: all 0.3s;">
                    <input type="radio" name="role" value="seller" 
                           onclick="document.getElementById('shop-fields').style.display='block'; 
                                    this.parentElement.style.borderColor='#667eea'; 
                                    this.parentElement.style.background='#f0f4ff';
                                    document.querySelectorAll('label').forEach(l => {
                                        if(l !== this.parentElement) {
                                            l.style.borderColor='#ddd'; 
                                            l.style.background='transparent';
                                        }
                                    });"
                           style="margin-right: 8px;">
                    <div>
                        <div style="font-size: 1.5rem; margin-bottom: 5px;">🏪</div>
                        <span style="font-weight: 600; display: block; margin-bottom: 5px;">
                            <?php echo __('seller_account'); ?>
                        </span>
                        <small style="color: #666; font-size: 0.85rem;">
                            <?php echo __('seller_description'); ?>
                        </small>
                    </div>
                </label>
            </div>
        </div>
        
        <!-- Champs boutique (cachés par défaut) -->
        <div id="shop-fields" style="display: none; background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%); padding: 25px; border-radius: 8px; margin-bottom: 20px; border: 2px solid #ffc107; animation: slideDown 0.3s ease;">
            <h3 style="margin-bottom: 20px; color: #856404; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.5rem;">🏪</span>
                <?php echo __('shop_information'); ?>
            </h3>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #856404;">
                    <?php echo __('shop_name'); ?> *
                </label>
                <input type="text" name="shop_name" id="shop_name"
                       oninput="generateSlug()"
                       placeholder="Ex: Ma Boutique Premium"
                       style="width: 100%; padding: 12px; border: 2px solid #ffc107; border-radius: 5px; font-size: 1rem; background: white;">
                <small style="color: #856404; font-size: 0.85rem;">
                    <?php echo __('shop_name_hint'); ?>
                </small>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #856404;">
                    <?php echo __('shop_url'); ?>
                </label>
                <div style="display: flex; align-items: center; background: white; padding: 12px; border-radius: 5px; border: 2px solid #ffc107;">
                    <span style="color: #666; font-size: 0.9rem; white-space: nowrap;">luxestarspower.com/boutique/</span>
                    <input type="text" name="shop_slug" id="shop_slug" readonly
                           style="flex: 1; border: none; background: transparent; padding: 0 5px; font-weight: 600; color: #667eea; outline: none;">
                </div>
                <small style="color: #856404; font-size: 0.85rem;">
                    <?php echo __('shop_url_hint'); ?>
                </small>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #856404;">
                    <?php echo __('shop_description'); ?>
                </label>
                <textarea name="shop_description" rows="3"
                          placeholder="<?php echo __('shop_description_placeholder'); ?>"
                          style="width: 100%; padding: 12px; border: 2px solid #ffc107; border-radius: 5px; font-size: 1rem; background: white; resize: vertical;"></textarea>
            </div>
        </div>
        
        <!-- Bouton -->
        <button type="submit" class="btn btn-primary" 
                style="width: 100%; padding: 14px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 5px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: transform 0.2s;">
            <?php echo __('sign_up'); ?>
        </button>
    </form>
    
    <p style="text-align: center; margin-top: 25px; color: #666;">
        <?php echo __('already_have_account'); ?> 
        <a href="/connexion" style="color: #667eea; text-decoration: none; font-weight: 600;">
            <?php echo __('sign_in'); ?>
        </a>
    </p>
</div>

<style>
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}
</style>

<script>
function generateSlug() {
    const shopName = document.getElementById('shop_name').value;
    const slug = shopName
        .toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // Enlève les accents
        .trim()
        .replace(/[^a-z0-9\s-]/g, '') // Enlève les caractères spéciaux
        .replace(/\s+/g, '-')          // Remplace espaces par -
        .replace(/-+/g, '-')           // Enlève les - multiples
        .replace(/^-|-$/g, '');        // Enlève les - au début/fin
    
    document.getElementById('shop_slug').value = slug;
}

// Style automatique pour le bouton radio sélectionné
document.addEventListener('DOMContentLoaded', function() {
    const buyerLabel = document.querySelector('input[value="buyer"]').parentElement;
    buyerLabel.style.borderColor = '#667eea';
    buyerLabel.style.background = '#f0f4ff';
});
</script>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>