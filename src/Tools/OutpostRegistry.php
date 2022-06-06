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
    protected $outposts = [
        // Desert
        'fyros_outpost_14' => 'region_imperialdunes.place',
        'fyros_outpost_13' => 'region_oflovaksoasis.place',
        'fyros_outpost_09' => 'region_frahartowers.place',
        'fyros_outpost_25' => 'region_thesavagedunes.place',
        'fyros_outpost_04' => 'region_dunesofexil.place',
        'fyros_outpost_27' => 'region_thescorchedcorridor.place',
        'fyros_outpost_28' => 'region_thescorchedcorridor.place',
        // Forest
        'matis_outpost_15' => 'region_majesticgarden.place',
        'matis_outpost_07' => 'region_fleetinggarden.place',
        'matis_outpost_17' => 'region_knollofdissent.place',
        'matis_outpost_30' => 'region_hiddensource.place',
        'matis_outpost_03' => 'region_upperbog.place',
        'matis_outpost_24' => 'region_groveofconfusion.place',
        'matis_outpost_27' => 'region_groveofconfusion.place',
        // Lakes
        'tryker_outpost_06' => 'region_libertylake.place',
        'tryker_outpost_24' => 'region_windsofmuse.place',
        'tryker_outpost_10' => 'region_thefount.place',
        'tryker_outpost_16' => 'region_enchantedisle.place',
        'tryker_outpost_22' => 'region_bountybeaches.place',
        'tryker_outpost_29' => 'region_lagoonsofloria.place',
        'tryker_outpost_31' => 'region_lagoonsofloria.place',
        // Jungle
        'zorai_outpost_08' => 'region_citiesofintuition.place',
        'zorai_outpost_10' => 'region_maidengrove.place',
        'zorai_outpost_22' => 'region_havenofpurity.place',
        'zorai_outpost_29' => 'region_knotofdementia.place',
        'zorai_outpost_02' => 'region_groveofumbra.place',
        'zorai_outpost_15' => 'region_thevoid.place',
        'zorai_outpost_16' => 'region_thevoid.place',
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
            <td height="20">{name}</td>
            <td>{region}</td>
            <td align="right">{guild_icon}</td>
            <td align="left">{guild}</td>
        </tr>
        ';

        $tplHeader = '
        <tr bgcolor="{color}" valign="middle">
            <td align="{align}" height="20"><span style="font-weight: bold;color:{font-color};">{name}</span></td>
            <td align="{align}" height="20"><span style="font-weight: bold;color:{font-color};">{region}</span></td>
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
        </tr>
        ';

        $html = '<table cellspacing="0" cellpadding="2">';
        $html .= strtr(
            $tplHeader,
            [
                '{color}' => '#101010',
                '{font-color}' => '#e0e0e0',
                '{align}' => 'center',
                '{name}' => _th('Name'),
                '{region}' => _th('Region'),
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
                        '{guild}' => '',
                    ]
                );

                $lastRegion = $region;
                $i = 0;
            }

            if (isset($this->outposts[$op])) {
                $opRegion = _th($this->outposts[$op]);
            } else {
                $opRegion = '?';
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

