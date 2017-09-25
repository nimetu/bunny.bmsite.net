<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

require_once __DIR__ . '/BunnyToolPage.php';

/**
 * Armor mats calculator
 */
abstract class MatsCalculator extends BunnyToolPage
{
    protected $tag = 'matsCalc';

    protected $maxBagBulk = 300;
    protected $maxMountBulk = 300;
    protected $maxPackerBulk = 500;

    /**
     * @param array $post
     *
     * @return string
     */
    public function run(array $get = [], array $post = [])
    {
        $form = $this->readForm(isset($post['mats_calc']) ? $post['mats_calc'] : []);

        $bagBulk = range(0, $this->maxBagBulk);
        $mountBulk = range(0, $this->maxMountBulk);
        $packerBulk = range(0, $this->maxPackerBulk);

        $form['bag'] = _clamp($form['bag'], 0, $this->maxBagBulk);
        $form['mount'] = _clamp($form['mount'], 0, $this->maxMountBulk);
        $form['packer1'] = _clamp($form['packer1'], 0, $this->maxPackerBulk);
        $form['packer2'] = _clamp($form['packer2'], 0, $this->maxPackerBulk);
        $form['packer3'] = _clamp($form['packer3'], 0, $this->maxPackerBulk);

        $_bag = _th('Character');
        $_mount = _th('Mount');
        $_packer1 = _th('Packer') . ' 1';
        $_packer2 = _th('Packer') . ' 2';
        $_packer3 = _th('Packer') . ' 3';
        $sbBag = $this->view->renderHtmlSelect(
            [
                'name' => 'mats_calc[bag]',
                'options' => $bagBulk,
                'selected' => $form['bag'],
            ]
        );
        $sbMount = $this->view->renderHtmlSelect(
            [
                'name' => 'mats_calc[mount]',
                'options' => $mountBulk,
                'selected' => $form['mount'],
            ]
        );
        $sbPacker1 = $this->view->renderHtmlSelect(
            [
                'name' => 'mats_calc[packer1]',
                'options' => $packerBulk,
                'selected' => $form['packer1'],
            ]
        );
        $sbPacker2 = $this->view->renderHtmlSelect(
            [
                'name' => 'mats_calc[packer2]',
                'options' => $packerBulk,
                'selected' => $form['packer2'],
            ]
        );
        $sbPacker3 = $this->view->renderHtmlSelect(
            [
                'name' => 'mats_calc[packer3]',
                'options' => $packerBulk,
                'selected' => $form['packer3'],
            ]
        );

        $cbMount = $this->view->renderHtmlCheckbox(
            [
                'name' => 'mats_calc[use_mount]',
                'checked' => $form['use_mount'],
            ]
        );

        $cbPacker1 = $this->view->renderHtmlCheckbox(
            [
                'name' => 'mats_calc[use_packer1]',
                'checked' => $form['use_packer1'],
            ]
        );
        $cbPacker2 = $this->view->renderHtmlCheckbox(
            [
                'name' => 'mats_calc[use_packer2]',
                'checked' => $form['use_packer2'],
            ]
        );
        $cbPacker3 = $this->view->renderHtmlCheckbox(
            [
                'name' => 'mats_calc[use_packer3]',
                'checked' => $form['use_packer3'],
            ]
        );

        $resources = $this->getUsedResources();

        $tpl = '
        <tr valign="middle">
            <td height="20">{text}</td>
            <td width="5"></td>
            <td>{input}</td>
        </tr>
        ';
        $resourceTable = '<table cellspacing="0" cellpadding="2">';
        foreach ($resources as $name) {
            $resourceTable .= strtr(
                $tpl,
                [
                    '{text}' => _th($name),
                    '{input}' => $this->view->renderHtmlInput(
                        [
                            'name' => 'mats_calc[' . _h($name) . ']',
                            'value' => $form[$name],
                            'size' => 100,
                            'style' => 'width: 100px',
                        ]
                    ),
                ]
            );
        }
        $resourceTable .= '</table>';

        $sbGrade = $this->view->renderHtmlSelect(
            [
                'name' => 'mats_calc[grade]',
                'options' => [
                    'b' => _th('Basic'),
                    'm' => _th('Medium'),
                    'h' => _th('High'),
                ],
                'selected' => $form['grade'],
            ]
        );
        $sbType = $this->view->renderHtmlSelect(
            [
                'name' => 'mats_calc[type]',
                'options' => $this->getCraftplanTypeArray(),
                'selected' => $form['type'],
            ]
        );
        $sbItem = $this->view->renderHtmlSelect(
            [
                'name' => 'mats_calc[item]',
                'options' => $this->getCraftplanItemArray(),
                'selected' => $form['item'],
            ]
        );
        $_calculate = _th('Calculate');

        $_bulkUsed = _th('Bulk used');
        $_matsOnHand = _th('Mats on hand*');

        $url = _h($this->url);

        $html = <<<EOF
<form method="post" action="{$url}">
<table cellspacing="0" cellpadding="0">
<tr valign="middle" bgcolor="#101010">
    <td width="20">&nbsp;</td>
    <td height="20" align="center"><span style="font-weight: bold; color: yellow;">{$_bulkUsed}</span></td>
    <td width="20">&nbsp;</td>
    <td align="center"><span style="font-weight: bold; color: yellow;">{$_matsOnHand}</span></td>
    <td width="20">&nbsp;</td>
</tr>
<tr valign="top">
<td></td>
<td>
    <table cellspacing="0" cellpadding="2">
    <tr valign="middle">
        <td height="25" align="right">{$_bag}</td><td width="2"></td><td>{$sbBag}</td><td></td>
    </tr>
    <tr valign="middle">
        <td height="25" align="right">{$_mount}</td><td width="2"></td><td>{$sbMount}</td><td>{$cbMount}</td>
    </tr>
    <tr valign="middle">
        <td height="25" align="right">{$_packer1}</td><td width="2"></td><td>{$sbPacker1}</td><td>{$cbPacker1}</td>
    </tr>
    <tr valign="middle">
        <td height="25" align="right">{$_packer2}</td><td width="2"></td><td>{$sbPacker2}</td><td>{$cbPacker2}</td>
    </tr>
    <tr valign="middle">
        <td height="25" align="right">{$_packer3}</td><td width="2"></td><td>{$sbPacker3}</td><td>{$cbPacker3}</td>
    </tr>
    </table>
</td>
<td></td>
<td>
    {$resourceTable}
</td>
<td></td>
</tr>
</table>
<br>
<table cellspacing="0" cellpadding="2">
<tr>
    <td>{$sbGrade}</td>
    <td>{$sbType}</td>
    <td>{$sbItem}</td>
    <td><input type="submit" name="submit" value="{$_calculate}"></td>
</tr>
</table>
</form>
EOF;

        $html .= '<br>';

        if ($form['__calculate']) {
            $result = $this->calculate($form);
            if (isset($result['error'])) {
                $html .= '<p>' . _th('Error') . ': ' . _h($result['error']) . '</p>';
            } else {
                $zebraColors = $this->view->getZebraColors();

                $tpl = '
                <tr valign="middle" bgcolor="{color}">
                    <td height="20" width="10"></td>
                    <td align="right">{1x}</td>
                    <td width="5"></td>
                    <td>{mat}</td>
                    <td align="right">{to_dig}</td>
                    <td align="right">{to_craft}</td>
                    <td align="right">{in_bag}</td>
                    <td width="10"></td>
                </tr>
                ';
                $sep = '
                <tr bgcolor="#808080">
                    <td height="2"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>';

                $html .= strtr(
                    _th('{items} items craftable with {available_bulk} bulk available.'),
                    [
                        '{items}' => $result['nb_items'],
                        '{available_bulk}' => $result['bulk'],
                    ]
                );
                $html .= '<br><br>';
                $html .= '<table width="300px" cellspacing="0" cellpadding="0">';
                $html .= strtr(
                    $tpl,
                    [
                        '{color}' => $zebraColors[0],
                        '{mat}' => '',
                        '{1x}' => '',
                        '{to_dig}' => _th('To harvest'),
                        '{to_craft}' => _th('In total'),
                        '{in_bag}' => _th('For bag'),
                    ]
                );
                $html .= $sep;

                $i = 0;
                foreach ($result['craftplan'] as $mat => $needed) {
                    $html .= strtr(
                        $tpl,
                        [
                            '{color}' => _cycle($i, $zebraColors),
                            '{mat}' => _th($mat),
                            '{1x}' => $needed,
                            '{to_dig}' => intval($result['to_dig'][$mat]),
                            '{to_craft}' => intval($result['to_craft'][$mat]),
                            '{in_bag}' => intval($result['in_bag'][$mat]),
                        ]
                    );
                    $i++;
                }

                $html .= $sep;
                $html .= strtr(
                    $tpl,
                    [
                        '{color}' => $zebraColors[1],
                        '{mat}' => '',
                        '{1x}' => '',
                        '{to_dig}' => array_sum($result['to_dig']),
                        '{to_craft}' => array_sum($result['to_craft']),
                        '{in_bag}' => array_sum($result['in_bag']),
                    ]
                );
                $html .= '</table>';

                if ($result['used_bulk'] != $result['bulk']) {
                    $html .= strtr(
                        _th('This leaves {free} bulk free.'),
                        [
                            '{free}' => sprintf('%.1f', $result['used_bulk'] - $result['bulk']),
                        ]
                    );
                }
            }
        }

        $html .= '<p style="font-size: smaller;">' . _th('* Already in bag or packer(s).') . '</p>';
        $html .= '<br>';

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
        $data = $this->restoreForm();

        if (!empty($post)) {
            $data['__calculate'] = true;
            foreach (array_keys($data) as $k) {
                if (isset($post[$k])) {
                    $data[$k] = $post[$k];
                }
            }

            // read checkboxes and set missing as false
            foreach (['use_mount', 'use_packer1', 'use_packer2', 'use_packer3'] as $k) {
                if (isset($post[$k])) {
                    $data[$k] = ($data[$k] === true || $data[$k] === 'on');
                } else {
                    $data[$k] = false;
                }
            }
        }

        $this->saveForm($data);

        return $data;
    }

