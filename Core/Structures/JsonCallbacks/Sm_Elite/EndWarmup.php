<?php

namespace ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks;

use ManiaLivePlugins\eXpansion\Core\Structures\RpcObject;

class EndWarmup extends RpcObject {

    /** var integer */
    public $timestamp = 0;

    /** var boolean */
    public $allReady = true;

}

