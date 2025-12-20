<?php
namespace App\Repositories;

use App\Database;

class CartRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function addToCart($userId, $productId, $quantity = 1) {
        return $this->db->query(
            "INSERT INTO cart (user_id, product_id, quantity)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE quantity = quantity + ?",
            [$userId, $productId, $quantity, $quantity]
        );
    }

    public function getCartItems($userId) {
        return $this->db->fetchAll(
            "SELECT c.*, 
                    p.id,
                    p.title, 
                    p.price, 
                    p.thumbnail_path, 
                    p.slug, 
                    p.description,
                    p.type,
                    p.seller_id,
                    u.name as seller_name
             FROM cart c
             JOIN products p ON c.product_id = p.id
             JOIN users u ON p.seller_id = u.id
             WHERE c.user_id = ?
             ORDER BY c.created_at DESC",
            [$userId]
        );
    }

    public function getCartTotal($userId) {
        $result = $this->db->fetchOne(
            "SELECT SUM(p.price * c.quantity) as total
             FROM cart c
             JOIN products p ON c.product_id = p.id
             WHERE c.user_id = ?",
            [$userId]
        );

        return $result['total'] ?? 0;
    }

    public function removeFromCart($userId, $productId) {
        return $this->db->query(
            "DELETE FROM cart WHERE user_id = ? AND product_id = ?",
            [$userId, $productId]
        );
    }

    public function clearCart($userId) {
        return $this->db->query(
            "DELETE FROM cart WHERE user_id = ?",
            [$userId]
        );
    }

    public function updateQuantity($userId, $productId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeFromCart($userId, $productId);
        }

        return $this->db->query(
            "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?",
            [$quantity, $userId, $productId]
        );
    }

    public function getCartCount($userId) {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM cart WHERE user_id = ?",
            [$userId]
        );

        return $result['count'] ?? 0;
    }
}