    /**
     * Restore class form data
     *
     * @return array
     */
    private function restoreForm()
    {
        $defaults = [
            'bag' => 100,
            'mount' => 0,
            'packer1' => 0,
            'packer2' => 0,
            'packer3' => 0,
            'use_mount' => true,
            'use_packer1' => true,
            'use_packer2' => true,
            'use_packer3' => true,
            //
            'grade' => '',
            'type' => '',
            'item' => '',
        ];

        //********************************************************************
        // restore bulk state - shared
        $saved = $this->user->get($this->tag, []);
        foreach ([
                     'bag',
                     'mount',
                     'packer1',
                     'packer2',
                     'packer3',
                     'use_mount',
                     'use_packer1',
                     'use_packer2',
                     'use_packer3',
                 ] as $k) {
            if (isset($saved['bulk'][$k])) {
                $defaults[$k] = $saved['bulk'][$k];
            }
        }

        //********************************************************************
        // form options and resources are per class
        $class = get_called_class();

        //********************************************************************
        // restore used resources
        foreach ($this->getUsedResources() as $k) {
            if (isset($saved[$class][$k])) {
                $defaults[$k] = $saved[$class][$k];
            } else {
                $defaults[$k] = 0;
            }
        }

        //********************************************************************
        // select boxes
        foreach (['grade', 'type', 'item'] as $k) {
            if (isset($saved[$class][$k])) {
                $defaults[$k] = $saved[$class][$k];
            }
        }

        $defaults['__calculate'] = !empty($defaults['grade']) && !empty($defaults['type']) && !empty($defaults['item']);

        return $defaults;
    }

