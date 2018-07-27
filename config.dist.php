<?php
// require_once __DIR__.'/vendor/autoload.php';

return [
    'debug' => false,
    // Ryzom AppZone security check
    // app_key - random string that ryzom server uses to sign user data, must kept private
    // app_url - app url set in AppZone
    // max_age - maximum age for signed user data
    'appzone' => [
        'app_key' => 'bunny-tools',
        'app_url' => 'http://localhost/index.php',
        'max_age' => 60,
    ],
    // where to save user data, ie occupation timers
    'save-path' => __DIR__ . '/save',
    'remotehost' => 'ryapp',
];
