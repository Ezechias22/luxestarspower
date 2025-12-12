<?php
namespace App\Middlewares;

class RateLimitMiddleware {
    private static $limits = [];
    
    public static function check($key, $maxAttempts = 5, $decayMinutes = 15) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $identifier = $key . ':' . $ip;
        
        if (!isset(self::$limits[$identifier])) {
            self::$limits[$identifier] = [
                'attempts' => 0,
                'reset_at' => time() + ($decayMinutes * 60)
            ];
        }
        
        $limit = &self::$limits[$identifier];
        
        if (time() > $limit['reset_at']) {
            $limit['attempts'] = 0;
            $limit['reset_at'] = time() + ($decayMinutes * 60);
        }
        
        $limit['attempts']++;
        
        if ($limit['attempts'] > $maxAttempts) {
            http_response_code(429);
            die(json_encode([
                'error' => 'Too many attempts. Please try again later.',
                'retry_after' => $limit['reset_at'] - time()
            ]));
        }
        
        return true;
    }
    
    public static function clear($key) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $identifier = $key . ':' . $ip;
        unset(self::$limits[$identifier]);
    }
}