    /**
     * Save form data
     *
     * @param array $data
     */
    private function saveForm(array $data)
    {
        $saved = $this->user->get($this->tag, false);
        $dirty = false;

        //********************************************************************
        // bulk info - shared
        foreach ([
                     'bag',
                     'mount',
                     'packer1',
                     'packer2',
                     'packer3',
                     'use_mount',
                     'use_packer1',
                     'use_packer2',
                     'use_packer3',
                 ] as $k) {
            if (!isset($saved['bulk'][$k]) || $saved['bulk'][$k] != $data[$k]) {
                $saved['bulk'][$k] = $data[$k];
                $dirty = true;
            }
        }

        //********************************************************************
        // form options and resources are per class
        $class = get_called_class();

        //********************************************************************
        // restore used resources
        foreach ($this->getUsedResources() as $k) {
            if (!isset($saved[$class][$k]) || $saved[$class][$k] != $data[$k]) {
                $saved[$class][$k] = $data[$k];
                $dirty = true;
            }
        }

        //********************************************************************
        // select boxes
        foreach (['grade', 'type', 'item'] as $k) {
            if (!isset($saved[$class][$k]) || $saved[$class][$k] != $data[$k]) {
                $saved[$class][$k] = $data[$k];
                $dirty = true;
            }
        }

        if ($dirty) {
            $this->user->set($this->tag, $saved);
        }
    }

