<?php

namespace ManiaLivePlugins\eXpansion\Maps;

class Config extends \ManiaLib\Utils\Singleton {

    public $bufferSize = 5;
    public $historySize = 7;
    
    public $showNextMapWidget = true;
    public $showEndMatchNotices = true;
	
	public $publicQueuAmount = array(1,100,300);
    
}
?>
