<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

require_once __DIR__ . '/BunnyToolPage.php';

/**
 * ResourceInfo.php
 */
class ResourceInfo extends BunnyToolPage
{
    protected $tag = 'resource';

    private $data = [];
    private $fields = [];

    /** {@inheritdoc} */
    public function getTitle()
    {
        return _th('Resource info (Excellent)');
    }

    public function run(array $get = [], array $post = [])
    {
        if (empty($this->data)) {
            $csvPath = __DIR__ . '/../../resources';
            if ($this->loadResources($csvPath) !== true) {
                return 'ERROR: failed to load resource info file';
            }
        }
        $form = $this->readForm(isset($post['res_info']) ? $post['res_info'] : []);

        // keys are from csv
        $sbZone = $this->view->renderHtmlSelect(
            [
                'name' => 'res_info[zone]',
                'options' => [
                    'fyros' => _th('Desert'),
                    'matis' => _th('Forest'),
                    'tryker' => _th('Lake'),
                    'zorai' => _th('Jungle'),
                ],
                'selected' => $form['zone'],
            ]
        );

        // keys are from csv
        $sbSeason = $this->view->renderHtmlSelect(
            [
                'name' => 'res_info[season]',
                'options' => [
                    'Spring' => _th('Spring'),
                    'Summer' => _th('Summer'),
                    'Autumn' => _th('Autumn'),
                    'Winter' => _th('Winter'),
                ],
                'selected' => $form['season'],
            ]
        );

        // keys are from csv
        $sbFamily = $this->view->renderHtmlSelect(
            [
                'name' => 'res_info[family]',
                'options' => [
                    'Amber' => _th('amber'),
                    'Seed' => _th('seed'),
                    'Fiber' => _th('fiber'),
                    'Bark' => _th('bark'),
                    'Sap' => _th('sap'),
                    'Resin' => _th('resin'),
                    'Oil' => _th('oil'),
                    'Shell' => _th('shell'),
                    'Wood' => _th('wood'),
                    'Wood Node' => _th('woodnode'),
                ],
                'selected' => $form['family'],
            ]
        );

        $sbQuality = $this->view->renderHtmlSelect(
            [
                'name' => 'res_info[quality]',
                'options' => [
                    '050' => '050',
                    '100' => '100',
                    '150' => '150',
                    '200' => '200',
                    '250' => '250',
                ],
                'selected' => $form['quality'],
            ]
        );

        $_submit = _th('Show info');

        $url = _h($this->url);
        $html = <<<EOF
<form method="post" action="$url">
<table cellspacing="0" cellpadding="2">
<tr valign="middle">
    <td>{$sbZone}</td>
    <td>{$sbSeason}</td>
    <td>{$sbFamily}</td>
    <td>{$sbQuality}</td>
    <td><input type="submit" name="submit" value="{$_submit}">
</tr>
</table>
</form>
EOF;

        if (empty($post)) {
            // noop
        } else {
            $zone = strtolower($form['zone']);
            $season = strtolower($form['season']);
            $family = strtolower($form['family']);
            $quality = strtolower($form['quality']);
            if (!isset($this->data[$zone][$quality][$season][$family])) {
                $html .= 'no resources';
                $rows = [];
            } else {
                // reference link
                $rows = &$this->data[$zone][$quality][$season][$family];
            }

            $tplHeader = '
            <tr bgcolor="{bgcolor}" valign="middle">
                <td height="20" colspan="5"><span style="font-weight: bold; color:{font-color};">{name}</span></td>
            </tr>
            ';
            $tplRow = '
            <tr valign="middle" bgcolor="{bgcolor}">
                <td height="20">{name}</td>
                <td align="center" nowrap>{best}</td>
                <td align="center" nowrap>{good}</td>
                <td align="center" nowrap>{bad}</td>
                <td align="center" nowrap>{worst}</td>
            </tr>
            ';

            $zebraColors = $this->view->getZebraColors();
            $html .= '<table width="90%" cellspacing="0" cellpadding="2" bgcolor="#cd6767">';

            $html .= strtr(
                $tplRow,
                [
                    '{bgcolor}' => '#808080',
                    '{name}' => _th('Humidity'),
                    '{best}' => _th('best'),
                    '{good}' => _th('good'),
                    '{bad}' => _th('bad'),
                    '{worst}' => _th('worst'),
                ]
            );

            $daySymbol = '☼';
            $nightSymbol = '◑';//'☽';
            $naSymbol = '<span style="color: #505050;">-</span>';

            // ingame has dark theme, so can use colors
            if ($this->view->isIngame()) {
                $daySymbol = '<span style="color: #ffff00;">' . $daySymbol . '</span>';
                $nightSymbol = '<span style="color: #ffffff;">' . $nightSymbol . '</span>';
            }

            if (empty($rows)) {
                $html .= strtr(
                    $tplHeader,
                    [
                        '{bgcolor}' => '#000000',
                        '{font-color}' => 'yellow',
                        '{name}' => 'no info',
                    ]
                );
            } else {
                foreach ($rows as $region => $resourceArray) {
                    $subregion = _h($region);
                    $ecosystem = '';
                    if (!empty($resourceArray)) {
                        $tmp = current($resourceArray);
                        $ecosystem = _h($tmp['ecosystem']);
                    }
                    $subregion .= ' (' . _h($ecosystem) . ' - ' . (int) $quality . ')';

                    $html .= strtr(
                        $tplHeader,
                        [
                            '{bgcolor}' => '#000000',
                            '{font-color}' => 'yellow',
                            '{name}' => $subregion,
                        ]
                    );
                    $i = 0;
                    foreach ($resourceArray as $resource) {
                        $inDay = isset($resource['day']) && $resource['day'] == true;
                        $inNight = isset($resource['night']) && $resource['night'] == true;

                        if ($inDay && $inNight) {
                            $timeOfDay = $daySymbol . '&nbsp;' . $nightSymbol;
                        } elseif ($inDay) {
                            $timeOfDay = $daySymbol;
                        } elseif ($inNight) {
                            $timeOfDay = $nightSymbol;
                        } else {
                            $timeOfDay = '?';
                        }

                        $html .= strtr(
                            $tplRow,
                            [
                                '{bgcolor}' => _cycle($i, $zebraColors),
                                '{name}' => _th($resource['resource']),
                                '{best}' => isset($resource['best']) ? $timeOfDay : $naSymbol,
                                '{good}' => isset($resource['good']) ? $timeOfDay : $naSymbol,
                                '{bad}' => isset($resource['bad']) ? $timeOfDay : $naSymbol,
                                '{worst}' => isset($resource['worst']) ? $timeOfDay : $naSymbol,
                            ]
                        );
                        $i++;
                    }
                }
            }

            $html .= '</table>';

            $html .= '
            <hr>
            <table width="100%">
            <tr valign="middle">
                <td nowrap height="20">Day (' . $daySymbol . ')</td><td width="95%">03:00 - 22:00</td>
            </tr>
            <tr valign="middle">
                <td nowrap height="20">Night (' . $nightSymbol . ')</td><td>22:00 - 03:00</td>
            </tr>
            <tr valign="middle">
                <td nowrap height="20">Both (' . $daySymbol.$nightSymbol . ')</td><td>no time restriction</td>
            </tr>
            </table>
            ';
        }

        return $html;
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
        $defaults = [
            'zone' => 'fyros',
            'season' => 'spring',
            'family' => 'amber',
            'quality' => '050',
        ];

        if (empty($post)) {
            return $defaults;
        }

        //********************************************************************
        $data = $this->user->get($this->tag, []);
        foreach ($defaults as $k => $v) {
            if (!isset($data[$k])) {
                $data[$k] = $v;
            }
        }

        //********************************************************************
        foreach (array_keys($data) as $k) {
            if (isset($post[$k])) {
                $data[$k] = $post[$k];
            }
        }

        //********************************************************************
        $this->storage->set($this->tag, $data);

        return $data;
    }

