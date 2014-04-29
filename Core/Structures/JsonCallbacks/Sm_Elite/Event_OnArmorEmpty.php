<?php

namespace ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks;

class Event_OnArmorEmpty extends \ManiaLiveManiaLiveDedicatedApiStructuresAbstractStructure {

    /** @var string */
    public $type = "";

    /** @var integer */
    public $damage = 0;

    /** @var integer */
    public $weaponNumber = 0;

    /** @var integer */
    public $missDist = 0;

    /** @var Player */
    public $shooter;

    /** @var Player */
    public $victim;

}