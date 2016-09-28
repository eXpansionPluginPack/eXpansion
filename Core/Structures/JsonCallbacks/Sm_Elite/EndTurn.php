<?php

namespace ManiaLivePlugins\eXpansion\Core\Structures\JsonCallbacks;

use ManiaLivePlugins\eXpansion\Core\Structures\RpcObject;

class EndTurn extends RpcObject
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

    /** @var integer */
    public $attackingClan = 0;

    /** @var integer */
    public $defendingClan = 0;

    /** @var Player */
    public $attackingPlayer;

    /** @var integer */
    public $turnWinnerClan;

    /** @var string */
    public $winType = "";

    /** @var integer */
    public $clan1RoundScore;

    /** @var integer */
    public $clan2RoundScore;

    /** @var integer */
    public $clan1MapScore;

    /** @var integer */
    public $clan2MapScore;

    /** @var ScorePlayer[] */
    public $scoresTable = null;
}
