<?php
namespace App\Models;

class Order {
    public $id;
    public $order_number;
    public $buyer_id;
    public $seller_id;
    public $product_id;
    public $amount;
    public $seller_earnings;
    public $platform_fee;
    public $status;
    public $payment_method;
    public $payment_reference;
    public $created_at;
    public $updated_at;
    
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) $this->$key = $value;
        }
    }
    
    public function isPaid() {
        return $this->status === 'paid';
    }
    
    public function isRefunded() {
        return $this->status === 'refunded';
    }
}
