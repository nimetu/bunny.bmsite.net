<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

require_once __DIR__ . '/BunnyToolPage.php';

/**
 * ShopReminder.php
 */
class ShopReminder extends BunnyToolPage
{
    protected $tag = 'shop';

    /** @var SimpleXMLElement */
    private $charXml;

    /** {@inheritdoc} */
    public function getTitle()
    {
        return _th('Vendor Inventory');
    }

    public function run(array $get = [], array $post = [])
    {
        $form = $this->readForm(isset($post['shop']) ? $post['shop'] : []);

        $xml = $this->user->getCharacterApi($form['apikey']);

        $html = '';
        if ($xml === false || !empty($xml->error) || !isset($xml->shop)) {
            $html .= $this->renderApiKeyForm($form['apikey'], $xml);
        }

        // even if apikey error, then show already cached version
        if (!empty($xml->error)) {
            $xml = $this->user->getCharacterApi($form['apikey'], true);
        }

        if ($xml !== false && empty($xml->error)) {
            $html .= $this->renderShop($xml);
        }

        return $html;
    }

    protected function renderApiKeyForm($apikey = '', $xml)
    {
        $_submit = _th('Save');
        $_text = _th('Character API key with C06 module');

        $tpl = '<br><span style="color: red;">%s %s</span>';
        $_error = '';
        if ($xml !== false) {
            if (!empty($xml->error)) {
                $_error = sprintf($tpl, _th('ERROR:'), sprintf('%d: %s', (int)$xml->error['code'], _h((string)$xml->error)));
            } else if (!isset($xml->shop)) {
                $_error = sprintf($tpl, _th('ERROR:'), _th('Missing C06 module'));
            }
        }

        $_apikey = _h($apikey);

        $url = _h($this->url);
        $html = <<<EOF
<form method="post" action="{$url}">
<table cellspacing="0" cellpadding="2">
<tr>
    <td height="20">{$_text}</td>
    <td>&nbsp;</td>
</tr>
<tr>
    <td height="20"><input type="text" value="{$_apikey}" name="shop[apikey]" placeholder="Character API Key with C06 module" style="width: 30em;">{$_error}</td>
    <td><input type="submit" name="submit" value="{$_submit}"></td>
</tr>
</table>
</form>
<br>
EOF;
        return $html;
    }


    protected function renderShop(\SimpleXMLElement $xml)
    {
        $html = '';

        $now = time();
        $diff = (int)$xml['cached_until'] - $now;
        $age = $now - (int)$xml['created'];
        $key = substr((string)$xml['apikey'], 0, 5).'*******';
        if ($diff >= 0) {
            $_msg = strtr(_th('API key {key} will be updated in {time}.'), [
                '{key}' => $key,
                '{time}' => $this->view->formatTimer($diff),
            ]);
        } else {
            $_msg = strtr(_th('API key {key} has expired.'), [
                '{key}' => $key,
            ]);
        }
        $_msg .= '<br>'.strtr(_th('Character XML age is {age}.'), [
            '{age}' => $this->view->formatTimer($age),
        ]);
        $html .= '<table width="100%"><tr>';
        $html .= '<td width="80%">'.$_msg.'</td>';
        $html .= '<td width="20%"><form method="POST" action="'._h($this->url).'"><input type="submit" name="shop[delete]" value="'._th('Clear API key').'"></td>';
        $html .= '</tr></table>';

        if (!isset($xml->shop))
        {
            $html .= _th('No shop info available');
            return $html;
        }

        $tplRow = '<tr bgcolor="{bgcolor}">
            <td>{#}</td>
            <td>{icon}</td>
            <td>{name}</td>
            <td>{continent}</td>
            <td>{age}</td>
            <td>{price}</td>
        </tr>';

        $html .= '<table width="100%" border>';
        $html .= strtr($tplRow, [
            '{bgcolor}' => '#808080',
            '{#}' => '#',
            '{icon}' => '',
            '{name}' => _th('Item'),
            '{continent}' => _th('Continent'),
            '{age}' => _th('Listing age'),
            '{price}' => _th('Price'),
        ]);

        $zebraColors = $this->view->getZebraColors();
        $count = 0;
        foreach($xml->shop->shopitem as $row) {
            $count++;
            $ts = $now - intval($row->timestamp);
            $name = ryzom_translate((string)$row->item->sheet, defined('LANG') ? LANG : 'en');
            $html .= strtr($tplRow, [
                '{bgcolor}' => _cycle($count, $zebraColors),
                '{#}' => $count,
                '{icon}' => '<img src="'.ryzom_item_icon_url((string)$row->item->sheet, -1, (int)$row->item->quality, (int)$row->stack, -1, false, true, false).'" alt="">',
                '{name}' => _h($name),
                '{continent}' => _th((string)$row->continent),
                '{age}' => $this->view->formatTimer($ts, true),
                '{price}' => (int)$row->price,
            ]);
        }
        $html .= '</table>';
        $html .= '<a href="'._h($this->url).'">'._th('refresh').'</a>';

        return $html;
    }

    /**
     * Read POST data and update user events
     *
     * @param array $post
     *
     * @return array
     */
    protected function readForm(array $post)
    {
        $defaults = [
            'apikey' => '',
        ];

        if (isset($post['delete'])) {
            $this->user->clearCharacterApi();
            return $defaults;
        }

        $data = $defaults;
        if (isset($post['apikey'])) {
            $data['apikey'] = $post['apikey'];
        }

        return $data;
    }
}