    /**
     * Return array of used resource names, ie [seed, amber]
     *
     * @return array
     */
    abstract protected function getUsedResources();

    /**
     * Return craftable item types
     *
     * @return array
     */
    abstract protected function getCraftplanTypeArray();

    /**
     * Return craftable items
     *
     * @return array
     */
    abstract protected function getCraftplanItemArray();

    /**
     * Calculate maximum amount of items that
     * can be crafted using available bulk worth of mats.
     *
     * @param array $data
     *
     * @return array
     */
    protected function calculate(array $data)
    {
        $matBulk = 0.5;
        $totalBulk = $this->maxBagBulk - $data['bag'];
        if ($data['use_mount']) {
            $totalBulk += $this->maxMountBulk - $data['mount'];
        }
        if ($data['use_packer1']) {
            $totalBulk += $this->maxPackerBulk - $data['packer1'];
        }
        if ($data['use_packer2']) {
            $totalBulk += $this->maxPackerBulk - $data['packer2'];
        }
        if ($data['use_packer3']) {
            $totalBulk += $this->maxPackerBulk - $data['packer3'];
        }

        $craftplan = $this->getCraftplan($data['grade'], $data['type'], $data['item']);
        if ($craftplan === false) {
            return ['error' => _th('Unknown craftplan')];
        }

        $toDig = floor($totalBulk / $matBulk);
        $toCraft = $toDig;

        $result = [
            'bulk' => $totalBulk,
            'used_bulk' => 0,
            'per_craft' => array_sum($craftplan),
            'craftplan' => $craftplan,
            'nb_items' => 0,
            'to_craft' => [],
            'to_dig' => [],
            'in_bag' => [],
        ];

        // include available mats
        foreach ($craftplan as $k => $nb) {
            if (isset($data[$k])) {
                $toCraft += $data[$k];
            } else {
                $data[$k] = 0;
            }
        }
        $result['nb_items'] = floor($toCraft / $result['per_craft']);

        // calculate each resource proportional share in available bulk
        $bulkInBag = $this->maxBagBulk - $data['bag'];
        foreach ($craftplan as $k => $nb) {
            // new algo - calculate mats by ratio
            $result['to_craft'][$k] = floor(($nb / $result['per_craft']) * $toCraft);
            $result['to_dig'][$k] = $result['to_craft'][$k] - $data[$k];
            $result['in_bag'][$k] = floor(2 * $nb * $bulkInBag / $result['per_craft']);
        }
        // there is some bulk available from rounding
        $k = key($craftplan);
        $freeBulk = ($result['bulk'] - array_sum($result['to_dig']) * $matBulk);
        $extra = floor($freeBulk / $matBulk);
        $result['to_dig'][$k] += $extra;
        $result['to_craft'][$k] += $extra;
        $result['used_bulk'] = array_sum($result['to_dig']) * $matBulk;
        // same for for bag
        $freeBulk = ($bulkInBag - array_sum($result['in_bag']) * $matBulk);
        $extra = floor($freeBulk / $matBulk);
        $result['in_bag'][$k] += $extra;

        return $result;
    }

    /**
     * TODO: read from csv that is set from child class
     *
     * Return item craftplan
     *
     * @param string $grade
     * @param string $type
     * @param string $item
     *
     * @return array
     */
    abstract protected function getCraftplan($grade, $type, $item);

}
