<?php

namespace ManiaLivePlugins\eXpansion\Core\Events;

class ServerSettingsEvent extends \ManiaLive\Event\Event
{

    const ON_SERVER_SETTINGS_CHANGE = 1;

    protected $params;

    public function __construct($onWhat)
    {
        parent::__construct($onWhat);
        $params = func_get_args();
        array_shift($params);
        $this->params = $params;
    }

    public function fireDo($listener)
    {
        $p = $this->params;
        switch ($this->onWhat) {
            case self::ON_SERVER_SETTINGS_CHANGE:
                $listener->onServerSettingsChange($p[0], $p[1], $p[2]);
                break;

        }
    }

}
