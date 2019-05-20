<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

require_once __DIR__ . '/BunnyStorage.php';

/**
 * BunnyUser.php
 */
class BunnyUser implements BunnyStorageInterface
{
    private $user;
    private $storage;

    protected $defaults = [
        'id' => 0,
        'char_name' => 'Guest',
        'race' => 'fyros',
        'civilisation' => 'neutral',
        'cult' => 'neutral',
        'civ' => 'neutral',
        'organization' => '',
        'guild_id' => 0,
        'guild_icon' => 17,
        'guild_name' => '',
        'grade' => 'Member',
        'lang' => 'en',
        // local info
        '@debug' => false,
        '@tester' => false,
        '@translator' => false,
    ];

    /**
     * BunnyUser constructor.
     *
     * @param array                 $user
     * @param BunnyStorageInterface $storage
     */
    public function __construct(array $user, BunnyStorageInterface $storage)
    {
        $this->user = $user;
        $this->storage = $storage;

        // normalize user array
        foreach ($this->defaults as $k => $v) {
            if (!isset($this->user[$k])) {
                $this->user[$k] = $v;
            }
        }
        $this->user['id'] = (int) $this->user['id'];
        $this->user['guild_id'] = (int) $this->user['guild_id'];
    }

    /** @return int */
    public function getId()
    {
        return $this->user['id'];
    }

    /** @return string */
    public function getCharName()
    {
        return $this->user['char_name'];
    }

    /** @return bool */
    public function isShowDebug()
    {
        return $this->user['@debug'];
    }

    /** @return bool */
    public function isTester()
    {
        return $this->user['@tester'];
    }

    /** @return bool */
    public function isTranslator()
    {
        return $this->user['@translator'];
    }

    /**
     * Delete character api info from cache
     */
    public function clearCharacterApi()
    {
        $this->storage->clear('api_cache');
    }

    /**
     * Fetches and caches Ryzom API character xml
     *
     * $apikey, if set, will override apikey from cache.
     *
     * @param string $apikey - if not set, then use one from cache
     * @param bool $offline - if true, return only cached version, no updates from api server
     *
     * @return \SimpleXMLElement | bool
     */
    public function getCharacterApi($apikey = '', $offline = false)
    {
        $cacheKey = 'api_cache';
        $cache = $this->get($cacheKey, ['xml' => '']);
        if (!is_array($cache) || !isset($cache['xml'])) {
            $cache = ['xml' => ''];
        }

        $xml = false;
        $now = time();
        if (!empty($cache['xml'])) {
            $xml = simplexml_load_string($cache['xml']);
        }

        if ($xml !== false && (int)$xml['cached_until'] > $now) {
            return $xml;
        }

        // return whatever was in cache
        if ($offline) {
            return $xml;
        }

        if (empty($apikey) && empty($xml['apikey'])) {
            return false;
        }

        if (empty($apikey)) {
            $apikey = (string)$xml['apikey'];
        }

        // fetch new
        $data = ryzom_character_api($apikey);
        if ($data === false || empty($data[$apikey])) {
            // connection error? return cached version
            return $xml;
        }
        $xml = $data[$apikey];

        // if request was success, then save back to cache
        if ($xml !== false && empty($xml->error)) {
            $cache['xml'] = $xml->asXML();
            $this->set($cacheKey, $cache);
        }

        return $xml;
    }



    /**
     * @see BunnyStorageInterface::clear
     */
    public function clear($key)
    {
        return $this->storage->clear($key);
    }

    /**
     * @see BunnyStorageInterface::get
     */
    public function get($key, $default = null)
    {
        return $this->storage->get($key, $default);
    }

    /**
     * @see BunnyStorageInterface::set
     */
    public function set($key, $value)
    {
        return $this->storage->set($key, $value);
    }

    /**
     * @see BunnyStorageInterface::save
     */
    public function save()
    {
        return $this->storage->save();
    }

    public function debug()
    {
        echo '<pre>';
        echo 'USER:';
        var_dump($this->user);
        echo 'STORAGE:';
        $this->storage->debug();
        echo '</pre>';
    }
}

