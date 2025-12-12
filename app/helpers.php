<?php

if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false) {
            return $default;
        }
        
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }
        
        return $value;
    }
}

if (!function_exists('config')) {
    function config($key, $default = null) {
        static $config = [];
        
        $keys = explode('.', $key);
        $file = array_shift($keys);
        
        if (!isset($config[$file])) {
            $path = __DIR__ . "/../config/{$file}.php";
            if (file_exists($path)) {
                $config[$file] = require $path;
            } else {
                return $default;
            }
        }
        
        $value = $config[$file];
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
}

if (!function_exists('storage_path')) {
    function storage_path($path = '') {
        return __DIR__ . '/../storage' . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('public_path')) {
    function public_path($path = '') {
        return __DIR__ . '/../public' . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('base_path')) {
    function base_path($path = '') {
        return __DIR__ . '/..' . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('url')) {
    function url($path = '', $secure = null) {
        $base = config('app.url');
        if ($secure === null) {
            $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        }
        
        if (!$secure && strpos($base, 'https://') === 0) {
            $base = str_replace('https://', 'http://', $base);
        }
        
        return rtrim($base, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset($path) {
        $cdn = env('CDN_URL');
        if ($cdn) {
            return rtrim($cdn, '/') . '/assets/' . ltrim($path, '/');
        }
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('route')) {
    function route($name, $params = []) {
        global $router;
        if ($router && method_exists($router, 'generateUrl')) {
            return $router->generateUrl($name, $params);
        }
        return '/';
    }
}

if (!function_exists('redirect')) {
    function redirect($url, $statusCode = 302) {
        header("Location: $url", true, $statusCode);
        exit;
    }
}

if (!function_exists('back')) {
    function back() {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        redirect($referer);
    }
}

if (!function_exists('old')) {
    function old($key, $default = '') {
        return $_SESSION['_old_input'][$key] ?? $default;
    }
}

if (!function_exists('session')) {
    function session($key = null, $default = null) {
        if ($key === null) {
            return $_SESSION;
        }
        
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $_SESSION[$k] = $v;
            }
            return;
        }
        
        return $_SESSION[$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    function flash($key, $value = null) {
        if ($value === null) {
            $val = $_SESSION["_flash_{$key}"] ?? null;
            unset($_SESSION["_flash_{$key}"]);
            return $val;
        }
        $_SESSION["_flash_{$key}"] = $value;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token() {
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field() {
        $token = csrf_token();
        $name = config('security.csrf_token_name', '_csrf_token');
        return "<input type='hidden' name='{$name}' value='{$token}'>";
    }
}

if (!function_exists('auth')) {
    function auth() {
        static $auth;
        if (!$auth) {
            $auth = new \App\Services\AuthService();
        }
        return $auth;
    }
}

if (!function_exists('user')) {
    function user() {
        return auth()->user();
    }
}

if (!function_exists('trans')) {
    function trans($key, $params = [], $locale = null) {
        return \App\I18n::translate($key, $params);
    }
}

if (!function_exists('__')) {
    function __($key, $params = []) {
        return \App\I18n::translate($key, $params);
    }
}

if (!function_exists('money_format')) {
    function money_format($amount, $currency = null) {
        $currency = $currency ?? config('app.default_currency', 'USD');
        
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CNY' => '¥',
            'AED' => 'د.إ',
        ];
        
        $symbol = $symbols[$currency] ?? $currency . ' ';
        return $symbol . number_format($amount, 2, '.', ',');
    }
}

if (!function_exists('sanitize')) {
    function sanitize($data) {
        if (is_array($data)) {
            return array_map('sanitize', $data);
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('slug')) {
    function slug($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        
        if (empty($text)) {
            return 'n-a';
        }
        
        return $text;
    }
}

if (!function_exists('generateOrderNumber')) {
    function generateOrderNumber() {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}

if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('dd')) {
    function dd(...$vars) {
        foreach ($vars as $var) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }
        die();
    }
}

if (!function_exists('logger')) {
    function logger() {
        static $logger;
        if (!$logger) {
            $logger = new \Monolog\Logger('app');
            $logger->pushHandler(
                new \Monolog\Handler\StreamHandler(
                    storage_path('logs/app-' . date('Y-m-d') . '.log'),
                    \Monolog\Level::Debug
                )
            );
        }
        return $logger;
    }
}

if (!function_exists('abort')) {
    function abort($code = 404, $message = '') {
        http_response_code($code);
        
        if ($code == 404) {
            $message = $message ?: 'Page not found';
        } elseif ($code == 403) {
            $message = $message ?: 'Forbidden';
        } elseif ($code == 401) {
            $message = $message ?: 'Unauthorized';
        } elseif ($code == 500) {
            $message = $message ?: 'Internal Server Error';
        }
        
        if (config('app.debug')) {
            die("Error $code: $message");
        }
        
        // Load error view
        if (file_exists(__DIR__ . "/../views/errors/{$code}.php")) {
            require __DIR__ . "/../views/errors/{$code}.php";
        } else {
            die($message);
        }
        exit;
    }
}

if (!function_exists('validateEmail')) {
    function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('now')) {
    function now() {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('uuid')) {
    function uuid() {
        return \Ramsey\Uuid\Uuid::uuid4()->toString();
    }
}

if (!function_exists('bcrypt')) {
    function bcrypt($password) {
        return password_hash($password, PASSWORD_ARGON2ID);
    }
}

if (!function_exists('view')) {
    function view($template, $data = []) {
        extract($data);
        
        $path = __DIR__ . "/../views/{$template}.php";
        
        if (!file_exists($path)) {
            throw new Exception("View not found: {$template}");
        }
        
        ob_start();
        require $path;
        $output = ob_get_clean();
        
        echo $output;
        return $output;
    }
}

if (!function_exists('json_response')) {
    function json_response($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

if (!function_exists('is_admin')) {
    function is_admin() {
        $user = user();
        return $user && $user->role === 'admin';
    }
}

if (!function_exists('is_seller')) {
    function is_seller() {
        $user = user();
        return $user && ($user->role === 'seller' || $user->role === 'admin');
    }
}

if (!function_exists('current_locale')) {
    function current_locale() {
        return $_SESSION['locale'] ?? config('app.locale', 'fr');
    }
}

if (!function_exists('set_locale')) {
    function set_locale($locale) {
        $supported = config('app.supported_locales', ['fr', 'en']);
        if (in_array($locale, $supported)) {
            $_SESSION['locale'] = $locale;
        }
    }
}