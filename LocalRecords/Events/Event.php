<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Events;

class Event extends \ManiaLive\Event\Event {

    const ON_NEW_RECORD = 1;
    const ON_UPDATE_RECORDS = 2;
    const ON_PERSONAl_BEST = 4;

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
            case self::ON_NEW_RECORD: $listener->onNewRecord($p[0]);
                break;
            case self::ON_UPDATE_RECORDS: $listener->onUpdateRecords($p[0]);
                break;
            case self::ON_PERSONAl_BEST: $listener->onPersonalBestRecord($p[0]);
                break;
        }
    }

}

?>