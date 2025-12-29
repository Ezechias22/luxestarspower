<?php
namespace App;

class I18n {
    private static $locale = null;
    private static $translations = [];

    public static function init() {
        // Initialise la locale depuis la session ou utilise 'fr' par défaut
        if (isset($_SESSION['locale'])) {
            self::$locale = $_SESSION['locale'];
        } elseif (isset($_SESSION['language'])) {
            // Support de l'ancien système (pour compatibilité)
            self::$locale = $_SESSION['language'];
            $_SESSION['locale'] = $_SESSION['language'];
        } else {
            self::$locale = 'fr';
            $_SESSION['locale'] = 'fr';
            $_SESSION['language'] = 'fr';
        }
    }

    public static function setLocale($locale) {
        // Support de toutes les langues : FR, EN, ES, DE, IT, PT
        $supported = ['fr', 'en', 'es', 'de', 'it', 'pt'];
        
        if (in_array($locale, $supported)) {
            self::$locale = $locale;
            $_SESSION['locale'] = $locale;
            $_SESSION['language'] = $locale; // Synchronisation avec l'ancien système
            
            // Force le rechargement des traductions pour la nouvelle langue
            self::$translations = [];
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
        
        // Récupère la traduction ou retourne la clé si non trouvée
        $translation = self::$translations[$locale][$key] ?? $key;
        
        // Remplace les paramètres dynamiques (ex: :name, :count)
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
            // Fallback vers le français si la langue n'existe pas
            $fallbackFile = __DIR__ . "/../lang/fr.php";
            if (file_exists($fallbackFile)) {
                self::$translations[$locale] = require $fallbackFile;
                error_log("I18n: Language file not found for '{$locale}', using French as fallback");
            } else {
                // Si même le français n'existe pas, tableau vide
                self::$translations[$locale] = [];
                error_log("I18n: No language files found, using empty translations");
            }
        }
    }
    
    /**
     * Retourne toutes les langues supportées
     */
    public static function getSupportedLocales() {
        return ['fr', 'en', 'es', 'de', 'it', 'pt'];
    }
    
    /**
     * Vérifie si une langue est supportée
     */
    public static function isSupported($locale) {
        return in_array($locale, self::getSupportedLocales());
    }
}