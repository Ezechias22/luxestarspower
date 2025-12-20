<?php
namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeService {
    
    public function __construct() {
        $config = require __DIR__ . '/../../config/config.php';
        $secretKey = $config['payment']['stripe_secret'];
        
        if (!$secretKey) {
            throw new \Exception('Stripe secret key not configured');
        }
        
        Stripe::setApiKey($secretKey);
    }
    
    /**
     * Crée une session de paiement Stripe Checkout
     */
    public function createCheckoutSession($items, $userId, $successUrl, $cancelUrl) {
        $lineItems = [];
        
        foreach ($items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item['title'],
                        'description' => substr($item['description'] ?? '', 0, 200),
                        'images' => !empty($item['thumbnail_path']) && strpos($item['thumbnail_path'], 'http') === 0 
                            ? [$item['thumbnail_path']] 
                            : [],
                    ],
                    'unit_amount' => (int)($item['price'] * 100), // Stripe utilise les centimes
                ],
                'quantity' => $item['quantity'] ?? 1,
            ];
        }
        
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'client_reference_id' => (string)$userId,
            'metadata' => [
                'user_id' => (string)$userId,
            ],
        ]);
        
        return $session;
    }
    
    /**
     * Récupère une session de paiement
     */
    public function getSession($sessionId) {
        return Session::retrieve($sessionId);
    }
    
    /**
     * Vérifie si un paiement est réussi
     */
    public function isPaymentSuccessful($sessionId) {
        $session = $this->getSession($sessionId);
        return $session->payment_status === 'paid';
    }
}