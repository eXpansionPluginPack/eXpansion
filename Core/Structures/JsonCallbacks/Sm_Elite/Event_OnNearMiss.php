<?php

namespace ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks;

class Event_OnNearMiss extends ManiaLiveDedicatedApiStructuresAbstractStructure {

    /** @var string */
    public $type = "";

    /** @var integer */
    public $damage = 0;

    /** @var integer */
    public $weaponNumber = 0;

    /** @var float */
    public $missDist = 0;

    /** @var Player */
    public $shooter;
    
    /** @var Player */
    public $victim;
    
    
}