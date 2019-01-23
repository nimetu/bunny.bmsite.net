<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

require_once __DIR__ . '/MatsCalculator.php';

/**
 * MatsCalculatorShield.php
 */
class MatsCalculatorShield extends MatsCalculator
{
    /** {@inheritdoc} */
    public function getTitle()
    {
        return _th('Mats Calculator - Shield');
    }

    /** {@inheritdoc} */
    protected function getUsedResources()
    {
        return ['wood', 'sap'];
    }

    /**
     * Return item craftplan
     *
     * @param string $grade [b, m, h]
     * @param string $type  [s]
     * @param string $item  [b, s]
     *
     * @return array|bool
     */
    protected function getCraftplan($grade, $type, $item)
    {
        $craftplans = [];

        //********************************************************************
        $craftplans['s'] = [
            // buckler
            'b' => [
                'b' => ['wood' => 4, 'sap' => 3],
                'm' => ['wood' => 5, 'sap' => 4],
                'h' => ['wood' => 6, 'sap' => 5],
            ],
            // shield
            's' => [
                'b' => ['wood' => 7, 'sap' => 6],
                'm' => ['wood' => 9, 'sap' => 8],
                'h' => ['wood' => 11, 'sap' => 10],
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
            's' => _th('Shields'),
        ];
    }

    /** {@inheritdoc} */
    protected function getCraftplanItemArray()
    {
        return [
            'b' => _th('Bucklers'),
            's' => _th('Shields'),
        ];
    }
}

