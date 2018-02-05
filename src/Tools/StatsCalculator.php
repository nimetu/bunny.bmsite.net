<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

require_once __DIR__ . '/BunnyToolPage.php';

/**
 * StatsCalculator.php
 */
class StatsCalculator extends BunnyToolPage
{
    protected $tag = 'statsCalc';

    private $typeStats = [];

    /**
     * Type selection dropdown, translated and made html safe in preload()
     *
     * @var array
     */
    private $typeArray = [
        'lightarmor' => 'Light Armor',
        'mediumarmor' => 'Medium Armor',
        'heavyarmor' => 'Heavy Armor',
        'shield' => 'Shields',
        'jewels' => 'Jewels',
        'amps' => 'Magic Amplifier',
        '1hmelee' => '1h Melee Weapons',
        '2hmelee' => '2h Melee Weapons',
        '1hrange' => '1h Range Weapons, Ammo',
        '2hrange' => '2h Range Weapons, Ammo',
    ];

    /**
     * Grouping for type selection.
     * If type selection not present, then only single type is displayed
     *
     * @var array
     */
    private static $itemGroups = [
        '1hrange' => ['1hrange', '1hammo'],
        '2hrange' => ['2hrange', '2hammo'],
    ];

    private static $typeItems = [
        '1hmelee' => [
            'Dagger',
            'Sword',
            'Axe',
            'Mace',
            'Spear',
            'Staff',
        ],
        '2hmelee' => [
            'TwoHandSword',
            'TwoHandAxe',
            'TwoHandMace',
            'Pike',
        ],
        'amps' => [
            'MagicianStaff',
        ],
        '1hrange' => [
            'Pistol',
            'Bowpistol',
        ],
        '1hammo' => [
            'PistolAmmo',
            'BowpistolAmmo',
        ],
        '2hrange' => [
            'Launcher',
            'Autolauch',
            'Rifle',
            'Bowrifle',
        ],
        '2hammo' => [
            'LauncherAmmo',
            'AutolaunchAmmo',
            'RifleAmmo',
            'BowrifleAmmo',
        ],
        'shield' => [
            'Shield',
            'Buckler',
        ],
        'lightarmor' => [
            'LightBoots',
            'LightGloves',
            'LightPants',
            'LightSleeves',
            'LightVest',
        ],
        'mediumarmor' => [
            'MediumBoots',
            'MediumGloves',
            'MediumPants',
            'MediumSleeves',
            'MediumVest',
        ],
        'heavyarmor' => [
            'HeavyBoots',
            'HeavyGloves',
            'HeavyPants',
            'HeavySleeves',
            'HeavyVest',
            'HeavyHelmet',
        ],
        'jewels' => [
            'Anklet',
            'Bracelet',
            'Diadem',
            'Earing',
            'Pendant',
            'Ring',
            // ignored in output, but needed to get resistance/protection stats for jewels
            'Jewel',
        ],
    ];

    // craft_stats.inc.php
    private static $stats = [];

    private static $durabilityBonusMap = [
        '__default' => ['b' => 1.0, 'm' => 1.5, 'h' => 2.0],
        'jewels' => ['b' => 1.0, 'm' => 3, 'h' => 5],
        '1hrange' => ['b' => 1.0, 'm' => 1.0, 'h' => 1.0],
        '2hrange' => ['b' => 1.0, 'm' => 1.0, 'h' => 1.0],
        '1hammo' => ['b' => 1.0, 'm' => 1.0, 'h' => 1.0],
        '2hammo' => ['b' => 1.0, 'm' => 1.0, 'h' => 1.0],
    ];

