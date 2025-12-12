<?php
namespace App\Services;

use Aws\S3\S3Client;

class StorageService {
    private $s3;
    private $config;
    
    public function __construct() {
        $this->config = require __DIR__ . '/../../config/config.php';
        
        $this->s3 = new S3Client([
            'version' => 'latest',
            'region' => $this->config['storage']['s3']['region'],
            'credentials' => [
                'key' => $this->config['storage']['s3']['key'],
                'secret' => $this->config['storage']['s3']['secret']
            ],
            'endpoint' => $this->config['storage']['s3']['endpoint'] ?? null
        ]);
    }
    
    public function uploadFile($filePath, $key, $visibility = 'private') {
        $bucket = $this->config['storage']['s3']['bucket'];
        
        $result = $this->s3->putObject([
            'Bucket' => $bucket,
            'Key' => $key,
            'SourceFile' => $filePath,
            'ACL' => $visibility === 'public' ? 'public-read' : 'private',
            'ContentType' => mime_content_type($filePath)
        ]);
        
        return $result['ObjectURL'] ?? null;
    }
    
    public function getSignedUrl($key, $expiryMinutes = 60) {
        $bucket = $this->config['storage']['s3']['bucket'];
        
        $cmd = $this->s3->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key' => $key
        ]);
        
        $request = $this->s3->createPresignedRequest($cmd, "+$expiryMinutes minutes");
        return (string) $request->getUri();
    }
    
    public function deleteFile($key) {
        $bucket = $this->config['storage']['s3']['bucket'];
        
        return $this->s3->deleteObject([
            'Bucket' => $bucket,
            'Key' => $key
        ]);
    }
    
    public function generateUploadPresignedUrl($key, $expiryMinutes = 15) {
        $bucket = $this->config['storage']['s3']['bucket'];
        
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
        $cdnUrl = $this->config['storage']['cdn_url'];
        if ($cdnUrl) {
            return rtrim($cdnUrl, '/') . '/' . ltrim($key, '/');
        }
        
        $bucket = $this->config['storage']['s3']['bucket'];
        $region = $this->config['storage']['s3']['region'];
        return "https://$bucket.s3.$region.amazonaws.com/$key";
    }
}
