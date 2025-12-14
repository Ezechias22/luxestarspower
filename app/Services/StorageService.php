<?php
namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;

class StorageService {
    private $cloudinary;
    
    public function __construct() {
        // Récupère les variables d'environnement
        $cloudName = getenv('CLOUDINARY_CLOUD_NAME') ?: $_ENV['CLOUDINARY_CLOUD_NAME'] ?? null;
        $apiKey = getenv('CLOUDINARY_API_KEY') ?: $_ENV['CLOUDINARY_API_KEY'] ?? null;
        $apiSecret = getenv('CLOUDINARY_API_SECRET') ?: $_ENV['CLOUDINARY_API_SECRET'] ?? null;
        
        // Vérifie que toutes les variables sont présentes
        if (!$cloudName || !$apiKey || !$apiSecret) {
            throw new \Exception('Cloudinary configuration missing. Please set CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, and CLOUDINARY_API_SECRET in Railway variables.');
        }
        
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
            ]
        ]);
    }
    
    /**
     * Upload un fichier vers Cloudinary
     * @param string $filePath Chemin du fichier temporaire
     * @param string $folder Dossier Cloudinary
     * @return string URL du fichier uploadé
     */
    public function uploadFile($filePath, $folder = 'products') {
        try {
            $upload = new UploadApi();
            $result = $upload->upload($filePath, [
                'folder' => $folder,
                'resource_type' => 'auto'
            ]);
            
            return $result['secure_url'];
        } catch (\Exception $e) {
            throw new \Exception("Upload failed: " . $e->getMessage());
        }
    }
    
    /**
     * Upload une image avec optimisation
     * @param string $filePath Chemin du fichier temporaire
     * @param string $folder Dossier Cloudinary
     * @return string URL de l'image optimisée
     */
    public function uploadImage($filePath, $folder = 'thumbnails') {
        try {
            $upload = new UploadApi();
            $result = $upload->upload($filePath, [
                'folder' => $folder,
                'transformation' => [
                    'width' => 800,
                    'height' => 600,
                    'crop' => 'limit',
                    'quality' => 'auto:good'
                ]
            ]);
            
            return $result['secure_url'];
        } catch (\Exception $e) {
            throw new \Exception("Upload failed: " . $e->getMessage());
        }
    }
    
    /**
     * Supprime un fichier de Cloudinary
     * @param string $url URL complète du fichier
     */
    public function deleteFile($url) {
        try {
            // Extrait le public_id depuis l'URL
            $parts = explode('/', $url);
            $filename = end($parts);
            $publicId = pathinfo($filename, PATHINFO_FILENAME);
            
            $upload = new UploadApi();
            return $upload->destroy($publicId);
        } catch (\Exception $e) {
            throw new \Exception("Delete failed: " . $e->getMessage());
        }
    }
}
