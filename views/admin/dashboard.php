<?php ob_start(); ?>

<div class="container" style="padding: 40px 20px; max-width: 1400px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">🎛️ Panel Administrateur</h1>
    
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2 style="margin-bottom: 20px;">
            Bienvenue, <?php echo ucwords(strtolower(htmlspecialchars($user['name']))); ?> !
        </h2>
        <p style="color: #666;">
            <strong>Rôle :</strong> 
            <span style="color: #e74c3c; font-weight: 600;">👑 Administrateur</span>
        </p>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px;">
            <h3 style="margin-bottom: 10px; font-size: 2.5rem;"><?php echo $stats['total_users']; ?></h3>
            <p style="opacity: 0.9;">Utilisateurs totaux</p>
        </div>
        
        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px; border-radius: 10px;">
            <h3 style="margin-bottom: 10px; font-size: 2.5rem;"><?php echo $stats['total_sellers']; ?></h3>
            <p style="opacity: 0.9;">Vendeurs actifs</p>
        </div>
        
        <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 30px; border-radius: 10px;">
            <h3 style="margin-bottom: 10px; font-size: 2.5rem;"><?php echo $stats['total_products']; ?></h3>
            <p style="opacity: 0.9;">Produits en ligne</p>
        </div>
        
        <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 30px; border-radius: 10px;">
            <h3 style="margin-bottom: 10px; font-size: 2.5rem;"><?php echo $stats['total_orders']; ?></h3>
            <p style="opacity: 0.9;">Commandes totales</p>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <a href="/admin/utilisateurs" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">👥 Utilisateurs</h3>
            <p style="color: #666;">Gérer les comptes utilisateurs</p>
        </a>
        
        <a href="/admin/produits" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">📦 Produits</h3>
            <p style="color: #666;">Modérer les produits</p>
        </a>
        
        <a href="/admin/commandes" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">🛒 Commandes</h3>
            <p style="color: #666;">Voir toutes les commandes</p>
        </a>
        
        <a href="/admin/paiements" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">💰 Paiements vendeurs</h3>
            <p style="color: #666;">Gérer les paiements</p>
        </a>
        
        <a href="/admin/categories" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">🏷️ Catégories</h3>
            <p style="color: #666;">Gérer les catégories</p>
        </a>
        
        <a href="/admin/avis" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">⭐ Avis</h3>
            <p style="color: #666;">Modérer les avis</p>
        </a>
        
        <a href="/admin/parametres" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">⚙️ Paramètres</h3>
            <p style="color: #666;">Configuration du site</p>
        </a>
        
        <a href="/admin/rapports" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-decoration: none; color: #333;">
            <h3 style="margin-bottom: 10px;">📊 Rapports</h3>
            <p style="color: #666;">Statistiques détaillées</p>
        </a>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>