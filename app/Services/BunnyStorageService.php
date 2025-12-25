<?php
namespace App\Services;

class BunnyStorageService {
    private $storageZone = 'luxestarspower';
    private $apiKey = '7b505ce7-e098-46ce-a9be864516a9-8beb-49c4';
    private $hostname = 'storage.bunnycdn.com';
    private $cdnUrl = 'https://luxestarspower.b-cdn.net'; // CHANGE SI TON PULL ZONE A UN NOM DIFFÉRENT

    /**
     * Upload un fichier vers BunnyCDN Storage
     * @param string $filePath Chemin du fichier temporaire
     * @param string $folder Dossier dans le storage
     * @return string URL du fichier uploadé
     */
    public function uploadFile($filePath, $folder = 'products') {
        try {
            error_log("BunnyStorage: Uploading file to folder '$folder'");
            error_log("File path: " . $filePath);
            error_log("File exists: " . (file_exists($filePath) ? 'YES' : 'NO'));

            if (!file_exists($filePath)) {
                throw new \Exception("File does not exist at path: " . $filePath);
            }

            // Détecte le type MIME
            $mimeType = mime_content_type($filePath);
            error_log("File MIME type: " . $mimeType);

            // Génère un nom unique pour le fichier
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            if (empty($extension)) {
                // Devine l'extension selon le MIME
                $mimeToExt = [
                    'application/pdf' => 'pdf',
                    'video/mp4' => 'mp4',
                    'application/zip' => 'zip',
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                ];
                $extension = $mimeToExt[$mimeType] ?? 'bin';
            }
            
            $filename = uniqid() . '_' . time() . '.' . $extension;
            
            // Chemin complet dans le storage
            $remotePath = $folder . '/' . $filename;

            // Upload via API
            $url = "https://{$this->hostname}/{$this->storageZone}/{$remotePath}";
            
            error_log("Uploading to: " . $url);
            
            $fileContent = file_get_contents($filePath);
            $fileSize = strlen($fileContent);
            
            error_log("File size: " . $fileSize . " bytes");
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'AccessKey: ' . $this->apiKey,
                'Content-Type: application/octet-stream',
                'Content-Length: ' . $fileSize
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 600); // 10 minutes pour les gros fichiers

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new \Exception("CURL error: " . $error);
            }
            
            curl_close($ch);

            if ($httpCode !== 201) {
                error_log("BunnyCDN upload failed. HTTP Code: $httpCode. Response: $response");
                throw new \Exception("Upload failed with HTTP code: $httpCode");
            }

            $publicUrl = $this->cdnUrl . '/' . $remotePath;
            
            error_log("Upload successful. URL: " . $publicUrl);

            return $publicUrl;

        } catch (\Exception $e) {
            error_log("BunnyStorage upload error: " . $e->getMessage());
            throw new \Exception("Upload failed: " . $e->getMessage());
        }
    }

    /**
     * Upload une image
     * @param string $filePath Chemin du fichier temporaire
     * @param string $folder Dossier dans le storage
     * @return string URL de l'image
     */
    public function uploadImage($filePath, $folder = 'thumbnails') {
        try {
            error_log("BunnyStorage: Uploading IMAGE to folder '$folder'");
            
            if (!file_exists($filePath)) {
                throw new \Exception("Image file does not exist at path: " . $filePath);
            }

            $fileSize = filesize($filePath);
            error_log("Image size: " . $fileSize . " bytes");

            // Génère un nom unique pour l'image
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $extension;
            
            $remotePath = $folder . '/' . $filename;

            // Upload via API
            $url = "https://{$this->hostname}/{$this->storageZone}/{$remotePath}";
            
            $fileContent = file_get_contents($filePath);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'AccessKey: ' . $this->apiKey,
                'Content-Type: ' . mime_content_type($filePath),
                'Content-Length: ' . strlen($fileContent)
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new \Exception("CURL error: " . $error);
            }
            
            curl_close($ch);

            if ($httpCode !== 201) {
                error_log("BunnyCDN image upload failed. HTTP Code: $httpCode");
                throw new \Exception("Image upload failed with HTTP code: $httpCode");
            }

            $publicUrl = $this->cdnUrl . '/' . $remotePath;
            
            error_log("✅ Image upload successful. URL: " . $publicUrl);

            return $publicUrl;

        } catch (\Exception $e) {
            error_log("❌ BunnyStorage uploadImage error: " . $e->getMessage());
            throw new \Exception("Image upload failed: " . $e->getMessage());
        }
    }

    /**
     * Supprime un fichier de BunnyCDN Storage
     * @param string $url URL complète du fichier
     */
    public function deleteFile($url) {
        try {
            error_log("BunnyStorage: Deleting file from URL: " . $url);

            // Extrait le chemin depuis l'URL
            // Format: https://luxestarspower.b-cdn.net/products/filename.pdf
            $path = str_replace($this->cdnUrl . '/', '', $url);

            $deleteUrl = "https://{$this->hostname}/{$this->storageZone}/{$path}";
            
            error_log("Delete URL: " . $deleteUrl);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $deleteUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'AccessKey: ' . $this->apiKey
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            error_log("Delete result. HTTP Code: $httpCode");

            return $httpCode === 200;

        } catch (\Exception $e) {
            error_log("BunnyStorage delete error: " . $e->getMessage());
            return false;
        }
    }
}