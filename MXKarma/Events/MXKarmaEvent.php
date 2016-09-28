<?php

namespace ManiaLivePlugins\eXpansion\MXKarma\Events;

class MXKarmaEvent extends \ManiaLive\Event\Event
{

    const ON_CONNECTED = 0x1;

    const ON_VOTES_RECIEVED = 0x2;

    const ON_VOTE_SAVE = 0x3;

    const ON_DISCONNETED = 0x4;

    const ON_ERROR = 0x5;

    protected $params;

    function __construct($onWhat)
    {
        parent::__construct($onWhat);
        $params = func_get_args();
        array_shift($params);
        $this->params = $params;
    }

    function fireDo($listener)
    {
        $p = $this->params;
        switch ($this->onWhat) {
            case self::ON_CONNECTED:
                $listener->MXKarma_onConnected();
                break;
            case self::ON_DISCONNETED:
                $listener->MXKarma_onDisconnected();
                break;
            case self::ON_VOTES_RECIEVED:
                $listener->MXKarma_onVotesRecieved($p[0]);
                break;
            case self::ON_VOTE_SAVE:
                $listener->MXKarma_onVotesSave($p[0]);
                break;
            case self::ON_ERROR:
                $listener->MXKarma_onError($p[0], $p[1], $p[2]);
                break;
        }
    }
}
