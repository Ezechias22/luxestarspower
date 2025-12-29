<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 800px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">✏️ Modifier le produit</h1>

    <?php if(isset($_SESSION['error'])): ?>
        <div style="background: #ffebee; border: 1px solid #f44336; color: #c62828; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            ❌ <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <form method="POST" action="/vendeur/produits/<?php echo $product['id']; ?>/modifier" enctype="multipart/form-data">

            <!-- Informations de base -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                    Titre du produit *
                </label>
                <input type="text" name="title" required
                       value="<?php echo htmlspecialchars($product['title']); ?>"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                    Description *
                </label>
                <textarea name="description" required rows="6"
                          style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                        Prix ($) *
                    </label>
                    <input type="number" name="price" id="main-price" step="0.01" min="0" required
                           value="<?php echo htmlspecialchars($product['price']); ?>"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                        Type de produit *
                    </label>
                    <select name="type" required
                            style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;">
                        <option value="ebook" <?php echo $product['type'] === 'ebook' ? 'selected' : ''; ?>>
                            📚 Ebook
                        </option>
                        <option value="video" <?php echo $product['type'] === 'video' ? 'selected' : ''; ?>>
                            🎥 Vidéo
                        </option>
                        <option value="image" <?php echo $product['type'] === 'image' ? 'selected' : ''; ?>>
                            🖼️ Image
                        </option>
                        <option value="course" <?php echo $product['type'] === 'course' ? 'selected' : ''; ?>>
                            🎓 Formation
                        </option>
                        <option value="file" <?php echo $product['type'] === 'file' ? 'selected' : ''; ?>>
                            📁 Fichier
                        </option>
                    </select>
                </div>
            </div>

            <!-- SECTION PROMOTION -->
            <div style="background: #fff3cd; padding: 20px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #ffc107;">
                <h3 style="margin-bottom: 15px; color: #856404;">🎯 Promotion (Optionnel)</h3>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="is_on_sale" id="is_on_sale" value="1"
                               <?php echo (!empty($product['is_on_sale']) ? 'checked' : ''); ?>
                               onchange="togglePromoFields()"
                               style="margin-right: 10px; width: 20px; height: 20px; cursor: pointer;">
                        <span style="font-weight: 600; color: #333;">
                            🔥 Mettre ce produit en promotion
                        </span>
                    </label>
                </div>
                
                <div id="promo-fields" style="display: <?php echo (!empty($product['is_on_sale']) ? 'block' : 'none'); ?>;">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                                Prix d'origine ($)
                            </label>
                            <input type="number" name="original_price" id="original_price" step="0.01" min="0"
                                   value="<?php echo htmlspecialchars($product['original_price'] ?? $product['price'] ?? ''); ?>"
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
                                   min="0" max="99"
                                   value="<?php echo htmlspecialchars($product['discount_percentage'] ?? '0'); ?>"
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
                            $<?php 
                                if (!empty($product['original_price']) && !empty($product['discount_percentage'])) {
                                    $salePrice = $product['original_price'] * (1 - $product['discount_percentage'] / 100);
                                    echo number_format($salePrice, 2);
                                } else {
                                    echo '0.00';
                                }
                            ?>
                        </span>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                                📅 Date de début (Optionnel)
                            </label>
                            <input type="datetime-local" name="sale_starts_at"
                                   value="<?php 
                                       if (!empty($product['sale_starts_at'])) {
                                           echo date('Y-m-d\TH:i', strtotime($product['sale_starts_at']));
                                       }
                                   ?>"
                                   style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                                📅 Date de fin (Optionnel)
                            </label>
                            <input type="datetime-local" name="sale_ends_at"
                                   value="<?php 
                                       if (!empty($product['sale_ends_at'])) {
                                           echo date('Y-m-d\TH:i', strtotime($product['sale_ends_at']));
                                       }
                                   ?>"
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
                    <input type="number" name="sales_goal" min="1"
                           value="<?php echo htmlspecialchars($product['sales_goal'] ?? '100'); ?>"
                           placeholder="100"
                           style="width: 100%; max-width: 300px; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px;">
                    <small style="color: #666; display: block; margin-top: 5px;">
                        📈 Une barre de progression s'affichera pour montrer vos ventes actuelles par rapport à cet objectif
                    </small>
                </div>
                
                <?php if (!empty($product['sales'])): ?>
                    <div style="margin-top: 15px; padding: 15px; background: white; border-radius: 8px;">
                        <strong style="color: #0d47a1;">Progression actuelle :</strong>
                        <div style="margin-top: 10px;">
                            <?php 
                                $currentSales = $product['sales'] ?? 0;
                                $goal = $product['sales_goal'] ?? 100;
                                $percentage = min(100, ($currentSales / $goal) * 100);
                            ?>
                            <div style="background: #e0e0e0; height: 30px; border-radius: 15px; overflow: hidden; position: relative;">
                                <div style="background: linear-gradient(90deg, #2196F3, #21CBF3); height: 100%; width: <?php echo $percentage; ?>%; transition: width 0.3s;"></div>
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; color: #333; font-size: 0.9rem;">
                                    <?php echo $currentSales; ?> / <?php echo $goal; ?> (<?php echo round($percentage); ?>%)
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Image de présentation -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                    Image de présentation
                </label>

                <?php if(!empty($product['thumbnail_path'])): ?>
                    <div style="margin-bottom: 15px;">
                        <img src="<?php echo htmlspecialchars($product['thumbnail_path']); ?>"
                             alt="Current thumbnail"
                             style="max-width: 200px; border-radius: 8px; border: 2px solid #ddd;">
                        <p style="color: #666; font-size: 0.875rem; margin-top: 8px;">
                            Image actuelle
                        </p>
                    </div>
                <?php endif; ?>

                <input type="file" name="thumbnail" accept="image/*"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                <small style="color: #666; font-size: 0.875rem;">
                    Laissez vide pour conserver l'image actuelle
                </small>
            </div>

            <!-- Affichage du fichier principal actuel -->
            <div style="margin-bottom: 20px; background: #f0f8ff; padding: 20px; border-radius: 8px; border: 2px solid #2196f3;">
                <label style="display: block; margin-bottom: 12px; font-weight: 600; font-size: 1.1rem;">
                    📦 Fichier du produit actuel
                </label>

                <?php if(!empty($product['file_storage_path'])): ?>
                    <div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="font-size: 2.5rem;">
                                <?php
                                $typeEmojis = [
                                    'ebook' => '📚',
                                    'video' => '🎥',
                                    'image' => '🖼️',
                                    'course' => '🎓',
                                    'file' => '📁'
                                ];
                                echo $typeEmojis[$product['type']] ?? '📄';
                                ?>
                            </div>
                            <div style="flex: 1;">
                                <p style="margin: 0 0 5px 0; font-weight: 600; color: #333;">
                                    Fichier <?php echo ucfirst($product['type']); ?> hébergé
                                </p>
                                <p style="margin: 0; font-size: 0.875rem; color: #666;">
                                    Type: <?php echo strtoupper(pathinfo($product['file_storage_path'], PATHINFO_EXTENSION)); ?>
                                </p>
                            </div>
                            <div>
                                <a href="/telecharger/produit/<?php echo $product['id']; ?>" 
                                   target="_blank" 
                                   class="btn btn-primary" 
                                   style="padding: 10px 20px; white-space: nowrap;">
                                    📥 Télécharger
                                </a>
                            </div>
                        </div>
                    </div>
                    <p style="color: #1976d2; font-size: 0.875rem; margin: 0;">
                        ✅ Ce produit a un fichier actif. Les acheteurs pourront le télécharger après paiement.
                    </p>
                <?php else: ?>
                    <div style="background: #fff3cd; padding: 15px; border-radius: 8px; border: 1px solid #ffc107;">
                        <p style="margin: 0; color: #856404;">
                            ⚠️ Aucun fichier n'est actuellement associé à ce produit.
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Options -->
            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1"
                           <?php echo $product['is_active'] ? 'checked' : ''; ?>
                           style="width: 20px; height: 20px; cursor: pointer;">
                    <span style="font-weight: 600;">Produit actif</span>
                </label>
            </div>

            <div style="margin-bottom: 20px; background: #fff3cd; padding: 20px; border-radius: 8px; border: 2px solid #ffc107;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 10px;">
                    <input type="checkbox" name="is_featured" value="1"
                           <?php echo !empty($product['is_featured']) ? 'checked' : ''; ?>
                           style="width: 20px; height: 20px; cursor: pointer;">
                    <span style="font-weight: 600; font-size: 1.1rem;">🌟 Produit en vedette</span>
                </label>
                <p style="color: #856404; font-size: 0.9rem; margin: 0;">
                    Les produits en vedette apparaissent en priorité sur la page d'accueil.
                </p>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    💾 Enregistrer les modifications
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

// Calcule automatiquement au chargement
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('is_on_sale').checked) {
        calculateSalePrice();
    }
});
</script>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../../layout.php'; ?>