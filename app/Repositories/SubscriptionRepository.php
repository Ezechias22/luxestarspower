<?php
namespace App\Repositories;

use App\Database;

class SubscriptionRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Récupère tous les plans actifs
     */
    public function getActivePlans() {
        return $this->db->fetchAll(
            "SELECT * FROM subscription_plans WHERE is_active = 1 ORDER BY price ASC"
        );
    }
    
    /**
     * Récupère un plan par slug
     */
    public function getPlanBySlug($slug) {
        return $this->db->fetchOne(
            "SELECT * FROM subscription_plans WHERE slug = ? AND is_active = 1",
            [$slug]
        );
    }
    
    /**
     * Récupère l'abonnement actif d'un utilisateur
     */
    public function getUserActiveSubscription($userId) {
        return $this->db->fetchOne(
            "SELECT us.*, sp.name as plan_name, sp.slug as plan_slug, 
                    sp.commission_rate, sp.max_products
             FROM user_subscriptions us
             JOIN subscription_plans sp ON us.plan_id = sp.id
             WHERE us.user_id = ? 
             AND us.status IN ('trial', 'active')
             AND (us.current_period_end IS NULL OR us.current_period_end > NOW())
             ORDER BY us.created_at DESC
             LIMIT 1",
            [$userId]
        );
    }
    
    /**
     * Crée un abonnement essai gratuit
     */
    public function createTrialSubscription($userId) {
        $trialPlan = $this->getPlanBySlug('trial');
        
        if (!$trialPlan) {
            throw new \Exception("Plan d'essai non trouvé");
        }
        
        $trialEndsAt = date('Y-m-d H:i:s', strtotime('+14 days'));
        
        $subscriptionId = $this->db->insert(
            "INSERT INTO user_subscriptions 
             (user_id, plan_id, status, trial_ends_at, current_period_start, current_period_end)
             VALUES (?, ?, 'trial', ?, NOW(), ?)",
            [$userId, $trialPlan['id'], $trialEndsAt, $trialEndsAt]
        );
        
        // Met à jour l'utilisateur
        $this->db->query(
            "UPDATE users SET current_subscription_id = ? WHERE id = ?",
            [$subscriptionId, $userId]
        );
        
        return $subscriptionId;
    }
    
    /**
     * Vérifie si un utilisateur peut ajouter un produit
     */
    public function canAddProduct($userId) {
        $subscription = $this->getUserActiveSubscription($userId);
        
        if (!$subscription) {
            return ['can' => false, 'reason' => 'Aucun abonnement actif'];
        }
        
        $maxProducts = $subscription['max_products'];
        
        // -1 = illimité
        if ($maxProducts == -1) {
            return ['can' => true];
        }
        
        // Compte les produits actuels
        $currentCount = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM products WHERE seller_id = ?",
            [$userId]
        );
        
        if ($currentCount['count'] >= $maxProducts) {
            return [
                'can' => false, 
                'reason' => "Limite atteinte ({$maxProducts} produits max). Passez à un plan supérieur !"
            ];
        }
        
        return ['can' => true];
    }
    
    /**
     * Calcule la commission pour un vendeur
     */
    public function getCommissionRate($userId) {
        $subscription = $this->getUserActiveSubscription($userId);
        
        if (!$subscription) {
            return 15.00; // Par défaut
        }
        
        return $subscription['commission_rate'];
    }
    
    /**
     * Annule un abonnement à la fin de la période
     */
    public function cancelAtPeriodEnd($subscriptionId) {
        return $this->db->query(
            "UPDATE user_subscriptions 
             SET cancel_at_period_end = 1, cancelled_at = NOW()
             WHERE id = ?",
            [$subscriptionId]
        );
    }
    
    /**
     * Vérifie et expire les abonnements
     */
    public function checkExpiredSubscriptions() {
        return $this->db->query(
            "UPDATE user_subscriptions 
             SET status = 'expired'
             WHERE status IN ('trial', 'active')
             AND current_period_end < NOW()"
        );
    }
}