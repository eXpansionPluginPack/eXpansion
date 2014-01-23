<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\ESportsManager\Structures;

/**
 * Description of MatchStatus
 *
 * @author Reaby
 */
class MatchStatus extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure {

    const VOTE_NONE = 0;
    const VOTE_READY = 2;
    const VOTE_SELECTMATCH = 4;

    public $isMatchActive = false;
    public $isMatchRunning = false;
    public $isAllPlayersReady = false;
    public $warmUp = false;
    public $voteRunning = self::VOTE_NONE;

}
