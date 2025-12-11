<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
    }
    
    public function createCheckoutSession(array $orderData): string
    {
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($orderData['currency']),
                    'product_data' => [
                        'name' => $orderData['product_name'],
                        'description' => $orderData['product_description'] ?? '',
                    ],
                    'unit_amount' => (int)($orderData['amount'] * 100), // Montant en centimes
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $_ENV['APP_URL'] . '/checkout/complete?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $_ENV['APP_URL'] . '/checkout/' . $orderData['product_id'],
            'client_reference_id' => $orderData['order_number'],
            'customer_email' => $orderData['buyer_email'],
            'metadata' => [
                'order_id' => $orderData['order_id'],
                'product_id' => $orderData['product_id'],
                'seller_id' => $orderData['seller_id'],
            ],
        ]);
        
        return $session->url;
    }
    
    public function verifyWebhook(string $payload, string $signature): object
    {
        $webhookSecret = $_ENV['STRIPE_WEBHOOK_SECRET'];
        
        return Webhook::constructEvent($payload, $signature, $webhookSecret);
    }
    
    public function refund(string $paymentIntentId, float $amount = null): object
    {
        $data = ['payment_intent' => $paymentIntentId];
        
        if ($amount !== null) {
            $data['amount'] = (int)($amount * 100);
        }
        
        return \Stripe\Refund::create($data);
    }
}
