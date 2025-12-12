<?php
namespace App\Controllers\Admin;

use App\Services\AuthService;
use App\Database;

class SettingsController {
    private $auth;
    private $db;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->db = Database::getInstance();
    }
    
    public function index() {
        $this->auth->requireRole('admin');
        
        $settings = [];
        $results = $this->db->fetchAll("SELECT * FROM site_settings");
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        return $this->render('admin/settings', ['settings' => $settings]);
    }
    
    public function update() {
        $this->auth->requireRole('admin');
        
        $settingsToUpdate = ['commission_rate', 'payout_threshold', 'maintenance_mode'];
        
        foreach ($settingsToUpdate as $key) {
            if (isset($_POST[$key])) {
                $this->db->query(
                    "INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?",
                    [$key, $_POST[$key], $_POST[$key]]
                );
            }
        }
        
        header('Location: /admin/settings');
        exit;
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
