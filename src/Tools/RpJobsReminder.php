<?php
// (c) 2016 Meelis Mägi <nimetu@gmail.com>
// License: AGPL-3.0 or later (https://www.gnu.org/licenses/agpl)

require_once __DIR__ . '/BunnyToolPage.php';

class ReminderEvent
{
    private $key;
    private $name;
    private $timer;
    private $group;

    /**
     * Reminder event
     *
     * @param string $key
     * @param string $name
     * @param string $timer
     * @param string $group
     */
    public function __construct($key, $name, $timer, $group)
    {
        $this->key = $key;
        $this->name = $name;
        $this->timer = $timer;
        $this->group = $group;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTimer()
    {
        return $this->timer;
    }

    public function getGroup()
    {
        return $this->group;
    }
}

/**
 * RpJobsReminder.php
 */
class RpJobsReminder extends BunnyToolPage
{
    private $availableEvents = [];

    private $availableGroups = [];

    private $userEvents = [];

    private $dirty;

    /**
     * RpJobsReminder constructor.
     *
     * @param BunnyUser             $user
     * @param BunnyView             $view
     * @param BunnyStorageInterface $storage
     * @param string                $url
     */
    public function __construct(BunnyUser $user, BunnyView $view, BunnyStorageInterface $storage, $url)
    {
        parent::__construct($user, $view, $storage, $url);

        // rpjobs - 21h
        $timer = 21 * 60 * 60;
        $group = 'Occupations';
        $this->registerEvent(new ReminderEvent('rpjob_200', _th('Butcher'), $timer, $group));
        $this->registerEvent(new ReminderEvent('rpjob_201', _th('Florist'), $timer, $group));
        $this->registerEvent(new ReminderEvent('rpjob_202', _th('Water-Carrier'), $timer, $group));
        $this->registerEvent(new ReminderEvent('rpjob_203', _th('Magnetic Cartographer'), $timer, $group));
        $this->registerEvent(new ReminderEvent('rpjob_204', _th('Toolmaker'), $timer, $group));
        $this->registerEvent(new ReminderEvent('rpjob_205', _th('Medic'), $timer, $group));
        $this->registerEvent(new ReminderEvent('rpjob_206', _th('Larvester'), $timer, $group));
        $this->registerEvent(new ReminderEvent('rpjob_207', _th('Scrollmaker'), $timer, $group));
        // special items - 47h15m
        $timer = 47 * 60 * 60 + 15 * 60;
        $group = 'Special items';
        $this->registerEvent(new ReminderEvent('rpjobitem_201', _th('Lucky Flower'), $timer, $group));
        $this->registerEvent(new ReminderEvent('rpjobitem_202', _th('Stimulating Water'), $timer, $group));
        $this->registerEvent(new ReminderEvent('rpjobitem_203', _th('Ambers of Protection'), $timer, $group));
        $this->registerEvent(new ReminderEvent('rpjobitem_204', _th('Improved Tools'), $timer, $group));
        $group = 'Occupation TPs';
        $this->registerEvent(new ReminderEvent('rpjobitem_200', _th('Almati Wood TP (butcher)'), $timer, $group));
        $this->registerEvent(new ReminderEvent('rpjobitem_205', _th('Almati Wood TP (medic)'), $timer, $group));
        $this->registerEvent(new ReminderEvent('rpjobitem_206', _th('Almati Wood TP (larvester)'), $timer, $group));
        $this->registerEvent(new ReminderEvent('rpjobitem_207', _th('Almati Wood TP (scrollmaker)'), $timer, $group));
        // new horizon - 23h
        $timer = 23 * 60 * 60;
        $group = 'New Horizons';
        $this->registerEvent(new ReminderEvent('new-horizon', _th('New Horizon turn-in'), $timer, $group));

        $this->userEvents = [];
        $this->dirty = false;
    }

    /**
     * @param ReminderEvent $event
     */
    private function registerEvent(ReminderEvent $event)
    {
        $this->availableEvents[$event->getKey()] = $event;
        $this->availableGroups[$event->getGroup()] = $event->getGroup();
    }

    /** {@inheritdoc} */
    public function getTitle()
    {
        return _th('Occupations');
    }

    /**
     * Restore saved user events from array
     *
     * @param array $events
     */
    public function loadUserEvents(array $events)
    {
        $this->userEvents = $events;
    }

    /**
     * Return registered user events as array
     *
     * @return array
     */
    public function saveUserEvents()
    {
        return $this->userEvents;
    }

    /**
     * Return true if user events have changed
     *
     * @return bool
     */
    public function isDirty()
    {
        return $this->dirty;
    }

