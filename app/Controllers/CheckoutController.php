<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Services\PaymentService;
use App\Repositories\{ProductRepository, OrderRepository};
use App\Services\DownloadService;

class CheckoutController {
    private $auth;
    private $payment;
    private $productRepo;
    private $orderRepo;
    private $download;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->payment = new PaymentService();
        $this->productRepo = new ProductRepository();
        $this->orderRepo = new OrderRepository();
        $this->download = new DownloadService();
    }
    
    public function create() {
        $user = $this->auth->requireAuth();
        $productId = $_POST['product_id'] ?? 0;
        
        $product = $this->productRepo->findById($productId);
        if (!$product) {
            return json_encode(['error' => 'Product not found']);
        }
        
        $fees = $this->payment->calculateFees($product->price);
        
        $order = $this->orderRepo->create([
            'buyer_id' => $user->id,
            'seller_id' => $product->seller_id,
            'product_id' => $product->id,
            'amount' => $product->price,
            'seller_earnings' => $fees['seller_earnings'],
            'platform_fee' => $fees['platform_fee'],
            'status' => 'pending',
            'payment_method' => $_POST['payment_method'] ?? 'stripe'
        ]);
        
        if ($_POST['payment_method'] === 'stripe') {
            $intent = $this->payment->createStripePaymentIntent($product->price, $product->currency, [
                'order_id' => $order->id,
                'product_id' => $product->id
            ]);
            
            return json_encode([
                'client_secret' => $intent->client_secret,
                'order_id' => $order->id
            ]);
        }
        
        return json_encode(['error' => 'Invalid payment method']);
    }
    
    public function complete() {
        $orderId = $_POST['order_id'] ?? 0;
        $paymentReference = $_POST['payment_reference'] ?? '';
        
        $order = $this->orderRepo->findById($orderId);
        if (!$order) {
            return json_encode(['error' => 'Order not found']);
        }
        
        $this->orderRepo->updateStatus($order->id, 'paid', $paymentReference);
        $this->productRepo->incrementSales($order->product_id);
        
        $token = $this->download->createDownloadToken($order->id, $order->buyer_id, $order->product_id);
        
        return json_encode([
            'success' => true,
            'download_url' => '/download/' . $token
        ]);
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
