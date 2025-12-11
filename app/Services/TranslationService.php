<?php

namespace App\Services;

class TranslationService
{
    private $locale;
    private $fallbackLocale = 'en';
    private $translations = [];
    
    public function __construct($locale = null)
    {
        $this->locale = $locale ?? current_locale();
        $this->loadTranslations($this->locale);
    }
    
    public function get($key, $params = [], $locale = null)
    {
        $locale = $locale ?? $this->locale;
        
        if (!isset($this->translations[$locale])) {
            $this->loadTranslations($locale);
        }
        
        $keys = explode('.', $key);
        $value = $this->translations[$locale] ?? [];
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                // Try fallback
                $value = $this->translations[$this->fallbackLocale] ?? [];
                foreach ($keys as $fk) {
                    if (!isset($value[$fk])) {
                        return $key;
                    }
                    $value = $value[$fk];
                }
                break;
            }
            $value = $value[$k];
        }
        
        // Replace parameters
        foreach ($params as $param => $val) {
            $value = str_replace(':' . $param, $val, $value);
        }
        
        return $value;
    }
    
    private function loadTranslations($locale)
    {
        $path = base_path("locales/{$locale}/messages.php");
        
        if (file_exists($path)) {
            $this->translations[$locale] = require $path;
        } else {
            $this->translations[$locale] = [];
        }
    }
    
    public function setLocale($locale)
    {
        $this->locale = $locale;
        set_locale($locale);
    }
    
    public function getLocale()
    {
        return $this->locale;
    }
}
