<?php

namespace ManiaLivePlugins\eXpansion\Adm\Structures;

class CustomPoint extends \DedicatedApi\Structures\AbstractStructure {

    public $name;
    public $points;

    function __construct($name, $points) {
        $this->name = $name;
        $this->points = $points;
    }

}

?>
