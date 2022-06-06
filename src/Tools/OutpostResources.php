<?php
// (c) 2022 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

require_once __DIR__ . '/BunnyToolPage.php';

/**
 * OutpostResources.php
 */
class OutpostResources extends BunnyToolPage
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
        'Constitution Flower' => '/outpost/ico_flower_constitution.png',
        'Metabolism Flower' => '/outpost/ico_flower_metabolism.png',
        'Strength Flower' => '/outpost/ico_flower_strength.png',
        'Balance Flower' => '/outpost/ico_flower_balance.png',
        'Intelligence Flower' => '/outpost/ico_flower_intelligence.png',
        'Wisdom Flower' => '/outpost/ico_flower_wisdom.png',
        'Dexterity Flower' => '/outpost/ico_flower_dexterity.png',
        'Will Flower' => '/outpost/ico_flower_will.png',
        //
        'Experience Catalyzer' => '/outpost/ico_cataliseur_xp.png',
    ];
    protected $resources = [
        'Magic amplifier' => [
            'Cheng Root' => [
                'mps' => ['m0750dxacc01.sitem', 'm0754dxacc01.sitem', 'm0832dxacc01.sitem'],
                'effect' => 'icokamm2ms_2.sitem'
            ],
            'Maga Creeper' => [
                'mps' => ['m0742dxacc01.sitem', 'm0746dxacc01.sitem', 'm0828dxacc01.sitem'],
                'effect' => 'icokamm2ms_1.sitem'
            ],
        ],
        'Pick' => [
            'Egiros Pollen' => [
                'mps' => ['m0752dxacc01.sitem', 'm0756dxacc01.sitem', 'm0834dxacc01.sitem'],
                'effect' => 'icokamtforage_2.sitem'
            ],
            'Greslin Filament' => [
                'mps' => ['m0744dxacc01.sitem', 'm0748dxacc01.sitem', 'm0830dxacc01.sitem'],
                'effect' => 'icokamtforage_1.sitem'
            ],
        ],
        'Crafting tool' => [
            'Armilo Lichen' => [
                'mps' => ['m0743dxacc01.sitem', 'm0747dxacc01.sitem', 'm0829dxacc01.sitem'],
                'effect' => 'icokamtammo_1.sitem'
            ],
            'Rubbarn Gum' => [
                'mps' => ['m0751dxacc01.sitem', 'm0755dxacc01.sitem', 'm0833dxacc01.sitem'],
                'effect' => 'icokamtammo_2.sitem'
            ],
        ],
        'Weapon' => [
            'Tekorn Bramble' => [
                'mps' => ['m0741dxacc01.sitem', 'm0745dxacc01.sitem', 'm0827dxacc01.sitem'],
                'effect' => 'icokarm1bm_1.sitem'
            ],
            'Vedice Sap' => [
                'mps' => ['m0749dxacc01.sitem', 'm0753dxacc01.sitem', 'm0831dxacc01.sitem'],
                'effect' => 'icokarm1bm_2.sitem'
            ],
        ],
        'XP boost' => [
            'Experience Catalyzer' => ['mps' => ['ixpca01.sitem'], 'effect' => 'ixpca01.sitem'],
        ],
        'Flower' => [
            'Balance Flower' => [
                'mps' => ['ipoc_bal.sitem'],
                'effect' => 'ipoc_bal.sitem'
            ],
            'Constitution Flower' => [
                'mps' => ['ipoc_con.sitem'],
                'effect' => 'ipoc_con.sitem'
            ],
            'Dexterity Flower' => [
                'mps' => ['ipoc_dex.sitem'],
                'effect' => 'ipoc_dex.sitem'
            ],
            'Intelligence Flower' => [
                'mps' => ['ipoc_int.sitem'],
                'effect' => 'ipoc_int.sitem'
            ],
            'Metabolism Flower' => [
                'mps' => ['ipoc_met.sitem'],
                'effect' => 'ipoc_met.sitem'
            ],
            'Strength Flower' => [
                'mps' => ['ipoc_str.sitem'],
                'effect' => 'ipoc_str.sitem'
            ],
            'Will Flower' => [
                'mps' => ['ipoc_wil.sitem'],
                'effect' => 'ipoc_wil.sitem'
            ],
            'Wisdom Flower' => [
                'mps' => ['ipoc_wis.sitem'],
                'effect' => 'ipoc_wis.sitem'
            ],
        ],
    ];

    /** {@inheritdoc} */
    public function getTitle()
    {
        return _th('Outpost resources');
    }

    public function run(array $get = [], array $post = [])
    {
        $zebraColors = $this->view->getZebraColors();

        $tpl = '
        <tr bgcolor="{color}" valign="top">
            <td height="20">{icon}</td>
            <td nowrap>{name}</td>
            <td>{description}</td>
            <td width="2px"></td>
        </tr>
        ';

        $tplHeader = '
        <tr bgcolor="{color}" valign="middle">
            <td width="24px"></td>
            <td><span style="font-weight: bold;color:{font-color};">{name}</span></td>
            <td></td>
            <td width="2px"></td>
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

        $i = 0;
        foreach ($this->resources as $k => $mpArray) {
            if ($i !== 0) {
                $html .= $sep;
                $i = 0;
            }
            $html .= strtr(
                $tplHeader,
                [
                    '{color}' => '#101010',
                    '{font-color}' => '#e0e0e0',
                    '{icon}' => '',
                    '{name}' => _t($k),
                    '{description}' => '',
                ]
            );

            foreach ($mpArray as $idx => $data) {
                $icon = isset($this->icons[$idx]) ? $this->view->imageUrl($this->icons[$idx]) : '';
                if (!empty($icon)) {
                    $icon = '<img src="' . _h($icon) . '" style="max-height:24px;">';
                }

                // translate all listed resources
                $names = [];
                foreach ($data['mps'] as $mp) {
                    $names[] = _t($mp);
                }

                $description = $this->translateEffect($data['effect']);
                if (empty($description)) {
                    $description = _t($data['effect'], 'description');
                }

                $html .= strtr(
                    $tpl,
                    [
                        '{color}' => _cycle($i, $zebraColors),
                        '{name}' => nl2br(_h(join("\n", $names))),
                        '{icon}' => $icon,
                        '{description}' => nl2br(_h($description)),
                    ]
                );

                $i++;
            }
        }
        $html .= '</table>';

        return $html;
    }

    protected function translateEffect($sheet)
    {
        if (empty($sheet)) {
            return '';
        }

        if (!class_exists('RyzomExtra')) {
            return '';
        }

        $lang = defined('LANG') ? LANG : 'en';

        $result = [];

        $quality = 250;
        $props = RyzomExtra::consumable_effects($sheet, $quality, $lang);
        if (!empty($props)) {
            $result[] = "(q{$quality}) " . $this->stripRyzomTags(join("\n", $props));
        }

        $props = RyzomExtra::special_effects($sheet, $lang);
        if (!empty($props)) {
            $result[] = $this->stripRyzomTags(join("\n", $props));
        }

        return join("\n", $result);
    }

    protected function stripRyzomTags($s)
    {
        return preg_replace('/@{[^}]+}/', '', $s);
    }
}

