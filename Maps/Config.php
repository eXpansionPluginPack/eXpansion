<?php

namespace ManiaLivePlugins\eXpansion\Maps;

class Config extends \ManiaLib\Utils\Singleton {

    public $bufferSize = 5;
    public $historySize = 7;
    public $showNextMapWidget = true;
    public $showEndMatchNotices = true;
    public $publicQueueAmount = array(20, 25, 30, 50, 75, 100, 150, 200);

}

?>
