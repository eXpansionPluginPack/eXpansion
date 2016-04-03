<?php

namespace ManiaLivePlugins\eXpansion\Core\Events;

class ConfigLoadEvent extends \ManiaLive\Event\Event
{

    const ON_CONFIG_FILE_LOADED = 1;

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
            case self::ON_CONFIG_FILE_LOADED:
                $listener->onConfigFileLoaded();
                break;
        }
    }

}

