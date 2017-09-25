<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

require_once __DIR__ . '/BunnyToolPage.php';

/**
 * OutpostRegistry.php
 */
class OutpostRegistry extends BunnyToolPage
{
    protected $tag = 'outpost';
    protected $icons = [
        'Tekorn Bramble' => '/outpost/mp_ronce.png',
        'Greslin Filament' => '/outpost/mp_filament.png',
        'Maga Creeper' => '/outpost/mp_liane.png',
        'Egiros Pollen' => '/outpost/mp_pollen.png',
        'Armilo Lichen' => '/outpost/mp_lichen.png',
        'Vedice Sap' => '/outpost/mp_suc.png',
        'Cheng Root' => '/outpost/mp_stem.png',
        'Rubbarn Gum' => '/outpost/mp_gomme.png',
        // flowers
        'Constitution' => '/outpost/ico_flower_constitution.png',
        'Metabolism' => '/outpost/ico_flower_metabolism.png',
        'Strength' => '/outpost/ico_flower_strength.png',
        'Balance' => '/outpost/ico_flower_balance.png',
        'Intelligence' => '/outpost/ico_flower_intelligence.png',
        'Wisdom' => '/outpost/ico_flower_wisdom.png',
        'Dexterity' => '/outpost/ico_flower_dexterity.png',
        'Will' => '/outpost/ico_flower_will.png',
    ];
    protected $outposts = [
        // Desert
        'fyros_outpost_14' => [
            'quality' => 50,
            'region' => 'region_imperialdunes.place',
            'material' => 'Tekorn Bramble',
            'flower' => 'Intelligence'
        ],
        'fyros_outpost_13' => [
            'quality' => 100,
            'region' => 'region_oflovaksoasis.place',
            'material' => 'Greslin Filament',
            'flower' => 'Balance'
        ],
        'fyros_outpost_09' => [
            'quality' => 150,
            'region' => 'region_frahartowers.place',
            'material' => 'Tekorn Bramble',
            'flower' => 'Dexterity'
        ],
        'fyros_outpost_25' => [
            'quality' => 200,
            'region' => 'region_thesavagedunes.place',
            'material' => 'Maga Creeper',
            'flower' => 'Wisdom'
        ],
        'fyros_outpost_04' => [
            'quality' => 200,
            'region' => 'region_dunesofexil.place',
            'material' => 'Egiros Pollen',
            'flower' => 'Constitution'
        ],
        'fyros_outpost_27' => [
            'quality' => 250,
            'region' => 'region_thescorchedcorridor.place',
            'material' => 'Armilo Lichen',
            'flower' => 'Will'
        ],
        'fyros_outpost_28' => [
            'quality' => 250,
            'region' => 'region_thescorchedcorridor.place',
            'material' => 'Vedice Sap',
            'flower' => 'Strength'
        ],
        // Forest
        'matis_outpost_15' => [
            'quality' => 50,
            'region' => 'region_majesticgarden.place',
            'material' => 'Armilo Lichen',
            'flower' => 'Constitution'
        ],
        'matis_outpost_07' => [
            'quality' => 100,
            'region' => 'region_fleetinggarden.place',
            'material' => 'Maga Creeper',
            'flower' => 'Will'
        ],
        'matis_outpost_17' => [
            'quality' => 150,
            'region' => 'region_knollofdissent.place',
            'material' => 'Armilo Lichen',
            'flower' => 'Strength'
        ],
        'matis_outpost_30' => [
            'quality' => 200,
            'region' => 'region_hiddensource.place',
            'material' => 'Greslin Filament',
            'flower' => 'Intelligence'
        ],
        'matis_outpost_03' => [
            'quality' => 200,
            'region' => 'region_upperbog.place',
            'material' => 'Cheng Root',
            'flower' => 'Metabolism'
        ],
        'matis_outpost_24' => [
            'quality' => 250,
            'region' => 'region_groveofconfusion.place',
            'material' => 'Tekorn Bramble',
            'flower' => 'Balance'
        ],
        'matis_outpost_27' => [
            'quality' => 250,
            'region' => 'region_groveofconfusion.place',
            'material' => 'Rubbarn Gum',
            'flower' => 'Dexterity'
        ],
        // Lakes
        'tryker_outpost_06' => [
            'quality' => 50,
            'region' => 'region_libertylake.place',
            'material' => 'Greslin Filament',
            'flower' => 'Strength'
        ],
        'tryker_outpost_24' => [
            'quality' => 100,
            'region' => 'region_windsofmuse.place',
            'material' => 'Tekorn Bramble',
            'flower' => 'Metabolism'
        ],
        'tryker_outpost_10' => [
            'quality' => 150,
            'region' => 'region_thefount.place',
            'material' => 'Greslin Filament',
            'flower' => 'Intelligence'
        ],
        'tryker_outpost_16' => [
            'quality' => 200,
            'region' => 'region_enchantedisle.place',
            'material' => 'Armilo Lichen',
            'flower' => 'Balance'
        ],
        'tryker_outpost_22' => [
            'quality' => 200,
            'region' => 'region_bountybeaches.place',
            'material' => 'Vedice Sap',
            'flower' => 'Constitution'
        ],
        'tryker_outpost_29' => [
            'quality' => 250,
            'region' => 'region_lagoonsofloria.place',
            'material' => 'Maga Creeper',
            'flower' => 'Wisdom'
        ],
        'tryker_outpost_31' => [
            'quality' => 250,
            'region' => 'region_lagoonsofloria.place',
            'material' => 'Egiros Pollen',
            'flower' => 'Dexterity'
        ],
        // Jungle
        'zorai_outpost_08' => [
            'quality' => 50,
            'region' => 'region_citiesofintuition.place',
            'material' => 'Maga Creeper',
            'flower' => 'Dexterity'
        ],
        'zorai_outpost_10' => [
            'quality' => 100,
            'region' => 'region_maidengrove.place',
            'material' => 'Armilo Lichen',
            'flower' => 'Wisdom'
        ],
        'zorai_outpost_22' => [
            'quality' => 150,
            'region' => 'region_havenofpurity.place',
            'material' => 'Maga Creeper',
            'flower' => 'Wisdom'
        ],
        'zorai_outpost_29' => [
            'quality' => 200,
            'region' => 'region_knotofdementia.place',
            'material' => 'Rubbarn Gum',
            'flower' => 'Metabolism'
        ],
        'zorai_outpost_02' => [
            'quality' => 200,
            'region' => 'region_groveofumbra.place',
            'material' => 'Tekorn Bramble',
            'flower' => 'Strength'
        ],
        'zorai_outpost_15' => [
            'quality' => 250,
            'region' => 'region_thevoid.place',
            'material' => 'Cheng Root',
            'flower' => 'Intelligence'
        ],
        'zorai_outpost_16' => [
            'quality' => 250,
            'region' => 'region_thevoid.place',
            'material' => 'Greslin Filament',
            'flower' => 'Will'
        ],
    ];

