<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 800px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">➕ Ajouter un Produit</h1>

    <?php if(isset($_SESSION['flash_error'])): ?>
        <div style="background: #ffebee; border: 1px solid #f44336; color: #c62828; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            ❌ <?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <!-- ========== ALERTE LIMITE DE PRODUITS ========== -->
    <?php 
    $isUnlimited = isset($maxProducts) && $maxProducts == -1;
    $percentage = $isUnlimited ? 0 : (isset($maxProducts) && $maxProducts > 0 ? ($productsCount / $maxProducts) * 100 : 0);
    ?>
    
    <?php if (isset($subscription) && !$isUnlimited): ?>
        <?php if ($percentage >= 80): ?>
        <div style="background: <?php echo $percentage >= 100 ? '#ffebee' : '#fff3cd'; ?>; border: 2px solid <?php echo $percentage >= 100 ? '#f44336' : '#ffc107'; ?>; color: <?php echo $percentage >= 100 ? '#c62828' : '#856404'; ?>; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                <span style="font-size: 2.5rem;"><?php echo $percentage >= 100 ? '⚠️' : '⏰'; ?></span>
                <div style="flex: 1;">
                    <h3 style="margin: 0 0 8px;">
                        <?php if ($percentage >= 100): ?>
                            Limite de produits atteinte !
                        <?php else: ?>
                            Attention : Limite bientôt atteinte
                        <?php endif; ?>
                    </h3>
                    <p style="margin: 0; font-size: 1.1rem;">
                        Vous utilisez <strong><?php echo $productsCount; ?> / <?php echo $maxProducts; ?> produits</strong> 
                        (<?php echo number_format($percentage, 1); ?>%)
                    </p>
                </div>
            </div>
            
            <!-- Barre de progression -->
            <div style="background: rgba(0,0,0,0.1); height: 10px; border-radius: 5px; overflow: hidden; margin-bottom: 15px;">
                <div style="background: <?php echo $percentage >= 100 ? '#f44336' : '#ffc107'; ?>; height: 100%; width: <?php echo min(100, $percentage); ?>%;"></div>
            </div>
            
            <p style="margin: 0 0 15px;">
                <?php if ($percentage >= 100): ?>
                    🚫 Vous ne pouvez plus ajouter de produits. Passez à un plan supérieur pour continuer !
                <?php else: ?>
                    Il vous reste seulement <strong><?php echo $maxProducts - $productsCount; ?> produit(s)</strong> avant d'atteindre la limite.
                <?php endif; ?>
            </p>
            
            <a href="/vendeur/abonnement" style="display: inline-block; background: <?php echo $percentage >= 100 ? '#f44336' : '#ffc107'; ?>; color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                ⬆️ Passer à un plan supérieur
            </a>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <form method="POST" action="/vendeur/produits" enctype="multipart/form-data">
            
            <!-- Informations de base -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                    Titre du produit *
                </label>
                <input type="text" name="title" required 
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                    Description *
                </label>
                <textarea name="description" required rows="5" 
                          style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                        Prix ($) *
                    </label>
                    <input type="number" name="price" id="main-price" required step="0.01" min="0" 
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                        Type de produit *
                    </label>
                    <select name="type" required 
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="">Sélectionnez un type</option>
                        <option value="ebook">📚 Ebook</option>
                        <option value="video">🎥 Vidéo</option>
                        <option value="image">🖼️ Image</option>
                        <option value="course">🎓 Formation</option>
                        <option value="file">📁 Fichier</option>
                    </select>
                </div>
            </div>

            <!-- SECTION PROMOTION -->
            <div style="background: #fff3cd; padding: 20px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #ffc107;">
                <h3 style="margin-bottom: 15px; color: #856404;">🎯 Promotion (Optionnel)</h3>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="is_on_sale" id="is_on_sale" value="1"
                               onchange="togglePromoFields()"
                               style="margin-right: 10px; width: 20px; height: 20px; cursor: pointer;">
                        <span style="font-weight: 600; color: #333;">
                            🔥 Mettre ce produit en promotion
                        </span>
                    </label>
                </div>
                
                <div id="promo-fields" style="display: none;">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                                Prix d'origine ($)
                            </label>
                            <input type="number" name="original_price" id="original_price" step="0.01" min="0"
                                   placeholder="49.99"
                                   oninput="calculateSalePrice()"
                                   style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
                            <small style="color: #666; display: block; margin-top: 5px;">
                                Le prix avant réduction
                            </small>
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                                Réduction (%)
                            </label>
                            <input type="number" name="discount_percentage" id="discount_percentage" 
                                   min="0" max="99" value="0"
                                   placeholder="20"
                                   oninput="calculateSalePrice()"
                                   style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
                            <small style="color: #666; display: block; margin-top: 5px;">
                                Pourcentage de réduction (0-99%)
                            </small>
                        </div>
                    </div>
                    
                    <div style="background: #d4edda; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <strong style="color: #155724;">💰 Prix après réduction : </strong>
                        <span id="sale-price-display" style="font-size: 1.3rem; color: #28a745; font-weight: bold;">
                            $0.00
                        </span>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                                📅 Date de début (Optionnel)
                            </label>
                            <input type="datetime-local" name="sale_starts_at"
                                   style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                                📅 Date de fin (Optionnel)
                            </label>
                            <input type="datetime-local" name="sale_ends_at"
                                   style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
                        </div>
                    </div>
                    
                    <small style="color: #856404; display: block; background: #fff; padding: 10px; border-radius: 5px;">
                        ℹ️ Si vous laissez les dates vides, la promotion sera permanente jusqu'à ce que vous la désactiviez.
                    </small>
                </div>
            </div>

            <!-- SECTION OBJECTIF DE VENTES -->
            <div style="background: #e7f3ff; padding: 20px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #2196F3;">
                <h3 style="margin-bottom: 15px; color: #0d47a1;">📊 Objectif de ventes</h3>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        Nombre de ventes ciblé
                    </label>
                    <input type="number" name="sales_goal" min="1" value="100"
                           placeholder="100"
                           style="width: 100%; max-width: 300px; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
                    <small style="color: #666; display: block; margin-top: 5px;">
                        📈 Une barre de progression s'affichera pour montrer vos ventes actuelles par rapport à cet objectif
                    </small>
                </div>
            </div>

            <!-- Images et fichiers -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                    Image de présentation (optionnel)
                </label>
                <input type="file" name="thumbnail" accept="image/*" 
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                <small style="color: #666; font-size: 0.875rem; display: block; margin-top: 5px;">
                    Une image par défaut sera utilisée si vous n'en uploadez pas.
                </small>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                    Fichier du produit *
                </label>
                <input type="file" name="file" required 
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                <small style="color: #666; font-size: 0.875rem; display: block; margin-top: 5px;">
                    PDF, ZIP, MP4, ou tout autre fichier numérique (Max 500MB)
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

<script>
function togglePromoFields() {
    const isChecked = document.getElementById('is_on_sale').checked;
    document.getElementById('promo-fields').style.display = isChecked ? 'block' : 'none';
}

function calculateSalePrice() {
    const originalPrice = parseFloat(document.getElementById('original_price').value) || 0;
    const discountPercentage = parseFloat(document.getElementById('discount_percentage').value) || 0;
    
    const salePrice = originalPrice * (1 - discountPercentage / 100);
    
    document.getElementById('sale-price-display').textContent = '$' + salePrice.toFixed(2);
    
    // Met à jour le prix principal
    document.getElementById('main-price').value = salePrice.toFixed(2);
}
</script>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>