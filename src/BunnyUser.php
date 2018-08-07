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