    /** {@inheritdoc} */
    public function getTitle()
    {
        return _th('Outpost registry');
    }

    public function run(array $get = [], array $post = [])
    {
        // TODO: could be cached
        // cron job is updating outposts.xml file
        $file = __DIR__ . '/../../.cache/outposts.json';
        if (!file_exists($file)) {
            return 'ERROR: outposts.json file does not exists';
        }
        $outposts = json_decode(file_get_contents($file), true);

        $zebraColors = $this->view->getZebraColors();

        $tpl = '
        <tr bgcolor="{color}" valign="middle">
            <td height="20">{name}<br><span style="font-size:smaller;">{region}</span></td>
            <td>{quality}</td>
            <td align="right">{material_icon}</td>
            <td align="left">{material}</td>
            <td align="right">{flower_icon}</td>
            <td align="left">{flower}</td>
            <td align="right">{guild_icon}</td>
            <td align="left">{guild}</td>
        </tr>
        ';

        $tplHeader = '
        <tr bgcolor="{color}" valign="middle">
            <td align="{align}" height="20"><span style="font-weight: bold;color:{font-color};">{name}</span></td>
            <td align="center" ><span style="font-weight: bold;color:{font-color};">{quality}</span></td>
            <td width="24px"></td>
            <td align="center" ><span style="font-weight: bold;color:{font-color};">{material}</span></td>
            <td width="24px"></td>
            <td align="center" ><span style="font-weight: bold;color:{font-color};">{flower}</span></td>
            <td width="24px"></td>
            <td align="center" ><span style="font-weight: bold;color:{font-color};">{guild}</span></td>
        </tr>
        ';

        $sep = '
        <tr valign="middle">
            <td height="5"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        ';

        $html = '<table cellspacing="0" cellpadding="2">';
        $html .= strtr(
            $tplHeader,
            [
                '{color}' => '#101010',
                '{font-color}' => '#e0e0e0',
                '{align}' => 'center',
                '{name}' => _th('Name') . ' / ' . _th('Region'),
                '{quality}' => _th('Quality'),
                '{material}' => _th('Material'),
                '{flower}' => _th('Flower'),
                '{guild}' => _th('Guild'),
            ]
        );

        $i = 0;
        $lastRegion = '';
        foreach ($outposts as $op => $guild) {
            $region = substr($op, 0, strpos($op, '_'));
            if ($lastRegion !== $region) {
                $map = [
                    'fyros' => 'Desert',
                    'matis' => 'Forest',
                    'tryker' => 'Lakes',
                    'zorai' => 'Jungle'
                ];
                if (isset($map[$region])) {
                    $reg = $map[$region];
                } else {
                    $reg = $region;
                }
                if ($lastRegion != '') {
                    $html .= $sep;
                }
                $html .= strtr(
                    $tplHeader,
                    [
                        '{color}' => '#808080',
                        '{font-color}' => 'yellow',
                        '{align}' => 'left',
                        '{name}' => _th($reg),
                        '{region}' => '',
                        '{quality}' => '',
                        '{material}' => '',
                        '{flower}' => '',
                        '{guild}' => '',
                    ]
                );

                $lastRegion = $region;
                $i = 0;
            }

            $opMaterialIcon = '';
            $opFlowerIcon = '';
            if (isset($this->outposts[$op])) {
                $opRegion = _th($this->outposts[$op]['region']);
                $opQuality = (int) $this->outposts[$op]['quality'];
                $opMaterial = _h($this->outposts[$op]['material']);
                if (isset($this->icons[$opMaterial])) {
                    $iconUrl = $this->view->imageUrl($this->icons[$opMaterial]);
                    $opMaterialIcon = '<img src="' . _h($iconUrl) . '" style="max-height:24px;">';
                }
                $opFlower = $this->outposts[$op]['flower'];
                if (isset($this->icons[$opFlower])) {
                    $iconUrl = $this->view->imageUrl($this->icons[$opFlower]);
                    $opFlowerIcon = '<img src="' . _h($iconUrl) . '" style="max-height:24px;">';
                }
            } else {
                $opRegion = '?';
                $opQuality = '?';
                $opMaterial = '?';
                $opFlower = '?';
            }

            $guildIcon = '<img src="http://api.ryzom.com/guild_icon.php?icon=' . _h(
                    $guild['icon']
                ) . '&amp;size=s" style="max-height:24px;">';
            $guildName = _h($guild['name']);

            $html .= strtr(
                $tpl,
                [
                    '{color}' => _cycle($i, $zebraColors),
                    '{name}' => _th($op),
                    '{region}' => $opRegion,
                    '{quality}' => $opQuality,
                    '{material_icon}' => $opMaterialIcon,
                    '{material}' => $opMaterial,
                    '{flower_icon}' => $opFlowerIcon,
                    '{flower}' => $opFlower,
                    '{guild_icon}' => $guildIcon,
                    '{guild}' => $guildName,
                ]
            );

            $i++;
        }
        $html .= '</table>';

        return $html;
    }
}

