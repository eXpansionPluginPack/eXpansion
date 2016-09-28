<?php

namespace ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks;

use ManiaLivePlugins\eXpansion\Core\Structures\RpcObject;

class EndSubMatch extends RpcObject
{

    /** var integer */
    public $timestamp = 0;

    /** var integer */
    public $submatchNumber = 0;
}
