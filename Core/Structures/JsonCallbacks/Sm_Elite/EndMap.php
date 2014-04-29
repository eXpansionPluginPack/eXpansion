<?php

namespace ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks;

use ManiaLivePlugins\eXpansion\Core\Structures\RpcObject;

class EndMap extends RpcObject {

    /** @var integer */
    public $timestamp = 0;

    /** @var integer */
    public $mapNumber = 0;

    /** @var integer */
    public $mapWinnerClan = 0;

    /** @var integer */
    public $clan1MapScore = 0;

    /** @var integer */
    public $clan2MapScore = 0;

    /** @var ScorePlayer[] */
    public $scoresTable = null;

}

