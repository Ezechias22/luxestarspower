<?php
/**
 * 🚀 MIGRATION AUTOMATIQUE - LUXE STARS POWER
 * 
 * URL: https://luxestarspower.com/run-migration.php?secret=luxestar2025
 * 
 * ⚠️ IMPORTANT: Supprimer ce fichier après utilisation !
 */

// ========== SÉCURITÉ ==========
$SECRET_KEY = 'luxestar2025'; // Change ce secret si tu veux

if (!isset($_GET['secret']) || $_GET['secret'] !== $SECRET_KEY) {
    http_response_code(403);
    die('🔒 Accès refusé - Secret invalide');
}

// ========== CONFIGURATION ==========
header('Content-Type: text/html; charset=utf-8');

// Charge l'autoloader
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
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
        .summary-item:last-child {
            border-bottom: none;
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
            text-align: center;
            text-decoration: none;
        }
        .btn-delete:hover {
            background: #d32f2f;
        }
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
    
    // Liste de toutes les requêtes SQL
    $queries = [
        [
            'name' => 'Ajout colonne store_name',
            'sql' => 'ALTER TABLE users ADD COLUMN IF NOT EXISTS store_name VARCHAR(255) NULL AFTER shop_description'
        ],
        [
            'name' => 'Ajout colonne store_slug',
            'sql' => 'ALTER TABLE users ADD COLUMN IF NOT EXISTS store_slug VARCHAR(255) NULL AFTER store_name'
        ],
        [
            'name' => 'Ajout colonne store_description',
            'sql' => 'ALTER TABLE users ADD COLUMN IF NOT EXISTS store_description TEXT NULL AFTER store_slug'
        ],
        [
            'name' => 'Ajout colonne store_logo',
            'sql' => 'ALTER TABLE users ADD COLUMN IF NOT EXISTS store_logo VARCHAR(500) NULL AFTER store_description'
        ],
        [
            'name' => 'Ajout colonne store_banner',
            'sql' => 'ALTER TABLE users ADD COLUMN IF NOT EXISTS store_banner VARCHAR(500) NULL AFTER store_logo'
        ],
        [
            'name' => 'Synchronisation shop_ -> store_',
            'sql' => "UPDATE users 
                      SET store_name = shop_name,
                          store_slug = shop_slug,
                          store_description = shop_description,
                          store_logo = shop_logo,
                          store_banner = shop_banner
                      WHERE role = 'seller' AND shop_slug IS NOT NULL"
        ],
        [
            'name' => 'Ajout index store_slug',
            'sql' => 'CREATE INDEX IF NOT EXISTS idx_store_slug ON users(store_slug)'
        ],
        [
            'name' => 'Ajout index shop_slug',
            'sql' => 'CREATE INDEX IF NOT EXISTS idx_shop_slug ON users(shop_slug)'
        ],
        [
            'name' => 'Ajout colonne last_login_at',
            'sql' => 'ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login_at TIMESTAMP NULL AFTER updated_at'
        ],
        [
            'name' => 'Ajout colonne is_suspended',
            'sql' => 'ALTER TABLE users ADD COLUMN IF NOT EXISTS is_suspended TINYINT(1) DEFAULT 0 AFTER role'
        ],
        [
            'name' => 'Ajout colonne password_reset_token',
            'sql' => 'ALTER TABLE users ADD COLUMN IF NOT EXISTS password_reset_token VARCHAR(255) NULL AFTER password_hash'
        ],
        [
            'name' => 'Ajout colonne password_reset_expires',
            'sql' => 'ALTER TABLE users ADD COLUMN IF NOT EXISTS password_reset_expires TIMESTAMP NULL AFTER password_reset_token'
        ]
    ];
    
    $success = 0;
    $failed = 0;
    $skipped = 0;
    
    foreach ($queries as $index => $query) {
        $num = $index + 1;
        $total = count($queries);
        
        echo '<div class="query">';
        echo "<strong class='info'>[$num/$total]</strong> {$query['name']}<br>";
        echo "<code style='color: #999; font-size: 12px;'>" . htmlspecialchars(substr($query['sql'], 0, 100)) . "...</code><br>";
        
        try {
            $stmt = $pdo->prepare($query['sql']);
            $stmt->execute();
            
            $rowCount = $stmt->rowCount();
            
            if ($rowCount > 0) {
                echo "<span class='success'>✅ Exécutée avec succès ($rowCount lignes affectées)</span>";
                $success++;
            } else {
                echo "<span class='warning'>⚠️ Exécutée (aucune ligne affectée - probablement déjà appliquée)</span>";
                $skipped++;
            }
            
        } catch (\PDOException $e) {
            $errorMsg = $e->getMessage();
            
            if (strpos($errorMsg, 'Duplicate column') !== false || 
                strpos($errorMsg, 'already exists') !== false ||
                strpos($errorMsg, 'Duplicate key') !== false) {
                echo "<span class='warning'>⚠️ Déjà existant (ignoré)</span>";
                $skipped++;
            } else {
                echo "<span class='error'>❌ Erreur: " . htmlspecialchars($errorMsg) . "</span>";
                $failed++;
            }
        }
        
        echo '</div>';
    }
    
    echo '<hr style="margin: 20px 0; border: 1px solid rgba(255,255,255,0.1);">';
    
    // Vérification finale
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
                <strong><?php echo count($queries); ?></strong>
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
                <p>Certaines requêtes ont échoué. Vérifiez les erreurs ci-dessus.</p>
            </div>
        <?php endif; ?>
        
        <a href="javascript:if(confirm('⚠️ ATTENTION: Voulez-vous vraiment supprimer ce fichier de migration ?\\n\\nCeci est irréversible !')) { window.location.href = '?secret=<?php echo $SECRET_KEY; ?>&delete=yes'; }" class="btn-delete">
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

// Gestion de la suppression du fichier
if (isset($_GET['delete']) && $_GET['delete'] === 'yes' && $_GET['secret'] === $SECRET_KEY) {
    echo '<hr style="margin: 30px 0; border: 1px solid rgba(255,255,255,0.1);">';
    echo '<div class="info">🗑️ Suppression du fichier de migration...</div>';
    
    if (unlink(__FILE__)) {
        echo '<div class="success">✅ Fichier supprimé avec succès !</div>';
        echo '<p style="text-align: center; margin-top: 20px;">Vous pouvez fermer cette fenêtre.</p>';
    } else {
        echo '<div class="error">❌ Impossible de supprimer le fichier automatiquement.</div>';
        echo '<p>Veuillez supprimer manuellement le fichier : <code>public/run-migration.php</code></p>';
    }
}

?>
        </div>
    </div>
</body>
</html>