<?php
namespace App\Controllers;

use App\I18n;

class LanguageController {
    public function switch($params) {
        $locale = $params['locale'] ?? 'fr';
        $availableLocales = ['fr', 'en', 'es', 'de', 'it'];

        if (in_array($locale, $availableLocales)) {
            $_SESSION['locale'] = $locale;
            I18n::setLocale($locale);
        }

        // Redirect back
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header('Location: ' . $referer);
        exit;
    }
}