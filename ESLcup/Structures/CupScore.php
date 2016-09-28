<?php

namespace ManiaLivePlugins\eXpansion\ESLcup\Structures;

/**
 * Description of newPHPClass
 *
 * @author Reaby
 */
class CupScore extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

    /** @var \Maniaplanet\DedicatedServer\Structures\Player */
    public $player;

    /** @var string */
    public $login, $nickName;

    /** @var integer */
    public $score, $playerId;
    public $finalist = false;
    public $hasWin = false;
    public $place = -1;
    public $isConnected = false;

    public function __construct($playerId, $login, $nickname, $score)
    {
        $this->playerId = $playerId;
        $this->login = $login;
        $this->nickName = $nickname;
        $this->score = $score;
    }
}
