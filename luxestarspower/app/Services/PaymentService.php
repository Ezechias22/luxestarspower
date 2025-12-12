<?php
namespace App\Services;

class PaymentService {
    private $config;
    
    public function __construct() {
        $this->config = require __DIR__ . '/../../config/config.php';
    }
    
    public function createStripePaymentIntent($amount, $currency, $metadata = []) {
        $stripe = new \Stripe\StripeClient($this->config['payment']['stripe']['secret_key']);
        
        return $stripe->paymentIntents->create([
            'amount' => intval($amount * 100),
            'currency' => strtolower($currency),
            'metadata' => $metadata,
            'automatic_payment_methods' => ['enabled' => true]
        ]);
    }
    
    public function verifyStripeWebhook($payload, $signature) {
        $webhookSecret = $this->config['payment']['stripe']['webhook_secret'];
        
        try {
            $event = \Stripe\Webhook::constructEvent($payload, $signature, $webhookSecret);
            return $event;
        } catch (\Exception $e) {
            throw new \Exception('Webhook signature verification failed');
        }
    }
    
    public function createPayPalOrder($amount, $currency, $metadata = []) {
        $clientId = $this->config['payment']['paypal']['client_id'];
        $secret = $this->config['payment']['paypal']['secret'];
        $mode = $this->config['payment']['paypal']['mode'];
        
        $baseUrl = $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
        
        $auth = base64_encode("$clientId:$secret");
        $accessToken = $this->getPayPalAccessToken($baseUrl, $auth);
        
        $ch = curl_init("$baseUrl/v2/checkout/orders");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer $accessToken"
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => number_format($amount, 2, '.', '')
                    ],
                    'custom_id' => $metadata['order_id'] ?? null
                ]]
            ])
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    private function getPayPalAccessToken($baseUrl, $auth) {
        $ch = curl_init("$baseUrl/v1/oauth2/token");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Basic $auth",
                "Content-Type: application/x-www-form-urlencoded"
            ],
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials'
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($response, true);
        return $data['access_token'] ?? null;
    }
    
    public function calculateFees($amount) {
        $commissionRate = floatval($this->config['payment']['commission_rate']);
        $platformFee = round($amount * $commissionRate, 2);
        $sellerEarnings = round($amount - $platformFee, 2);
        
        return [
            'platform_fee' => $platformFee,
            'seller_earnings' => $sellerEarnings
        ];
    }
}