    /**
     * Group regions to zone names if all regions from zones are present
     *
     * @param string $name
     *
     * @return array
     */
    private function getRegionZone($name)
    {
        // TODO: load from file
        static $groups = [
            'fyros' => [
                'Dunes of Exile',
                'Frahar Towers',
                'Imperial Dunes',
                'Oflak\'s Oasis',
                'Outlaw Canyon',
                'Savage Dunes',
                'Sawdust Mines',
                'Scorched Corridor',
            ],
            'matis' => [
                'Fleeting Garden',
                'Grove of Confusion',
                'Heretics Hovel',
                'Knoll of Dissent',
                'Majestic Garden',
                'Upper Bog',
                // desert
                'Hidden Source',
                // pr?
                'Nexus',
            ],
            'tryker' => [
                'Bounty Beaches',
                'Dew Drops',
                'Enchanted Islae',
                'Fount',
                'Lands of Loria',
                'Liberty Lakes',
                'Resting Water',
                'Winds of Muse',
            ],
            'zorai' => [
                'Cities of Intuition',
                'Grove of Umbra',
                'Haven of Purity',
                'Knot of Dementia',
                'Maiden Grove',
                'Void'
            ],
        ];

        foreach ($groups as $gIndex => $gArray) {
            if (in_array($name, $gArray)) {
                return $gIndex;
            }
        }

        return 'unk.zone';
    }

