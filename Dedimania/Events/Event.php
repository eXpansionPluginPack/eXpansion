<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Events;

class Event extends \ManiaLive\Event\Event {

    const ON_OPEN_SESSION = 1;
    const ON_GET_RECORDS = 2;

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
            case self::ON_OPEN_SESSION: $listener->onDedimaniaOpenSession($p[0]);
                break;
            case self::ON_GET_RECORDS: $listener->onDedimaniaGetRecords($p[0]);
                break;
        }
    }

}

?>