<?php

namespace ManiaLivePlugins\eXpansion\ESLcup\Structures;

/**
 * Description of newPHPClass
 *
 * @author Reaby
 */
class CupScore extends \DedicatedApi\Structures\AbstractStructure {

    /** @var \DedicatedApi\Structures\Player */
    public $player;

    /** @var string */
    public $login, $nickname;

    /** @var integer */
    public $score, $playerId;
    public $finalist = false;
    public $hasWin = false;
    public $place = -1;
    public $isConnected = false;

    public function __construct($playerId, $login, $nickname, $score) {
        $this->playerId = $playerId;
        $this->login = $login;
        $this->nickname = $nickname;
        $this->score = $score;
    }

}
