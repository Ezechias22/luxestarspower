<?php

/**
 * Create Admin User Script
 * Usage: php scripts/create_admin.php
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

echo "=== Create Admin User ===\n\n";

// Check if admin already exists
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
$adminExists = $stmt->fetch()['count'] > 0;

if ($adminExists) {
    echo "Warning: An admin user already exists.\n";
    echo "Do you want to create another admin? (y/n): ";
    $confirm = trim(fgets(STDIN));
    if (strtolower($confirm) !== 'y') {
        echo "Aborted.\n";
        exit(0);
    }
}

// Get admin details
echo "Enter admin name: ";
$name = trim(fgets(STDIN));

if (empty($name)) {
    die("Error: Name cannot be empty.\n");
}

echo "Enter admin email: ";
$email = trim(fgets(STDIN));

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Error: Invalid email address.\n");
}

// Check if email already exists
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()['count'] > 0) {
    die("Error: Email already exists.\n");
}

echo "Enter admin password (minimum 8 characters): ";
$password = trim(fgets(STDIN));

if (strlen($password) < 8) {
    die("Error: Password must be at least 8 characters.\n");
}

echo "Confirm password: ";
$passwordConfirm = trim(fgets(STDIN));

if ($password !== $passwordConfirm) {
    die("Error: Passwords do not match.\n");
}

// Hash password
$passwordHash = password_hash($password, PASSWORD_ARGON2ID);

// Create admin user
try {
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password_hash, role, is_active, email_verified_at, created_at, updated_at)
        VALUES (?, ?, ?, 'admin', 1, NOW(), NOW(), NOW())
    ");
    
    $stmt->execute([$name, $email, $passwordHash]);
    
    $userId = $pdo->lastInsertId();
    
    echo "\n✓ Admin user created successfully!\n\n";
    echo "User ID: {$userId}\n";
    echo "Name: {$name}\n";
    echo "Email: {$email}\n";
    echo "Role: admin\n\n";
    
    echo "You can now login at: " . $_ENV['APP_URL'] . "/admin\n";
    
    // Log activity
    $stmt = $pdo->prepare("
        INSERT INTO activity_logs (user_id, action_type, ip_address, created_at)
        VALUES (?, 'admin_created', '127.0.0.1', NOW())
    ");
    $stmt->execute([$userId]);
    
    // Optional: Send welcome email
    echo "\nSend welcome email? (y/n): ";
    $sendEmail = trim(fgets(STDIN));
    
    if (strtolower($sendEmail) === 'y') {
        // Load mail service
        require_once __DIR__ . '/../app/Services/MailService.php';
        
        $mailService = new \App\Services\MailService();
        $sent = $mailService->send(
            $email,
            "Welcome to " . $_ENV['APP_NAME'],
            'emails/welcome-admin',
            ['name' => $name, 'email' => $email]
        );
        
        if ($sent) {
            echo "✓ Welcome email sent.\n";
        } else {
            echo "✗ Failed to send email.\n";
        }
    }
    
} catch (PDOException $e) {
    die("Error creating admin user: " . $e->getMessage() . "\n");
}
