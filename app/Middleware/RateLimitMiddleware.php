<?php

namespace App\Middlewares;

use Predis\Client as Redis;

class RateLimitMiddleware
{
    private static ?Redis $redis = null;
    
    private static function getRedis(): ?Redis
    {
        if (self::$redis === null) {
            try {
                self::$redis = new Redis([
                    'scheme' => 'tcp',
                    'host' => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
                    'port' => $_ENV['REDIS_PORT'] ?? 6379,
                    'password' => $_ENV['REDIS_PASSWORD'] ?? null,
                ]);
            } catch (\Exception $e) {
                // Redis non disponible, on continue sans rate limiting
                return null;
            }
        }
        
        return self::$redis;
    }
    
    public static function handle(): bool
    {
        if (($_ENV['RATE_LIMIT_ENABLED'] ?? 'true') !== 'true') {
            return true;
        }
        
        $redis = self::getRedis();
        if ($redis === null) {
            // Pas de Redis, on laisse passer
            return true;
        }
        
        $ip = self::getClientIp();
        $route = $_SERVER['REQUEST_URI'];
        
        // Configuration par route
        $limits = [
            '/connexion' => [
                'max' => (int)($_ENV['RATE_LIMIT_LOGIN_ATTEMPTS'] ?? 5),
                'decay' => (int)($_ENV['RATE_LIMIT_LOGIN_DECAY_MINUTES'] ?? 15) * 60,
            ],
            'default' => [
                'max' => (int)($_ENV['RATE_LIMIT_API_REQUESTS'] ?? 60),
                'decay' => (int)($_ENV['RATE_LIMIT_API_DECAY_MINUTES'] ?? 1) * 60,
            ],
        ];
        
        $limit = $limits[$route] ?? $limits['default'];
        
        $key = "rate_limit:{$ip}:{$route}";
        $attempts = (int)$redis->get($key);
        
        if ($attempts >= $limit['max']) {
            $ttl = $redis->ttl($key);
            
            header('X-RateLimit-Limit: ' . $limit['max']);
            header('X-RateLimit-Remaining: 0');
            header('X-RateLimit-Reset: ' . (time() + $ttl));
            header('Retry-After: ' . $ttl);
            
            http_response_code(429);
            die(json_encode([
                'error' => 'Trop de tentatives. Veuillez réessayer dans ' . ceil($ttl / 60) . ' minutes.',
                'retry_after' => $ttl
            ]));
        }
        
        // Incrémenter le compteur
        $redis->incr($key);
        if ($attempts === 0) {
            $redis->expire($key, $limit['decay']);
        }
        
        $remaining = max(0, $limit['max'] - $attempts - 1);
        header('X-RateLimit-Limit: ' . $limit['max']);
        header('X-RateLimit-Remaining: ' . $remaining);
        
        return true;
    }
    
    private static function getClientIp(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR'
        ];
        
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                return trim($ip);
            }
        }
        
        return '0.0.0.0';
    }
}
