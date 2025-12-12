<?php
namespace App\Services;

use App\Database;

class DownloadService {
    private $db;
    private $storage;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->storage = new StorageService();
    }
    
    public function createDownloadToken($orderId, $userId, $productId) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $this->db->insert(
            "INSERT INTO downloads (order_id, user_id, product_id, download_token, expire_at, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$orderId, $userId, $productId, $token, $expiry, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']
        );
        
        return $token;
    }
    
    public function verifyToken($token) {
        $download = $this->db->fetchOne(
            "SELECT d.*, p.file_storage_path, p.title FROM downloads d JOIN products p ON d.product_id = p.id WHERE d.download_token = ? AND d.expire_at > NOW()",
            [$token]
        );
        
        if (!$download) {
            throw new \Exception("Invalid or expired download token");
        }
        
        return $download;
    }
    
    public function markAsDownloaded($token) {
        $this->db->query(
            "UPDATE downloads SET downloaded_at = NOW() WHERE download_token = ?",
            [$token]
        );
    }
    
    public function getSecureDownloadUrl($token) {
        $download = $this->verifyToken($token);
        $this->markAsDownloaded($token);
        
        return $this->storage->getSignedUrl($download['file_storage_path'], 60);
    }
    
    public function getUserDownloads($userId, $page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        
        return $this->db->fetchAll(
            "SELECT d.*, p.title, p.thumbnail_path, o.order_number FROM downloads d JOIN products p ON d.product_id = p.id JOIN orders o ON d.order_id = o.id WHERE d.user_id = ? ORDER BY d.created_at DESC LIMIT ? OFFSET ?",
            [$userId, $perPage, $offset]
        );
    }
}
