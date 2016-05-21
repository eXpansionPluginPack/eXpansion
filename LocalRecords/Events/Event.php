<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Events;

class Event extends \ManiaLive\Event\Event
{

    const ON_NEW_RECORD = 1;

    const ON_UPDATE_RECORDS = 2;

    const ON_PERSONAL_BEST = 4;

    const ON_RECORDS_LOADED = 8;

    const ON_NEW_FINISH = 16;

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
            case self::ON_NEW_RECORD:
                $listener->onNewRecord($p[0], $p[1]);
                break;
            case self::ON_UPDATE_RECORDS:
                $listener->onUpdateRecords($p[0]);
                break;
            case self::ON_PERSONAL_BEST:
                $listener->onPersonalBestRecord($p[0]);
                break;
            case self::ON_RECORDS_LOADED:
                $listener->onRecordsLoaded($p[0]);
                break;
            case self::ON_NEW_FINISH:
                $listener->onRecordPlayerFinished($p[0]);
                break;
        }
    }
}
