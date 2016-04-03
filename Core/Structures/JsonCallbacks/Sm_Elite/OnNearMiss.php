<?php

namespace ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks;

use ManiaLivePlugins\eXpansion\Core\Structures\RpcObject;

class OnNearMiss extends RpcObject
{

    /** @var integer */
    public $timestamp = 0;

    /** @var integer */
    public $turnNumber = 0;

    /** @var integer */
    public $startTime = 0;

    /** @var integer */
    public $endTime = 0;

    /** @var integer */
    public $poleTime = 0;

    /** @var Event_OnNearMiss */
    public $event;

}

