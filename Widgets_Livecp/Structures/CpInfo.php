<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Livecp\Structures;

use Maniaplanet\DedicatedServer\Structures\AbstractStructure;

class CpInfo extends AbstractStructure
{
    /** @var int */
    public $cpIndex = -1;
    /** @var int */
    public $time = 0;

    /**
     * CpInfo constructor.
     * @param int $cp
     * @param int $time
     */
    public function __construct($cp = -1, $time = 0)
    {
        $this->cpIndex = $cp;
        $this->time = $time;
    }

}