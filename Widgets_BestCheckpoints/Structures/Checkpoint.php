<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Structures;

class Checkpoint extends \DedicatedApi\Structures\AbstractStructure {

    public $time = 0;
    public $nickname = "";
    public $index;

    function __construct($index, $nickname, $time) {
        $this->time = $time;
        $this->nickname = $nickname;
        $this->index = $index;
    }

}

?>
