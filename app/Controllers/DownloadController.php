<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;

class DownloadController {
    private $auth;
    private $orderRepo;
    private $productRepo;

    public function __construct() {
        $this->auth = new AuthService();
        $this->orderRepo = new OrderRepository();
        $this->productRepo = new ProductRepository();
    }

    public function downloadProduct($params) {
        $user = $this->auth->requireAuth();
        $productId = $params['id'] ?? null;

        if (!$productId) {
            http_response_code(404);
            die('Produit non trouvé');
        }

        // Récupère le produit
        $product = $this->productRepo->findById($productId);
        
        if (!$product) {
            http_response_code(404);
            die('Produit non trouvé');
        }

        // Vérifie que l'utilisateur a acheté ce produit
        $orders = $this->orderRepo->getByUser($user['id']);
        $hasAccess = false;

        foreach ($orders as $order) {
            if ($order['payment_status'] === 'paid') {
                $items = $this->orderRepo->getOrderItems($order['id']);
                foreach ($items as $item) {
                    if ($item['product_id'] == $productId) {
                        $hasAccess = true;
                        break 2;
                    }
                }
            }
        }

        if (!$hasAccess) {
            http_response_code(403);
            die('Vous n\'avez pas accès à ce fichier');
        }

        // Télécharge et sert le fichier avec le bon nom
        $this->serveFile($product);
    }

    private function serveFile($product) {
        $cloudinaryUrl = $product['file_storage_path'];
        
        if (empty($cloudinaryUrl)) {
            http_response_code(404);
            die('Fichier non disponible');
        }

        // Génère un nom de fichier propre
        $filename = $this->sanitizeFilename($product['title']);
        
        // Détecte l'extension
        $extension = $this->getFileExtension($cloudinaryUrl, $product['type']);
        $filename .= '.' . $extension;

        // Détecte le type MIME
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'zip' => 'application/zip',
            'mp4' => 'video/mp4',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

        // Télécharge le fichier depuis Cloudinary
        $fileContent = @file_get_contents($cloudinaryUrl);
        
        if ($fileContent === false) {
            http_response_code(500);
            die('Impossible de télécharger le fichier');
        }

        // Envoie les headers pour le téléchargement
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($fileContent));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Envoie le contenu du fichier
        echo $fileContent;
        exit;
    }

    private function getFileExtension($url, $type) {
        // Essaie d'extraire l'extension de l'URL
        $path = parse_url($url, PHP_URL_PATH);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        if (!empty($extension) && strlen($extension) <= 5) {
            return strtolower($extension);
        }

        // Sinon, devine selon le type
        $typeExtensions = [
            'ebook' => 'pdf',
            'video' => 'mp4',
            'image' => 'jpg',
            'course' => 'zip',
            'file' => 'pdf'
        ];

        return $typeExtensions[$type] ?? 'pdf';
    }

    private function sanitizeFilename($filename) {
        // Remplace les caractères spéciaux
        $filename = preg_replace('/[^a-zA-Z0-9-_\s]/', '-', $filename);
        // Remplace les espaces multiples par un seul tiret
        $filename = preg_replace('/\s+/', '-', $filename);
        // Limite la longueur
        $filename = substr($filename, 0, 100);
        // Retire les tirets en début/fin
        $filename = trim($filename, '-');
        
        return $filename;
    }

    // Méthodes existantes pour le système de tokens
    public function download($params) {
        $token = $params['token'] ?? null;
        
        if (!$token) {
            http_response_code(404);
            die('Token invalide');
        }

        http_response_code(501);
        die('Fonctionnalité non implémentée');
    }

    public function stream($params) {
        http_response_code(501);
        die('Fonctionnalité non implémentée');
    }
}