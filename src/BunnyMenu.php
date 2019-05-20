<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

/**
 * BunnyMenu.php
 */
class BunnyMenu
{
    /**
     * If debug, then show extra menu entries
     *
     * @var bool
     */
    private $debug;

    /**
     * Active menu index
     *
     * @var string
     */
    private $menuIndex = 'main';

    /** @var string */
    private $headerColor = '#888888';

    /** @var array */
    private $zebraColors = ['#202020', '#2f2f2f'];

    /**
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * Set alternating colors for rows
     *
     * @param array $colors
     */
    public function setZebraColors(array $colors)
    {
        $this->zebraColors = $colors;
    }

    /**
     * Active menu index
     *
     * @param string $index
     */
    public function setActiveMenuIndex($index)
    {
        $this->menuIndex = $index;
    }

    /** @return string */
    public function getActiveMenuIndex()
    {
        return $this->menuIndex;
    }

    /** @return string */
    public function renderMenu()
    {
        // menu item key must be uniq, case sensitive
        // example url: ?action=gearCalc
        $menu = [];
        $menu[_t('Calculators')] = [
            'gearCalc' => _t('Gear Requirements'),
            'statsCalc' => _t('Crafting Ranges'),
            'xpCalc' => _t('XP'),
            'matsArmorCalc' => _t('Mats (Armor)'),
            'matsJewelCalc' => _t('Mats (Jewel)'),
            'matsMeleeCalc' => _t('Mats (Melee)'),
            'matsRangeCalc' => _t('Mats (Range)'),
            'matsShieldCalc' => _t('Mats (Shield)'),
        ];
        $menu[_t('Encyclopedia')] = [
            'mobData' => _t('Mobs data'),
            'outpostRegistry' => _t('Outpost Registry'),
            'resourceInfo' => _t('Exe resource info'),
        ];
        $menu[_t('Reminders')] = [
            'rpjobReminder' => _t('Occupations'),
        ];
        if (function_exists('ryzom_character_api')) {
            $menu[_t('Reminders')]['shopReminder'] = _t('Vendor Inventory');
        }
        $menu[_t('Info')] = [
            'http://app.ryzom.com/app_forum/?page=topic/view/25037/1' => _t('Forum BT 3.0'),
            'http://app.ryzom.com/app_forum/?page=topic/view/13298/1' => _t('Forum BT 2.0'),
        ];

        if ($this->debug) {
            //$menu['Testing'] = [
            //];
        }

        $html = $this->renderSubMenu($menu);
        return '<table width="100%" cellspacing="0" cellpadding="2">' . $html . '</table>';
    }

    /**
     * Recursively render menu items
     *
     * @param array $menu
     *
     * @return string
     */
    private function renderSubMenu(array $menu)
    {
        $html = '';

        $tplHeader = '<tr><td height="20" bgcolor="{color}"><span style="font-weight: bold;">{text}</span></td>';
        $tplItem = '<tr><td height="20" bgcolor="{color}" nowrap>{text}</td>';

        $i = 0;
        foreach ($menu as $key => $value) {
            if (is_array($value)) {
                $html .= strtr(
                    $tplHeader,
                    [
                        '{color}' => $this->headerColor,
                        '{text}' => _h($key)
                    ]
                );

                $html .= $this->renderSubMenu($value);
                $i = 0;
            } else {
                $link = $this->renderMenuLink($key, $value, $this->menuIndex === $key);
                $html .= strtr(
                    $tplItem,
                    [
                        '{color}' => _cycle($i, $this->zebraColors),
                        '{text}' => $link,
                    ]
                );
                $i++;
            }
        }
        return $html;
    }

    /**
     * Render single menu item link
     *
     * @param string $url
     * @param string $text
     * @param bool   $active
     *
     * @return string
     */
    private function renderMenuLink($url, $text, $active)
    {
        if ($active) {
            $style = ' style="color:#9090ff; text-decoration: none;"';
        } else {
            $style = '';
        }

        if (strstr($url, '://') === false) {
            $url = '?action=' . $url;
        }
        $link = '<a href="' . _h($url) . '"' . $style . '>' . _h($text) . '</a>';
        return $link;
    }
}
