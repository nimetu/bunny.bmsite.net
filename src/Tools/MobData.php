<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

require_once __DIR__ . '/BunnyToolPage.php';

/**
 * Class MobData
 */
class MobData extends BunnyToolPage
{
    private $data = [];
    private $groups = [];
    private $fields = [];

    /** {@inheritdoc} */
    public function getTitle()
    {
        return _th('Mob_info');
    }

    /**
     * @param array $post
     *
     * @return string
     */
    public function run(array $get = [], array $post = [])
    {
        if (empty($this->data)) {
            $csvFile = __DIR__ . '/../../resources/MobData.csv';
            if ($this->load($csvFile) !== true) {
                return 'ERROR: failed to load mob data';
            }
        }
        $form = $this->readForm($post);

        $mobs = [];
        foreach (array_keys($this->data) as $k) {
            $mobs[$k] = ucfirst($k);
        }

        $_choose_mob = _th('Choose a mob:');
        $sbMobs = $this->view->renderHtmlSelect(
            [
                'name' => 'mob_data[mob]',
                'options' => $mobs,
                'selected' => $form['mob'],
            ]
        );
        $_submit = _th('Enlighten me');

        $url = _h($this->url);
        $html = <<<EOF
<form method="post" action="$url">
<table cellspacing="0" cellpadding="2">
<tr valign="middle">
    <td>{$_choose_mob}</td>
    <td>{$sbMobs}</td>
    <td><input type="submit" name="submit" value="{$_submit}">
</tr>
</table>
</form>
EOF;
        $mob = [];
        if (isset($this->data[$form['mob']])) {
            $mob = $this->data[$form['mob']];
        }

        $zebraColors = $this->view->getZebraColors();

        if (!empty($mob)) {
            $html .= '<br>';
            $html .= '<h2>' . _h(ucfirst($mob[0])) . '</h2>';
            unset($mob[0]);

            $tpl = '
            <tr valign="middle" bgcolor="{color}">
                <td width="190px" height="20" align="right">{text}</td>
                <td width="10px">&nbsp;</td>
                <td width="100px">{value}</td>
            </tr>
            ';

            $html .= '<table cellspacing="0" cellpadding="0">';
            $i = 0;
            foreach ($mob as $k => $value) {
                if (empty($this->fields[$k])) {
                    continue;
                }

                if (!empty($this->groups[$k])) {
                    $text = '<span style="font-weight: bold; color: yellow">' . _th($this->groups[$k]) . '</span>';
                    $html .= strtr(
                        $tpl,
                        [
                            '{color}' => '#101010',
                            '{text}' => $text,
                            '{value}' => '',
                        ]
                    );
                }

                if ($value == 'NA') {
                    $value = '-';
                } elseif ($value == 'X') {
                    // CHECK MARK - 0x2713
                    $value = '✓';
                } elseif (in_array(
                    $value,
                    [
                        'parry',
                        'dodge',
                        'blunt',
                        'pierce',
                        'slash',
                        'blind',
                        'root',
                        'fear',
                        'madness',
                        'poison DoT',
                        'acid',
                        'cold',
                        'rot',
                        'electric',
                        'fire',
                        'shockwave',
                        'poison',
                        'sw & ele',
                        'cold & root',
                    ]
                )) {
                    $value = _t($value);
                }

                $text = _th($this->fields[$k]);

                if ($value == '0%') {
                    $value = '<span style="color:#505050;">' . _h($value) . '</span>';
                } else {
                    $value = _h($value);
                }

                $html .= strtr(
                    $tpl,
                    [
                        '{color}' => _cycle($i, $zebraColors),
                        '{text}' => _h($text),
                        '{value}' => $value,
                    ]
                );

                $i++;
            }
            $html .= '</table>';
        }

        return $html;
    }

    /**
     * Load mob info from csv file
     *
     * @param string $csvFile
     *
     * @return bool
     */
    public function load($csvFile)
    {
        if (!file_exists($csvFile)) {
            return false;
        }

        $this->data = [];
        $this->fields = [];

        $lines = file($csvFile);
        // skip this one
        unset($lines[0]);

        $group = '-';
        foreach ($lines as $i => $line) {
            $parts = explode("\t", $line);
            $parts = array_map('trim', $parts);

            if ($parts[0] === 'Description') {
                // column header groups
                $this->groups = $parts;
                continue;
            } elseif ($parts[0] === 'Mob Name') {
                // column headers
                $this->fields = $parts;
                continue;
            } elseif (empty($parts[1]) && !empty($parts[8])) {
                $group = $parts[8];
                continue;
            } elseif (empty($parts[1]) && empty($parts[8])) {
                continue;
            } elseif (empty($parts[0])) {
                continue;
            }

            $parts['group'] = $group;
            $this->data[$parts[0]] = $parts;
        }

        return true;
    }

    /**
     * Read form data from array
     *
     * @param array $post
     *
     * @return array
     */
    private function readForm($post)
    {
        $data = [
            'mob' => '',
        ];

        if (isset($post['mob_data']['mob'])) {
            $data['mob'] = $post['mob_data']['mob'];
        }

        return $data;
    }
}
