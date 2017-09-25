<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

/**
 * BunnyToolPage.php
 */
abstract class BunnyToolPage
{
    /** @var BunnyUser */
    protected $user;

    /** @var BunnyView */
    protected $view;

    /** @var BunnyStorageInterface */
    protected $storage;

    /** @var string */
    protected $url;

    /**
     * BunnyToolPage constructor.
     *
     * @param BunnyUser             $user
     * @param BunnyView             $view
     * @param BunnyStorageInterface $storage
     * @param string                $url
     */
    public function __construct(BunnyUser $user, BunnyView $view, BunnyStorageInterface $storage, $url)
    {
        $this->user = $user;
        $this->view = $view;
        $this->storage = $storage;
        $this->url = $url;
    }

    public function setGlobalStorage(BunnyStorageInterface $globalStorage)
    {
        $this->globalStorage = $globalStorage;
    }

    /**
     * Return page title
     *
     * @return string
     */
    abstract public function getTitle();

    /**
     * @param array $get  $_GET
     * @param array $post $_POST
     *
     * @return string
     */
    abstract public function run(array $get = [], array $post = []);
}
