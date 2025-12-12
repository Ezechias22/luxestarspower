<?php
namespace App\Services;

use Aws\S3\S3Client;

class StorageService {
    private $s3;
    private $config;
    private $driver;
    
    public function __construct() {
        $this->config = require __DIR__ . '/../../config/config.php';
        $this->driver = $this->config['storage']['driver'] ?? 'local';
        
        // Initialiser S3 seulement si configuré
        if ($this->driver === 's3' && !empty($this->config['storage']['aws_key'])) {
            $this->s3 = new S3Client([
                'version' => 'latest',
                'region' => $this->config['storage']['aws_region'] ?? 'us-east-1',
                'credentials' => [
                    'key' => $this->config['storage']['aws_key'],
                    'secret' => $this->config['storage']['aws_secret']
                ]
            ]);
        }
    }
    
    public function uploadFile($filePath, $key, $visibility = 'private') {
        if ($this->driver === 'local' || !$this->s3) {
            return $this->uploadLocal($filePath, $key);
        }
        
        $bucket = $this->config['storage']['aws_bucket'];
        
        $result = $this->s3->putObject([
            'Bucket' => $bucket,
            'Key' => $key,
            'SourceFile' => $filePath,
            'ACL' => $visibility === 'public' ? 'public-read' : 'private',
            'ContentType' => mime_content_type($filePath)
        ]);
        
        return $result['ObjectURL'] ?? null;
    }
    
    private function uploadLocal($filePath, $key) {
        $uploadDir = __DIR__ . '/../../storage/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $destination = $uploadDir . basename($key);
        copy($filePath, $destination);
        
        return '/storage/uploads/' . basename($key);
    }
    
    public function getSignedUrl($key, $expiryMinutes = 60) {
        if ($this->driver === 'local' || !$this->s3) {
            return '/storage/uploads/' . basename($key);
        }
        
        $bucket = $this->config['storage']['aws_bucket'];
        
        $cmd = $this->s3->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key' => $key
        ]);
        
        $request = $this->s3->createPresignedRequest($cmd, "+$expiryMinutes minutes");
        return (string) $request->getUri();
    }
    
    public function deleteFile($key) {
        if ($this->driver === 'local' || !$this->s3) {
            $filePath = __DIR__ . '/../../storage/uploads/' . basename($key);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            return true;
        }
        
        $bucket = $this->config['storage']['aws_bucket'];
        
        return $this->s3->deleteObject([
            'Bucket' => $bucket,
            'Key' => $key
        ]);
    }
    
    public function generateUploadPresignedUrl($key, $expiryMinutes = 15) {
        if ($this->driver === 'local' || !$this->s3) {
            return [
                'url' => '/api/upload',
                'fields' => ['key' => $key]
            ];
        }
        
        $bucket = $this->config['storage']['aws_bucket'];
        
        $cmd = $this->s3->getCommand('PutObject', [
            'Bucket' => $bucket,
            'Key' => $key
        ]);
        
        $request = $this->s3->createPresignedRequest($cmd, "+$expiryMinutes minutes");
        
        return [
            'url' => (string) $request->getUri(),
            'fields' => []
        ];
    }
    
    public function getCdnUrl($key) {
        if ($this->driver === 'local' || !$this->s3) {
            return '/storage/uploads/' . basename($key);
        }
        
        $cdnUrl = $this->config['storage']['cdn_url'];
        if ($cdnUrl) {
            return rtrim($cdnUrl, '/') . '/' . ltrim($key, '/');
        }
        
        $bucket = $this->config['storage']['aws_bucket'];
        $region = $this->config['storage']['aws_region'] ?? 'us-east-1';
        
        return "https://$bucket.s3.$region.amazonaws.com/$key";
    }
}