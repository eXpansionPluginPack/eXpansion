<?php
namespace ManiaLivePlugins\eXpansion\Core\Events;

use ManiaLive\Event\Event;

class ConfigLoadEvent extends Event
{

    const ON_CONFIG_FILE_LOADED = 1;

    protected $params;

    public function __construct($onWhat)
    {
        parent::__construct($onWhat);
        $params = func_get_args();
        array_shift($params);
        $this->params = $params;
    }

    /**
     * @param $listener
     */
    public function fireDo($listener)
    {
        switch ($this->onWhat) {
            case self::ON_CONFIG_FILE_LOADED:
                $listener->onConfigFileLoaded();
                break;
        }
    }
}
