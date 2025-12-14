<?php
namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;

class StorageService {
    private $cloudinary;
    
    public function __construct() {
        // Récupère les variables d'environnement depuis $_SERVER (Railway)
        $cloudName = $_SERVER['CLOUDINARY_CLOUD_NAME'] ?? $_ENV['CLOUDINARY_CLOUD_NAME'] ?? getenv('CLOUDINARY_CLOUD_NAME') ?? null;
        $apiKey = $_SERVER['CLOUDINARY_API_KEY'] ?? $_ENV['CLOUDINARY_API_KEY'] ?? getenv('CLOUDINARY_API_KEY') ?? null;
        $apiSecret = $_SERVER['CLOUDINARY_API_SECRET'] ?? $_ENV['CLOUDINARY_API_SECRET'] ?? getenv('CLOUDINARY_API_SECRET') ?? null;
        
        // Vérifie que toutes les variables sont présentes
        if (!$cloudName || !$apiKey || !$apiSecret) {
            throw new \Exception(
                'Cloudinary configuration missing. Please set CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, and CLOUDINARY_API_SECRET in Railway variables. ' .
                'Current values: cloud_name=' . ($cloudName ? 'SET' : 'NULL') . 
                ', api_key=' . ($apiKey ? 'SET' : 'NULL') . 
                ', api_secret=' . ($apiSecret ? 'SET' : 'NULL')
            );
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
            // Extrait le public_id depuis l'URL Cloudinary
            if (preg_match('#/([^/]+)/([^/]+)\.[a-z]+$#i', $url, $matches)) {
                $publicId = $matches[1] . '/' . $matches[2];
            } else {
                $parts = explode('/', $url);
                $filename = end($parts);
                $publicId = pathinfo($filename, PATHINFO_FILENAME);
            }
            
            $upload = new UploadApi();
            return $upload->destroy($publicId);
        } catch (\Exception $e) {
            // Ignore les erreurs de suppression
            return false;
        }
    }
}