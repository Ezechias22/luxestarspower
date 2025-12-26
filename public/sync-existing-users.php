<?php
/**
 * 🔄 SYNCHRONISATION DES UTILISATEURS EXISTANTS
 * 
 * URL: https://luxestarspower.com/sync-existing-users.php?secret=luxestar2025
 * 
 * Ce script met à jour tous les vendeurs existants pour synchroniser
 * shop_* avec store_*
 */

// ========== SÉCURITÉ ==========
$SECRET_KEY = 'luxestar2025';

if (!isset($_GET['secret']) || $_GET['secret'] !== $SECRET_KEY) {
    http_response_code(403);
    die('🔒 Accès refusé');
}

header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sync Existing Users - Luxe Stars Power</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: rgba(0,0,0,0.3);
            border-radius: 15px;
            padding: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        h1 { text-align: center; margin-bottom: 30px; font-size: 2rem; }
        .log-box {
            background: #1a1a1a;
            border-radius: 10px;
            padding: 20px;
            font-size: 14px;
            line-height: 1.6;
            max-height: 600px;
            overflow-y: auto;
        }
        .success { color: #4caf50; }
        .error { color: #f44336; }
        .warning { color: #ff9800; }
        .info { color: #2196f3; }
        .user-block {
            background: rgba(255,255,255,0.05);
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #667eea;
            border-radius: 5px;
        }
        .summary {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .btn-delete {
            display: block;
            width: 100%;
            padding: 15px;
            background: #f44336;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            text-decoration: none;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        th {
            background: rgba(255,255,255,0.1);
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 SYNCHRONISATION UTILISATEURS</h1>
        <div class="log-box">
<?php

try {
    echo '<div class="info">📊 Connexion à la base de données...</div>';
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    echo '<div class="success">✅ Connexion établie</div>';
    echo '<hr style="margin: 20px 0; border: 1px solid rgba(255,255,255,0.1);">';
    
    // ========== ÉTAPE 1: Récupérer tous les vendeurs ==========
    echo '<div class="info">🔍 Recherche des vendeurs existants...</div>';
    
    $sellers = $db->fetchAll(
        "SELECT id, name, email, shop_name, shop_slug, shop_description, shop_logo, shop_banner,
                store_name, store_slug, store_description, store_logo, store_banner
         FROM users 
         WHERE role = 'seller'
         ORDER BY created_at DESC"
    );
    
    $totalSellers = count($sellers);
    echo "<div class='success'>✅ $totalSellers vendeur(s) trouvé(s)</div>";
    echo '<hr style="margin: 20px 0; border: 1px solid rgba(255,255,255,0.1);">';
    
    if ($totalSellers === 0) {
        echo '<div class="warning">⚠️ Aucun vendeur à synchroniser</div>';
    } else {
        echo '<div class="info">🔄 Début de la synchronisation...</div><br>';
        
        $updated = 0;
        $alreadySync = 0;
        $failed = 0;
        
        foreach ($sellers as $seller) {
            echo '<div class="user-block">';
            echo '<strong class="info">👤 ' . htmlspecialchars($seller['name']) . '</strong><br>';
            echo '<small style="color: #999;">📧 ' . htmlspecialchars($seller['email']) . '</small><br><br>';
            
            // Vérifie ce qui doit être synchronisé
            $needsUpdate = false;
            $updates = [];
            
            // Synchronise shop_ -> store_
            if (!empty($seller['shop_name']) && empty($seller['store_name'])) {
                $updates['store_name'] = $seller['shop_name'];
                $needsUpdate = true;
            }
            
            if (!empty($seller['shop_slug']) && empty($seller['store_slug'])) {
                $updates['store_slug'] = $seller['shop_slug'];
                $needsUpdate = true;
            }
            
            if (!empty($seller['shop_description']) && empty($seller['store_description'])) {
                $updates['store_description'] = $seller['shop_description'];
                $needsUpdate = true;
            }
            
            if (!empty($seller['shop_logo']) && empty($seller['store_logo'])) {
                $updates['store_logo'] = $seller['shop_logo'];
                $needsUpdate = true;
            }
            
            if (!empty($seller['shop_banner']) && empty($seller['store_banner'])) {
                $updates['store_banner'] = $seller['shop_banner'];
                $needsUpdate = true;
            }
            
            // Synchronise store_ -> shop_ (cas inverse)
            if (!empty($seller['store_name']) && empty($seller['shop_name'])) {
                $updates['shop_name'] = $seller['store_name'];
                $needsUpdate = true;
            }
            
            if (!empty($seller['store_slug']) && empty($seller['shop_slug'])) {
                $updates['shop_slug'] = $seller['store_slug'];
                $needsUpdate = true;
            }
            
            if (!empty($seller['store_description']) && empty($seller['shop_description'])) {
                $updates['shop_description'] = $seller['store_description'];
                $needsUpdate = true;
            }
            
            if (!empty($seller['store_logo']) && empty($seller['shop_logo'])) {
                $updates['shop_logo'] = $seller['store_logo'];
                $needsUpdate = true;
            }
            
            if (!empty($seller['store_banner']) && empty($seller['shop_banner'])) {
                $updates['shop_banner'] = $seller['store_banner'];
                $needsUpdate = true;
            }
            
            if ($needsUpdate && !empty($updates)) {
                echo '<div class="warning">⚠️ Synchronisation nécessaire :</div>';
                echo '<ul style="margin-left: 20px; margin-top: 5px;">';
                
                foreach ($updates as $field => $value) {
                    $displayValue = strlen($value) > 40 ? substr($value, 0, 40) . '...' : $value;
                    echo '<li style="color: #ff9800;">' . htmlspecialchars($field) . ' = ' . htmlspecialchars($displayValue) . '</li>';
                }
                
                echo '</ul>';
                
                try {
                    // Prépare la requête UPDATE
                    $fields = [];
                    $params = [];
                    
                    foreach ($updates as $field => $value) {
                        $fields[] = "$field = ?";
                        $params[] = $value;
                    }
                    
                    $params[] = $seller['id'];
                    
                    $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    
                    echo '<div class="success">✅ Mis à jour avec succès !</div>';
                    $updated++;
                    
                } catch (\PDOException $e) {
                    echo '<div class="error">❌ Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    $failed++;
                }
                
            } else {
                echo '<div class="success">✅ Déjà synchronisé</div>';
                $alreadySync++;
            }
            
            // Affiche l'état actuel
            echo '<div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.1);">';
            echo '<strong style="color: #2196f3;">État actuel :</strong><br>';
            echo '<table style="font-size: 12px; margin-top: 5px;">';
            echo '<tr><th>Champ</th><th>shop_*</th><th>store_*</th></tr>';
            
            $fields = ['name', 'slug', 'description', 'logo', 'banner'];
            foreach ($fields as $field) {
                $shopField = 'shop_' . $field;
                $storeField = 'store_' . $field;
                
                $shopValue = $seller[$shopField] ?? 'NULL';
                $storeValue = $seller[$storeField] ?? 'NULL';
                
                // Tronque si trop long
                if (strlen($shopValue) > 30) $shopValue = substr($shopValue, 0, 30) . '...';
                if (strlen($storeValue) > 30) $storeValue = substr($storeValue, 0, 30) . '...';
                
                $match = ($seller[$shopField] ?? '') === ($seller[$storeField] ?? '');
                $color = $match ? '#4caf50' : '#ff9800';
                
                echo '<tr>';
                echo '<td>' . htmlspecialchars($field) . '</td>';
                echo '<td style="color: ' . $color . ';">' . htmlspecialchars($shopValue) . '</td>';
                echo '<td style="color: ' . $color . ';">' . htmlspecialchars($storeValue) . '</td>';
                echo '</tr>';
            }
            
            echo '</table>';
            echo '</div>';
            
            echo '</div>';
        }
        
        echo '<hr style="margin: 20px 0; border: 1px solid rgba(255,255,255,0.1);">';
        
        // ========== VÉRIFICATION FINALE ==========
        echo '<div class="info">🔍 Vérification finale...</div>';
        
        $verif = $db->fetchAll(
            "SELECT id, name, shop_slug, store_slug 
             FROM users 
             WHERE role = 'seller' 
             LIMIT 10"
        );
        
        if (!empty($verif)) {
            echo '<div class="result" style="background: rgba(76, 175, 80, 0.1); padding: 15px; margin: 15px 0; border-left: 3px solid #4caf50; border-radius: 5px;">';
            echo '<strong class="success">✅ Échantillon de vendeurs après synchronisation :</strong><br><br>';
            
            echo '<table>';
            echo '<tr><th>Nom</th><th>shop_slug</th><th>store_slug</th><th>Status</th></tr>';
            
            foreach ($verif as $v) {
                $match = $v['shop_slug'] === $v['store_slug'];
                $statusIcon = $match ? '✅' : '⚠️';
                $statusColor = $match ? '#4caf50' : '#ff9800';
                
                echo '<tr>';
                echo '<td>' . htmlspecialchars($v['name']) . '</td>';
                echo '<td><code>' . htmlspecialchars($v['shop_slug'] ?? 'NULL') . '</code></td>';
                echo '<td><code>' . htmlspecialchars($v['store_slug'] ?? 'NULL') . '</code></td>';
                echo '<td style="color: ' . $statusColor . ';">' . $statusIcon . '</td>';
                echo '</tr>';
            }
            
            echo '</table>';
            echo '</div>';
        }
    }
    
    ?>
        </div>
        
        <div class="summary">
            <h2 style="margin-bottom: 20px;">📊 RÉSUMÉ</h2>
            
            <div class="summary-item">
                <span>👥 Total vendeurs</span>
                <strong class="info"><?php echo $totalSellers; ?></strong>
            </div>
            
            <div class="summary-item">
                <span>🔄 Mis à jour</span>
                <strong class="success"><?php echo $updated ?? 0; ?></strong>
            </div>
            
            <div class="summary-item">
                <span>✅ Déjà synchronisés</span>
                <strong class="warning"><?php echo $alreadySync ?? 0; ?></strong>
            </div>
            
            <div class="summary-item">
                <span>❌ Échecs</span>
                <strong class="error"><?php echo $failed ?? 0; ?></strong>
            </div>
        </div>
        
        <?php if (($failed ?? 0) === 0): ?>
            <div style="background: rgba(76, 175, 80, 0.2); padding: 20px; border-radius: 10px; margin-top: 20px; text-align: center;">
                <h2 style="color: #4caf50;">🎉 SYNCHRONISATION RÉUSSIE !</h2>
                <p>Tous les vendeurs ont été synchronisés avec succès.</p>
            </div>
        <?php endif; ?>
        
        <a href="javascript:if(confirm('Supprimer ce fichier ?')) { window.location.href = '?secret=<?php echo $SECRET_KEY; ?>&delete=yes'; }" class="btn-delete">
            🗑️ SUPPRIMER CE FICHIER
        </a>
        
    <?php
    
} catch (\Exception $e) {
    echo '<div class="error">';
    echo '<h3>❌ ERREUR FATALE</h3>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<pre style="background: rgba(0,0,0,0.3); padding: 15px; overflow-x: auto;">';
    echo htmlspecialchars($e->getTraceAsString());
    echo '</pre>';
    echo '</div>';
}

if (isset($_GET['delete']) && $_GET['delete'] === 'yes') {
    echo '<hr style="margin: 30px 0;">';
    if (unlink(__FILE__)) {
        echo '<div class="success">✅ Fichier supprimé !</div>';
    } else {
        echo '<div class="error">❌ Suppression manuelle requise</div>';
    }
}

?>
        </div>
    </div>
</body>
</html>