<?php

namespace ManiaLivePlugins\eXpansion\PersonalBestTimes\Structures;

class BestTime extends \DedicatedApi\Structures\AbstractStructure {

    public $login;
    public $time = 0;    
    
    public function __construct($login, $time) {
        $this->login = $login;
        $this->time = $time;        
    }
    
}

?>
