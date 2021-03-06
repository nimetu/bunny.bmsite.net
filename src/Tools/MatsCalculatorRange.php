<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

require_once __DIR__ . '/MatsCalculator.php';

/**
 * MatsCalculatorRange.php
 */
class MatsCalculatorRange extends MatsCalculator
{
    /** {@inheritdoc} */
    public function getTitle()
    {
        return _th('Mats Calculator - Range Weapons');
    }

    /** {@inheritdoc} */
    protected function getUsedResources()
    {   // barrel, trigger, firing pin, shaft/bullet, ammoJacket, explosive
        return ['wood', 'seed', 'sap', 'bark', 'resin', 'oil'];
    }

    /**
     * Return item craftplan
     *
     * @param string $grade [b, m, h]
     * @param string $type  [r, p]
     * @param string $item  [1b, 1p, 2a,  2b, 2l, 2r]
     *
     * @return array|bool
     */
    protected function getCraftplan($grade, $type, $item)
    {
        $craftplans = [];

        //********************************************************************
        $craftplans['r'] = [
            // bowpistol
            '1b' => [
                'b' => ['wood' => 2, 'seed' => 1, 'sap' => 1, 'bark' => 1],
                'm' => ['wood' => 2, 'seed' => 2, 'sap' => 2, 'bark' => 1],
                'h' => ['wood' => 2, 'seed' => 2, 'sap' => 2, 'bark' => 2],
            ],
            // pistol
            '1p' => [
                'b' => ['wood' => 1, 'seed' => 1, 'sap' => 1, 'bark' => 1],
                'm' => ['wood' => 2, 'seed' => 1, 'sap' => 1, 'bark' => 1],
                'h' => ['wood' => 2, 'seed' => 2, 'sap' => 2, 'bark' => 1],
            ],
            // autolauncher
            '2a' => [
                'b' => ['wood' => 3, 'seed' => 3, 'sap' => 3, 'bark' => 3],
                'm' => ['wood' => 4, 'seed' => 4, 'sap' => 4, 'bark' => 4],
                'h' => ['wood' => 5, 'seed' => 5, 'sap' => 5, 'bark' => 5],
            ],
            // bowrifle
            '2b' => [
                'b' => ['wood' => 4, 'seed' => 2, 'sap' => 2, 'bark' => 2],
                'm' => ['wood' => 4, 'seed' => 3, 'sap' => 3, 'bark' => 3],
                'h' => ['wood' => 4, 'seed' => 4, 'sap' => 4, 'bark' => 4],
            ],
            // launcher
            '2l' => [
                'b' => ['wood' => 3, 'seed' => 3, 'sap' => 3, 'bark' => 3],
                'm' => ['wood' => 4, 'seed' => 4, 'sap' => 4, 'bark' => 4],
                'h' => ['wood' => 5, 'seed' => 5, 'sap' => 5, 'bark' => 5],
            ],
            // rifle
            '2r' => [
                'b' => ['wood' => 2, 'seed' => 2, 'sap' => 2, 'bark' => 2],
                'm' => ['wood' => 3, 'seed' => 3, 'sap' => 3, 'bark' => 2],
                'h' => ['wood' => 4, 'seed' => 3, 'sap' => 3, 'bark' => 3],
            ],
        ];
        $craftplans['p'] = [
            // bowpistol
            '1b' => [
                'b' => ['resin' => 2, 'bark' => 2, 'oil' => 2],
            ],
            // pistol
            '1p' => [
                'b' => ['resin' => 2, 'bark' => 1, 'oil' => 1],
            ],
            // autolauncher
            '2a' => [
                'b' => ['resin' => 4, 'bark' => 4, 'oil' => 4],
            ],
            // bowrifle
            '2b' => [
                'b' => ['resin' => 2, 'bark' => 2, 'oil' => 2],
            ],
            // launcher
            '2l' => [
                'b' => ['resin' => 4, 'bark' => 4, 'oil' => 4],
            ],
            // rifle
            '2r' => [
                'b' => ['resin' => 2, 'bark' => 1, 'oil' => 1],
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
            'r' => _th('Ranged Weapons'),
            'p' => _th('Ammo'),
        ];
    }

    /** {@inheritdoc} */
    protected function getCraftplanItemArray()
    {
        return [
            '1b' => _th('Bowpistol'),
            '1p' => _th('Pistol'),
            '2a' => _th('Autolauncher'),
            '2b' => _th('Bowrifle'),
            '2l' => _th('Launcher'),
            '2r' => _th('Rifle'),
        ];
    }
}
