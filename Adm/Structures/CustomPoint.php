<?php

namespace ManiaLivePlugins\eXpansion\Adm\Structures;

class CustomPoint extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

    public $name;
    public $points;

    function __construct($name, $points)
    {
        $this->name = $name;
        $this->points = $points;
    }

}

?>
