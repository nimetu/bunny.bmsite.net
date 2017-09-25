<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

define('RYAPI_URL', 'http://api.ryzom.com');

// hidden dir - easy to deny web access
define('LOCAL_CACHE', __DIR__ . '/.cache');

if (!file_exists(LOCAL_CACHE) && mkdir(LOCAL_CACHE, 0755, true) !== true) {
    echo 'ERROR: unable to create local cache directory' . PHP_EOL;
    exit;
}

if (file_exists(__DIR__ . '/config.php')) {
    $config = include __DIR__ . '/config.php';
} else {
    $config = include __DIR__ . '/config.dist.php';
}
define('LOGGER', $config['debug'] === true);

$guilds = load_guilds();
if ($guilds !== false) {
    generate_outposts($guilds);
}

//****************************************************************************
// read guilds.xml
function load_guilds()
{
    $xml = false;

    $localGuildsXmlFile = LOCAL_CACHE . '/guilds.xml';
    if (file_exists($localGuildsXmlFile)) {
        $xml = simplexml_load_file($localGuildsXmlFile);
        $expires = $xml->cache['expire'] - time();
        logger('Guilds file exists, expires in %d seconds.', $expires);
        if ($expires < 0) {
            $xml = false;
        }
    }

    if ($xml === false) {
        $xml = update_guilds($localGuildsXmlFile);
    }

    return $xml;
}

function update_guilds($localGuildsXmlFile)
{
    $guildsXml = RYAPI_URL . '/guilds.php';
    $tmp = file_get_contents($guildsXml);
    $xml = simplexml_load_string($tmp);
    if ($xml !== false) {
        $xml->asXML($localGuildsXmlFile);
    }
    return $xml;
}

//****************************************************************************
// extract outpost info from guilds
function generate_outposts(SimpleXMLElement $guilds)
{
    $jsonFile = LOCAL_CACHE . '/outposts.json';
    $mtime = file_exists($jsonFile) ? filemtime($jsonFile) : 0;
    if ($guilds->cache['created'] < $mtime) {
        logger("- do not generate outposts");
        return;
    }
    logger("+ generate outposts");

    // this sets sorting on webpage
    $outposts = [
        // Desert
        'fyros_outpost_14' => [],
        'fyros_outpost_13' => [],
        'fyros_outpost_09' => [],
        'fyros_outpost_25' => [],
        'fyros_outpost_04' => [],
        'fyros_outpost_27' => [],
        'fyros_outpost_28' => [],
        // Forest
        'matis_outpost_15' => [],
        'matis_outpost_07' => [],
        'matis_outpost_17' => [],
        'matis_outpost_30' => [],
        'matis_outpost_03' => [],
        'matis_outpost_24' => [],
        'matis_outpost_27' => [],
        // Lakes
        'tryker_outpost_06' => [],
        'tryker_outpost_24' => [],
        'tryker_outpost_10' => [],
        'tryker_outpost_16' => [],
        'tryker_outpost_22' => [],
        'tryker_outpost_29' => [],
        'tryker_outpost_31' => [],
        // Jungle
        'zorai_outpost_08' => [],
        'zorai_outpost_10' => [],
        'zorai_outpost_22' => [],
        'zorai_outpost_29' => [],
        'zorai_outpost_02' => [],
        'zorai_outpost_15' => [],
        'zorai_outpost_16' => [],
    ];
    foreach ($guilds->guild as $guild) {
        if (!isset($guild->outposts)) {
            continue;
        }
        $gid = (string) $guild->gid;
        foreach ($guild->outposts->outpost as $outpost) {
            $k = (string) $outpost;
            $outposts[$k] = [
                'gid' => $gid,
                'name' => (string) $guild->name,
                'icon' => (string) $guild->icon,
            ];
        }
    }

    file_put_contents($jsonFile, json_encode($outposts, JSON_PRETTY_PRINT));
}

//****************************************************************************
// print text to console
function logger($msg)
{
    if (LOGGER) {
        $args = func_get_args();
        array_shift($args);
        vprintf($msg . "\n", $args);
    }
}
