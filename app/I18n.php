<?php
namespace App;

class I18n {
    private static $locale = null;
    private static $translations = [];

    public static function init() {
        // Initialise la locale depuis la session ou utilise 'fr' par défaut
        if (isset($_SESSION['locale'])) {
            self::$locale = $_SESSION['locale'];
        } else {
            self::$locale = 'fr';
            $_SESSION['locale'] = 'fr';
        }
    }

    public static function setLocale($locale) {
        $supported = ['fr', 'en', 'es', 'de', 'it'];
        if (in_array($locale, $supported)) {
            self::$locale = $locale;
            $_SESSION['locale'] = $locale;
        }
    }

    public static function getLocale() {
        if (self::$locale === null) {
            self::init();
        }
        return self::$locale;
    }

    public static function translate($key, $params = []) {
        $locale = self::getLocale();
        
        // Charge les traductions si nécessaire
        if (!isset(self::$translations[$locale])) {
            self::loadTranslations($locale);
        }
        
        $translation = self::$translations[$locale][$key] ?? $key;
        
        // Remplace les paramètres
        foreach ($params as $k => $v) {
            $translation = str_replace(":{$k}", $v, $translation);
        }
        
        return $translation;
    }
    
    private static function loadTranslations($locale) {
        $file = __DIR__ . "/../lang/{$locale}.php";
        
        if (file_exists($file)) {
            self::$translations[$locale] = require $file;
        } else {
            self::$translations[$locale] = [];
        }
    }
}

// Fonction helper globale
function __($key, $params = []) {
    return \App\I18n::translate($key, $params);
}