<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Services\BunnyStorageService;
use App\Repositories\ProductRepository;
use App\Repositories\SubscriptionRepository;
use App\Database;

class SellerProductController {
    private $auth;
    private $productRepo;
    private $storage;
    private $subscriptionRepo;
    private $db;

    public function __construct() {
        $this->auth = new AuthService();
        $this->productRepo = new ProductRepository();
        $this->storage = new BunnyStorageService();
        $this->subscriptionRepo = new SubscriptionRepository();
        $this->db = Database::getInstance();
    }

    public function index() {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            header('Location: /vendre');
            exit;
        }

        $products = $this->productRepo->getBySeller($user['id']);

        view('seller/products/index', [
            'user' => $user,
            'products' => $products
        ]);
    }

    public function create() {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            header('Location: /vendre');
            exit;
        }

        // ========== VÉRIFICATION DE LA LIMITE DE PRODUITS ==========
        $canAdd = $this->subscriptionRepo->canAddProduct($user['id']);
        $subscription = $this->subscriptionRepo->getUserActiveSubscription($user['id']);
        
        // Compte les produits actuels
        $currentProductsCount = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM products WHERE seller_id = ? AND deleted_at IS NULL",
            [$user['id']]
        );
        
        $productsCount = $currentProductsCount['count'] ?? 0;
        $maxProducts = $subscription['max_products'] ?? 0;
        
        if (!$canAdd) {
            $_SESSION['error'] = "⚠️ Vous avez atteint la limite de $maxProducts produits de votre plan actuel. Passez à un plan supérieur pour débloquer !";
            header('Location: /vendeur/abonnement');
            exit;
        }
        // ========== FIN VÉRIFICATION ==========

        view('seller/products/create', [
            'user' => $user,
            'productsCount' => $productsCount,
            'maxProducts' => $maxProducts,
            'subscription' => $subscription
        ]);
    }

    public function store() {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        // ========== VÉRIFICATION DE LA LIMITE DE PRODUITS ==========
        $canAdd = $this->subscriptionRepo->canAddProduct($user['id']);
        $subscription = $this->subscriptionRepo->getUserActiveSubscription($user['id']);
        
        if (!$canAdd) {
            $maxProducts = $subscription['max_products'] ?? 0;
            $_SESSION['flash_error'] = "⚠️ Vous avez atteint la limite de $maxProducts produits. Passez à un plan supérieur !";
            header('Location: /vendeur/abonnement');
            exit;
        }
        // ========== FIN VÉRIFICATION ==========

        try {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;
            $type = $_POST['type'] ?? '';
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

            error_log("=== PRODUCT CREATION START ===");
            error_log("Title: " . $title);
            error_log("Type: " . $type);

            // Validation
            if (empty($title) || empty($description) || empty($type)) {
                throw new \Exception('Tous les champs requis doivent être remplis');
            }

            // NOUVEAU : Logge les infos du fichier
            error_log("File upload info:");
            error_log("  isset: " . (isset($_FILES['file']) ? 'YES' : 'NO'));
            if (isset($_FILES['file'])) {
                error_log("  name: " . ($_FILES['file']['name'] ?? 'N/A'));
                error_log("  size: " . ($_FILES['file']['size'] ?? 'N/A'));
                error_log("  error: " . ($_FILES['file']['error'] ?? 'N/A'));
                error_log("  tmp_name: " . ($_FILES['file']['tmp_name'] ?? 'N/A'));
            }

            if (!isset($_FILES['file'])) {
                throw new \Exception('Le fichier du produit est requis');
            }

            if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'Le fichier dépasse upload_max_filesize (500M)',
                    UPLOAD_ERR_FORM_SIZE => 'Le fichier dépasse MAX_FILE_SIZE du formulaire',
                    UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement uploadé',
                    UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été uploadé',
                    UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant',
                    UPLOAD_ERR_CANT_WRITE => 'Échec de l\'écriture sur le disque',
                    UPLOAD_ERR_EXTENSION => 'Une extension PHP a arrêté l\'upload',
                ];
                
                $errorCode = $_FILES['file']['error'];
                $errorMsg = $errorMessages[$errorCode] ?? "Erreur d'upload inconnue (code: $errorCode)";
                
                error_log("❌ File upload error: " . $errorMsg);
                throw new \Exception($errorMsg);
            }

            // Génère un slug unique
            $slug = $this->generateUniqueSlug($title);
            error_log("Generated slug: " . $slug);

            // Upload du fichier principal vers BunnyCDN
            error_log("Uploading main file...");
            $fileUrl = $this->storage->uploadFile($_FILES['file']['tmp_name'], 'products');
            error_log("Main file URL: " . $fileUrl);

            // Upload de la miniature si présente, sinon null
            $thumbnailUrl = null;
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                error_log("Thumbnail file detected. Size: " . $_FILES['thumbnail']['size'] . " bytes");
                error_log("Thumbnail name: " . $_FILES['thumbnail']['name']);
                error_log("Uploading thumbnail to BunnyCDN...");
                
                $thumbnailUrl = $this->storage->uploadImage($_FILES['thumbnail']['tmp_name'], 'thumbnails');
                
                error_log("Thumbnail upload result: " . ($thumbnailUrl ?? 'NULL'));
                
                if ($thumbnailUrl) {
                    error_log("✅ Thumbnail uploaded successfully: " . $thumbnailUrl);
                } else {
                    error_log("❌ Thumbnail upload returned NULL");
                }
            } else {
                $fileError = $_FILES['thumbnail']['error'] ?? 'No file uploaded';
                error_log("Thumbnail upload skipped. Error code: " . $fileError);
            }

            // Prépare les données
            $productData = [
                'seller_id' => $user['id'],
                'title' => $title,
                'slug' => $slug,
                'description' => $description,
                'type' => $type,
                'price' => $price,
                'currency' => 'USD',
                'file_storage_path' => $fileUrl,
                'thumbnail_path' => $thumbnailUrl,
                'is_featured' => $isFeatured,
                'is_active' => 1
            ];

            // ✨ NOUVEAU : Données de promotion
            $productData['is_on_sale'] = isset($_POST['is_on_sale']) ? 1 : 0;
            $productData['original_price'] = !empty($_POST['original_price']) ? floatval($_POST['original_price']) : null;
            $productData['discount_percentage'] = !empty($_POST['discount_percentage']) ? intval($_POST['discount_percentage']) : 0;
            $productData['sale_starts_at'] = !empty($_POST['sale_starts_at']) ? $_POST['sale_starts_at'] : null;
            $productData['sale_ends_at'] = !empty($_POST['sale_ends_at']) ? $_POST['sale_ends_at'] : null;

            // ✨ NOUVEAU : Objectif de ventes
            $productData['sales_goal'] = !empty($_POST['sales_goal']) ? intval($_POST['sales_goal']) : 100;

            error_log("Product data prepared:");
            error_log(print_r($productData, true));

            // Crée le produit
            $product = $this->productRepo->create($productData);
            
            error_log("Product created with ID: " . ($product['id'] ?? 'UNKNOWN'));
            error_log("=== PRODUCT CREATION END ===");

            $_SESSION['flash_success'] = 'Produit ajouté avec succès ! 🎉';

        } catch (\Exception $e) {
            error_log("❌ Product creation error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $_SESSION['flash_error'] = $e->getMessage();
            header('Location: /vendeur/produits/nouveau');
            exit;
        }

        header('Location: /vendeur/produits');
        exit;
    }

    public function edit($params) {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            header('Location: /vendre');
            exit;
        }

        $id = $params['id'] ?? null;
        if (!$id) {
            http_response_code(404);
            die('Produit non trouvé');
        }

        $product = $this->productRepo->findById($id);

        if (!$product || ($product['seller_id'] != $user['id'] && $user['role'] !== 'admin')) {
            http_response_code(404);
            die('Produit non trouvé');
        }

        view('seller/products/edit', [
            'user' => $user,
            'product' => $product
        ]);
    }

    public function update($params) {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $id = $params['id'] ?? null;
        if (!$id) {
            $_SESSION['flash_error'] = 'ID produit invalide';
            header('Location: /vendeur/produits');
            exit;
        }

        try {
            $product = $this->productRepo->findById($id);

            if (!$product || ($product['seller_id'] != $user['id'] && $user['role'] !== 'admin')) {
                throw new \Exception('Produit non trouvé');
            }

            $data = [
                'title' => $_POST['title'] ?? $product['title'],
                'description' => $_POST['description'] ?? $product['description'],
                'price' => $_POST['price'] ?? $product['price'],
                'type' => $_POST['type'] ?? $product['type'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0
            ];

            // ✨ NOUVEAU : Données de promotion
            $data['is_on_sale'] = isset($_POST['is_on_sale']) ? 1 : 0;
            $data['original_price'] = !empty($_POST['original_price']) ? floatval($_POST['original_price']) : null;
            $data['discount_percentage'] = !empty($_POST['discount_percentage']) ? intval($_POST['discount_percentage']) : 0;
            $data['sale_starts_at'] = !empty($_POST['sale_starts_at']) ? $_POST['sale_starts_at'] : null;
            $data['sale_ends_at'] = !empty($_POST['sale_ends_at']) ? $_POST['sale_ends_at'] : null;

            // ✨ NOUVEAU : Objectif de ventes
            $data['sales_goal'] = !empty($_POST['sales_goal']) ? intval($_POST['sales_goal']) : 100;

            // Upload nouvelle miniature si fournie
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                error_log("Updating product thumbnail for product ID: " . $id);
                
                $newThumbnailUrl = $this->storage->uploadImage($_FILES['thumbnail']['tmp_name'], 'thumbnails');
                
                error_log("New thumbnail URL: " . ($newThumbnailUrl ?? 'NULL'));
                
                $data['thumbnail_path'] = $newThumbnailUrl;

                // Supprime l'ancienne miniature de BunnyCDN si elle existe
                if (!empty($product['thumbnail_path']) && strpos($product['thumbnail_path'], 'b-cdn.net') !== false) {
                    try {
                        $this->storage->deleteFile($product['thumbnail_path']);
                        error_log("Old thumbnail deleted: " . $product['thumbnail_path']);
                    } catch (\Exception $e) {
                        error_log("Failed to delete old thumbnail: " . $e->getMessage());
                    }
                }
            }

            $this->productRepo->update($id, $data);

            $_SESSION['flash_success'] = 'Produit mis à jour avec succès ! ✅';

        } catch (\Exception $e) {
            error_log("Product update error: " . $e->getMessage());
            $_SESSION['flash_error'] = $e->getMessage();
        }

        header('Location: /vendeur/produits');
        exit;
    }

    public function destroy($params) {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $id = $params['id'] ?? null;
        if (!$id) {
            $_SESSION['flash_error'] = 'ID produit invalide';
            header('Location: /vendeur/produits');
            exit;
        }

        try {
            $product = $this->productRepo->findById($id);

            if (!$product || ($product['seller_id'] != $user['id'] && $user['role'] !== 'admin')) {
                throw new \Exception('Produit non trouvé');
            }

            // Supprime les fichiers de BunnyCDN
            if (!empty($product['file_storage_path']) && strpos($product['file_storage_path'], 'b-cdn.net') !== false) {
                try {
                    $this->storage->deleteFile($product['file_storage_path']);
                } catch (\Exception $e) {
                    error_log("Failed to delete main file: " . $e->getMessage());
                }
            }

            if (!empty($product['thumbnail_path']) && strpos($product['thumbnail_path'], 'b-cdn.net') !== false) {
                try {
                    $this->storage->deleteFile($product['thumbnail_path']);
                } catch (\Exception $e) {
                    error_log("Failed to delete thumbnail: " . $e->getMessage());
                }
            }

            $this->productRepo->delete($id);

            $_SESSION['flash_success'] = 'Produit supprimé avec succès ! 🗑️';

        } catch (\Exception $e) {
            error_log("Product deletion error: " . $e->getMessage());
            $_SESSION['flash_error'] = $e->getMessage();
        }

        header('Location: /vendeur/produits');
        exit;
    }

    private function generateUniqueSlug($title) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

        $originalSlug = $slug;
        $counter = 1;

        while ($this->productRepo->findBySlug($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}