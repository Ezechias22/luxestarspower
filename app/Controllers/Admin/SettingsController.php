<?php
namespace App\Controllers\Admin;

use App\Services\AuthService;

class SettingsController {
    private $auth;

    public function __construct() {
        $this->auth = new AuthService();
    }

    public function index() {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        view('admin/settings/index', [
            'user' => $user
        ]);
    }

    public function update() {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        // TODO: Implémenter la sauvegarde des paramètres
        // Pour l'instant, juste un message de succès

        $_SESSION['flash_success'] = 'Paramètres mis à jour avec succès';
        header('Location: /admin/parametres');
        exit;
    }
}