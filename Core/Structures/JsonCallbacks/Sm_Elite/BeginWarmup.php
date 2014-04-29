<?php

namespace ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks;

use ManiaLivePlugins\eXpansion\Core\Structures\RpcObject;

class BeginWarmup extends RpcObject {

    /** var integer */
    public $timestamp = 0;

    /** var boolean */
    public $allReady = false;

}

