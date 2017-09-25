<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

require_once __DIR__ . '/BunnyToolPage.php';

/**
 * XpCalculator.php
 */
class XpCalculator extends BunnyToolPage
{
    // melee/magic/forage xp
    private $fightXp = [
        0,
        933,
        1800,
        1932,
        2399,
        2189,
        6063,
        5144,
        6332,
        6163,
        6899,
        9594,
        5641,
        9644,
        10076,
        11200,
        11700,
        12520,
        13210,
        13797,
        14006, // 0 - 20
        14640,
        14709,
        15422,
        15840,
        16460,
        16474,
        17996,
        18394,
        18003,
        19511,
        18991,
        19331,
        19966,
        20162,
        20700,
        22000,
        23100,
        22000,
        22800,
        23400, // 21 -40
        23800,
        24800,
        25200,
        25600,
        26000,
        24000,
        26000,
        27000,
        28156,
        28566,
        29099,
        29282,
        29050,
        32117,
        30259,
        31314,
        31348,
        31522,
        32281,
        31567, // 41 - 60
        33000,
        36008,
        36003,
        36000,
        37575,
        35658,
        36000,
        38066,
        39300,
        37900,
        39900,
        39900,
        40900,
        41500,
        41900,
        40728,
        41148,
        41360,
        41659,
        43390, // 61 - 80
        42845,
        43761,
        44451,
        43966,
        44055,
        44863,
        45499,
        45631,
        46457,
        48289,
        47270,
        47546,
        47808,
        48704,
        49019,
        50086,
        50510,
        51251,
        51000,
        51867, // 81 - 100
        52165,
        51768,
        52091,
        51872,
        53137,
        53847,
        53552,
        51152,
        56656,
        53989,
        55818,
        55952,
        55987,
        55952,
        57491,
        57153,
        57122,
        57353,
        58035,
        59178, // 101 - 120
        60672,
        60473,
        60617,
        60231,
        61060,
        62410,
        62426,
        63419,
        64315,
        65574,
        67108,
        68627,
        68628,
        70312,
        71642,
        72136,
        73780,
        74790,
        74846,
        76843, // 121 - 140
        76597,
        78210,
        78209,
        81048,
        81884,
        82470,
        83049,
        83139,
        85123,
        85322,
        87468,
        88384,
        88189,
        90314,
        90716,
        92848,
        92670,
        94366,
        95123,
        96719, // 141 - 160
        96095,
        99237,
        99858,
        99808,
        101813,
        101989,
        103813,
        104234,
        105535,
        105215,
        107918,
        108728,
        109434,
        110330,
        110392,
        112578,
        113554,
        114870,
        115241,
        116789, // 161 - 180
        117893,
        119220,
        119485,
        120239,
        120247,
        122727,
        122362,
        123931,
        125130,
        125187,
        128271,
        128157,
        128074,
        129905,
        130126,
        132682,
        133556,
        134170,
        134996,
        135961, // 181 - 200
        137615,
        139153,
        140130,
        139072,
        141586,
        143068,
        143774,
        143111,
        144325,
        146485,
        146665,
        148266,
        148880,
        150929,
        150611,
        151291,
        152578,
        154605,
        154976,
        155204, // 201 - 220
        158340,
        157640,
        159286,
        159194,
        160864,
        162050,
        162458,
        163598,
        165519,
        165221,
        167805,
        168675,
        169978,
        169361,
        170891,
        171771,
        172132,
        174481,
        175511,
        176974, // 221 - 240
        178143,
        177502,
        179336,
        179309,
        181306,
        181767,
        182164,
        184174,
        184573,
        185129
    ]; // 241 - 250];

// craft xp
    private $craftXp = [
        0,
        695,
        2000,
        2228,
        2206,
        2016,
        2100,
        2620,
        2952,
        3084,
        3311,
        3352,
        3225,
        3752,
        3708,
        4156,
        4580,
        4635,
        5018,
        5267,
        5270,  // 0 - 20
        5202,
        5985,
        5908,
        6338,
        6234,
        6689,
        6960,
        7021,
        7084,
        7040,
        7601,
        7675,
        7800,
        7900,
        8018,
        8233,
        8504,
        8723,
        8900,
        9484,  // 21 - 40
        9640,
        9965,
        9904,
        10162,
        10285,
        10300,
        10500,
        10700,
        11000,
        11118,
        11263,
        11386,
        12926,
        12321,
        11823,
        13522,
        12357,
        13620,
        12801,
        13101, // 41 - 60
        14221,
        14731,
        14561,
        13807,
        14959,
        15027,
        14418,
        14690,
        15053,
        16143,
        15011,
        15389,
        15730,
        16014,
        17046,
        16999,
        17381,
        16944,
        17125,
        17680, // 61 - 80
        17740,
        18458,
        18915,
        19074,
        19706,
        19535,
        20639,
        20288,
        20524,
        20932,
        21883,
        21283,
        22411,
        21813,
        22499,
        22917,
        23719,
        23384,
        24140,
        23808, // 81 - 100
        24735,
        24977,
        25004,
        25640,
        25543,
        26832,
        26228,
        26414,
        26801,
        26975,
        27198,
        28329,
        29082,
        28560,
        28580,
        29231,
        29157,
        29910,
        30348,
        30794, // 101 - 120
        31121,
        31291,
        31985,
        32081,
        31974,
        32446,
        33628,
        33459,
        33912,
        35365,
        36473,
        36602,
        37222,
        37558,
        37928,
        39310,
        39572,
        39750,
        41040,
        41636, // 121 - 140
        41998,
        43156,
        43340,
        44556,
        45103,
        45452,
        46445,
        46504,
        47088,
        47418,
        48511,
        48719,
        49711,
        49864,
        50624,
        51420,
        52297,
        52795,
        53561,
        54237, // 141 - 160
        55136,
        56234,
        57396,
        57130,
        57786,
        57968,
        59441,
        60185,
        60300,
        60379,
        62295,
        61953,
        62975,
        63078,
        63492,
        65141,
        66158,
        67377,
        67809,
        69935, // 161 - 180
        70392,
        73210,
        73955,
        74159,
        75845,
        77032,
        78998,
        79925,
        81324,
        82764,
        83538,
        85788,
        86061,
        88132,
        89385,
        90718,
        91925,
        92353,
        94014,
        94928, // 181 - 200
        96641,
        98023,
        99044,
        100204,
        101860,
        102715,
        104759,
        105345,
        106380,
        107831,
        109390,
        110995,
        112463,
        112660,
        115008,
        116059,
        117360,
        118299,
        119880,
        121094, // 201 - 220
        121989,
        123567,
        124804,
        125573,
        127481,
        128494,
        129698,
        130826,
        132516,
        133503,
        135052,
        136944,
        137232,
        138352,
        139975,
        140830,
        142526,
        143472,
        145260,
        146785, // 221 - 240
        148377,
        149845,
        150345,
        151048,
        152701,
        154362,
        155394,
        156291,
        157470,
        159576 // 241 - 250
    ];

