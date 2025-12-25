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

        // Redirige vers Cloudinary avec le bon nom de fichier
        $this->downloadFile($product);
    }

    private function downloadFile($product) {
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

        // Ajoute le paramètre fl_attachment pour forcer le téléchargement avec le bon nom
        if (strpos($cloudinaryUrl, '/raw/upload/') !== false) {
            $cloudinaryUrl = str_replace('/raw/upload/', '/raw/upload/fl_attachment:' . urlencode($filename) . '/', $cloudinaryUrl);
        } elseif (strpos($cloudinaryUrl, '/image/upload/') !== false) {
            $cloudinaryUrl = str_replace('/image/upload/', '/image/upload/fl_attachment:' . urlencode($filename) . '/', $cloudinaryUrl);
        } elseif (strpos($cloudinaryUrl, '/video/upload/') !== false) {
            $cloudinaryUrl = str_replace('/video/upload/', '/video/upload/fl_attachment:' . urlencode($filename) . '/', $cloudinaryUrl);
        }

        header('Location: ' . $cloudinaryUrl);
        exit;
    }

    private function getFileExtension($url, $type) {
        // Essaie d'extraire l'extension de l'URL
        $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        
        if (!empty($extension) && $extension !== 'php') {
            return $extension;
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

    // Méthodes existantes pour le système de tokens (si utilisées)
    public function download($params) {
        // Garde l'ancienne méthode pour compatibilité
        $token = $params['token'] ?? null;
        
        if (!$token) {
            http_response_code(404);
            die('Token invalide');
        }

        // TODO: Implémenter le téléchargement par token si nécessaire
        http_response_code(501);
        die('Fonctionnalité non implémentée');
    }

    public function stream($params) {
        // Garde l'ancienne méthode pour compatibilité
        http_response_code(501);
        die('Fonctionnalité non implémentée');
    }
}