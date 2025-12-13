<?php
require __DIR__ . '/../app/Database.php';

use App\Database;

$db = Database::getInstance();

try {
    $db->query("INSERT INTO categories (name, slug, description, icon, display_order) VALUES
        ('Ebooks', 'ebooks', 'Livres numériques et guides', '📚', 1),
        ('Vidéos', 'videos', 'Cours vidéo et tutoriels', '🎥', 2),
        ('Images', 'images', 'Photos et graphiques', '🖼️', 3),
        ('Formations', 'formations', 'Cours et formations complètes', '🎓', 4),
        ('Fichiers', 'fichiers', 'Documents et fichiers divers', '📁', 5)
    ", []);
    
    echo "<h1 style='color: green;'>✅ Catégories insérées avec succès !</h1>";
    echo "<p><a href='/admin/categories'>Voir les catégories</a></p>";
} catch (Exception $e) {
    echo "<h1 style='color: red;'>❌ Erreur : " . $e->getMessage() . "</h1>";
}