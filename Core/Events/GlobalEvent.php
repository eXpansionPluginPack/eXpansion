<?php

namespace ManiaLivePlugins\eXpansion\Core\Events;

class GlobalEvent extends \ManiaLive\Event\Event
{
    const ON_ADMIN_RESTART = 1;
    const ON_ADMIN_SKIP = 2;
    const ON_VOTE_RESTART = 4;
    const ON_VOTE_SKIP = 8;
    const ON_AUTOLOAD_COMPLETE = 16;

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
            case self::ON_ADMIN_RESTART:
            case self::ON_VOTE_RESTART:
                $listener->onMapRestart();
                break;
            case self::ON_ADMIN_SKIP:
            case self::ON_VOTE_SKIP:
                $listener->onMapSkip();
                break;
            case self::ON_AUTOLOAD_COMPLETE:
                $listener->eXpAutoloadComplete();
                break;
        }
    }
}
