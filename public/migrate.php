<?php
echo "<h1>Migration de la base de données</h1>";
echo "<pre>";

require_once __DIR__ . '/../migrations/001_initial_schema.php';

echo "</pre>";
echo "<p><strong>✅ Migration terminée !</strong></p>";
echo '<p><a href="/">← Retour à l\'accueil</a></p>';