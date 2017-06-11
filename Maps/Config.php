<?php

namespace ManiaLivePlugins\eXpansion\Maps;

class Config extends \ManiaLib\Utils\Singleton
{
    public $bufferSize = 5;
    public $historySize = 7;
    public $showCurrentMapWidget = false;
    public $showNextMapWidget = false;
    public $showEndMatchNotices = false;
    public $publicQueueAmount = array(0);
}
