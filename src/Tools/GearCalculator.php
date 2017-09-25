<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

require_once __DIR__ . '/BunnyToolPage.php';

/**
 * GearCalculator.php
 */
class GearCalculator extends BunnyToolPage
{
    protected $tag = 'gearCalc';

    /** @var int */
    private $level = 1;

    /** @var int */
    private $constitution = 10;

    /** @var int */
    private $strength = 10;

    /** @var int */
    private $intelligence = 10;

    /** @var int */
    private $balance = 10;

    /** {@inheritdoc} */
    public function getTitle()
    {
        return _th('Gear_calculator');
    }

    /**
     * @return array
     */
    public function run(array $get = [], array $post = [])
    {
        $form = $this->readForm($post);

        $maxLevel = 250;
        $maxStat = 260;

        $this->setLevel(_clamp($form['level'], 1, $maxLevel));
        $this->setConstitution(_clamp($form['constitution'], 1, $maxStat));
        $this->setStrength(_clamp($form['strength'], 1, $maxStat));
        $this->setIntelligence(_clamp($form['intelligence'], 1, $maxStat));
        $this->setBalance(_clamp($form['balance'], 1, $maxStat));

        $levelRange = range(0, 250);
        unset($levelRange[0]);
        $statRange = [];
        for ($i = 10; $i <= $maxStat; $i += 5) {
            $statRange[$i] = $i;
        }

        $_maxLevel = _th('Maximum level');
        $sbMaxLevel = $this->view->renderHtmlSelect(
            [
                'name' => 'gear_calc[level]',
                'options' => $levelRange,
                'selected' => $form['level'],
            ]
        );

        $_constitution = _th('Constitution');
        $sbConstitution = $this->view->renderHtmlSelect(
            [
                'name' => 'gear_calc[constitution]',
                'options' => $statRange,
                'selected' => $form['constitution'],
            ]
        );

        $_strength = _th('Strength');
        $sbStrength = $this->view->renderHtmlSelect(
            [
                'name' => 'gear_calc[strength]',
                'options' => $statRange,
                'selected' => $form['strength'],
            ]
        );

        $_intelligence = _th('Intelligence');
        $sbIntelligence = $this->view->renderHtmlSelect(
            [
                'name' => 'gear_calc[intelligence]',
                'options' => $statRange,
                'selected' => $form['intelligence'],
            ]
        );

        $_balance = _th('Balance');
        $sbBalance = $this->view->renderHtmlSelect(
            [
                'name' => 'gear_calc[balance]',
                'options' => $statRange,
                'selected' => $form['balance'],
            ]
        );

        $_text = _th('Characteristic*');
        $_value = _th('Value');

        $url = _h($this->url);

        $rows = [
            'th1' => [$_text, $_value],
            [$_maxLevel, $sbMaxLevel],
            [$_constitution, $sbConstitution],
            [$_strength, $sbStrength],
            [$_intelligence, $sbIntelligence],
            [$_balance, $sbBalance],
            [],
            ['', '<input type="submit" name="submit" value="' . _th('Calculate') . '">'],
            [],
            'th2' => [_th('Equipment'), _th('Quality level')],
        ];

        $tpl = '
        <tr valign="middle"{color}>
            <td width="20" height="20">&nbsp;</td>
            <td align="right">{col0}</td>
            <td width="20">&nbsp;</td>
            <td align="left">{col1}</td>
            <td width="20">&nbsp;</td>
        </tr>
        ';

        $html = '<form method="post" action="' . $url . '">';
        $html .= '<table cellspacing="0" cellpadding="0">';
        foreach ($rows as $k => $cols) {
            if (substr($k, 0, 2) === 'th') {
                $col0 = '<span style="font-weight:bold;color:yellow;">' . _h($cols[0]) . '</span>';
                $col1 = '<span style="font-weight:bold;color:yellow;">' . _h($cols[1]) . '</span>';
                $color = ' bgcolor="#101010"';
            } elseif (empty($cols)) {
                $col0 = '';
                $col1 = '';
                $color = '';
            } else {
                $col0 = $cols[0];
                $col1 = $cols[1];
                $color = '';
            }
            $html .= strtr(
                $tpl,
                [
                    '{col0}' => $col0,
                    '{col1}' => $col1,
                    '{color}' => $color,
                ]
            );
        }

        $result = $this->calculate();
        $zebraColors = $this->view->getZebraColors();
        $i = 0;
        foreach ($result as $k => $v) {
            $html .= strtr(
                $tpl,
                [
                    '{color}' => ' bgcolor="' . _cycle($i, $zebraColors) . '"',
                    '{col0}' => _th($k),
                    '{col1}' => _h($v),
                ]
            );
            $i++;
        }

        $html .= '</table>';
        $html .= '</form>';
        $html .= '<br>';

        //
        $html .= '<p style="font-size: smaller;">* Take these values from your IDENTITY tab in game.</p>';
        $html .= '<br>';

        return $html;
    }

    /**
     * Read form data from array
     *
     * @param array $post
     *
     * @return array
     */
    private function readForm(array $post)
    {
        $data = $this->user->get(
            $this->tag,
            [
                'level' => $this->level,
                'constitution' => $this->constitution,
                'strength' => $this->strength,
                'intelligence' => $this->intelligence,
                'balance' => $this->balance,
            ]
        );

        if (!empty($post['gear_calc'])) {
            foreach (['level', 'constitution', 'strength', 'intelligence', 'balance'] as $key) {
                if (isset($post['gear_calc'][$key])) {
                    $data[$key] = (int) $post['gear_calc'][$key];
                }
            }
        }
        if (!empty($this->tag)) {
            $tmp = $this->user->get($this->tag, false);
            $dirty = false;
            foreach ($data as $k => $v) {
                if (!isset($tmp[$k]) || $tmp[$k] != $data[$k]) {
                    $dirty = true;
                    $tmp[$k] = $data[$k];
                }
            }
            if ($dirty) {
                $this->user->set($this->tag, $tmp);
            }
        }

        return $data;
    }

    /** @return array */
    private function calculate()
    {
        $result = [
            'Light Armor' => 25 + $this->level,
            'Medium Armor' => min(
                25 + $this->level,
                floor(1.5 * $this->constitution)
            ),
            'Heavy Armor' => 10 + $this->constitution,
            'Shield' => 10 + $this->constitution,
            'Buckler' => min(
                25 + $this->level,
                floor(1.5 * $this->constitution)
            ),
            'Jewels' => 25 + $this->level,
            'Melee Weapon' => 10 + $this->strength,
            'Magic Amplifier' => 10 + $this->intelligence,
            'Ranged Weapon' => 10 + $this->balance,
        ];

        return $result;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return int
     */
    public function getConstitution()
    {
        return $this->constitution;
    }

    /**
     * @param int $constitution
     */
    public function setConstitution($constitution)
    {
        $this->constitution = $constitution;
    }

    /**
     * @return int
     */
    public function getStrength()
    {
        return $this->strength;
    }

    /**
     * @param int $strength
     */
    public function setStrength($strength)
    {
        $this->strength = $strength;
    }

    /**
     * @return int
     */
    public function getIntelligence()
    {
        return $this->intelligence;
    }

    /**
     * @param int $intelligence
     */
    public function setIntelligence($intelligence)
    {
        $this->intelligence = $intelligence;
    }

    /**
     * @return int
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param int $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }
}
