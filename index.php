<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)


// work aroung a bug in client - cookie domain is case-sensitive
if ($_SERVER['HTTP_HOST'] != strtolower($_SERVER['HTTP_HOST'])) {
    header('Location: http://' . strtolower($_SERVER['HTTP_HOST']) . $_SERVER['REQUEST_URI']);
}

// mask possible unset index warning
define('isLOCAL', @$_SERVER['HTTP_HOST'] == 'ryapp');

//****************************************************************************
// defaults
date_default_timezone_set('UTC');

//****************************************************************************
// include local config file or fallback to default config
if (file_exists(__DIR__ . '/config.php')) {
    $config = include __DIR__ . '/config.php';
} else {
    $config = include __DIR__ . '/config.dist.php';
}

if (!function_exists('ryzom_translate')) {
    // placeholder incase nimetu/ryzom_extra is not included
    function ryzom_translate($key, $lang = 'en') {
        return $key;
    }
}

//****************************************************************************
// start session if one is not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//****************************************************************************
// AppZone auth
$user = [];
if (isset($_GET['user']) && isset($_GET['checksum'])) {
    $data = $_GET['user'];
    $hmac = hash_hmac('sha1', $data, $config['appzone']['app_key']);
    if ($_GET['checksum'] !== $hmac) {
        halt_auth_error('checkum failed');
    }

    // if checksum is not verified, then unserialize is potentially unsafe
    // https://www.owasp.org/index.php/PHP_Object_Injection
    $user = unserialize(base64_decode($data));
    if ($config['appzone']['max_age'] > 0) {
        $ts = $user['timestamp'];
        if (strstr($ts, ' ')) {
            list($usec, $sec) = explode(' ', $ts);
            $ts = (float) $usec + (float) $sec;
        }
        $age = microtime(true) - $ts;
        if ($age > $config['appzone']['max_age']) {
            halt_auth_error(
                'max age (' . $age . ') greater than allowed (' . intval($config['appzone']['max_age']) . ')'
            );
        }
    }

    // user verified, save to session
    $_SESSION['user'] = $user;
    // TODO: client bug, no redirect
    //redirect('http://bunny.bmsite.net/?logged');
}

//****************************************************************************
// Session auth
if (empty($user) && isset($_SESSION['user'])) {
    // debug lang
    if (isLOCAL) {
        $knownLanguages = ['en', 'fr', 'de', 'ru', 'es'];
        if (isset($_GET['lang']) && in_array($_GET['lang'], $knownLanguages)) {
            $_SESSION['user']['lang'] = $_GET['lang'];
        }
    }

    $user = $_SESSION['user'];
}

//****************************************************************************
// new session
if (empty($user)) {
    $user = [];
}

//****************************************************************************
// ensure basic values are set
if (!is_array($user)) {
    $user = array($user);
}
$defaults = ['id' => 0, 'char_name' => 'Guest', 'lang' => 'en'];
foreach ($defaults as $k => $v) {
    if (!isset($user[$k])) {
        $user[$k] = $v;
    }
}
$user['id'] = (int) $user['id'];

// debug lang
if (isLOCAL) {
    $_SESSION['user'] = $user;
}

// language used for _t() translations
define('LANG', $user['lang']);

//****************************************************************************
// launch app
require_once __DIR__ . '/src/BunnyTools.php';

header('Content-Type: text/html; charset=utf-8');

if ($user['id'] > 0) {
    $userStorage = new BunnyFileStorage($user['id'], $config['save-path']);
} else {
    $userStorage = new BunnySessionStorage();
}

$u = new BunnyUser($user, $userStorage);

$storage = new BunnyFileStorage('global', $config['save-path']);
$bunny = new BunnyTools($u, $storage);
echo $bunny->run($_SERVER['QUERY_STRING']);

// save user session
$u->save();

if (isLOCAL) {
    echo '<hr>';
    echo '<pre>POST:';
    var_dump($_POST);
    echo '</pre>';
    echo '<pre>Global:';
    $storage->debug();
    echo '</pre>';
    echo '<hr>';
    $u->debug();
    echo '<hr>';
}

//****************************************************************************
// helper functions
/**
 * Render AppZone auth error and then exit
 *
 * @param $reason
 */
function halt_auth_error($reason)
{
    header('Content-Type: text/html; charset=utf-8');

    $reason = _h($reason);
    echo <<<EOF
<html>
<head>
    <title>ERROR: auth</title>
</head>
<body>
    <h1>ERROR: verifying appzone user data failed</h1>
    <p>{$reason}</p>
</html>
EOF;

    exit;
}

/**
 * Do redirect to specified url
 *
 * @param string $url
 * @param array  $params
 */
function redirect($url = '', array $params = array())
{
    if (empty($url)) {
        $url = '?';
    }

    if (!empty($params)) {
        $query = http_build_query($params);
        if (strstr($url, '?') === false) {
            $url .= '?' . $query;
        } else {
            $url .= '&' . $query;
        }
    }

    header('Location: ' . $url);

    $url = _h($url);
    echo <<<EOF
<html>
<head>
    <title>Redirect</title>
    <meta http-equiv="refresh" content="5; URL=$url">
</head>
<body>
<p>
    If automatic redirect does not work, then please use link below.<br>
    <a href="$url">continue</a>
</p>
</html>
EOF;
    exit;
}

/**
 * Cycle thru array values
 *
 * @param int   $index
 * @param array $values
 *
 * @return mixed
 */
function _cycle($index, array $values)
{
    return $values[$index % count($values)];
}

/**
 * Clamp value between min anx max
 *
 * @param int $value
 * @param int $min
 * @param int $max
 *
 * @return int
 */
function _clamp($value, $min, $max)
{
    return min($max, max($min, $value));
}

/**
 * Translate key into selected language
 *
 * @param string $key
 *
 * @return string
 */
function _t($key)
{
    static $words = null;
    if ($words === null) {
        if (file_exists(__DIR__ . '/words.php')) {
            $words = include_once __DIR__ . '/words.php';
        }
    }

    $lang = defined('LANG') ? LANG : 'en';

    // try requested language
    if (isset($words[$key][$lang])) {
        return $words[$key][$lang];
    }

    // fallback to english
    if ($lang !== 'en' && isset($words[$key]['en'])) {
        return $words[$key]['en'];
    }

    if (isset($words[$key]) && !is_array($words[$key])) {
        return ryzom_translate($words[$key], $lang);
    }

    // local debug to highlight missing translations
    if (isLOCAL) {
        return $key . '[' . $lang . ']';
    }

    // non-existing key, 'cache' it to skip search again for this session
    $words[$key][$lang] = $key;
    return $key;
}

/**
 * Return HTML safe string
 *
 * @param string $str
 *
 * @return string
 */
function _h($str)
{
    if (is_object($str)) {
        throw new \RuntimeException('object');
    }
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Return HTML safe translated string
 *
 * @param string $str
 *
 * @return string
 */
function _th($str)
{
    return _h(_t($str));
}

