<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

require_once __DIR__ . '/MatsCalculator.php';

/**
 * MatsCalculatorArmor.php
 */
class MatsCalculatorArmor extends MatsCalculator
{
    /** {@inheritdoc} */
    public function getTitle()
    {
        return _th('Mats Calculator - Armor');
    }

    /** {@inheritdoc} */
    protected function getUsedResources()
    {
        return ['fiber', 'wood', 'resin', 'oil', 'sap'];
    }

    /**
     * Return item craftplan
     *
     * @param string $grade [b, m, h]
     * @param string $type  [l, m, h]
     * @param string $item  [b, p, v, s, g, h]
     *
     * @return array|bool
     */
    protected function getCraftplan($grade, $type, $item)
    {
        $craftplans = [];

        //********************************************************************
        // light armor
        $craftplans['l'] = [
            // pants, vest
            'p' => [
                'b' => ['fiber' => 3, 'resin' => 3, 'oil' => 1, 'sap' => 1],
                'm' => ['fiber' => 4, 'resin' => 4, 'oil' => 1, 'sap' => 1],
                'h' => ['fiber' => 5, 'resin' => 5, 'oil' => 2, 'sap' => 2],
            ],
            // sleeves, gloves, boots
            's' => [
                'b' => ['fiber' => 2, 'resin' => 2, 'oil' => 1, 'sap' => 1],
                'm' => ['fiber' => 3, 'resin' => 3, 'oil' => 1, 'sap' => 1],
                'h' => ['fiber' => 3, 'resin' => 3, 'oil' => 2, 'sap' => 2],
            ],
        ];
        // vest = pants
        $craftplans['l']['v'] = $craftplans['l']['p'];
        // gloves = sleeves
        $craftplans['l']['g'] = $craftplans['l']['s'];
        // boots = sleeves
        $craftplans['l']['b'] = $craftplans['l']['s'];

        //********************************************************************
        // medium armor
        $craftplans['m'] = [
            // pants, vest
            'p' => [
                'b' => ['wood' => 5, 'resin' => 5, 'oil' => 3, 'sap' => 2],
                'm' => ['wood' => 7, 'resin' => 7, 'oil' => 3, 'sap' => 3],
                'h' => ['wood' => 8, 'resin' => 8, 'oil' => 5, 'sap' => 4],
            ],
            // sleeves, gloves, boots
            's' => [
                'b' => ['wood' => 4, 'resin' => 4, 'oil' => 2, 'sap' => 2],
                'm' => ['wood' => 5, 'resin' => 5, 'oil' => 3, 'sap' => 3],
                'h' => ['wood' => 6, 'resin' => 6, 'oil' => 4, 'sap' => 4],
            ],
        ];
        // vest = pants
        $craftplans['m']['v'] = $craftplans['m']['p'];
        // gloves = sleeves
        $craftplans['m']['g'] = $craftplans['m']['s'];
        // boots = sleeves
        $craftplans['m']['b'] = $craftplans['m']['s'];

        //********************************************************************
        // heavy armor
        $craftplans['h'] = [
            // pants, vest, helmet
            'p' => [
                'b' => ['wood' => 7, 'resin' => 7, 'oil' => 5, 'sap' => 5],
                'm' => ['wood' => 9, 'resin' => 9, 'oil' => 6, 'sap' => 6],
                'h' => ['wood' => 12, 'resin' => 12, 'oil' => 8, 'sap' => 8],
            ],
            // sleeves, gloves, boots
            's' => [
                'b' => ['wood' => 5, 'resin' => 5, 'oil' => 4, 'sap' => 4],
                'm' => ['wood' => 7, 'resin' => 7, 'oil' => 5, 'sap' => 5],
                'h' => ['wood' => 10, 'resin' => 10, 'oil' => 6, 'sap' => 6],
            ],
        ];
        // helmet = pants
        $craftplans['h']['h'] = $craftplans['h']['p'];
        // vest = pants
        $craftplans['h']['v'] = $craftplans['h']['p'];
        // gloves = sleeves
        $craftplans['h']['g'] = $craftplans['h']['s'];
        // boots = sleeves
        $craftplans['h']['b'] = $craftplans['h']['s'];

        if (isset($craftplans[$type][$item][$grade])) {
            return $craftplans[$type][$item][$grade];
        }

        return false;
    }

    /** {@inheritdoc} */
    protected function getCraftplanTypeArray()
    {
        return [
            'l' => _th('Light Armor'),
            'm' => _th('Medium Armor'),
            'h' => _th('Heavy Armor'),
        ];
    }

    /** {@inheritdoc} */
    protected function getCraftplanItemArray()
    {
        return [
            'b' => _th('Boots'),
            'p' => _th('Pants'),
            'v' => _th('Vest'),
            's' => _th('Sleeves'),
            'g' => _th('Gloves'),
            'h' => _th('Helmet'),
        ];
    }
}
