<?php
// require_once __DIR__.'/vendor/autoload.php';

return [
    // enable debug, tester, translator, anonymous access
    // only enable for local testing, use debug_names for production
    'debug' => false,
    // lowercase names who should receive debug info when debug is false
    // use in production
    'debug_names' => array(),
    // lowercase names who should see experimental features.
    // allows anonymous access if 'guest' is in this list (guest info is kept in session only)
    'tester_names' => array(),
    // lowercase names who should see missing translations
    'translator_names' => array(),
    // which languages to show translators. fallback is always 'en'
    'languages' => array('en', 'fr', 'de', 'ru', 'es'),
    // Ryzom AppZone security check
    // app_key - random string that ryzom server uses to sign user data, must kept private
    // app_url - app url set in AppZone
    // max_age - maximum age for signed user data
    'appzone' => array(
        'app_key' => 'bunny-tools',
        'app_url' => 'http://localhost/index.php',
        'max_age' => 60,
    ),
    // where to save user data, ie occupation timers
    'save-path' => __DIR__ . '/save',
    // session keys to store user and lang info
    // sess_lang_key is only used for translators
    'sess_user_key' => 'bunny_user',
    'sess_lang_key' => 'bunny_lang',
];
