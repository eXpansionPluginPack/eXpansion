<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Events;

class Event extends \ManiaLive\Event\Event
{

    const ON_ADMIN_NEW = 1;
    const ON_ADMIN_REMOVED = 2;

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
            case self::ON_ADMIN_NEW:
                $listener->exp_admin_added($p[0]);
                break;
            case self::ON_ADMIN_REMOVED:
                $listener->exp_admin_removed($p[0]);
                break;
        }
    }

}

?>