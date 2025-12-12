<?php
namespace App;

class I18n {
    private static $locale = 'fr';
    private static $translations = [];
    
    public static function setLocale($locale) {
        $config = require __DIR__ . '/../config/config.php';
        if (in_array($locale, $config['locales']['available'])) {
            self::$locale = $locale;
            $_SESSION['locale'] = $locale;
        }
    }
    
    public static function getLocale() {
        return self::$locale;
    }
    
    public static function load($file) {
        $path = __DIR__ . "/../config/lang/" . self::$locale . "/$file.php";
        if (file_exists($path)) {
            self::$translations = array_merge(self::$translations, require $path);
        }
    }
    
    public static function translate($key, $params = []) {
        $keys = explode('.', $key);
        $value = self::$translations;
        foreach ($keys as $k) {
            if (!isset($value[$k])) return $key;
            $value = $value[$k];
        }
        foreach ($params as $k => $v) {
            $value = str_replace(':' . $k, $v, $value);
        }
        return $value;
    }
}

function __($key, $params = []) {
    return \App\I18n::translate($key, $params);
}