    /**
     * Load all resource files from path
     *
     * @param string $csvPath
     *
     * @return bool
     */
    private function loadResources($csvPath)
    {
        $cacheFile = $csvPath . '/../.cache/resource-info.json';
        if (file_exists($cacheFile)) {
            // use cache if its newer than this php file
            if (filemtime($cacheFile) > filemtime(__FILE__)) {
                $json = json_decode(file_get_contents($cacheFile), true);
                $this->data = $json;
                return true;
            }
        }

        $this->data = [];
        $this->fields = [];

        $files = glob($csvPath . '/ex-mats-*.csv');
        foreach ($files as $file) {
            $this->loadFile($file);
        }

        $json = json_encode($this->data, JSON_PRETTY_PRINT);
        file_put_contents($cacheFile, $json);

        return true;
    }

    /**
     * Parse resource csv file
     *
     * @param $csvFile
     *
     * @return bool
     */
    private function loadFile($csvFile)
    {
        $lines = file($csvFile);

        $regions = [];
        $types = [];
        $cFamily = 'unk.family';

        // TODO: load from file
        $knownFamilies = [
            'amber',
            'seed',
            'fiber',
            'bark',
            'sap',
            'resin',
            'oil',
            'shell',
            'wood',
            'wood node',
        ];

        $dayNight = [
            'anete' => 'night',
            'dzao' => 'night',
            'shu' => 'night',
            'dung' => 'night',
            'fung' => 'night',
            'glue' => 'night',
            'moon' => 'night',
            'gulatch' => 'night',
            'irin' => 'night',
            'koorin' => 'night',
            'pilan' => 'night',
        ];
        $dayNightVoid = [
            'sha' => 'night',
            'zun' => 'night',
        ];

        foreach ($lines as $i => $line) {
            $parts = explode("\t", $line);
            $parts = array_map('trim', $parts);

            if ($i === 0) {
                $regions = $this->parseRegionLine($parts);
                if ($regions === false) {
                    return false;
                }
                continue;
            } elseif ($i === 1) {
                $types = $this->parseMatTypeLine($parts);
                if ($types === false) {
                    return false;
                }
                continue;
            } elseif ($i === 2) {
                // Material, Spring, Sum. Aut. ...
                $fields = $this->readFields($parts);
                continue;
            } else if ($i > 2 && empty($parts[0])) {
                // empty line
                continue;
            } else if ($i > 2 && empty($parts[1])) {
                // Amber, Seed, ...

                $tmp = strtolower($parts[0]);
                if (in_array($tmp, $knownFamilies)) {
                    $cFamily = $tmp;
                    continue;
                }
            }

            {
                $cZone = 'unk.zone';
                $cSeason = 'unk.field';
                $cResource = 'unk.resource';
                $cQuality = 'unk.quality';
                $cRegion = 'unk.region';
                $cEcosystem = 'unk.type';

                foreach ($parts as $pk => $pv) {
                    if ($pk === 0) {
                        $cResource = strtolower($pv);
                        continue;
                    }

                    // skip totally empty columns
                    if (empty($pv)) {
                        continue;
                    }

                    // Imperial Dunes (050)
                    if (isset($regions[$pk])) {
                        $cRegion = $regions[$pk];
                        if (preg_match('/(.*)\(([^)]*)\)$/', $cRegion, $match)) {
                            $cRegion = trim($match[1]);
                            $cQuality = $match[2];
                        } else {
                            $cQuality = 'unk.quality';
                        }
                        $cZone = $this->getRegionZone($cRegion);
                    }
                    // Desert Materials
                    if (isset($types[$pk])) {
                        $cEcosystem = $types[$pk];
                    }
                    // Spring
                    if (isset($fields[$pk])) {
                        $cSeason = $fields[$pk];
                        $replace = ['Sum.' => 'Summer', 'Aut.' => 'Autumn'];
                        if (isset($replace[$cSeason])) {
                            $cSeason = $replace[$cSeason];
                        }
                        $cSeason = strtolower($cSeason);
                    }

                    // reference link
                    $row = &$this->data[$cZone][$cQuality][$cSeason][$cFamily][$cRegion][$cResource];
                    $row['zone'] = $cZone;
                    $row['region'] = $cRegion;
                    $row['ecosystem'] = $cEcosystem;
                    $row['quality'] = $cQuality;
                    $row['season'] = $cSeason;
                    $row['family'] = $cFamily;
                    $row['resource'] = $cResource;

                    $pv = strtolower($pv);
                    if ($pv !== 'na') {
                        if ($pv == 'worse') {
                            $pv = 'worst';
                        }
                        $row[$pv] = true;

                        if ($cRegion == 'Nexus') {
                            $row['day'] = true;
                            $row['night'] = true;
                        } else {
                            if ($cRegion === 'Void' && isset($dayNightVoid[$cResource])) {
                                $row['night'] = $dayNightVoid[$cResource] == 'night' || $dayNightVoid[$cResource] == 'day/night';
                                $row['day'] = $dayNightVoid[$cResource] == 'day' || $dayNightVoid[$cResource] == 'day/night';
                            } elseif (isset($dayNight[$cResource])) {
                                $row['night'] = $dayNight[$cResource] == 'night' || $dayNight[$cResource] == 'day/night';
                                $row['day'] = $dayNight[$cResource] == 'day' || $dayNight[$cResource] == 'day/night';
                            } else {
                                $row['day'] = true;
                            }
                        }
                    }

                    unset($row);
                }
            }
        }

        return true;
    }

    /**
     * Parse csv file 'Region' line
     *
     * @param array $parts
     *
     * @return bool
     */
    private function parseRegionLine(array $parts)
    {
        if ($parts[0] !== 'Region') {
            return false;
        }
        unset($parts[0]);
        return $this->readFields($parts);
    }

    /**
     * Read ecosystem line
     *
     * @param array $parts
     *
     * @return array|bool
     */
    private function parseMatTypeLine(array $parts)
    {
        if ($parts[0] !== 'Mat. Type') {
            return false;
        }
        unset($parts[0]);
        return $this->readFields($parts);
    }

    /**
     * Return non-empty fields from array
     *
     * @param array $parts
     *
     * @return array
     */
    private function readFields($parts)
    {
        $fields = [];
        foreach ($parts as $pi => $pv) {
            if (!empty($pv)) {
                $fields[$pi] = $pv;
            }
        }
        return $fields;
    }

}

