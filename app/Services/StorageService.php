<?php
namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;

class StorageService {
    private $cloudinary;
    
    public function __construct() {
        // Utilise CLOUDINARY_URL (format: cloudinary://api_key:api_secret@cloud_name)
        $cloudinaryUrl = $_ENV['CLOUDINARY_URL'] ?? null;
        
        if (!$cloudinaryUrl) {
            throw new \Exception(
                'CLOUDINARY_URL environment variable is missing. ' .
                'Please set it in Railway variables in this format: ' .
                'cloudinary://api_key:api_secret@cloud_name'
            );
        }
        
        // Cloudinary parse automatiquement l'URL
        $this->cloudinary = new Cloudinary($cloudinaryUrl);
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
            // Format: https://res.cloudinary.com/CLOUD_NAME/image/upload/v1234567890/folder/filename.jpg
            preg_match('#/v\d+/(.+)\.[a-z]+$#i', $url, $matches);
            
            if (isset($matches[1])) {
                $publicId = $matches[1];
            } else {
                // Fallback
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