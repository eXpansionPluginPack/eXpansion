<?php

namespace ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks;

use ManiaLivePlugins\eXpansion\Core\Structures\RpcObject;

class EndMatch extends RpcObject
{

    /** var integer */
    public $timestamp = 0;

    /** var integer */
    public $matchNumber = 0;

    /** var integer */
    public $matchWinnerClan = 0;

    /** var integer */
    public $clan1MapScore = 0;

    /** var integer */
    public $clan2MapScore = 0;
}
