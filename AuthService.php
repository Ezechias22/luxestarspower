<?php

namespace App\Services;

use PDO;

class AuthService
{
    private $db;
    
    public function __construct()
    {
        global $db;
        $this->db = $db;
    }
    
    public function attempt($email, $password, $remember = false)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->logFailedAttempt($email);
            return false;
        }
        
        // Check if account is locked due to too many failed attempts
        if ($this->isAccountLocked($email)) {
            return false;
        }
        
        // Start session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = $user;
        
        if ($remember) {
            $this->createRememberToken($user['id']);
        }
        
        // Log activity
        $this->logActivity($user['id'], 'login');
        
        // Clear failed attempts
        $this->clearFailedAttempts($email);
        
        // Update last login
        $stmt = $this->db->prepare("UPDATE users SET updated_at = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        return true;
    }
    
    public function register($data)
    {
        // Validation
        if (!validateEmail($data['email'])) {
            return ['success' => false, 'error' => __('validation.invalid_email')];
        }
        
        if (strlen($data['password']) < 8) {
            return ['success' => false, 'error' => __('validation.password_min_length')];
        }
        
        // Check if email exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            return ['success' => false, 'error' => __('auth.email_exists')];
        }
        
        // Create user
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password_hash, role, currency, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        $role = $data['role'] ?? 'buyer';
        $currency = $data['currency'] ?? config('app.default_currency', 'USD');
        
        $success = $stmt->execute([
            $data['name'],
            $data['email'],
            bcrypt($data['password']),
            $role,
            $currency
        ]);
        
        if (!$success) {
            return ['success' => false, 'error' => __('errors.registration_failed')];
        }
        
        $userId = $this->db->lastInsertId();
        
        // Create wallet for seller
        if ($role === 'seller') {
            $stmt = $this->db->prepare("
                INSERT INTO wallets (user_id, balance, currency, updated_at)
                VALUES (?, 0.00, ?, NOW())
            ");
            $stmt->execute([$userId, $currency]);
        }
        
        // Send verification email
        $this->sendVerificationEmail($userId, $data['email']);
        
        // Log activity
        $this->logActivity($userId, 'register');
        
        return ['success' => true, 'user_id' => $userId];
    }
    
    public function logout()
    {
        $userId = $_SESSION['user_id'] ?? null;
        
        if ($userId) {
            $this->logActivity($userId, 'logout');
        }
        
        session_destroy();
        
        // Delete remember cookie if exists
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
    }
    
    public function user()
    {
        return $_SESSION['user'] ?? null;
    }
    
    public function check()
    {
        return isset($_SESSION['user_id']);
    }
    
    public function sendPasswordResetLink($email)
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return true; // Don't reveal if email exists
        }
        
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $stmt = $this->db->prepare("
            INSERT INTO password_resets (email, token, expires_at, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$email, $token, $expiresAt]);
        
        // Send email
        $mailService = new MailService();
        $resetUrl = route('password.reset', ['token' => $token]);
        $mailService->send(
            $email,
            __('mail.password_reset_subject'),
            'emails/password-reset',
            ['resetUrl' => $resetUrl, 'token' => $token]
        );
        
        return true;
    }
    
    public function resetPassword($token, $password)
    {
        $stmt = $this->db->prepare("
            SELECT email FROM password_resets
            WHERE token = ? AND expires_at > NOW() AND used_at IS NULL
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$token]);
        $reset = $stmt->fetch();
        
        if (!$reset) {
            return false;
        }
        
        // Update password
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        $stmt->execute([bcrypt($password), $reset['email']]);
        
        // Mark token as used
        $stmt = $this->db->prepare("UPDATE password_resets SET used_at = NOW() WHERE token = ?");
        $stmt->execute([$token]);
        
        return true;
    }
    
    public function verifyEmail($token)
    {
        $stmt = $this->db->prepare("
            SELECT user_id FROM email_verifications
            WHERE token = ? AND expires_at > NOW() AND verified_at IS NULL
        ");
        $stmt->execute([$token]);
        $verification = $stmt->fetch();
        
        if (!$verification) {
            return false;
        }
        
        // Update user
        $stmt = $this->db->prepare("
            UPDATE users SET email_verified_at = NOW() WHERE id = ?
        ");
        $stmt->execute([$verification['user_id']]);
        
        // Mark as verified
        $stmt = $this->db->prepare("
            UPDATE email_verifications SET verified_at = NOW() WHERE token = ?
        ");
        $stmt->execute([$token]);
        
        return true;
    }
    
    private function sendVerificationEmail($userId, $email)
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $stmt = $this->db->prepare("
            INSERT INTO email_verifications (user_id, token, expires_at, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $token, $expiresAt]);
        
        $mailService = new MailService();
        $verifyUrl = route('email.verify', ['token' => $token]);
        $mailService->send(
            $email,
            __('mail.verify_email_subject'),
            'emails/verify-email',
            ['verifyUrl' => $verifyUrl]
        );
    }
    
    private function logFailedAttempt($email)
    {
        $key = "failed_login_{$email}";
        $attempts = $_SESSION[$key] ?? 0;
        $_SESSION[$key] = $attempts + 1;
        $_SESSION["{$key}_time"] = time();
    }
    
    private function clearFailedAttempts($email)
    {
        $key = "failed_login_{$email}";
        unset($_SESSION[$key]);
        unset($_SESSION["{$key}_time"]);
    }
    
    private function isAccountLocked($email)
    {
        $key = "failed_login_{$email}";
        $attempts = $_SESSION[$key] ?? 0;
        $lastAttempt = $_SESSION["{$key}_time"] ?? 0;
        
        $maxAttempts = config('security.max_login_attempts', 5);
        $lockoutTime = config('security.lockout_time', 900); // 15 minutes
        
        if ($attempts >= $maxAttempts) {
            if (time() - $lastAttempt < $lockoutTime) {
                return true;
            } else {
                $this->clearFailedAttempts($email);
            }
        }
        
        return false;
    }
    
    private function createRememberToken($userId)
    {
        $token = bin2hex(random_bytes(32));
        setcookie('remember_token', $token, time() + (86400 * 30), '/', '', true, true);
        
        // Store hashed token in database
        $stmt = $this->db->prepare("
            UPDATE users SET remember_token = ? WHERE id = ?
        ");
        $stmt->execute([hash('sha256', $token), $userId]);
    }
    
    private function logActivity($userId, $action)
    {
        $stmt = $this->db->prepare("
            INSERT INTO activity_logs (user_id, action_type, ip_address, user_agent, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $userId,
            $action,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
}
