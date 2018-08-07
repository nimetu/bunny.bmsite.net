<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

/**
 * BunnyView.php
 */
class BunnyView
{
    /** @var bool */
    private $debug = false;

    /** @var bool */
    private $translator = false;

    /** @var string[] */
    private $languages = array();

    /** @var bool */
    private $ingame = false;

    /** @var BunnyMenu */
    private $menu;

    /**
     * Page HTML title
     *
     * @var string
     */
    private $pageTitle = 'BunnyTools';

    /**
     * Page sub-title
     *
     * @var string
     */
    private $pageHeader = 'BunnyTools';

    /**
     * Copyright message
     *
     * @var string
     */
    private $pageCopyright = 'Fluffy Bunnies';

    /**
     * Current logged in character name
     *
     * @var string
     */
    private $charName;

    /**
     * BunnyView constructor.
     *
     * @param BunnyMenu $menu
     */
    public function __construct(BunnyMenu $menu, $baseUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->menu = $menu;
    }

    /**
     * Enable/disable debug messages
     *
     * @param bool $b
     */
    public function setDebug($b)
    {
        $this->debug = $b;
    }

    /**
     * Enable/disable language links
     *
     * @param bool $b
     */
    public function setTranslator($b)
    {
        $this->translator = $b;
    }

    /**
     * Set available languages. Links shown to translators
     *
     * @param string[] $langs
     */
    public function setLanguages(array $langs)
    {
        $this->languages = $langs;
    }

    /**
     * Return TRUE if user is ingame
     *
     * @return bool
     */
    public function isIngame()
    {
        return $this->ingame;
    }

    /**
     * Set ingame state
     *
     * @param bool $b
     */
    public function setIngame($b)
    {
        $this->ingame = $b;
    }

    /** @return string */
    public function getCharName()
    {
        return $this->charName;
    }

    /** @param string $charName */
    public function setCharName($charName)
    {
        $this->charName = $charName;
    }

    /** @param string $title */
    public function setTitle($title)
    {
        $this->pageTitle = $title;
    }

    /** @return string */
    public function getTitle()
    {
        return $this->pageTitle;
    }

    /**
     * Append string to current page title.
     *
     * @param string $s
     * @param string $sep
     */
    public function appendTitle($s, $sep = ' | ')
    {
        if (empty($this->pageTitle)) {
            $this->pageTitle .= $s;
        } else {
            $this->pageTitle .= $sep . $s;
        }
    }

    /** @param string $header */
    public function setHeader($header)
    {
        $this->pageHeader = $header;
    }

    /** @return string */
    public function getHeader()
    {
        return $this->pageHeader;
    }

    /**
     * Append string to current page header
     *
     * @param string $s
     * @param string $sep
     */
    public function appendHeader($s, $sep = '-')
    {
        if (empty($this->header)) {
            $this->pageHeader = $s;
        } else {
            $this->pageHeader .= $sep . $s;
        }
    }

    /** @param string $copyright */
    public function setCopyright($copyright)
    {
        $this->pageCopyright = $copyright;
    }

    /** @return string */
    public function getCopyright()
    {
        return $this->pageCopyright;
    }

    /**
     * Render full page
     *
     * @param string $content
     *
     * @return string
     */
    public function render($content)
    {
        $title = _h($this->pageTitle);

        $content = $this->renderContent($content);

        if ($this->ingame) {
            $bgcolor = ' bgcolor="#10101000"';
        } else {
            $bgcolor = '';
        }

        return <<<EOF
<!DOCTYPE html>
<html>
<head>
    <title>{$title}</title>
    <link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body{$bgcolor}>
    {$content}
</body>
</html>
EOF;
    }

