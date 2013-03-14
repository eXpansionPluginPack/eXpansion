<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Events;

class Event extends \ManiaLive\Event\Event {

    const ON_OPEN_SESSION = 1;
    const ON_GET_RECORDS = 2;
    const ON_NEW_DEDI_RECORD = 4;
    const ON_UPDATE_DEDI_RECORDS = 8;
    const ON_PLAYER_CONNECT = 10;
    const ON_PLAYER_DISCONNECT = 12;
    
    protected $params;

    function __construct($onWhat) {
        parent::__construct($onWhat);
        $params = func_get_args();
        array_shift($params);
        $this->params = $params;
    }

    function fireDo($listener) {
        $p = $this->params;
        switch ($this->onWhat) {
            case self::ON_OPEN_SESSION: $listener->onDedimaniaOpenSession();
                break;
            case self::ON_GET_RECORDS: $listener->onDedimaniaGetRecords($p[0]);
                break;
            case self::ON_NEW_DEDI_RECORD: $listener->onDedimaniaNewRecord($p[0]);
                break;
            case self::ON_UPDATE_DEDI_RECORDS: $listener->onDedimaniaUpdateRecords($p[0]);
                break;
             case self::ON_PLAYER_CONNECT: $listener->onDedimaniaPlayerConnect($p[0]);
                break;
             case self::ON_PLAYER_DISCONNECT: $listener->onDedimaniaPlayerDisconnect();
                break;
        }
    }

}

?>