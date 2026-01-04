<?php
namespace App\Controllers;

use App\Services\PaymentService;
use App\Repositories\OrderRepository;
use App\Database;

class WebhookController {
    private $payment;
    private $orderRepo;
    private $db;
    
    public function __construct() {
        $this->payment = new PaymentService();
        $this->orderRepo = new OrderRepository();
        $this->db = Database::getInstance();
    }
    
    /**
     * Webhook Stripe pour les paiements de produits
     */
    public function stripe() {
        $payload = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        
        $this->logWebhook('stripe', $payload);
        
        try {
            $event = $this->payment->verifyStripeWebhook($payload, $signature);
            
            if ($event->type === 'payment_intent.succeeded') {
                $paymentIntent = $event->data->object;
                $orderId = $paymentIntent->metadata->order_id ?? null;
                
                if ($orderId) {
                    $this->orderRepo->updateStatus($orderId, 'paid', $paymentIntent->id);
                }
            }
            
            http_response_code(200);
            return json_encode(['received' => true]);
        } catch (\Exception $e) {
            http_response_code(400);
            return json_encode(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Webhook Stripe pour les abonnements
     */
    public function subscription() {
        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $webhookSecret = env('STRIPE_WEBHOOK_SECRET');
        
        $this->logWebhook('stripe_subscription', $payload);
        
        try {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
            
            // Vérifie la signature
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
            
            // Gère les différents types d'événements
            switch ($event->type) {
                case 'invoice.payment_succeeded':
                    $this->handlePaymentSucceeded($event->data->object);
                    break;
                    
                case 'invoice.payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                    break;
                    
                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event->data->object);
                    break;
                    
                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($event->data->object);
                    break;
            }
            
            http_response_code(200);
            echo json_encode(['success' => true]);
            
        } catch (\Exception $e) {
            error_log("Webhook subscription error: " . $e->getMessage());
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Webhook PayPal
     */
    public function paypal() {
        $payload = file_get_contents('php://input');
        $this->logWebhook('paypal', $payload);
        
        $data = json_decode($payload, true);
        
        if ($data['event_type'] === 'PAYMENT.CAPTURE.COMPLETED') {
            $customId = $data['resource']['custom_id'] ?? null;
            
            if ($customId) {
                $this->orderRepo->updateStatus($customId, 'paid', $data['resource']['id']);
            }
        }
        
        http_response_code(200);
        return json_encode(['received' => true]);
    }
    
    /**
     * Paiement d'abonnement réussi
     */
    private function handlePaymentSucceeded($invoice) {
        $subscriptionId = $invoice->subscription;
        
        // Trouve l'abonnement dans notre base
        $subscription = $this->db->fetchOne(
            "SELECT * FROM user_subscriptions WHERE stripe_subscription_id = ?",
            [$subscriptionId]
        );
        
        if ($subscription) {
            // Enregistre le paiement
            $this->db->insert(
                "INSERT INTO subscription_payments 
                 (subscription_id, amount, currency, status, stripe_payment_intent_id, stripe_invoice_id, paid_at)
                 VALUES (?, ?, ?, 'succeeded', ?, ?, NOW())",
                [
                    $subscription['id'],
                    $invoice->amount_paid / 100,
                    strtoupper($invoice->currency),
                    $invoice->payment_intent,
                    $invoice->id
                ]
            );
            
            // Met à jour les dates de période
            if (isset($invoice->lines->data[0])) {
                $periodEnd = date('Y-m-d H:i:s', $invoice->lines->data[0]->period->end);
                
                $this->db->query(
                    "UPDATE user_subscriptions 
                     SET current_period_end = ?, status = 'active' 
                     WHERE id = ?",
                    [$periodEnd, $subscription['id']]
                );
            }
            
            error_log("Subscription payment succeeded for subscription ID: {$subscription['id']}");
        }
    }
    
    /**
     * Paiement d'abonnement échoué
     */
    private function handlePaymentFailed($invoice) {
        $subscriptionId = $invoice->subscription;
        
        $subscription = $this->db->fetchOne(
            "SELECT * FROM user_subscriptions WHERE stripe_subscription_id = ?",
            [$subscriptionId]
        );
        
        if ($subscription) {
            // Enregistre le paiement échoué
            $this->db->insert(
                "INSERT INTO subscription_payments 
                 (subscription_id, amount, currency, status, stripe_invoice_id, failure_reason)
                 VALUES (?, ?, ?, 'failed', ?, ?)",
                [
                    $subscription['id'],
                    $invoice->amount_due / 100,
                    strtoupper($invoice->currency),
                    $invoice->id,
                    'Payment failed'
                ]
            );
            
            // Met à jour le statut
            $this->db->query(
                "UPDATE user_subscriptions SET status = 'past_due' WHERE id = ?",
                [$subscription['id']]
            );
            
            error_log("Subscription payment failed for subscription ID: {$subscription['id']}");
        }
    }
    
    /**
     * Abonnement supprimé/annulé
     */
    private function handleSubscriptionDeleted($stripeSubscription) {
        $this->db->query(
            "UPDATE user_subscriptions 
             SET status = 'cancelled', cancelled_at = NOW() 
             WHERE stripe_subscription_id = ?",
            [$stripeSubscription->id]
        );
        
        error_log("Subscription cancelled: {$stripeSubscription->id}");
    }
    
    /**
     * Abonnement mis à jour
     */
    private function handleSubscriptionUpdated($stripeSubscription) {
        $periodEnd = date('Y-m-d H:i:s', $stripeSubscription->current_period_end);
        $cancelAtPeriodEnd = $stripeSubscription->cancel_at_period_end ? 1 : 0;
        
        $this->db->query(
            "UPDATE user_subscriptions 
             SET current_period_end = ?, cancel_at_period_end = ? 
             WHERE stripe_subscription_id = ?",
            [$periodEnd, $cancelAtPeriodEnd, $stripeSubscription->id]
        );
        
        error_log("Subscription updated: {$stripeSubscription->id}");
    }
    
    /**
     * Log des webhooks
     */
    private function logWebhook($gateway, $payload) {
        $data = json_decode($payload, true);
        $eventType = $data['type'] ?? $data['event_type'] ?? 'unknown';
        
        try {
            $this->db->insert(
                "INSERT INTO webhooks_logs (gateway, event_type, payload, status) VALUES (?, ?, ?, ?)",
                [$gateway, $eventType, $payload, 'pending']
            );
        } catch (\Exception $e) {
            error_log("Failed to log webhook: " . $e->getMessage());
        }
    }
}