    /**
     * Render page inner content
     *
     * @param string $content
     *
     * @return string
     */
    public function renderContent($content)
    {
        $header = _h($this->pageHeader);
        $copyright = _h($this->pageCopyright);
        $menu = $this->menu->renderMenu();

        $userName = _h($this->charName);

        if ($this->translator) {
            $tmp = array();
            foreach($this->languages as $lang) {
                $tmp[] = '<a href="?lang='._h($lang).'">'._h($lang).'</a>';
            }
            $langSwitch = join(' | ', $tmp);
        } else {
            $langSwitch = '';
        }

        return <<<EOF
<table width="100%" bgcolor="#101010" cellspacing="0" cellpadding="5">
    <tr valign="middle">
        <td align="left"><span style="color: #d0d0d0">{$header}</span></td>
        <td align="right"><span style="color: #d0d0d0">{$userName}</span></td>
    </tr>
</table>
<table width="100%" cellspacing="0" cellpadding="5">
    <tr valign="top">
        <td width="130px" align="left">
            {$menu}
            {$langSwitch}
        </td>
        <td align="center" width="100%">
            {$content}
        </td>
    </tr>
</table>
<hr style="color: #000000;">
<table width="100%" cellspacing="0" cellpadding="5">
    <tr valign="middle">
        <td align="left"><a href="?">index</a></td>
        <td align="right">(c) 2016 {$copyright}</td>
    </tr>
</table>
EOF;
    }

    /**
     * html <select> element
     *
     * @param $options
     *
     * @return string
     */
    public function renderHtmlSelect($options)
    {
        $html = '<select name="';
        if (isset($options['name'])) {
            $html .= _h($options['name']);
        }
        $html .= '">';
        $selected = isset($options['selected']) ? $options['selected'] : false;
        $first = true;
        foreach ($options['options'] as $k => $v) {
            if (is_array($v)) {
                if (!$first) {
                    $html .= $this->renderHtmlSelectOption('', '', false, true);
                }
                $html .= $this->renderHtmlSelectOption('', $k, false, true);
                foreach ($v as $sk => $sv) {
                    $html .= $this->renderHtmlSelectOption($sk, $sv, $selected == $sk);
                }
            } else {
                $html .= $this->renderHtmlSelectOption($k, $v, $selected == $k);
            }
            $first = false;
        }
        $html .= '</select>';

        return $html;
    }

    /**
     * @param string $value
     * @param string $text
     * @param bool   $selected
     *
     * @param bool   $disabled
     *
     * @return string
     */
    private function renderHtmlSelectOption($value, $text, $selected, $disabled = false)
    {
        $html = '<option value="' . _h($value) . '"';
        if ($selected) {
            $html .= ' selected="selected"';
        }
        if ($disabled) {
            $html .= ' disabled="disabled"';
        }
        $html .= '>' . _h($text) . '</option>';
        return $html;
    }

    /**
     * html <input> element
     *
     * @param $options
     *
     * @return string
     */
    public function renderHtmlInput($options)
    {
        if (!isset($options['type'])) {
            $options['type'] = 'text';
        }
        // ingame input size expects pixel width
        if (isset($options['size']) && $this->ingame) {
            $options['size'] *= 14;
        }
        $html = '<input ';
        foreach ($options as $k => $v) {
            $html .= ' ' . _h($k) . '="' . _h($v) . '"';
        }
        $html .= '>';

        return $html;
    }

    /**
     * html <input type=checkbox> element
     *
     * @param array $options
     *
     * @return string
     */
    public function renderHtmlCheckbox($options)
    {
        $options['type'] = 'checkbox';
        $options['value'] = 'on';
        if (isset($options['checked'])) {
            if ($options['checked']) {
                $options['checked'] = 'checked';
            } else {
                unset($options['checked']);
            }
        }
        return $this->renderHtmlInput($options);
    }

    /**
     * Alternating colors for table rows
     *
     * @theme
     */
    public function getZebraColors()
    {
        if ($this->ingame) {
            $zebraColors = ['#202020', '#2f2f2f'];
        } else {
            $zebraColors = ['#e0e0e0', '#efefef'];
        }
        return $zebraColors;
    }

    public function imageUrl($src)
    {
        if (empty($src)) {
            return '';
        } elseif ($src[0] != '/') {
            $src = '/' . $src;
        }

        return $this->baseUrl . '/images' . $src;
    }

    /**
     * Display debug info if user has showDebug set.
     *
     * If output is HTML, then $msg must already be made safe.
     *
     * @param string
     */
    public function debug($msg)
    {
        if ($this->debug) {
            echo $msg;
        }
    }
}
