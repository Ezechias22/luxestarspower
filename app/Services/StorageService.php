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
            error_log("StorageService: Uploading file to folder '$folder'");
            error_log("File path: " . $filePath);
            error_log("File exists: " . (file_exists($filePath) ? 'YES' : 'NO'));
            
            if (!file_exists($filePath)) {
                throw new \Exception("File does not exist at path: " . $filePath);
            }
            
            $upload = new UploadApi();
            $result = $upload->upload($filePath, [
                'folder' => $folder,
                'resource_type' => 'auto'
            ]);
            
            error_log("Upload successful. URL: " . $result['secure_url']);
            
            return $result['secure_url'];
        } catch (\Exception $e) {
            error_log("StorageService upload error: " . $e->getMessage());
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
            error_log("StorageService: Uploading IMAGE to folder '$folder'");
            error_log("Image path: " . $filePath);
            error_log("Image exists: " . (file_exists($filePath) ? 'YES' : 'NO'));
            
            if (!file_exists($filePath)) {
                error_log("❌ Image file does not exist at: " . $filePath);
                throw new \Exception("Image file does not exist at path: " . $filePath);
            }
            
            $fileSize = filesize($filePath);
            error_log("Image size: " . $fileSize . " bytes");
            
            if ($fileSize === 0) {
                error_log("❌ Image file is empty");
                throw new \Exception("Image file is empty");
            }
            
            $upload = new UploadApi();
            
            error_log("Calling Cloudinary upload API...");
            
            $result = $upload->upload($filePath, [
                'folder' => $folder,
                'resource_type' => 'image',
                'transformation' => [
                    'width' => 800,
                    'height' => 600,
                    'crop' => 'limit',
                    'quality' => 'auto:good'
                ]
            ]);
            
            if (!isset($result['secure_url'])) {
                error_log("❌ Cloudinary result missing secure_url");
                error_log("Full result: " . print_r($result, true));
                throw new \Exception("Cloudinary upload succeeded but no secure_url returned");
            }
            
            error_log("✅ Image upload successful. URL: " . $result['secure_url']);
            
            return $result['secure_url'];
            
        } catch (\Exception $e) {
            error_log("❌ StorageService uploadImage error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new \Exception("Image upload failed: " . $e->getMessage());
        }
    }
    
    /**
     * Supprime un fichier de Cloudinary
     * @param string $url URL complète du fichier
     */
    public function deleteFile($url) {
        try {
            error_log("StorageService: Deleting file from URL: " . $url);
            
            // Extrait le public_id depuis l'URL Cloudinary
            // Format: https://res.cloudinary.com/CLOUD_NAME/image/upload/v1234567890/folder/filename.jpg
            preg_match('#/v\d+/(.+)\.[a-z]+$#i', $url, $matches);
            
            if (isset($matches[1])) {
                $publicId = $matches[1];
            } else {
                // Fallback: essaie d'extraire depuis le dernier segment
                $parts = explode('/', parse_url($url, PHP_URL_PATH));
                $lastSegment = end($parts);
                $publicId = pathinfo($lastSegment, PATHINFO_FILENAME);
                
                // Si on a un dossier dans l'URL
                if (count($parts) > 2) {
                    $folder = $parts[count($parts) - 2];
                    $publicId = $folder . '/' . $publicId;
                }
            }
            
            error_log("Deleting with public_id: " . $publicId);
            
            $upload = new UploadApi();
            $result = $upload->destroy($publicId);
            
            error_log("Delete result: " . print_r($result, true));
            
            return $result;
        } catch (\Exception $e) {
            error_log("StorageService delete error: " . $e->getMessage());
            // Ignore les erreurs de suppression
            return false;
        }
    }
}