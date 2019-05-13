<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

interface BunnyStorageInterface
{
    public function clear($key);

    public function set($key, $value);

    public function get($key, $default = null);

    public function save();
}

/**
 * Class BunnySessionStorage
 */
class BunnySessionStorage implements BunnyStorageInterface
{
    /**
     * @param string $key
     */
    public function clear($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }

        return $default;
    }

    public function save()
    {
        return;
    }

    public function debug()
    {
        echo '<ul style="text-align: left;">';
        echo '<li>SESSION</li>';
        echo '</ul>';
        echo '<pre>';
        var_dump($_SESSION);
        echo '</pre>';
    }
}

/**
 * Class BunnyFileStorage
 */
class BunnyFileStorage implements BunnyStorageInterface
{

    /**
     * Storage object key
     *
     * @var string
     */
    private $key;

    /**
     * Storage save path
     *
     * @var string
     */
    private $path;

    /**
     * Percistent data
     *
     * @var array
     */
    private $data;

    /**
     * TRUE if data was changed
     *
     * @var bool
     */
    private $dirty;

    /**
     * BunnyFileStorage constructor.
     *
     * @param string $key
     * @param string $path
     */
    public function __construct($key, $path)
    {
        $this->key = (string) $key;
        $this->path = $path;

        $this->data = null;
        $this->dirty = false;
    }

    /** {@inheritdoc} */
    public function clear($key)
    {
        if ($this->data === null) {
            $this->doLoad();
        }

        $this->dirty = true;
        unset($this->data[$key]);
    }

    /** {@inheritdoc} */
    public function set($key, $value)
    {
        if ($this->data === null) {
            $this->doLoad();
        }

        $this->dirty = true;
        $this->data[$key] = $value;
    }

    protected function doLoad()
    {
        if ($this->data !== null) {
            return;
        }

        $file = $this->getDataFile();
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $content = substr($content, strpos($content, "\n") + 1);
            $this->data = json_decode($content, true);
        }

        if ($this->data === null) {
            $this->data = [];
        }
    }

    /**
     * Return full path for user data file
     *
     * @return string
     */
    protected function getDataFile()
    {
        $dir = $this->path;
        $dir .= '/' . substr($this->key, -1);
        $dir .= '/' . $this->key . '.php';
        return $dir;
    }

    /** {@inheritdoc} */
    public function get($key, $default = null)
    {
        if ($this->data === null) {
            $this->doLoad();
        }
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return $default;
    }

    /**
     * Save data to file
     *
     * @throws \RuntimeException when creating path fails
     */
    public function save()
    {
        if ($this->dirty && $this->data !== null) {
            $this->doSave();
        }
    }

    protected function doSave()
    {
        $this->dirty = false;

        $file = $this->getDataFile();
        $dir = dirname($file);
        if (!file_exists($dir) && (@mkdir($dir, 0755, true) === false)) {
            throw new \RuntimeException("ERROR: unable to save user data");
        }

        $content = '<?php exit;?' . ">\n";
        $content .= json_encode($this->data, JSON_PRETTY_PRINT);
        file_put_contents($file, $content, LOCK_EX);
    }

    public function debug()
    {
        $this->doLoad();
        echo '<ul style="text-align: left;">';
        echo '<li>Path:' . _h($this->path) . '</li>';
        echo '<li>File:' . _h($this->getDataFile()) . '</li>';
        if (file_exists($this->getDataFile())) {
            echo '<li>Size:' . filesize($this->getDataFile()) . '</li>';
            echo '<li>MTime:' . filemtime($this->getDataFile()) . '</li>';
        } else {
            echo '<li>File: not created</li>';
        }
        echo '</li>';
        echo '</ul>';
        echo '<pre>';
        var_dump($this->data);
        echo '</pre>';
    }
}