    /** {@inheritdoc} */
    public function getTitle()
    {
        return _th('XP Calculator');
    }

    public function run(array $get = [], array $post = [])
    {
        $form = $this->readForm(isset($post['xp_calc']) ? $post['xp_calc'] : []);

        $url = _h($this->url);
        $levelRange = range(0, 250);
        unset($levelRange[0]);

        $_type = _th('Type');
        $sbType = $this->view->renderHtmlSelect(
            [
                'name' => 'xp_calc[type]',
                'options' => [
                    'fight' => _th('Fight'),
                    'magic' => _th('Magic'),
                    'craft' => _th('Craft'),
                    'forage' => _th('Forage'),
                ],
                'selected' => _h($form['type']),
            ]
        );

        $_cLevel = _th('Current level');
        $sbStartLevel = $this->view->renderHtmlSelect(
            [
                'name' => 'xp_calc[start_level]',
                'options' => $levelRange,
                'selected' => (int)$form['start_level'],
            ]
        );

        $_eLevel = _th('Level to reach');
        $sbEndLevel = $this->view->renderHtmlSelect(
            [
                'name' => 'xp_calc[end_level]',
                'options' => $levelRange,
                'selected' => (int)$form['end_level'],
            ]
        );

        $_aXp = _th('Average XP');
        $ibAverageXp = $this->view->renderHtmlInput(
            [
                'name' => 'xp_calc[average_xp]',
                'value' => (int)$form['average_xp'],
                'size' => 100,
                'style' => 'width: 100px;',
            ]
        );

        $_submit = _th('Calculate');

        $html = <<<EOF
<form method="post" action="{$url}">
<table cellspacing="0" cellpadding="2">
<tr>
    <td height="20">{$_type}</td>
    <td>{$sbType}</td>
</tr>
<tr>
    <td height="20">{$_cLevel}</td>
    <td>{$sbStartLevel}</td>
</tr>
<tr>
    <td height="20">{$_eLevel}</td>
    <td>{$sbEndLevel}</td>
</tr>
<tr>
    <td height="20">{$_aXp}</td>
    <td>{$ibAverageXp}</td>
</tr>
</table>
<br>
<input type="submit" name="submit" value="{$_submit}">
</form>
EOF;

        if (!empty($post)) {
            $result = $this->calculate($form);

            if ($form['type'] == 'craft') {
                $txt = 'You need about {xp} xp or {nb} crafts to reach that level';
            } elseif ($form['type'] == 'fight' || $form['type'] == 'magic') {
                $txt = 'You need about {xp} xp or {nb} kills to reach that level';
            } else {
                $txt = 'You need about {xp} xp or {nb} foraging to reach that level';
            }
            $txt = strtr(
                _th($txt),
                [
                    '{xp}' => (int)$result['total_xp'],
                    '{nb}' => (int)$result['total'],
                ]
            );

            $html .= '<br>';
            $html .= '<p>' . $txt . '</p>';
        }

        return $html;
    }

    protected function readForm(array $post)
    {
        $result = [
            'type' => 'fight',
            'start_level' => 1,
            'end_level' => 21,
            'average_xp' => 1000,
        ];

        if (isset($post['type']) && in_array($post['type'], ['fight', 'magic', 'craft', 'forage'])) {
            $result['type'] = $post['type'];
        }
        foreach (['start_level', 'end_level', 'average_xp'] as $k) {
            if (isset($post[$k])) {
                $result[$k] = (int) $post[$k];
            }
        }

        $result['start_level'] = _clamp($result['start_level'], 1, 250);
        $result['end_level'] = _clamp($result['end_level'], 1, 250);
        if ($result['end_level'] < $result['start_level']) {
            $a = $result['end_level'];
            $result['end_level'] = $result['start_level'];
            $result['start_level'] = $a;
        }

        if ($result['average_xp'] <= 0) {
            $result['average_xp'] = 1;
        }

        return $result;
    }

    protected function calculate(array $form)
    {
        if ($form['type'] == 'craft') {
            $xpTable = $this->craftXp;
        } else {
            $xpTable = $this->fightXp;
        }

        $total = 0;
        for ($i = $form['start_level'] + 1; $i <= $form['end_level']; $i++) {
            if (isset($xpTable[$i])) {
                $total += $xpTable[$i];
            }
        }

        return [
            'total_xp' => $total,
            'total' => ceil($total / $form['average_xp']),
        ];
    }
}
