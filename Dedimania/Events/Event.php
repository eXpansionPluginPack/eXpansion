<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Events;

class Event extends \ManiaLive\Event\Event {

    const ON_OPEN_SESSION = 0x1;
    const ON_GET_RECORDS = 0x2;
    const ON_NEW_DEDI_RECORD = 0x4;
    const ON_DEDI_RECORD = 0x8;
    const ON_UPDATE_RECORDS = 0x16;
    const ON_PLAYER_CONNECT = 0x24;
    const ON_PLAYER_DISCONNECT = 0x32;

    protected $params;

    function __construct() {
        $params = func_get_args();
        $onWhat = array_shift($params);
        parent::__construct($onWhat); 
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
            case self::ON_DEDI_RECORD: $listener->onDedimaniaRecord($p[0], $p[1]);
                break;
            case self::ON_UPDATE_RECORDS: $listener->onDedimaniaUpdateRecords($p[0]);
                break;
            case self::ON_PLAYER_CONNECT: $listener->onDedimaniaPlayerConnect($p[0]);
                break;
            case self::ON_PLAYER_DISCONNECT: $listener->onDedimaniaPlayerDisconnect();
                break;
        }
    }

}

?>