    private static $stat2uxt = [
        'Durability' => 'mpstat0',
        'Weight' => 'mpstat1',
        'SapLoad' => 'mpstat2',
        'Dmg' => 'mpstat3',
        'HitRate' => 'mpstat4',
        'Range' => 'mpstat5',
        'DodgeModifier' => 'mpstat6',
        'ParryModifier' => 'mpstat7',
        'AdversaryDodgeModifier' => 'mpstat8',
        'AdversaryParryModifier' => 'mpstat9',
        'ProtectionFactor' => 'mpstat10',
        'MaxSlashingProtection' => 'mpstat11',
        'MaxBluntProtection' => 'mpstat12',
        'MaxPiercingProtection' => 'mpstat13',
        'AcidJewelProtection' => 'mpstat14',
        'ColdJewelProtection' => 'mpstat15',
        'RotJewelProtection' => 'mpstat16',
        'FireJewelProtection' => 'mpstat17',
        'ShockWaveJewelProtection' => 'mpstat18',
        'PoisonJewelProtection' => 'mpstat19',
        'ElectricityJewelProtection' => 'mpstat20',
        'DesertResistance' => 'mpstat21',
        'ForestResistance' => 'mpstat22',
        'LacustreResistance' => 'mpstat23',
        'JungleResistance' => 'mpstat24',
        'PrimaryRootResistance' => 'mpstat25',
        'ElementalCastingTimeFactor' => 'mpstat26',
        'ElementalPowerFactor' => 'mpstat27',
        'OffensiveAfflictionCastingTimeFactor' => 'mpstat28',
        'OffensiveAfflictionPowerFactor' => 'mpstat29',
        'DefensiveAfflictionCastingTimeFactor' => 'mpstat30',
        'DefensiveAfflictionPowerFactor' => 'mpstat31',
        'HealCastingTimeFactor' => 'mpstat32',
        'HealPowerFactor' => 'mpstat33',
    ];

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return _th('Gear_calculator');
    }

    /**
     * @param array $get
     * @param array $post
     *
     * @return array
     */
    public function run(array $get = [], array $post = [])
    {
        $ret = $this->preload();
        if ($ret !== true) {
            return $ret;
        }

        $form = $this->readForm(isset($post[$this->tag]) ? $post[$this->tag] : []);

        $html = $this->renderForm($form);

        if (!empty($post)) {
            if (isset(self::$itemGroups[$form['type']])) {
                $groups = self::$itemGroups[$form['type']];
            } else {
                $groups = [$form['type']];
            }

            foreach ($groups as $type) {
                $form['type'] = $type;
                $html .= '<h2>' . _th($type) . '</h2>';
                $html .= $this->renderItemType($form);
            }
        }

        $html = '<form method="post" action="' . _h($this->url) . '">' . $html . '</form>';

        return $html;
    }

    /**
     * @param array $form
     *
     * @return string
     */
    private function renderForm(array $form)
    {
        $sbType = $this->view->renderHtmlSelect(
            [
                'name' => $this->tag . '[type]',
                'options' => $this->typeArray,
                'selected' => $form['type'],
            ]
        );

        $sbLevel = $this->view->renderHtmlInput(
            [
                'name' => $this->tag . '[level]',
                'value' => $form['level']
            ]
        );

        $cbDurability = $this->view->renderHtmlCheckbox(
            [
                'name' => $this->tag . '[durability]',
                'checked' => $form['durability'],
            ]
        );

        $cbRubbarn = $this->view->renderHtmlCheckbox(
            [
                'name' => $this->tag . '[rubbarn]',
                'checked' => $form['rubbarn'],
            ]
        );

        $cbMinMax = $this->view->renderHtmlCheckbox(
            [
                'name' => $this->tag . '[min-max]',
                'checked' => $form['min-max'],
            ]
        );

        $sbGrade = $this->view->renderHtmlSelect(
            [
                'name' => $this->tag . '[grade]',
                'options' => [
                    'b' => _th('Basic'),
                    'm' => _th('Medium'),
                    'h' => _th('High'),
                ],
                'selected' => $form['grade'],
            ]
        );

        $tpl = '
        <tr valign="middle"{color}>
            <td width="20" height="20">&nbsp;</td>
            <td align="right">{col0}</td>
            <td width="20">&nbsp;</td>
            <td align="left">{col1}</td>
            <td width="20">&nbsp;</td>
        </tr>
        ';

        $rows = [
            'th1' => ['', _th('Parameters')],
            [_th('Type'), $sbType],
            [_th('Level (1-250)'), $sbLevel],
            [_th('Durability Bonus'), $cbDurability],
            [_th('Rubbarn Boost'), $cbRubbarn],
            [_th('Grade'), $sbGrade],
            [_th('Min / Max'), $cbMinMax],
            [],
            ['', '<input type="submit" name="submit" value="' . _th('Calculate') . '">'],
        ];

        $html = '<table cellspacing="0" cellpadding="0">';

        foreach ($rows as $k => $cols) {
            if (substr($k, 0, 2) === 'th') {
                $col0 = '<span style="font-weight:bold;color:yellow;">' . _h($cols[0]) . '</span>';
                $col1 = '<span style="font-weight:bold;color:yellow;">' . _h($cols[1]) . '</span>';
                $color = 'bgcolor="#101010"';
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

        $html .= '</table>';

        return $html;
    }

    /**
     * @param array $form
     *
     * @return string
     */
    private function renderItemType(array $form)
    {
        $result = $this->calculate($form);

        if (isset($result['error'])) {
            return '<p>' . _th('Error') . ': ' . _h($result['error']) . '</p>';
        }

        $compare = function ($a, $b) {
            foreach ($a as $k => $v) {
                if (!isset($b[$k]) || $b[$k] !== $v) {
                    return false;
                }
            }

            return true;
        };

        // combine similar items (useful for jewels)
        $colTitles = [];
        $colHeader = array_keys($result);
        for ($i = 0; $i < count($colHeader) - 1; $i++) {
            $item = $colHeader[$i];
            if (!isset($result[$item])) {
                continue;
            }

            for ($j = $i + 1; $j < count($colHeader); $j++) {
                $item2 = $colHeader[$j];
                if (!isset($result[$item2])) {
                    continue;
                }
                if ($compare($result[$item], $result[$item2])) {
                    if (empty($colTitles[$item])) {
                        $colTitles[$item] = [_t($item)];
                    }
                    $colTitles[$item][] = _t($item2);
                    unset($result[$item2]);
                }
            }
        }

        $zebraColors = $this->view->getZebraColors();

        $tplRow = '
			<tr valign="middle" bgcolor="{bgcolor}">
				<td height="{height}" width="5"></td>
				<td width="20" align="center" nowrap>{cb}</td>
				<td width="150" align="center">{item}</td>
				{columns}
			</tr>';

        // stat column template
        $tplCol = '
			<td width="1" bgcolor="#101010"></td>
			<td width="5"></td>
			<td align="center" nowrap>{stat}</td>
			<td width="5"></td>
		';

        // stat column filler for hr and calculate button
        $tplColFiller = '
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		';

        reset($result);
        $key = key($result);
        $rowHeader = array_keys($result[$key]);
        $colHeader = array_keys($result);


        // columns
        $cols = '';
        $hrCols = '';
        foreach ($colHeader as $item) {
            if (isset($colTitles[$item])) {
                sort($colTitles[$item]);
                $stat = _h(join(', ', $colTitles[$item]));
            } else {
                $stat = _th($item);
            }
            $cols .= strtr($tplCol, ['{stat}' => _h($stat)]);
            $hrCols .= $tplColFiller;
        }

        $hr = strtr(
            $tplRow,
            [
                '{bgcolor}' => '#101010',
                '{height}' => 1,
                '{cb}' => '',
                '{item}' => '',
                '{columns}' => $hrCols,
            ]
        );

        $html = strtr(
            $tplRow,
            [
                '{bgcolor}' => $zebraColors[0],
                '{height}' => 30,
                '{cb}' => _th('Precraft %'),
                '{item}' => _th('Characteristic'),
                '{columns}' => $cols,
            ]
        );

        $html .= $hr;

        $tplRow = str_replace('{height}', 30, $tplRow);
        $tplRow = str_replace(' align="center"', '', $tplRow);

        $type = $form['type'];

        $i = 1;
        foreach ($rowHeader as $stat) {
            $cols = '';
            foreach ($colHeader as $item) {
                $tmp = explode("\n", _h($result[$item][$stat]));
                if (isset($tmp[1])) {
                    $tmp[0] = '<table width="100%"><tr><td align="center">' . _h($tmp[0]) . '</td></tr>';
                    $tmp[0] .= '<tr><td align="center">' . _h($tmp[1]) . '</td></tr></table>';
                }
                $cols .= strtr($tplCol, ['{stat}' => $tmp[0]]);
            }

            if (!isset($form[$type][$stat])) {
                $form[$type][$stat] = 100;
            }

            if ($stat === 'dmg/min') {
                $box = '';
            } else {
                $box = $this->view->renderHtmlInput(
                    [
                        'name' => $this->tag . '[' . _h($type) . '][' . _h($stat) . ']',
                        'value' => (int) $form[$type][$stat],
                        'size' => 4,
                    ]
                );
            }

            $html .= strtr(
                $tplRow,
                [
                    '{bgcolor}' => _cycle($i, $zebraColors),
                    '{cb}' => $box,
                    '{item}' => _th($this->getStatUxt($stat)),
                    '{columns}' => $cols,
                ]
            );

            $i++;
        }

        $html .= $hr;

        $tpl = '
		<tr bgcolor="{bgcolor}">
			<td height="30" width="5"></td>
			<td colspan="2">{item}</td>
			{columns}
		</tr>';
        $html .= strtr(
            $tpl,
            [
                '{bgcolor}' => _cycle($i, $zebraColors),
                '{item}' => '<input type="submit" name="submit" value="' . _th('Calculate') . '">',
                '{columns}' => $hrCols,
            ]
        );

        $html = '<table width="100%" cellspacing="0" cellpadding="0">' . $html . '</table>';

        if (in_array($type, ['1hmelee', '2hmelee', 'amps'])) {
            $footnote = _t('* Inc.Damage stanza not included');
        } elseif (in_array($type, ['1hammo', '2hammo'])) {
            $footnote = _t('* HitRate stanza not included');
        } else {
            $footnote = '';
        }

        if ($footnote !== '') {
            $html .= '
            <table width="100%">
            <tr>
                <td align="center"><span style="font-size: smaller;">' . _h($footnote) . '</span></td>
            </tr>
            </table>';
        }

        return '<span style="font-size: smaller;">' . $html . '</span>';
    }

    /**
     * @param string $stat
     *
     * @return number|string
     */
    private function formatStat($stat, $value)
    {
        switch ($stat) {
            case 'Durability':
                $value = number_format($value, 0);
                break;
            case 'Weight':
                $value = number_format($value, 2);
                break;
            case 'SapLoad':
                $value = number_format($value, 0);
                break;
            case 'Dmg':
                $value = number_format($value, 0);
                break;
            case 'HitRate':
            case 'dmg/min':
                $value = number_format($value, 0);
                break;
            case 'Range':
                $value = intval($value / 1000);
                break;
            case 'DodgeModifier':
            case 'ParryModifier':
            case 'AdversaryDodgeModifier':
            case 'AdversaryParryModifier':
                $value = intval($value);
                break;
            case 'ProtectionFactor':
                $value = number_format($value, 1) . '%';
                break;
            case 'MaxSlashingProtection':
            case 'MaxBluntProtection':
            case 'MaxPiercingProtection':
                $value = round($value);
                break;
            case 'ElementalCastingTimeFactor':
            case 'ElementalPowerFactor':
            case 'OffensiveAfflictionCastingTimeFactor':
            case 'OffensiveAfflictionPowerFactor':
            case 'HealCastingTimeFactor':
            case 'HealPowerFactor':
            case 'DefensiveAfflictionCastingTimeFactor':
            case 'DefensiveAfflictionPowerFactor':
                $value .= '%';
                break;
            case 'AcidJewelProtection':
            case 'ColdJewelProtection':
            case 'FireJewelProtection':
            case 'RotJewelProtection':
            case 'ShockWaveJewelProtection':
            case 'PoisonJewelProtection':
            case 'ElectricityJewelProtection':
                $value = number_format($value, 0) . '%';
                break;
            case 'DesertResistance':
            case 'ForestResistance':
            case 'LacustreResistance':
            case 'JungleResistance':
            case 'PrimaryRootResistance':
                $value = number_format($value, 1);
                break;
            default:
                $value = '[BUG:' . $stat . ']=' . $value;
                break;
        }

        return $value;
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
        $defaults = [
            'level' => 250,
            //'precraft' => 100,
            'type' => 'lightarmor',
            'durability' => false,
            'rubbarn' => false,
            'grade' => 'b',
            'min-max' => false,
        ];
        $data = $this->user->get($this->tag, []);
        if (!is_array($data)) {
            $data = [];
        }

        $data = array_merge($defaults, $data);
        if (!empty($post)) {
            foreach (array_keys($data) as $k) {
                if (isset($post[$k])) {
                    $data[$k] = $post[$k];
                }
            }

            // read checkboxes and set missing as false
            foreach (['durability', 'rubbarn', 'min-max'] as $k) {
                if (isset($post[$k])) {
                    $data[$k] = ($data[$k] === true || $data[$k] === 'on');
                } else {
                    $data[$k] = false;
                }
            }

            $types = isset(self::$itemGroups[$data['type']]) ? self::$itemGroups[$data['type']] : [$data['type']];
            foreach ($types as $type) {
                $stats = array_keys($this->typeStats[$data['type']]);
                foreach ($stats as $stat) {
                    if (isset($data[$type][$stat])) {
                        $data[$type][$stat] = max(0, min(100, $data[$type][$stat]));
                    } else {
                        $data[$type][$stat] = 100;
                    }
                }
            }
        }

        $data['level'] = max(1, min(250, $data['level']));

        $tmp = $this->user->get($this->tag, false);
        $dirty = false;

        foreach ($data as $k => $v) {
            if (!isset($tmp[$k]) || $tmp[$k] !== $data[$k]) {
                $dirty = true;
                $tmp[$k] = $data[$k];
            }
        }
        if ($dirty) {
            $this->user->set($this->tag, $tmp);
        }

        // not saved
        $data['rubbarn_bonus'] = $data['rubbarn'] ? 1.20 : 1.0;

        return $data;
    }

    /**
     * @param array $form
     *
     * @return array
     */
    private function calculate(array $form)
    {
        $type = isset($form['type']) ? $form['type'] : '';
        if (!isset(self::$typeItems[$type])) {
            return [
                'error' => _t('Invalid item type:') . " '$type'",
            ];
        }
        if (!isset($this->typeStats[$type])) {
            return [
                'error' => _t('No stats for item type:') . " '$type'",
            ];
        }

        $result = [];
        foreach (self::$typeItems[$type] as $item) {
            if ($item === 'Jewel') {
                // this is only used to set rest of jewel stats
                continue;
            }
            foreach ($this->typeStats[$type] as $stat => $t) {
                $result = $this->calculateStat(
                    $stat,
                    $item,
                    $form,
                    $t,
                    $result
                );
            }
        }

        return $result;
    }

    /**
     * @param string $stat
     * @param string $item
     * @param array  $form
     * @param array  $statMinMaxIndex
     * @param array  $result
     *
     * @return array
     */
    private function calculateStat($stat, $item, array $form, array $statMinMaxIndex, array $result)
    {
        $precraft = isset($form[$form['type']][$stat]) ? $form[$form['type']][$stat] : 0;

        $result[$item][$stat] = $this->getStatValue(
            ($precraft / 100) * $form['rubbarn_bonus'],
            $form['level'],
            $form['grade'],
            $item,
            $form['type'],
            $statMinMaxIndex[0],
            $statMinMaxIndex[1]
        );

        // durability rite
        if ($stat === 'Durability' && $form['durability']) {
            $result[$item][$stat] += 20;
        }

        if (!isset($result[$item]['dmg/min'])) {
            // melee weapons
            if (isset($result[$item]['Dmg']) && isset($result[$item]['HitRate'])) {
                if ($result[$item]['Dmg'] > 0 && $result[$item]['HitRate'] > 0) {
                    $result[$item]['dmg/min'] = $this->formatStat(
                            'dmg/min',
                            $result[$item]['HitRate'] * $result[$item]['Dmg']
                        ) . ' *';
                }
            }

            if (substr($item, -4) === 'Ammo' && isset($result[$item]['Dmg'])) {
                // get weapon form values
                $i = substr($item, 0, -4);
                $tt = substr($form['type'], 0, 2) . 'range';
                if ($i === 'Autolaunch') {
                    $i = 'Autolauch';
                }
                $precraft = isset($form[$tt]['HitRate']) ? $form[$tt]['HitRate'] : 100;
                $hitRate = $this->getStatValue(
                    ($precraft / 100) * $form['rubbarn_bonus'],
                    $form['level'],
                    $form['grade'],
                    $i,
                    $tt,
                    'HitRate',
                    'HitRateMax'
                );

                $result[$item]['dmg/min'] = $this->formatStat('dmg/min', $hitRate * $result[$item]['Dmg']) . ' *';
            }
        }

        $result[$item][$stat] = $this->formatStat($stat, $result[$item][$stat]);
        if ($form['min-max']) {
            $min = $this->getStatValue(
                0.0,
                $form['level'],
                $form['grade'],
                $item,
                $form['type'],
                $statMinMaxIndex[0],
                $statMinMaxIndex[1]
            );
            $max = $this->getStatValue(
                1.0 * $form['rubbarn_bonus'],
                $form['level'],
                $form['grade'],
                $item,
                $form['type'],
                $statMinMaxIndex[0],
                $statMinMaxIndex[1]
            );
            if ($stat === 'Durability' && $form['durability']) {
                $min += 20;
                $max += 20;
            }
            $result[$item][$stat] .= "\n(" . $this->formatStat($stat, $min) . " .. " . $this->formatStat(
                    $stat,
                    $max
                ) . ")";
        }

        return $result;
    }

    /**
     * Calculate stat value
     *
     * $value = getStatValue(0.5, 250, 'Jewel', 'Durability')
     *
     * @param float  $craftValue
     * @param int    $itemQuality
     * @param string $itemGrade
     * @param string $item
     * @param string $itemType
     * @param string $statName
     * @param string $statNameMax
     *
     * @return float
     */
    private function getStatValue(
        $craftValue,
        $itemQuality,
        $itemGrade,
        $item,
        $itemType,
        $statName,
        $statNameMax = null
    ) {
        $gradeBoost = $this->getDurabilityBonus($itemType, $itemGrade);

        if (substr($statName, -15) == 'JewelProtection' || substr($statName, -10) == 'Resistance') {
            $item = 'Jewel';
        }
        if (!isset(self::$stats[$statName][$item])) {
            if (isLOCAL) {
                echo "ERROR: $statName does not have " . _h($item) . "<br>";
            }
            return false;
        }

        if ($statNameMax === null && isset(self::$stats[$statName . 'Max'][$item])) {
            $statNameMax = $statName . 'Max';
        }

        $min = (float) self::$stats[$statName][$item];
        if ($statNameMax !== null && isset(self::$stats[$statNameMax][$item])) {
            $max = (float) self::$stats[$statNameMax][$item];
        } else {
            $max = $min * 2;
        }

        switch ($statName) {
            case 'Durability':
                $result = round($min + $min * $craftValue * $gradeBoost);
                break;
            case 'Weight':
                $result = $min * 2 - $min * $craftValue;
                break;
            case 'Dmg':
                $result = $min + ($max - $min) * $craftValue;
                $result = round($this->getReferenceDamage($itemQuality) * $result);
                break;
            case 'HitRate':
                $result = $min + ($max - $min) * $craftValue;
                // HitRate factor is for 10sec, convert it to Hit/Min
                $result = 6 * $result;
                break;
            case 'Range':
                $result = round($min + $min * $craftValue);
                break;
            case 'AcidJewelProtection':
            case 'ColdJewelProtection':
            case 'FireJewelProtection':
            case 'RotJewelProtection':
            case 'ShockWaveJewelProtection':
            case 'PoisonJewelProtection':
            case 'ElectricityJewelProtection':
                $result = round($craftValue * $min * 100);
                break;
            case 'DesertResistance':
            case 'ForestResistance':
            case 'LacustreResistance':
            case 'JungleResistance':
            case 'PrimaryRootResistance':
                $result = sprintf('%.1f', $craftValue * $min);
                break;
            case 'ElementalCastingTimeFactor':
            case 'ElementalPowerFactor':
            case 'OffensiveAfflictionCastingTimeFactor':
            case 'OffensiveAfflictionPowerFactor':
            case 'HealCastingTimeFactor':
            case 'HealPowerFactor':
            case 'DefensiveAfflictionCastingTimeFactor':
            case 'DefensiveAfflictionPowerFactor':
                $result = round(100 * ($min + ($max - $min) * $craftValue));
                break;
            case 'ProtectionFactor':
                $result = 100 * ($min + ($max - $min) * $craftValue);
                break;
            case 'MaxSlashingProtection':
            case 'MaxBluntProtection':
            case 'MaxPiercingProtection':
                $result = $itemQuality * ($min + ($max - $min) * $craftValue);
                break;
            default:
                $result = ($min + ($max - $min) * $craftValue);
                break;
        }
        return $result;
    }

    /**
     * Return weapon damage for given weapon quality
     *
     * @param int $quality
     *
     * @return int
     */
    private function getReferenceDamage($quality)
    {
        static $damageTable = array(); // [250][250] matrix

        $maxSkillValue = 250;
        $maxReferenceSkillValue = 250;

        if (empty($damageTable[$quality])) {
            $minDamage = 27;
            $damageStep = 1;
            $exponentialPower = 1;
            $smoothingFactor = 0;

            //for($reference = 0; $reference <= $maxReferenceSkillValue; ++$reference)
            $reference = $quality;
            {
                $dmgLimit = $minDamage + $damageStep * $reference;
                for ($skill = 0; $skill <= $maxSkillValue; ++$skill) {
                    $ref = $minDamage + $damageStep * $skill;
                    $pos = ($skill >= $reference) ? 1 : $skill / $reference;
                    if ($pos < 1) {
                        $value = (($minDamage + ($dmgLimit - $minDamage) * pow($pos, $exponentialPower) + $ref) / 2);
                    } else {
                        if ($skill <= 1) {
                            $value = $ref;
                        } else {
                            $value = $damageTable[$reference][$skill - 1] + ($damageTable[$reference][$skill - 1] - $damageTable[$reference][$skill - 2]) * $smoothingFactor;
                        }
                    }
                    $damageTable[$reference][$skill] = $value;
                }
            }
        }

        $quality = min($quality, $maxReferenceSkillValue);
        $skill = min($quality, $maxSkillValue);

        return (int) $damageTable[$quality][$skill];
    }

    /**
     * Return bonus durability factor based item type.
     * Default bonus is 1.0
     *
     * @param string $type
     * @param string $grade
     *
     * @return float
     */
    private function getDurabilityBonus($type, $grade)
    {
        if (!isset(self::$durabilityBonusMap[$type][$grade])) {
            $type = '__default';
        }
        return self::$durabilityBonusMap[$type][$grade]
            ? self::$durabilityBonusMap[$type][$grade]
            : 1.0;
    }

    /**
     * Map stat name to translation key
     *
     * @param string $stat
     *
     * @return string
     */
    private function getStatUxt($stat)
    {
        if (isset(self::$stat2uxt[$stat])) {
            return self::$stat2uxt[$stat] . '.uxt';
        }
        return $stat;
    }


    /**
     * @return bool|string
     */
    private function preload()
    {
        if (!empty(self::$stats)) {
            return true;
        }

        $path = __DIR__ . '/../../resources/craft_stats.inc.php';
        if (!file_exists($path)) {
            return 'ERROR: failed to load resource info file';
        }
        self::$stats = include $path;

        $this->preloadTypeStats();

        $this->preloadTypeArray();

        return true;
    }

    /**
     * Build type-stat map from type-item list
     */
    private function preloadTypeStats()
    {
        static $map = [
            'DodgeMinModifier' => ['DodgeModifier', 'DodgeMinModifier', 'DodgeMaxModifier'],
            'DodgeMaxModifier' => ['DodgeModifier', 'DodgeMinModifier', 'DodgeMaxModifier'],
            'ParryMinModifier' => ['ParryModifier', 'ParryMinModifier', 'ParryMaxModifier'],
            'ParryMaxModifier' => ['ParryModifier', 'ParryMinModifier', 'ParryMaxModifier'],
            'AdversaryDodgeMinModifier' => [
                'AdversaryDodgeModifier',
                'AdversaryDodgeMinModifier',
                'AdversaryDodgeMaxModifier'
            ],
            'AdversaryDodgeMaxModifier' => [
                'AdversaryDodgeModifier',
                'AdversaryDodgeMinModifier',
                'AdversaryDodgeMaxModifier'
            ],
            'AdversaryParryMinModifier' => [
                'AdversaryParryModifier',
                'AdversaryParryMinModifier',
                'AdversaryParryMaxModifier'
            ],
            'AdversaryParryMaxModifier' => [
                'AdversaryParryModifier',
                'AdversaryParryMinModifier',
                'AdversaryParryMaxModifier'
            ],
        ];

        $notFound = [];
        $this->typeStats = [];
        foreach (self::$typeItems as $type => $itemArray) {
            // $type = 'melee'
            // $itemArray = [Dagger, Sword...]
            foreach ($itemArray as $item) {
                // $item = 'Dagger'
                $found = false;
                // stats = ['Durability' => ['Dagger'=>1, 'Pike'=>0.2, ...]
                foreach (self::$stats as $stat => $statArray) {
                    if (isset($statArray[$item])) {
                        if (substr($stat, -3) === 'Max') {
                            $stat = substr($stat, 0, -3);
                        }
                        if (isset($map[$stat])) {
                            $key = $map[$stat][0];
                            $min = $map[$stat][1];
                            $max = $map[$stat][2];
                        } else {
                            $key = $stat;
                            $min = $stat;
                            $max = $stat . 'Max';
                        }
                        $this->typeStats[$type][$key] = [$min, $max];
                        $found = true;
                    }
                }
                if (!$found) {
                    $notFound[] = $item;
                }
            }
        }
        if (isLOCAL && !empty($notFound)) {
            var_dump('preload: items without stats', $notFound);
        }
    }

    /**
     * Translate and make html safe type dropdown menu items
     */
    private function preloadTypeArray()
    {
        foreach ($this->typeArray as $k => $v) {
            $this->typeArray[$k] = _th($v);
        }
    }

}

