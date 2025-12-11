<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class PaymentService
{
    private $db;
    
    public function __construct()
    {
        global $db;
        $this->db = $db;
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
    }
    
    public function createPaymentIntent($orderId, $amount, $currency = 'USD')
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100, // Convert to cents
                'currency' => strtolower($currency),
                'metadata' => [
                    'order_id' => $orderId,
                ],
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);
            
            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ];
        } catch (\Exception $e) {
            logger()->error('Stripe payment intent creation failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function handleStripeWebhook()
    {
        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $webhookSecret = env('STRIPE_WEBHOOK_SECRET');
        
        $this->logWebhook('stripe', $payload);
        
        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            logger()->error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage()
            ]);
            http_response_code(400);
            exit;
        }
        
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSuccess($event->data->object);
                break;
            
            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;
            
            case 'charge.refunded':
                $this->handleRefund($event->data->object);
                break;
        }
        
        $this->updateWebhookStatus($event->id, 'processed');
        http_response_code(200);
    }
    
    private function handlePaymentSuccess($paymentIntent)
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;
        
        if (!$orderId) {
            logger()->warning('Payment success without order ID', [
                'payment_intent' => $paymentIntent->id
            ]);
            return;
        }
        
        // Update order status
        $stmt = $this->db->prepare("
            UPDATE orders
            SET status = 'paid',
                payment_reference = ?,
                updated_at = NOW()
            WHERE id = ? AND status = 'pending'
        ");
        $stmt->execute([$paymentIntent->id, $orderId]);
        
        // Get order details
        $stmt = $this->db->prepare("
            SELECT o.*, u.email as buyer_email
            FROM orders o
            JOIN users u ON o.buyer_id = u.id
            WHERE o.id = ?
        ");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        
        if (!$order) {
            return;
        }
        
        // Update seller wallet
        $this->creditSellerWallet($order['seller_id'], $order['seller_earnings'], $orderId);
        
        // Generate download link
        $downloadService = new DownloadService();
        $downloadService->generateDownloadLink($orderId);
        
        // Send confirmation email
        $mailService = new MailService();
        $mailService->sendPurchaseConfirmation($order);
        
        // Notify seller
        $notificationService = new NotificationService();
        $notificationService->notifySale($order['seller_id'], $orderId);
        
        // Update product sales count
        $stmt = $this->db->prepare("
            UPDATE products SET sales_count = sales_count + 1 WHERE id = ?
        ");
        $stmt->execute([$order['product_id']]);
    }
    
    private function handlePaymentFailed($paymentIntent)
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;
        
        if ($orderId) {
            $stmt = $this->db->prepare("
                UPDATE orders SET status = 'failed', updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$orderId]);
        }
    }
    
    private function handleRefund($charge)
    {
        // Find order by payment reference
        $stmt = $this->db->prepare("
            SELECT * FROM orders WHERE payment_reference = ?
        ");
        $stmt->execute([$charge->payment_intent]);
        $order = $stmt->fetch();
        
        if (!$order) {
            return;
        }
        
        // Update order status
        $stmt = $this->db->prepare("
            UPDATE orders
            SET status = 'refunded',
                refunded_at = NOW(),
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$order['id']]);
        
        // Debit seller wallet
        $this->debitSellerWallet($order['seller_id'], $order['seller_earnings'], $order['id'], 'refund');
        
        // Revoke download access
        $stmt = $this->db->prepare("
            UPDATE downloads SET expire_at = NOW() WHERE order_id = ?
        ");
        $stmt->execute([$order['id']]);
        
        // Notify parties
        $mailService = new MailService();
        $mailService->sendRefundNotification($order);
    }
    
    private function creditSellerWallet($sellerId, $amount, $orderId)
    {
        $this->db->beginTransaction();
        
        try {
            // Get or create wallet
            $stmt = $this->db->prepare("
                SELECT * FROM wallets WHERE user_id = ? FOR UPDATE
            ");
            $stmt->execute([$sellerId]);
            $wallet = $stmt->fetch();
            
            if (!$wallet) {
                $stmt = $this->db->prepare("
                    INSERT INTO wallets (user_id, balance, updated_at)
                    VALUES (?, 0.00, NOW())
                ");
                $stmt->execute([$sellerId]);
                $walletId = $this->db->lastInsertId();
                $balanceBefore = 0;
            } else {
                $walletId = $wallet['id'];
                $balanceBefore = $wallet['balance'];
            }
            
            // Update balance
            $balanceAfter = $balanceBefore + $amount;
            $stmt = $this->db->prepare("
                UPDATE wallets SET balance = ?, updated_at = NOW() WHERE id = ?
            ");
            $stmt->execute([$balanceAfter, $walletId]);
            
            // Record transaction
            $stmt = $this->db->prepare("
                INSERT INTO transactions
                (wallet_id, order_id, type, amount, balance_before, balance_after, description, created_at)
                VALUES (?, ?, 'credit', ?, ?, ?, 'Sale commission', NOW())
            ");
            $stmt->execute([$walletId, $orderId, $amount, $balanceBefore, $balanceAfter]);
            
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            logger()->error('Failed to credit seller wallet', [
                'seller_id' => $sellerId,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    private function debitSellerWallet($sellerId, $amount, $orderId, $type = 'debit')
    {
        $this->db->beginTransaction();
        
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM wallets WHERE user_id = ? FOR UPDATE
            ");
            $stmt->execute([$sellerId]);
            $wallet = $stmt->fetch();
            
            if (!$wallet) {
                $this->db->rollBack();
                return;
            }
            
            $balanceBefore = $wallet['balance'];
            $balanceAfter = max(0, $balanceBefore - $amount);
            
            $stmt = $this->db->prepare("
                UPDATE wallets SET balance = ?, updated_at = NOW() WHERE id = ?
            ");
            $stmt->execute([$balanceAfter, $wallet['id']]);
            
            $stmt = $this->db->prepare("
                INSERT INTO transactions
                (wallet_id, order_id, type, amount, balance_before, balance_after, description, created_at)
                VALUES (?, ?, ?, ?, ?, ?, 'Refund deduction', NOW())
            ");
            $stmt->execute([$wallet['id'], $orderId, $type, $amount, $balanceBefore, $balanceAfter]);
            
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            logger()->error('Failed to debit seller wallet', [
                'seller_id' => $sellerId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    private function logWebhook($gateway, $payload)
    {
        $stmt = $this->db->prepare("
            INSERT INTO webhook_logs (gateway, event_type, payload, status, created_at)
            VALUES (?, 'unknown', ?, 'received', NOW())
        ");
        $stmt->execute([$gateway, $payload]);
        
        return $this->db->lastInsertId();
    }
    
    private function updateWebhookStatus($eventId, $status)
    {
        $stmt = $this->db->prepare("
            UPDATE webhook_logs SET status = ?, processed_at = NOW()
            WHERE id = (SELECT id FROM webhook_logs ORDER BY created_at DESC LIMIT 1)
        ");
        $stmt->execute([$status]);
    }
    
    public function refundOrder($orderId, $reason = '')
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        
        if (!$order || $order['status'] !== 'paid') {
            return ['success' => false, 'error' => 'Order cannot be refunded'];
        }
        
        try {
            \Stripe\Refund::create([
                'payment_intent' => $order['payment_reference'],
                'reason' => 'requested_by_customer',
            ]);
            
            $stmt = $this->db->prepare("
                UPDATE orders
                SET status = 'refunded',
                    refund_reason = ?,
                    refunded_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$reason, $orderId]);
            
            return ['success' => true];
        } catch (\Exception $e) {
            logger()->error('Refund failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
