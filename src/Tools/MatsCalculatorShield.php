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
        return ['sap', 'wood'];
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
                'b' => ['sap' => 3, 'wood' => 4],
                'm' => ['sap' => 4, 'wood' => 5],
                'h' => ['sap' => 5, 'wood' => 6],
            ],
            // shield
            's' => [
                'b' => ['sap' => 6, 'wood' => 7],
                'm' => ['sap' => 8, 'wood' => 9],
                'h' => ['sap' => 10, 'wood' => 11],
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

