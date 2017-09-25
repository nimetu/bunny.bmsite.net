<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

require_once __DIR__ . '/MatsCalculator.php';

/**
 * MatsCalculatorJewel.php
 */
class MatsCalculatorJewel extends MatsCalculator
{
    /** {@inheritdoc} */
    public function getTitle()
    {
        return _th('Mats Calculator - Jewel');
    }

    /** {@inheritdoc} */
    protected function getUsedResources()
    {
        return ['amber', 'seed'];
    }

    /**
     * Return item craftplan
     *
     * @param string $grade [b, m, h]
     * @param string $type  [j]
     * @param string $item  [j]
     *
     * @return array|bool
     */
    protected function getCraftplan($grade, $type, $item)
    {
        $craftplans = [];

        //********************************************************************
        $craftplans['j'] = [
            'j' => [
                'b' => ['amber' => 3, 'seed' => 3],
                'm' => ['amber' => 4, 'seed' => 4],
                'h' => ['amber' => 5, 'seed' => 5],
            ],
        ];

        if (isset($craftplans[$type][$item][$grade])) {
            return $craftplans[$type][$item][$grade];
        }

        return false;
    }

    /** {@inheritdoc} */
    protected function getCraftplanItemArray()
    {
        return $this->getCraftplanTypeArray();
    }

    /** {@inheritdoc} */
    protected function getCraftplanTypeArray()
    {
        return [
            'j' => _th('Jewels'),
        ];
    }
}
