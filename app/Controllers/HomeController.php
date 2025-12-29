<?php
namespace App\Controllers;

use App\Repositories\ProductRepository;

class HomeController {
    private $productRepo;
    
    public function __construct() {
        $this->productRepo = new ProductRepository();
    }
    
    public function index() {
        // ✨ GESTION DU CHANGEMENT DE LANGUE
        if (isset($_GET['lang'])) {
            $allowedLanguages = ['fr', 'en', 'pt', 'es', 'it', 'de'];
            $lang = $_GET['lang'];
            
            if (in_array($lang, $allowedLanguages)) {
                // Synchronise les deux variables de session
                $_SESSION['language'] = $lang;
                $_SESSION['locale'] = $lang;
                
                // Met à jour I18n
                \App\I18n::setLocale($lang);
            }
            
            // Redirige vers l'URL propre (sans ?lang=)
            header("Location: /");
            exit;
        }
        
        // Initialise I18n pour cette requête
        \App\I18n::init();
        
        // Récupère les produits
        $featured = $this->productRepo->getFeatured(12);
        $recent = $this->productRepo->getAllPaginated(1, 12, ['sort' => 'created_at DESC']);
        
        view('front/home', [
            'featuredProducts' => $featured,
            'latestProducts' => $recent['data'] ?? $recent
        ]);
    }
}