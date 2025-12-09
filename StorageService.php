<?php

namespace App\Services;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class StorageService
{
    private $s3Client;
    private $bucket;
    
    public function __construct()
    {
        $this->bucket = env('AWS_BUCKET');
        
        $config = [
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ];
        
        // Support for S3-compatible services (DigitalOcean Spaces, etc.)
        if (env('AWS_ENDPOINT')) {
            $config['endpoint'] = env('AWS_ENDPOINT');
        }
        
        $this->s3Client = new S3Client($config);
    }
    
    public function generateSignedUploadUrl($key, $contentType, $expiresIn = 3600)
    {
        try {
            $cmd = $this->s3Client->getCommand('PutObject', [
                'Bucket' => $this->bucket,
                'Key' => $key,
                'ContentType' => $contentType,
                'ACL' => 'private',
            ]);
            
            $request = $this->s3Client->createPresignedRequest($cmd, "+{$expiresIn} seconds");
            
            return [
                'success' => true,
                'url' => (string) $request->getUri(),
                'key' => $key,
                'expires_in' => $expiresIn,
            ];
        } catch (AwsException $e) {
            logger()->error('Failed to generate signed upload URL', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function generateSignedDownloadUrl($key, $expiresIn = 3600, $filename = null)
    {
        try {
            $params = [
                'Bucket' => $this->bucket,
                'Key' => $key,
            ];
            
            if ($filename) {
                $params['ResponseContentDisposition'] = 'attachment; filename="' . $filename . '"';
            }
            
            $cmd = $this->s3Client->getCommand('GetObject', $params);
            $request = $this->s3Client->createPresignedRequest($cmd, "+{$expiresIn} seconds");
            
            return [
                'success' => true,
                'url' => (string) $request->getUri(),
                'expires_in' => $expiresIn,
            ];
        } catch (AwsException $e) {
            logger()->error('Failed to generate signed download URL', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function uploadFile($localPath, $key)
    {
        try {
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
                'SourceFile' => $localPath,
                'ACL' => 'private',
            ]);
            
            return [
                'success' => true,
                'key' => $key,
                'etag' => $result['ETag'],
            ];
        } catch (AwsException $e) {
            logger()->error('File upload failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function deleteFile($key)
    {
        try {
            $this->s3Client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);
            
            return ['success' => true];
        } catch (AwsException $e) {
            logger()->error('File deletion failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function fileExists($key)
    {
        return $this->s3Client->doesObjectExist($this->bucket, $key);
    }
    
    public function getFileSize($key)
    {
        try {
            $result = $this->s3Client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);
            
            return $result['ContentLength'];
        } catch (AwsException $e) {
            return null;
        }
    }
    
    public function copyFile($sourceKey, $destinationKey)
    {
        try {
            $this->s3Client->copyObject([
                'Bucket' => $this->bucket,
                'CopySource' => $this->bucket . '/' . $sourceKey,
                'Key' => $destinationKey,
            ]);
            
            return ['success' => true];
        } catch (AwsException $e) {
            logger()->error('File copy failed', [
                'source' => $sourceKey,
                'destination' => $destinationKey,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function streamFile($key)
    {
        try {
            $result = $this->s3Client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);
            
            return $result['Body'];
        } catch (AwsException $e) {
            logger()->error('File stream failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
}
