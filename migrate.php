<?php

/**
 * Database Migration Script
 * Usage: php scripts/migrate.php [up|down|status]
 */

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Database connection
$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
    $_ENV['DB_HOST'],
    $_ENV['DB_PORT'],
    $_ENV['DB_DATABASE']
);

try {
    $pdo = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "\n");
}

// Create migrations tracking table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        batch INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$command = $argv[1] ?? 'status';

switch ($command) {
    case 'up':
        runMigrations($pdo);
        break;
    case 'down':
        rollbackMigrations($pdo);
        break;
    case 'status':
        showStatus($pdo);
        break;
    default:
        echo "Usage: php migrate.php [up|down|status]\n";
}

function runMigrations($pdo)
{
    $migrationsPath = __DIR__ . '/../migrations';
    $files = glob($migrationsPath . '/*.php');
    sort($files);
    
    // Get current batch number
    $stmt = $pdo->query("SELECT MAX(batch) as batch FROM migrations");
    $result = $stmt->fetch();
    $batch = ($result['batch'] ?? 0) + 1;
    
    $ran = 0;
    
    foreach ($files as $file) {
        $migrationName = basename($file);
        
        // Check if already ran
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM migrations WHERE migration = ?");
        $stmt->execute([$migrationName]);
        if ($stmt->fetch()['count'] > 0) {
            continue;
        }
        
        echo "Running: {$migrationName}\n";
        
        // Backup before migration
        backup($pdo, "before_migration_{$migrationName}");
        
        try {
            require_once $file;
            $className = getMigrationClassName($file);
            
            if (class_exists($className)) {
                $migration = new $className($pdo);
                $migration->up();
                
                // Record migration
                $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
                $stmt->execute([$migrationName, $batch]);
                
                echo "✓ Migrated: {$migrationName}\n";
                $ran++;
            }
        } catch (Exception $e) {
            echo "✗ Failed: {$migrationName}\n";
            echo "Error: " . $e->getMessage() . "\n";
            echo "Rolling back...\n";
            // Restore from backup
            restore($pdo, "before_migration_{$migrationName}");
            break;
        }
    }
    
    if ($ran === 0) {
        echo "Nothing to migrate.\n";
    } else {
        echo "\nMigrated {$ran} file(s).\n";
    }
}

function rollbackMigrations($pdo)
{
    // Get last batch
    $stmt = $pdo->query("SELECT MAX(batch) as batch FROM migrations");
    $result = $stmt->fetch();
    $lastBatch = $result['batch'] ?? 0;
    
    if ($lastBatch === 0) {
        echo "Nothing to rollback.\n";
        return;
    }
    
    // Get migrations from last batch
    $stmt = $pdo->prepare("SELECT migration FROM migrations WHERE batch = ? ORDER BY id DESC");
    $stmt->execute([$lastBatch]);
    $migrations = $stmt->fetchAll();
    
    $migrationsPath = __DIR__ . '/../migrations';
    
    foreach ($migrations as $migration) {
        $file = $migrationsPath . '/' . $migration['migration'];
        
        if (!file_exists($file)) {
            echo "Migration file not found: {$migration['migration']}\n";
            continue;
        }
        
        echo "Rolling back: {$migration['migration']}\n";
        
        try {
            require_once $file;
            $className = getMigrationClassName($file);
            
            if (class_exists($className)) {
                $migrationObj = new $className($pdo);
                $migrationObj->down();
                
                // Remove from migrations table
                $stmt = $pdo->prepare("DELETE FROM migrations WHERE migration = ?");
                $stmt->execute([$migration['migration']]);
                
                echo "✓ Rolled back: {$migration['migration']}\n";
            }
        } catch (Exception $e) {
            echo "✗ Failed to rollback: {$migration['migration']}\n";
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
}

function showStatus($pdo)
{
    $stmt = $pdo->query("SELECT * FROM migrations ORDER BY batch, id");
    $migrations = $stmt->fetchAll();
    
    if (empty($migrations)) {
        echo "No migrations have been run.\n";
        return;
    }
    
    echo "\nMigration Status:\n";
    echo str_repeat('-', 80) . "\n";
    printf("%-50s %-10s %-20s\n", "Migration", "Batch", "Date");
    echo str_repeat('-', 80) . "\n";
    
    foreach ($migrations as $migration) {
        printf(
            "%-50s %-10s %-20s\n",
            $migration['migration'],
            $migration['batch'],
            $migration['created_at']
        );
    }
    
    echo str_repeat('-', 80) . "\n";
    echo "Total: " . count($migrations) . " migration(s)\n";
}

function getMigrationClassName($file)
{
    $content = file_get_contents($file);
    preg_match('/class\s+(\w+)/', $content, $matches);
    return $matches[1] ?? '';
}

function backup($pdo, $name)
{
    $backupPath = __DIR__ . '/../storage/backups';
    if (!is_dir($backupPath)) {
        mkdir($backupPath, 0755, true);
    }
    
    $filename = $backupPath . '/' . $name . '_' . date('Y-m-d_His') . '.sql';
    
    $dbName = $_ENV['DB_DATABASE'];
    $username = $_ENV['DB_USERNAME'];
    $password = $_ENV['DB_PASSWORD'];
    $host = $_ENV['DB_HOST'];
    
    $command = "mysqldump -h {$host} -u {$username} -p{$password} {$dbName} > {$filename} 2>&1";
    exec($command, $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "Backup created: {$filename}\n";
    }
}

function restore($pdo, $name)
{
    $backupPath = __DIR__ . '/../storage/backups';
    $files = glob($backupPath . '/' . $name . '*.sql');
    
    if (empty($files)) {
        echo "No backup found for: {$name}\n";
        return;
    }
    
    rsort($files); // Get most recent
    $filename = $files[0];
    
    $dbName = $_ENV['DB_DATABASE'];
    $username = $_ENV['DB_USERNAME'];
    $password = $_ENV['DB_PASSWORD'];
    $host = $_ENV['DB_HOST'];
    
    $command = "mysql -h {$host} -u {$username} -p{$password} {$dbName} < {$filename} 2>&1";
    exec($command, $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "Database restored from: {$filename}\n";
    } else {
        echo "Failed to restore from backup\n";
    }
}
