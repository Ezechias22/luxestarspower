<?php
// Script one-time pour créer les dossiers uploads

$dirs = [
    __DIR__ . '/public/uploads',
    __DIR__ . '/public/uploads/products',
    __DIR__ . '/public/uploads/thumbnails',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✅ Créé : $dir\n";
        } else {
            echo "❌ Erreur création : $dir\n";
        }
    } else {
        echo "✓ Existe déjà : $dir\n";
    }
}

echo "\n✅ Tous les dossiers sont prêts !\n";