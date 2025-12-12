<?php
namespace App\Models;

class Product {
    public $id;
    public $seller_id;
    public $title;
    public $slug;
    public $description;
    public $type;
    public $price;
    public $currency;
    public $file_storage_path;
    public $thumbnail_path;
    public $is_active;
    public $is_featured;
    public $download_limit;
    public $views;
    public $sales;
    public $created_at;
    public $updated_at;
    
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
    
    public function formatPrice() {
        return number_format($this->price, 2) . ' ' . $this->currency;
    }
    
    public function isFree() {
        return floatval($this->price) === 0.0;
    }
}
