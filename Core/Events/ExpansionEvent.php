<?php

namespace ManiaLivePlugins\eXpansion\Core\Events;

class ExpansionEvent extends \ManiaLive\Event\Event
{

    const ON_RESTART_START = 1;
    const ON_RESTART_END = 1;

    protected $params;

    public function __construct($onWhat)
    {
        parent::__construct($onWhat);
        $params = func_get_args();
        array_shift($params);
        $this->params = $params;
    }

    /**
     * @param ExpansionEventListener $listener
     */
    public function fireDo($listener)
    {
        $p = $this->params;
        switch ($this->onWhat) {
            case self::ON_RESTART_START:
                $listener->eXp_onRestartStart();
                break;
            case self::ON_RESTART_END:
                $listener->eXp_onRestartStop();
                break;
        }
    }
}
