<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;

class DownloadService
{
    private $db;
    private $storageService;
    
    public function __construct()
    {
        global $db;
        $this->db = $db;
        $this->storageService = new StorageService();
    }
    
    public function generateDownloadLink($orderId)
    {
        // Get order details
        $stmt = $this->db->prepare("
            SELECT o.*, p.file_storage_path, p.title as product_title
            FROM orders o
            JOIN products p ON o.product_id = p.id
            WHERE o.id = ? AND o.status = 'paid'
        ");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        
        if (!$order) {
            return ['success' => false, 'error' => 'Order not found'];
        }
        
        // Check if download link already exists
        $stmt = $this->db->prepare("
            SELECT * FROM downloads
            WHERE order_id = ? AND expire_at > NOW()
        ");
        $stmt->execute([$orderId]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            return [
                'success' => true,
                'download_url' => route('download', ['token' => $existing['download_token']]),
                'token' => $existing['download_token'],
            ];
        }
        
        // Generate new download token
        $token = Uuid::uuid4()->toString();
        $expiresIn = (int)config('storage.download_link_expiry', 3600);
        $expireAt = date('Y-m-d H:i:s', time() + $expiresIn);
        
        // Generate signed S3 URL
        $s3Result = $this->storageService->generateSignedDownloadUrl(
            $order['file_storage_path'],
            $expiresIn,
            slug($order['product_title']) . '.' . pathinfo($order['file_storage_path'], PATHINFO_EXTENSION)
        );
        
        if (!$s3Result['success']) {
            return $s3Result;
        }
        
        // Store download record
        $stmt = $this->db->prepare("
            INSERT INTO downloads
            (order_id, user_id, product_id, download_token, download_url, expire_at, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $orderId,
            $order['buyer_id'],
            $order['product_id'],
            $token,
            $s3Result['url'],
            $expireAt
        ]);
        
        return [
            'success' => true,
            'download_url' => route('download', ['token' => $token]),
            'token' => $token,
            'expires_at' => $expireAt,
        ];
    }
    
    public function processDownload($token)
    {
        $stmt = $this->db->prepare("
            SELECT d.*, o.buyer_id, p.file_storage_path, p.title, p.file_mime_type
            FROM downloads d
            JOIN orders o ON d.order_id = o.id
            JOIN products p ON d.product_id = p.id
            WHERE d.download_token = ?
        ");
        $stmt->execute([$token]);
        $download = $stmt->fetch();
        
        if (!$download) {
            return ['success' => false, 'error' => 'Download not found'];
        }
        
        // Check expiry
        if (strtotime($download['expire_at']) < time()) {
            return ['success' => false, 'error' => 'Download link expired'];
        }
        
        // Check user authorization
        $currentUser = user();
        if (!$currentUser || $currentUser['id'] != $download['buyer_id']) {
            return ['success' => false, 'error' => 'Unauthorized'];
        }
        
        // Check download limit
        $maxAttempts = (int)config('storage.max_download_attempts', 5);
        if ($download['download_count'] >= $maxAttempts) {
            return ['success' => false, 'error' => 'Download limit exceeded'];
        }
        
        // Update download count
        $stmt = $this->db->prepare("
            UPDATE downloads
            SET download_count = download_count + 1,
                downloaded_at = NOW(),
                ip_address = ?,
                user_agent = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            $download['id']
        ]);
        
        // Generate fresh signed URL
        $result = $this->storageService->generateSignedDownloadUrl(
            $download['file_storage_path'],
            300, // 5 minutes
            slug($download['title']) . '.' . pathinfo($download['file_storage_path'], PATHINFO_EXTENSION)
        );
        
        if (!$result['success']) {
            return $result;
        }
        
        return [
            'success' => true,
            'url' => $result['url'],
            'filename' => $download['title'],
            'mime_type' => $download['file_mime_type'],
        ];
    }
    
    public function streamDownload($token)
    {
        $result = $this->processDownload($token);
        
        if (!$result['success']) {
            return $result;
        }
        
        // Redirect to signed S3 URL
        redirect($result['url']);
    }
}