    public function run(array $get = [], array $post = [])
    {
        $this->userEvents = $this->user->get('events', []);
        $form = $this->readForm(isset($post['reminder']) ? $post['reminder'] : []);

        $url = _h($this->url);

        $array = [];
        foreach ($this->availableEvents as $event) {
            $key = $event->getKey();
            $name = $event->getName() . ' (' . $this->formatTimer($event->getTimer(), true) . ')';
            $group = _th($event->getGroup());
            $array[$group][$key] = $name;
        }

        $sbType = $this->view->renderHtmlSelect(
            [
                'name' => 'reminder[remind_me]',
                'options' => $array,
                'selected' => key($array),
            ]
        );
        $_submit = _th('Remind me');

        $html = <<<EOF
<form method="post" action="{$url}">
<table cellspacing="0" cellpadding="2">
<tr>
    <td height="20">{$sbType}</td>
    <td><input type="submit" name="submit" value="{$_submit}"></td>
</tr>
</table>
</form>
<br>
EOF;

        if (!empty($this->userEvents)) {
            $now = time();
            $tplGroup = '
            <tr>
                <td height="20" nowrap><span style="font-weight: bold;color:yellow;">{text}</span></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            ';
            $tpl = '
            <tr>
                <td height="20" nowrap>{text}</td>
                <td nowrap>{timer}</td>
                <td><input type="submit" name="reminder[reset][{key}]" value="{_reset}"></td>
                <td><input type="submit" name="reminder[delete][{key}]" value="{_delete}"></td>
            </tr>
            ';
            $tpl2 = '
            <tr>
                <td height="20"></td>
                <td></td>
                <td></td>
                <td><a href="{url}">{_refresh}</td>
            </tr>
            ';
            $_reset = _th('Reset');
            $_delete = _th('Delete');

            $rows = [];
            foreach ($this->userEvents as $key => $ev) {
                $evKey = $ev['event'];
                if (!isset($this->availableEvents[$evKey])) {
                    // unknown event
                    $this->deleteUserEvent($key);
                    continue;
                }
                $event = $this->availableEvents[$evKey];
                $group = $event->getGroup();

                $expires = $ev['timer_start'] + $event->getTimer();
                if ($now > $expires) {
                    $timer = '-';
                } else {
                    $timer = $this->formatTimer($expires - $now);
                }

                $rows[$group][] = strtr(
                    $tpl,
                    [
                        '{text}' => _h($event->getName()),
                        '{timer}' => _h($timer),
                        '{key}' => $key,
                        '{_reset}' => _h($_reset),
                        '{_delete}' => _h($_delete),
                    ]
                );
            }

            $html .= '<br>';
            $html .= '<form method="post" action="' . $url . '">';
            $html .= '<table cellspacing="0" cellpadding="2">';

            foreach ($this->availableGroups as $group) {
                if (empty($rows[$group])) {
                    continue;
                }
                $html .= strtr(
                    $tplGroup,
                    [
                        '{text}' => _h($group),
                    ]
                );
                $html .= join("\n", $rows[$group]);
            }

            $html .= strtr(
                $tpl2,
                [
                    '{url}' => $url,
                    '{_refresh}' => _th('Refresh'),
                ]
            );
            $html .= '</table>';
            $html .= '</form>';
        }

        if ($this->dirty) {
            $this->user->set('events', $this->userEvents);
        }

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
        $result = [
            'remind_me' => '',
        ];

        if (isset($post['remind_me']) && isset($this->availableEvents[$post['remind_me']])) {
            $result['remind_me'] = $post['remind_me'];
            $event = $this->availableEvents[$post['remind_me']];
            $this->addUserEvent($event);
        }

        if (isset($post['reset'])) {
            $key = key($post['reset']);
            $this->resetUserEvent($key);
        }

        if (isset($post['delete'])) {
            $key = key($post['delete']);
            $this->deleteUserEvent($key);
        }

        return $result;
    }

    private function addUserEvent(ReminderEvent $event)
    {
        $key = md5(time() . $event->getKey());
        $this->userEvents[$key] = [
            'event' => $event->getKey(),
            'timer_start' => time(),
        ];
        $this->dirty = true;
    }

    private function resetUserEvent($key)
    {
        if (isset($this->userEvents[$key])) {
            $this->userEvents[$key]['timer_start'] = time();
            $this->dirty = true;
        };
    }

    private function deleteUserEvent($key)
    {
        if (isset($this->userEvents[$key])) {
            unset($this->userEvents[$key]);
            $this->dirty = true;
        }
    }

    private function formatTimer($sec, $short = false)
    {
        $h = floor($sec / 3600);
        $m = floor($sec / 60) % 60;
        $s = $sec % 60;
        $ret = [];
        $ret[$h . 'h'] = $h;
        $ret[$m . 'm'] = $m;
        $ret[$s . 's'] = $s;

        if ($short) {
            foreach ($ret as $k => $v) {
                if ($v == 0) {
                    unset($ret[$k]);
                }
            }
        } else {
            foreach ($ret as $k => $v) {
                if ($v != 0) {
                    break;
                }
                unset($ret[$k]);
            }
        }

        return join(' ', array_keys($ret));
    }

    public function hasUserEvent($key)
    {
        foreach ($this->userEvents as $event) {
            if ($event['event'] == $key) {
                return true;
            }
        }
        return false;
    }
}
