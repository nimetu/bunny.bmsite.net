<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

require_once __DIR__ . '/MatsCalculator.php';

/**
 * MatsCalculatorJewel.php
 */
class MatsCalculatorMelee extends MatsCalculator
{
    /** {@inheritdoc} */
    public function getTitle()
    {
        return _th('Mats Calculator - Melee Weapons');
    }

    /** {@inheritdoc} */
    protected function getUsedResources()
    {
        // blade/point, shaft, grip, hammer/counterweight, magic_focus
        return ['shell', 'bark', 'grip', 'woodnode', 'amber'];
    }

    /**
     * Return item craftplan
     *
     * @param string $grade [b, m, h]
     * @param string $type  [m]
     * @param string $item  [1bm, 1bs, 1pd, 1ps, 1sa, 1ss, 2bm, 2pp, 2sa, 2ss]
     *
     * @return array|bool
     */
    protected function getCraftplan($grade, $type, $item)
    {
        $craftplans = [];

        //********************************************************************
        $craftplans['m'] = [
            // mace
            '1bm' => [
                'b' => ['bark' => 3, 'grip' => 3, 'woodnode' => 5 + 3],
                'm' => ['bark' => 5, 'grip' => 5, 'woodnode' => 5 + 4],
                'h' => ['bark' => 6, 'grip' => 6, 'woodnode' => 6 + 5],
            ],
            // staff
            '1bs' => [
                'b' => ['bark' => 7, 'grip' => 7],
                'm' => ['bark' => 10, 'grip' => 9],
                'h' => ['bark' => 12, 'grip' => 11],
            ],
            // dagger
            '1pd' => [
                'b' => ['shell' => 2, 'bark' => 2, 'grip' => 1, 'woodnode' => 1],
                'm' => ['shell' => 2, 'bark' => 2, 'grip' => 2, 'woodnode' => 2],
                'h' => ['shell' => 3, 'bark' => 3, 'grip' => 2, 'woodnode' => 2],
            ],
            // spear
            '1ps' => [
                'b' => ['shell' => 5, 'bark' => 5, 'grip' => 4],
                'm' => ['shell' => 7, 'bark' => 6, 'grip' => 6],
                'h' => ['shell' => 8, 'bark' => 8, 'grip' => 7],
            ],
            // axe
            '1sa' => [
                'b' => ['shell' => 5, 'bark' => 3, 'grip' => 3, 'woodnode' => 3],
                'm' => ['shell' => 5, 'bark' => 5, 'grip' => 4, 'woodnode' => 4],
                'h' => ['shell' => 6, 'bark' => 6, 'grip' => 6, 'woodnode' => 5],
            ],
            // sword
            '1ss' => [
                'b' => ['shell' => 4, 'bark' => 4, 'grip' => 3, 'woodnode' => 3],
                'm' => ['shell' => 5, 'bark' => 5, 'grip' => 5, 'woodnode' => 4],
                'h' => ['shell' => 6, 'bark' => 6, 'grip' => 6, 'woodnode' => 5],
            ],
            // 2h mace
            '2bm' => [
                'b' => ['bark' => 5, 'grip' => 5, 'woodnode' => 5 + 5],
                'm' => ['bark' => 7, 'grip' => 7, 'woodnode' => 7 + 6],
                'h' => ['bark' => 8, 'grip' => 8, 'woodnode' => 9 + 8],
            ],
            // magic amp
            '2ms' => [
                'b' => ['bark' => 5, 'grip' => 5, 'amber' => 10],
                'm' => ['bark' => 6, 'grip' => 6, 'amber' => 15],
                'h' => ['bark' => 7, 'grip' => 6, 'amber' => 20],
            ],
            // 2h pike
            '2pp' => [
                'b' => ['shell' => 7, 'bark' => 7, 'grip' => 6],
                'm' => ['shell' => 9, 'bark' => 9, 'grip' => 9],
                'h' => ['shell' => 11, 'bark' => 11, 'grip' => 11],
            ],
            // 2h axe
            '2sa' => [
                'b' => ['shell' => 5, 'bark' => 5, 'grip' => 5, 'woodnode' => 5],
                'm' => ['shell' => 7, 'bark' => 7, 'grip' => 7, 'woodnode' => 6],
                'h' => ['shell' => 9, 'bark' => 8, 'grip' => 8, 'woodnode' => 8],
            ],
            // 2h sword
            '2ss' => [
                'b' => ['shell' => 5, 'bark' => 5, 'grip' => 5, 'woodnode' => 5],
                'm' => ['shell' => 7, 'bark' => 7, 'grip' => 7, 'woodnode' => 6],
                'h' => ['shell' => 9, 'bark' => 8, 'grip' => 8, 'woodnode' => 8],
            ],
        ];

        if (isset($craftplans[$type][$item][$grade])) {
            return $craftplans[$type][$item][$grade];
        }

        return false;
    }

    /** {@inheritdoc} */
    protected function getCraftplanTypeArray()
    {
        return [
            'm' => _th('Melee weapons'),
        ];
    }

    /** {@inheritdoc} */
    protected function getCraftplanItemArray()
    {
        return [
            '1bm' => _th('Mace'),
            '1bs' => _th('Staff'),
            '1pd' => _th('Dagger'),
            '1ps' => _th('Spear'),
            '1sa' => _th('Axe'),
            '1ss' => _th('Sword'),
            '2bm' => _th('2h Mace'),
            '2ms' => _th('Magic Amplifier'),
            '2pp' => _th('2h Pike'),
            '2sa' => _th('2h Axe'),
            '2ss' => _th('2h Sword'),
        ];
    }
}
