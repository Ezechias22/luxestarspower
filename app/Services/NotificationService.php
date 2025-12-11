<?php

namespace App\Services;

class NotificationService
{
    private $db;
    
    public function __construct()
    {
        global $db;
        $this->db = $db;
    }
    
    public function create($userId, $type, $title, $message, $data = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, type, title, message, data, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        return $stmt->execute([
            $userId,
            $type,
            $title,
            $message,
            $data ? json_encode($data) : null
        ]);
    }
    
    public function notifySale($sellerId, $orderId)
    {
        return $this->create(
            $sellerId,
            'sale',
            __('notifications.new_sale'),
            __('notifications.you_made_a_sale'),
            ['order_id' => $orderId]
        );
    }
    
    public function notifyPayout($sellerId, $payoutId)
    {
        return $this->create(
            $sellerId,
            'payout',
            __('notifications.payout_processed'),
            __('notifications.payout_details'),
            ['payout_id' => $payoutId]
        );
    }
    
    public function getUnreadCount($userId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM notifications
            WHERE user_id = ? AND read_at IS NULL
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    public function markAsRead($notificationId, $userId)
    {
        $stmt = $this->db->prepare("
            UPDATE notifications
            SET read_at = NOW()
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$notificationId, $userId]);
    }
    
    public function markAllAsRead($userId)
    {
        $stmt = $this->db->prepare("
            UPDATE notifications
            SET read_at = NOW()
            WHERE user_id = ? AND read_at IS NULL
        ");
        return $stmt->execute([$userId]);
    }
    
    public function getNotifications($userId, $limit = 20, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM notifications
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll();
    }
}
