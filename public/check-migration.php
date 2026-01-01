<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;

header('Content-Type: text/html; charset=utf-8');

try {
    $db = Database::getInstance();
    
    echo "<h1>✅ Vérification de la migration</h1>";
    echo "<style>body { font-family: monospace; padding: 20px; } table { border-collapse: collapse; } td, th { border: 1px solid #ddd; padding: 8px; }</style>";
    
    // Vérifie les tables
    $tables = ['subscription_plans', 'user_subscriptions', 'subscription_payments', 'subscription_features_usage'];
    
    echo "<h2>📋 Tables créées :</h2>";
    foreach ($tables as $table) {
        $exists = $db->fetchOne("SHOW TABLES LIKE '$table'");
        $status = $exists ? '✅' : '❌';
        echo "<p>$status $table</p>";
    }
    
    // Vérifie les plans
    echo "<h2>💎 Plans d'abonnement :</h2>";
    $plans = $db->fetchAll("SELECT * FROM subscription_plans");
    
    if ($plans) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Nom</th><th>Prix</th><th>Période</th><th>Produits Max</th><th>Commission</th></tr>";
        foreach ($plans as $plan) {
            echo "<tr>";
            echo "<td>{$plan['id']}</td>";
            echo "<td>{$plan['name']}</td>";
            echo "<td>\${$plan['price']}</td>";
            echo "<td>{$plan['billing_period']}</td>";
            echo "<td>" . ($plan['max_products'] == -1 ? 'Illimité' : $plan['max_products']) . "</td>";
            echo "<td>{$plan['commission_rate']}%</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>🎉 Migration réussie !</h2>";
    
} catch (Exception $e) {
    echo "<h1>❌ Erreur</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
