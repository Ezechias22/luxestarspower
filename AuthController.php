<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\MailService;
use App\Validators\AuthValidator;

class AuthController
{
    private User $userModel;
    private MailService $mailService;
    
    public function __construct()
    {
        $this->userModel = new User();
        $this->mailService = new MailService();
    }
    
    /**
     * Afficher la page de connexion
     */
    public function showLogin(): void
    {
        if (isset($_SESSION['user_id'])) {
            redirect('/compte');
        }
        
        require __DIR__ . '/../../views/auth/login.php';
    }
    
    /**
     * Traiter la connexion
     */
    public function login(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Validation
        $validator = new AuthValidator();
        $errors = $validator->validateLogin($email, $password);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['email' => $email];
            back();
        }
        
        // Vérifier les credentials
        $user = $this->userModel->findByEmail($email);
        
        if (!$user || !password_verify($password, $user['password_hash'])) {
            log_activity('login_failed', 'user', null, ['email' => $email]);
            
            $_SESSION['errors'] = ['email' => 'Email ou mot de passe incorrect'];
            $_SESSION['old'] = ['email' => $email];
            back();
        }
        
        // Vérifier si le compte est actif
        if (!$user['is_active']) {
            $_SESSION['errors'] = ['email' => 'Votre compte a été désactivé. Contactez le support.'];
            back();
        }
        
        // Vérifier si l'email est vérifié (si requis)
        if (env('REQUIRE_EMAIL_VERIFICATION', true) && !$user['email_verified_at']) {
            $_SESSION['errors'] = ['email' => 'Veuillez vérifier votre email avant de vous connecter.'];
            $_SESSION['user_id_pending_verification'] = $user['id'];
            back();
        }
        
        // Vérifier 2FA si activé
        if (!empty($user['two_factor_secret'])) {
            $_SESSION['2fa_user_id'] = $user['id'];
            redirect('/2fa/verify');
        }
        
        // Connexion réussie
        $this->createSession($user, $remember);
        
        log_activity('login_success', 'user', $user['id']);
        
        // Rediriger vers la page demandée ou dashboard
        $intended = $_SESSION['intended_url'] ?? '/compte';
        unset($_SESSION['intended_url']);
        
        redirect($intended);
    }
    
    /**
     * Afficher la page d'inscription
     */
    public function showRegister(): void
    {
        if (isset($_SESSION['user_id'])) {
            redirect('/compte');
        }
        
        // Vérifier si les inscriptions sont autorisées
        if (env('ALLOW_REGISTRATION', true) === false) {
            flash('error', 'Les inscriptions sont temporairement fermées.');
            redirect('/');
        }
        
        require __DIR__ . '/../../views/auth/register.php';
    }
    
    /**
     * Traiter l'inscription
     */
    public function register(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        // Validation
        $validator = new AuthValidator();
        $errors = $validator->validateRegister($name, $email, $password, $passwordConfirm);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['name' => $name, 'email' => $email];
            back();
        }
        
        // Vérifier si l'email existe déjà
        if ($this->userModel->findByEmail($email)) {
            $_SESSION['errors'] = ['email' => 'Cet email est déjà utilisé'];
            $_SESSION['old'] = ['name' => $name, 'email' => $email];
            back();
        }
        
        // Créer l'utilisateur
        try {
            $userId = $this->userModel->create([
                'name' => $name,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_ARGON2ID),
                'role' => 'buyer',
                'currency' => 'USD',
            ]);
            
            log_activity('user_registered', 'user', $userId, [
                'email' => $email,
                'ip' => get_client_ip()
            ]);
            
            // Envoyer l'email de vérification
            if (env('REQUIRE_EMAIL_VERIFICATION', true)) {
                $token = bin2hex(random_bytes(32));
                $this->storeVerificationToken($email, $token);
                $this->mailService->sendVerificationEmail($email, $name, $token);
                
                flash('success', 'Inscription réussie ! Veuillez vérifier votre email pour activer votre compte.');
                redirect('/connexion');
            } else {
                // Connexion automatique
                $user = $this->userModel->findById($userId);
                $this->createSession($user, false);
                
                flash('success', 'Bienvenue sur LuxeStarsPower !');
                redirect('/compte');
            }
            
        } catch (\Exception $e) {
            $GLOBALS['logger']->error('Registration failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            
            $_SESSION['errors'] = ['general' => 'Une erreur est survenue. Veuillez réessayer.'];
            back();
        }
    }
    
    /**
     * Vérifier l'email
     */
    public function verifyEmail(string $token): void
    {
        $verification = $this->getVerificationToken($token);
        
        if (!$verification) {
            flash('error', 'Token de vérification invalide ou expiré.');
            redirect('/connexion');
        }
        
        $user = $this->userModel->findByEmail($verification['email']);
        
        if (!$user) {
            flash('error', 'Utilisateur non trouvé.');
            redirect('/connexion');
        }
        
        // Vérifier l'email
        $this->userModel->verifyEmail($user['id']);
        $this->deleteVerificationToken($token);
        
        log_activity('email_verified', 'user', $user['id']);
        
        flash('success', 'Email vérifié avec succès ! Vous pouvez maintenant vous connecter.');
        redirect('/connexion');
    }
    
    /**
     * Déconnexion
     */
    public function logout(): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        if ($userId) {
            log_activity('logout', 'user', $userId);
        }
        
        // Détruire la session
        $_SESSION = [];
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
        
        redirect('/');
    }
    
    /**
     * Créer une session utilisateur
     */
    private function createSession(array $user, bool $remember = false): void
    {
        // Régénérer l'ID de session pour éviter session fixation
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_avatar'] = $user['avatar_url'];
        $_SESSION['last_activity'] = time();
        
        if ($remember) {
            // Cookie "remember me" pour 30 jours
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + (30 * 24 * 3600), '/', '', true, true);
            
            // Stocker le token en DB
            $this->userModel->update($user['id'], ['remember_token' => hash('sha256', $token)]);
        }
    }
    
    /**
     * Stocker un token de vérification
     */
    private function storeVerificationToken(string $email, string $token): void
    {
        $db = \App\Config\Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("
            INSERT INTO password_resets (email, token, created_at)
            VALUES (:email, :token, NOW())
            ON DUPLICATE KEY UPDATE token = :token, created_at = NOW()
        ");
        
        $stmt->execute([
            'email' => $email,
            'token' => hash('sha256', $token),
        ]);
    }
    
    /**
     * Récupérer un token de vérification
     */
    private function getVerificationToken(string $token): ?array
    {
        $db = \App\Config\Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("
            SELECT email, created_at 
            FROM password_resets 
            WHERE token = :token 
            AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ");
        
        $stmt->execute(['token' => hash('sha256', $token)]);
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Supprimer un token de vérification
     */
    private function deleteVerificationToken(string $token): void
    {
        $db = \App\Config\Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("DELETE FROM password_resets WHERE token = :token");
        $stmt->execute(['token' => hash('sha256', $token)]);
    }
}
