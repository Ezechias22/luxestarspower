#!/usr/bin/env php
<?php
/**
 * Script de création du compte admin initial
 * Usage: php scripts/create_admin.php <email> <name>
 */

require_once __DIR__ . '/../app/Database.php';
require_once __DIR__ . '/../app/Repositories/UserRepository.php';

if ($argc < 3) {
    echo "Usage: php create_admin.php <email> <name>\n";
    echo "Example: php create_admin.php admin@luxestarspower.com \"Admin User\"\n";
    exit(1);
}

$email = $argv[1];
$name = $argv[2];

// Générer un mot de passe aléatoire sécurisé
$password = bin2hex(random_bytes(16));
$passwordHash = password_hash($password, PASSWORD_ARGON2ID);

$userRepo = new \App\Repositories\UserRepository();

// Vérifier si l'email existe déjà
$existing = $userRepo->findByEmail($email);
if ($existing) {
    echo "Error: User with email $email already exists\n";
    exit(1);
}

// Créer l'admin
$user = $userRepo->create([
    'name' => $name,
    'email' => $email,
    'password_hash' => $passwordHash,
    'role' => 'admin',
    'currency' => 'USD'
]);

// Vérifier l'email immédiatement
$userRepo->verifyEmail($user->id);

echo "Admin user created successfully!\n";
echo "===============================================\n";
echo "Email: $email\n";
echo "Temporary Password: $password\n";
echo "===============================================\n";
echo "IMPORTANT: Save this password securely and change it after first login!\n";
echo "Login at: https://luxestarspower.com/admin/login\n";
