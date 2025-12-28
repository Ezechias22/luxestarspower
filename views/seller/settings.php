<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
    
    <!-- En-tête -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">⚙️ Paramètres du compte</h1>
        <a href="/vendeur/tableau-de-bord" class="btn" style="background: #667eea; text-decoration: none;">
            ← Retour au tableau de bord
        </a>
    </div>

    <!-- Messages flash -->
    <?php if (isset($_SESSION['success'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            ✅ <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #dc3545;">
            ❌ <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Tabs Navigation -->
    <div style="background: white; border-radius: 10px 10px 0 0; padding: 0; margin-bottom: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div style="display: flex; gap: 0; border-bottom: 2px solid #e0e0e0; overflow-x: auto;">
            <button onclick="showTab('profile')" id="tab-profile" class="tab-btn" style="flex: 1; padding: 15px 20px; background: white; border: none; cursor: pointer; font-weight: 600; border-bottom: 3px solid #667eea; color: #667eea; min-width: 150px;">
                👤 Mon Profil
            </button>
            <button onclick="showTab('shop')" id="tab-shop" class="tab-btn" style="flex: 1; padding: 15px 20px; background: white; border: none; cursor: pointer; font-weight: 600; border-bottom: 3px solid transparent; color: #666; min-width: 150px;">
                🏪 Ma Boutique
            </button>
            <button onclick="showTab('social')" id="tab-social" class="tab-btn" style="flex: 1; padding: 15px 20px; background: white; border: none; cursor: pointer; font-weight: 600; border-bottom: 3px solid transparent; color: #666; min-width: 150px;">
                🌐 Réseaux Sociaux
            </button>
            <button onclick="showTab('password')" id="tab-password" class="tab-btn" style="flex: 1; padding: 15px 20px; background: white; border: none; cursor: pointer; font-weight: 600; border-bottom: 3px solid transparent; color: #666; min-width: 150px;">
                🔒 Mot de passe
            </button>
        </div>
    </div>

    <!-- Tab Content Container -->
    <div style="background: white; border-radius: 0 0 10px 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        
        <!-- TAB 1: Profil -->
        <div id="content-profile" class="tab-content">
            <h2 style="margin-bottom: 20px; color: #333;">👤 Informations personnelles</h2>
            
            <form method="POST" action="/vendeur/parametres/profil" style="max-width: 600px;">
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        Nom complet *
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        value="<?php echo htmlspecialchars($user['name']); ?>"
                        required
                        style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border 0.3s;"
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#e0e0e0'"
                    >
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        Email *
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($user['email']); ?>"
                        required
                        style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border 0.3s;"
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#e0e0e0'"
                    >
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        Bio / Description
                    </label>
                    <textarea 
                        name="bio" 
                        rows="4"
                        style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; resize: vertical; transition: border 0.3s;"
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#e0e0e0'"
                        placeholder="Parlez-nous de vous..."
                    ><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>

                <button 
                    type="submit" 
                    class="btn btn-primary"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem; transition: transform 0.2s;"
                    onmouseover="this.style.transform='translateY(-2px)'"
                    onmouseout="this.style.transform='translateY(0)'"
                >
                    💾 Enregistrer les modifications
                </button>
            </form>
        </div>

        <!-- TAB 2: Boutique -->
        <div id="content-shop" class="tab-content" style="display: none;">
            <h2 style="margin-bottom: 20px; color: #333;">🏪 Informations de la boutique</h2>
            
            <form method="POST" action="/vendeur/parametres/boutique" style="max-width: 600px;">
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        Nom de la boutique *
                    </label>
                    <input 
                        type="text" 
                        name="shop_name" 
                        value="<?php echo htmlspecialchars($user['shop_name'] ?? $user['store_name'] ?? ''); ?>"
                        required
                        style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border 0.3s;"
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#e0e0e0'"
                    >
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        URL de la boutique *
                    </label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="color: #666; font-weight: 500;">luxestarspower.com/boutique/</span>
                        <input 
                            type="text" 
                            name="shop_slug" 
                            value="<?php echo htmlspecialchars($user['shop_slug'] ?? $user['store_slug'] ?? ''); ?>"
                            required
                            pattern="[a-z0-9-]+"
                            style="flex: 1; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border 0.3s;"
                            onfocus="this.style.borderColor='#667eea'"
                            onblur="this.style.borderColor='#e0e0e0'"
                        >
                    </div>
                    <small style="color: #666; display: block; margin-top: 5px;">
                        ⚠️ Uniquement lettres minuscules, chiffres et tirets
                    </small>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        Description de la boutique
                    </label>
                    <textarea 
                        name="shop_description" 
                        rows="5"
                        style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; resize: vertical; transition: border 0.3s;"
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#e0e0e0'"
                        placeholder="Décrivez votre boutique et vos produits..."
                    ><?php echo htmlspecialchars($user['shop_description'] ?? $user['store_description'] ?? ''); ?></textarea>
                </div>

                <!-- Lien vers la boutique -->
                <?php if (!empty($user['shop_slug']) || !empty($user['store_slug'])): ?>
                    <?php $slug = $user['shop_slug'] ?? $user['store_slug']; ?>
                    <div style="background: #f0f7ff; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #667eea;">
                        <strong style="color: #667eea;">🔗 Lien de votre boutique :</strong><br>
                        <a href="/boutique/<?php echo htmlspecialchars($slug); ?>" target="_blank" style="color: #667eea; font-weight: 600; text-decoration: none;">
                            https://luxestarspower.com/boutique/<?php echo htmlspecialchars($slug); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <button 
                    type="submit" 
                    class="btn btn-primary"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem; transition: transform 0.2s;"
                    onmouseover="this.style.transform='translateY(-2px)'"
                    onmouseout="this.style.transform='translateY(0)'"
                >
                    💾 Enregistrer les modifications
                </button>
            </form>
        </div>

        <!-- TAB 3: Réseaux Sociaux -->
        <div id="content-social" class="tab-content" style="display: none;">
            <h2 style="margin-bottom: 20px; color: #333;">🌐 Liens des réseaux sociaux</h2>
            
            <p style="color: #666; margin-bottom: 30px;">
                Ces liens apparaîtront dans la barre de navigation de votre boutique pour que vos clients puissent vous suivre facilement.
            </p>
            
            <form method="POST" action="/vendeur/parametres/reseaux-sociaux" style="max-width: 600px;">
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        📘 Facebook
                    </label>
                    <input 
                        type="url" 
                        name="facebook_url" 
                        value="<?php echo htmlspecialchars($user['facebook_url'] ?? ''); ?>"
                        placeholder="https://facebook.com/votre-page"
                        style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border 0.3s;"
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#e0e0e0'"
                    >
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        🐦 Twitter / X
                    </label>
                    <input 
                        type="url" 
                        name="twitter_url" 
                        value="<?php echo htmlspecialchars($user['twitter_url'] ?? ''); ?>"
                        placeholder="https://twitter.com/votre-compte"
                        style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border 0.3s;"
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#e0e0e0'"
                    >
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        📸 Instagram
                    </label>
                    <input 
                        type="url" 
                        name="instagram_url" 
                        value="<?php echo htmlspecialchars($user['instagram_url'] ?? ''); ?>"
                        placeholder="https://instagram.com/votre-compte"
                        style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border 0.3s;"
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#e0e0e0'"
                    >
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        💼 LinkedIn
                    </label>
                    <input 
                        type="url" 
                        name="linkedin_url" 
                        value="<?php echo htmlspecialchars($user['linkedin_url'] ?? ''); ?>"
                        placeholder="https://linkedin.com/in/votre-profil"
                        style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border 0.3s;"
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#e0e0e0'"
                    >
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        📹 YouTube
                    </label>
                    <input 
                        type="url" 
                        name="youtube_url" 
                        value="<?php echo htmlspecialchars($user['youtube_url'] ?? ''); ?>"
                        placeholder="https://youtube.com/@votre-chaine"
                        style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border 0.3s;"
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#e0e0e0'"
                    >
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        🎵 TikTok
                    </label>
                    <input 
                        type="url" 
                        name="tiktok_url" 
                        value="<?php echo htmlspecialchars($user['tiktok_url'] ?? ''); ?>"
                        placeholder="https://tiktok.com/@votre-compte"
                        style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border 0.3s;"
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#e0e0e0'"
                    >
                </div>

                <button 
                    type="submit" 
                    class="btn btn-primary"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem; transition: transform 0.2s;"
                    onmouseover="this.style.transform='translateY(-2px)'"
                    onmouseout="this.style.transform='translateY(0)'"
                >
                    💾 Enregistrer les liens
                </button>
            </form>
        </div>

        <!-- TAB 4: Mot de passe -->
        <div id="content-password" class="tab-content" style="display: none;">
            <h2 style="margin-bottom: 20px; color: #333;">🔒 Changer le mot de passe</h2>
            
            <form method="POST" action="/vendeur/parametres/mot-de-passe" style="max-width: 600px;">
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        Mot de passe actuel *
                    </label>
                    <input 
                        type="password" 
                        name="current_password" 
                        required
                        style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border 0.3s;"
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#e0e0e0'"
                    >
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        Nouveau mot de passe *
                    </label>
                    <input 
                        type="password" 
                        name="new_password" 
                        required
                        minlength="8"
                        style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border 0.3s;"
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#e0e0e0'"
                    >
                    <small style="color: #666; display: block; margin-top: 5px;">
                        Minimum 8 caractères
                    </small>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                        Confirmer le nouveau mot de passe *
                    </label>
                    <input 
                        type="password" 
                        name="confirm_password" 
                        required
                        minlength="8"
                        style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border 0.3s;"
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#e0e0e0'"
                    >
                </div>

                <button 
                    type="submit" 
                    class="btn btn-primary"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 1rem; transition: transform 0.2s;"
                    onmouseover="this.style.transform='translateY(-2px)'"
                    onmouseout="this.style.transform='translateY(0)'"
                >
                    🔐 Changer le mot de passe
                </button>
            </form>
        </div>

    </div>
</div>

<script>
function showTab(tabName) {
    // Cache tous les contenus
    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.display = 'none';
    });
    
    // Retire le style actif de tous les boutons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.style.borderBottom = '3px solid transparent';
        btn.style.color = '#666';
    });
    
    // Affiche le contenu sélectionné
    document.getElementById('content-' + tabName).style.display = 'block';
    
    // Active le bouton sélectionné
    const activeBtn = document.getElementById('tab-' + tabName);
    activeBtn.style.borderBottom = '3px solid #667eea';
    activeBtn.style.color = '#667eea';
}
</script>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>