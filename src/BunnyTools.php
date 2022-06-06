<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

require_once __DIR__ . '/BunnyUser.php';
require_once __DIR__ . '/BunnyMenu.php';
require_once __DIR__ . '/BunnyView.php';
require_once __DIR__ . '/BunnyStorage.php';

/**
 * BunnyTools
 */
class BunnyTools
{
    /** @var BunnyUser */
    private $user;

    /**
     * BunnyTools constructor.
     *
     * @param BunnyUser             $user
     * @param BunnyStorageInterface $storage
     */
    public function __construct(BunnyUser $user, BunnyStorageInterface $storage)
    {
        $this->user = $user;
        $this->storage = $storage;
        $this->menu = new BunnyMenu;

        $uri = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));
        // strip script name
        if (substr($uri, -1) !== '/') {
            $uri = dirname($uri);
        }

        $baseUrl = 'http'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 's' : '').'://' . $_SERVER['SERVER_NAME'] . $uri;
        if (substr($baseUrl, -1) == '/') {
            $baseUrl = substr($baseUrl, 0, -1);
        }

        $this->view = new BunnyView($this->menu, $baseUrl);
        $this->view->setIngame(strstr($_SERVER['HTTP_USER_AGENT'], 'Ryzom') !== false);
        $this->view->setCharName($this->user->getCharName());

        $this->menu->setDebug($this->user->isTester());
        $this->view->setDebug($this->user->isShowDebug());
        $this->view->setTranslator($this->user->isTranslator());
    }

    /**
     * Set available languages for translators
     *
     * @param string[]
     */
    public function setLanguages(array $langs) {
        $this->view->setLanguages($langs);
    }

    /** @return bool */
    public function isLoggedIn()
    {
        return $this->user->getId() > 0;
    }

    /**
     * dispatcher
     *
     * @param string $query
     *
     * @return string
     */
    public function run($query)
    {
        $uri = [];
        parse_str($query, $uri);
        if (empty($uri['action'])) {
            $uri['action'] = 'index';
        }

        // allow anonymous access if 'Guest' is marked as tester in config
        if (!$this->user->isTester() && !$this->isLoggedIn()) {
            $uri['action'] = 'index';
        }

        $this->menu->setActiveMenuIndex($uri['action']);

        $routes = [
            // Calculators
            'gearCalc' => 'GearCalculator',
            'statsCalc' => 'StatsCalculator',
            'xpCalc' => 'XpCalculator',
            'matsArmorCalc' => 'MatsCalculatorArmor',
            'matsJewelCalc' => 'MatsCalculatorJewel',
            'matsMeleeCalc' => 'MatsCalculatorMelee',
            'matsRangeCalc' => 'MatsCalculatorRange',
            'matsShieldCalc' => 'MatsCalculatorShield',
            // Reminders
            'rpjobReminder' => 'RpJobsReminder',
            'shopReminder' => 'ShopReminder',
            // Encyclipedia
            'mobData' => 'MobData',
            'outpostRegistry' => 'OutpostRegistry',
            'outpostResources' => 'OutpostResources',
            'resourceInfo' => 'ResourceInfo',
        ];

        if ($uri['action'] == 'index') {
            $ret = $this->indexAction();
        } elseif (isset($uri['action']) && isset($routes[$uri['action']])) {
            $class = $routes[$uri['action']];
            $classFile = __DIR__ . '/Tools/' . $class . '.php';
            if (!isset($classFile)) {
                $ret = $this->errorControllerAction();
            } else {
                require_once $classFile;
                /** @var BunnyToolPage $page */
                $page = new $class($this->user, $this->view, $this->storage, '?action=' . $uri['action']);
                $ret = $page->run($_GET, $_POST);

                $title = $page->getTitle();
                $this->view->setHeader($title);
                $this->view->appendTitle($title);
            }
        } else {
            $ret = $this->errorAction();
        }

        return $this->render($ret);
    }

    /** @return string */
    public function indexAction()
    {
        $html = '<h2>BunnyTools</h2>';
        $html .= '<img src="images/ArmoredBunny.png">';
        $html .= '<h3>Welcome ' . _h($this->user->getCharName()) . '</h3>';
        if (!$this->isLoggedIn()) {
            $html .= '<p style="color:red;">' . _th('You must be logged in to use these tools.') . '</p>';
            $html .= '<p>' . _th(
                    'If following AppZone link, then using [home] or [refresh] button should fix this.'
                ) . '</p>';
        }

        return $html;
    }

    /** @return string */
    public function errorAction()
    {
        $this->view->setHeader('ERROR');
        return 'ERROR: unknown page';
    }

    /** @return string */
    public function errorControllerAction()
    {
        $this->view->setHeader('ERROR');
        return 'ERROR: invalid page';
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private function render($content)
    {
        return $this->view->render($content);
    }

}

