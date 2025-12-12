<?php
namespace App\Models;

class User {
    public $id;
    public $name;
    public $email;
    public $password_hash;
    public $role;
    public $bio;
    public $avatar_url;
    public $currency;
    public $settings;
    public $is_active;
    public $email_verified_at;
    public $created_at;
    public $updated_at;
    
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
    
    public function isAdmin() {
        return $this->role === 'admin';
    }
    
    public function isSeller() {
        return $this->role === 'seller' || $this->role === 'admin';
    }
    
    public function toArray() {
        return get_object_vars($this);
    }
}
