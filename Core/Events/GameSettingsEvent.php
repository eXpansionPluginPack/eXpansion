<?php

namespace ManiaLivePlugins\eXpansion\Core\Events;

use ManiaLive\Event\Event;

class GameSettingsEvent extends Event
{

    const ON_GAME_MODE_CHANGE = 1;
    const ON_GAME_SETTINGS_CHANGE = 2;

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
            case self::ON_GAME_MODE_CHANGE:
                $listener->onGameModeChange($p[0], $p[1]);
                break;
            case self::ON_GAME_SETTINGS_CHANGE:
                $listener->onGameSettingsChange($p[0], $p[1], $p[2]);
                break;
        }
    }
}
