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
    
    private function logWebhook($gateway, $payload) {
        $data = json_decode($payload, true);
        $eventType = $data['type'] ?? $data['event_type'] ?? 'unknown';
        
        $this->db->insert(
            "INSERT INTO webhooks_logs (gateway, event_type, payload, status) VALUES (?, ?, ?, ?)",
            [$gateway, $eventType, $payload, 'pending']
        );
    }
}
