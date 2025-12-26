<?php
/**
 * 🚀 MIGRATION AUTOMATIQUE - LUXE STARS POWER
 * 
 * URL: https://luxestarspower.com/run-migration.php?secret=luxestar2025
 * 
 * ⚠️ IMPORTANT: Supprimer ce fichier après utilisation !
 */

// ========== SÉCURITÉ ==========
$SECRET_KEY = 'luxestar2025';

if (!isset($_GET['secret']) || $_GET['secret'] !== $SECRET_KEY) {
    http_response_code(403);
    die('🔒 Accès refusé - Secret invalide');
}

// ========== CONFIGURATION ==========
header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migration Database - Luxe Stars Power</title>
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
        h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .log-box {
            background: #1a1a1a;
            border-radius: 10px;
            padding: 20px;
            font-size: 14px;
            line-height: 1.6;
            max-height: 600px;
            overflow-y: auto;
            margin-bottom: 20px;
        }
        .success { color: #4caf50; }
        .error { color: #f44336; }
        .warning { color: #ff9800; }
        .info { color: #2196f3; }
        .query {
            background: rgba(255,255,255,0.05);
            padding: 10px;
            margin: 10px 0;
            border-left: 3px solid #667eea;
            border-radius: 5px;
        }
        .result {
            background: rgba(76, 175, 80, 0.1);
            padding: 10px;
            margin: 10px 0;
            border-left: 3px solid #4caf50;
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
        .summary-item:last-child { border-bottom: none; }
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
            text-align: center;
            text-decoration: none;
        }
        .btn-delete:hover { background: #d32f2f; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 MIGRATION DATABASE</h1>
        
        <div class="log-box">
<?php

try {
    echo '<div class="info">📊 Connexion à la base de données...</div>';
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    echo '<div class="success">✅ Connexion établie avec succès</div>';
    echo '<hr style="margin: 20px 0; border: 1px solid rgba(255,255,255,0.1);">';
    
    // Fonction pour vérifier si une colonne existe
    function columnExists($pdo, $table, $column) {
        $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
        $stmt->execute([$column]);
        return $stmt->fetch() !== false;
    }
    
    // Fonction pour vérifier si un index existe
    function indexExists($pdo, $table, $index) {
        $stmt = $pdo->prepare("SHOW INDEX FROM `$table` WHERE Key_name = ?");
        $stmt->execute([$index]);
        return $stmt->fetch() !== false;
    }
    
    $success = 0;
    $failed = 0;
    $skipped = 0;
    $total = 0;
    
    // ========== 1. Ajout des colonnes store_* ==========
    $storeColumns = [
        ['name' => 'store_name', 'type' => 'VARCHAR(255)', 'after' => 'shop_description'],
        ['name' => 'store_slug', 'type' => 'VARCHAR(255)', 'after' => 'store_name'],
        ['name' => 'store_description', 'type' => 'TEXT', 'after' => 'store_slug'],
        ['name' => 'store_logo', 'type' => 'VARCHAR(500)', 'after' => 'store_description'],
        ['name' => 'store_banner', 'type' => 'VARCHAR(500)', 'after' => 'store_logo'],
    ];
    
    foreach ($storeColumns as $col) {
        $total++;
        echo '<div class="query">';
        echo "<strong class='info'>[" . $total . "]</strong> Ajout colonne {$col['name']}<br>";
        
        if (columnExists($pdo, 'users', $col['name'])) {
            echo "<span class='warning'>⚠️ Colonne déjà existante (ignorée)</span>";
            $skipped++;
        } else {
            try {
                $sql = "ALTER TABLE users ADD COLUMN {$col['name']} {$col['type']} NULL AFTER {$col['after']}";
                $pdo->exec($sql);
                echo "<span class='success'>✅ Colonne ajoutée avec succès</span>";
                $success++;
            } catch (\PDOException $e) {
                echo "<span class='error'>❌ Erreur: " . htmlspecialchars($e->getMessage()) . "</span>";
                $failed++;
            }
        }
        echo '</div>';
    }
    
    // ========== 2. Synchronisation des données ==========
    $total++;
    echo '<div class="query">';
    echo "<strong class='info'>[" . $total . "]</strong> Synchronisation shop_ → store_<br>";
    
    try {
        $sql = "UPDATE users 
                SET store_name = shop_name,
                    store_slug = shop_slug,
                    store_description = shop_description,
                    store_logo = shop_logo,
                    store_banner = shop_banner
                WHERE role = 'seller' AND shop_slug IS NOT NULL";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $rowCount = $stmt->rowCount();
        echo "<span class='success'>✅ Synchronisé avec succès ($rowCount vendeurs mis à jour)</span>";
        $success++;
    } catch (\PDOException $e) {
        echo "<span class='error'>❌ Erreur: " . htmlspecialchars($e->getMessage()) . "</span>";
        $failed++;
    }
    echo '</div>';
    
    // ========== 3. Ajout des index ==========
    $indexes = [
        ['name' => 'idx_store_slug', 'column' => 'store_slug'],
        ['name' => 'idx_shop_slug', 'column' => 'shop_slug'],
    ];
    
    foreach ($indexes as $idx) {
        $total++;
        echo '<div class="query">';
        echo "<strong class='info'>[" . $total . "]</strong> Ajout index {$idx['name']}<br>";
        
        if (indexExists($pdo, 'users', $idx['name'])) {
            echo "<span class='warning'>⚠️ Index déjà existant (ignoré)</span>";
            $skipped++;
        } else {
            try {
                $sql = "CREATE INDEX {$idx['name']} ON users({$idx['column']})";
                $pdo->exec($sql);
                echo "<span class='success'>✅ Index créé avec succès</span>";
                $success++;
            } catch (\PDOException $e) {
                echo "<span class='warning'>⚠️ Index déjà existant ou erreur mineure (ignoré)</span>";
                $skipped++;
            }
        }
        echo '</div>';
    }
    
    // ========== 4. Ajout colonnes supplémentaires ==========
    $extraColumns = [
        ['name' => 'last_login_at', 'type' => 'TIMESTAMP', 'after' => 'updated_at'],
        ['name' => 'is_suspended', 'type' => 'TINYINT(1) DEFAULT 0', 'after' => 'role'],
        ['name' => 'password_reset_token', 'type' => 'VARCHAR(255)', 'after' => 'password_hash'],
        ['name' => 'password_reset_expires', 'type' => 'TIMESTAMP', 'after' => 'password_reset_token'],
    ];
    
    foreach ($extraColumns as $col) {
        $total++;
        echo '<div class="query">';
        echo "<strong class='info'>[" . $total . "]</strong> Ajout colonne {$col['name']}<br>";
        
        if (columnExists($pdo, 'users', $col['name'])) {
            echo "<span class='warning'>⚠️ Colonne déjà existante (ignorée)</span>";
            $skipped++;
        } else {
            try {
                $sql = "ALTER TABLE users ADD COLUMN {$col['name']} {$col['type']} NULL AFTER {$col['after']}";
                $pdo->exec($sql);
                echo "<span class='success'>✅ Colonne ajoutée avec succès</span>";
                $success++;
            } catch (\PDOException $e) {
                echo "<span class='error'>❌ Erreur: " . htmlspecialchars($e->getMessage()) . "</span>";
                $failed++;
            }
        }
        echo '</div>';
    }
    
    echo '<hr style="margin: 20px 0; border: 1px solid rgba(255,255,255,0.1);">';
    
    // ========== Vérification finale ==========
    echo '<div class="info">🔍 Vérification des vendeurs...</div>';
    
    $sellers = $db->fetchAll(
        "SELECT id, name, email, shop_slug, store_slug 
         FROM users 
         WHERE role = 'seller' 
         LIMIT 10"
    );
    
    if (!empty($sellers)) {
        echo '<div class="result">';
        echo '<strong class="success">✅ Vendeurs trouvés : ' . count($sellers) . '</strong><br><br>';
        
        foreach ($sellers as $seller) {
            echo "👤 <strong>" . htmlspecialchars($seller['name']) . "</strong><br>";
            echo "   📧 " . htmlspecialchars($seller['email']) . "<br>";
            echo "   🏪 shop_slug: <code>" . htmlspecialchars($seller['shop_slug'] ?? 'NULL') . "</code><br>";
            echo "   🏬 store_slug: <code>" . htmlspecialchars($seller['store_slug'] ?? 'NULL') . "</code><br><br>";
        }
        
        echo '</div>';
    } else {
        echo '<div class="warning">⚠️ Aucun vendeur trouvé</div>';
    }
    
    ?>
        </div>
        
        <div class="summary">
            <h2 style="margin-bottom: 20px;">📊 RÉSUMÉ DE LA MIGRATION</h2>
            
            <div class="summary-item">
                <span>✅ Succès</span>
                <strong class="success"><?php echo $success; ?></strong>
            </div>
            
            <div class="summary-item">
                <span>⚠️ Ignorées</span>
                <strong class="warning"><?php echo $skipped; ?></strong>
            </div>
            
            <div class="summary-item">
                <span>❌ Échecs</span>
                <strong class="error"><?php echo $failed; ?></strong>
            </div>
            
            <div class="summary-item">
                <span>📊 Total</span>
                <strong><?php echo $total; ?></strong>
            </div>
        </div>
        
        <?php if ($failed === 0): ?>
            <div style="background: rgba(76, 175, 80, 0.2); padding: 20px; border-radius: 10px; margin-top: 20px; text-align: center;">
                <h2 style="color: #4caf50; margin-bottom: 10px;">🎉 MIGRATION RÉUSSIE !</h2>
                <p>Toutes les colonnes ont été ajoutées avec succès.</p>
            </div>
        <?php else: ?>
            <div style="background: rgba(244, 67, 54, 0.2); padding: 20px; border-radius: 10px; margin-top: 20px; text-align: center;">
                <h2 style="color: #f44336; margin-bottom: 10px;">⚠️ MIGRATION PARTIELLE</h2>
                <p><?php echo $failed; ?> erreur(s) détectée(s). Vérifiez les détails ci-dessus.</p>
            </div>
        <?php endif; ?>
        
        <a href="javascript:if(confirm('⚠️ ATTENTION: Voulez-vous vraiment supprimer ce fichier ?\\n\\nCeci est irréversible !')) { window.location.href = '?secret=<?php echo $SECRET_KEY; ?>&delete=yes'; }" class="btn-delete">
            🗑️ SUPPRIMER CE FICHIER DE MIGRATION
        </a>
        
        <p style="text-align: center; margin-top: 20px; opacity: 0.7; font-size: 12px;">
            ⚠️ Pour des raisons de sécurité, supprimez ce fichier après la migration
        </p>
        
    <?php
    
} catch (\Exception $e) {
    echo '<div class="error">';
    echo '<h3>❌ ERREUR FATALE</h3>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<pre style="background: rgba(0,0,0,0.3); padding: 15px; border-radius: 5px; margin-top: 10px; overflow-x: auto;">';
    echo htmlspecialchars($e->getTraceAsString());
    echo '</pre>';
    echo '</div>';
}

// Gestion de la suppression
if (isset($_GET['delete']) && $_GET['delete'] === 'yes' && $_GET['secret'] === $SECRET_KEY) {
    echo '<hr style="margin: 30px 0; border: 1px solid rgba(255,255,255,0.1);">';
    echo '<div class="info">🗑️ Suppression du fichier...</div>';
    
    if (unlink(__FILE__)) {
        echo '<div class="success">✅ Fichier supprimé avec succès !</div>';
        echo '<p style="text-align: center; margin-top: 20px;">Vous pouvez fermer cette fenêtre.</p>';
    } else {
        echo '<div class="error">❌ Suppression manuelle requise : public/run-migration.php</div>';
    }
}

?>
        </div>
    </div>
</body>
</html>