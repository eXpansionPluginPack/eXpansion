<?php

namespace ManiaLivePlugins\eXpansion\Core\Events;

class PlayerEvent extends \ManiaLive\Event\Event
{

    const ON_PLAYER_POSITION_CHANGE = 1;
    const ON_PLAYER_GIVEUP = 2;
    const ON_PLAYER_POSITIONS_CALCULATED = 4;
    const ON_PLAYER_NETLOST = 8;

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
            case self::ON_PLAYER_POSITION_CHANGE:
                $listener->onPlayerPositionChange($p[0], $p[1], $p[2]);
                break;
            case self::ON_PLAYER_GIVEUP:
                $listener->onPlayerGiveup($p[0]);
                break;
            case self::ON_PLAYER_POSITIONS_CALCULATED:
                $listener->onPlayerNewPositions($p[0]);
                break;
            case self::ON_PLAYER_NETLOST:
                $listener->onPlayerNetLost($p[0]);
                break;
        }
    }

}
