<?php
echo "<h1>Créer un compte Admin</h1>";

// Charger autoload seulement si .env existe
if (file_exists(__DIR__ . '/../.env')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
}

// Connexion DB avec Railway variables
$host = getenv('DB_HOST') ?: $_ENV['DB_HOST'] ?? null;
$dbname = getenv('DB_NAME') ?: $_ENV['DB_DATABASE'] ?? null;
$user = getenv('DB_USER') ?: $_ENV['DB_USERNAME'] ?? null;
$pass = getenv('DB_PASS') ?: $_ENV['DB_PASSWORD'] ?? null;

if (!$host || !$dbname || !$user) {
    die("<p style='color:red'>Variables DB manquantes!</p>");
}

try {
    $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<p style='color:red'>Connexion DB échouée: " . $e->getMessage() . "</p>");
}

// Traiter le formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($name) || empty($email) || empty($password)) {
        echo "<p style='color:red'>Tous les champs sont requis!</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color:red'>Email invalide!</p>";
    } elseif (strlen($password) < 8) {
        echo "<p style='color:red'>Le mot de passe doit faire au moins 8 caractères!</p>";
    } else {
        // Vérifier si email existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetchColumn() > 0) {
            echo "<p style='color:red'>Cet email existe déjà!</p>";
        } else {
            // Créer l'admin
            $passwordHash = password_hash($password, PASSWORD_ARGON2ID);
            
            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, password_hash, role, is_active, email_verified_at, created_at, updated_at)
                VALUES (?, ?, ?, 'admin', 1, NOW(), NOW(), NOW())
            ");
            
            if ($stmt->execute([$name, $email, $passwordHash])) {
                echo "<div style='color:green; padding:20px; background:#d4edda; border:1px solid #c3e6cb; border-radius:5px;'>";
                echo "<h2>✅ Admin créé avec succès!</h2>";
                echo "<p><strong>Nom:</strong> $name</p>";
                echo "<p><strong>Email:</strong> $email</p>";
                echo "<p><strong>Rôle:</strong> admin</p>";
                echo "<p><a href='/'>← Aller à l'accueil</a></p>";
                echo "</div>";
                exit;
            } else {
                echo "<p style='color:red'>Erreur lors de la création!</p>";
            }
        }
    }
}
?>

<form method="POST" style="max-width:500px; margin:20px auto; padding:20px; border:1px solid #ddd; border-radius:5px;">
    <div style="margin-bottom:15px;">
        <label>Nom complet:</label><br>
        <input type="text" name="name" required style="width:100%; padding:8px; margin-top:5px;">
    </div>
    
    <div style="margin-bottom:15px;">
        <label>Email:</label><br>
        <input type="email" name="email" required style="width:100%; padding:8px; margin-top:5px;">
    </div>
    
    <div style="margin-bottom:15px;">
        <label>Mot de passe (min 8 caractères):</label><br>
        <input type="password" name="password" required minlength="8" style="width:100%; padding:8px; margin-top:5px;">
    </div>
    
    <button type="submit" style="background:#007bff; color:white; padding:10px 20px; border:none; border-radius:5px; cursor:pointer;">
        Créer le compte Admin
    </button>
</form>