<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\Widgets_TeamPlayerScores\Structures;

/**
 * Description of RoundScore
 *
 * @author Petri
 */
class PlayerScore extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

    /**
     * Total Score of a player
     *
     * @var int
     */
    public $score = 0;

    /**
     * Counter for top3 places
     *
     * @var array
     */
    public $winScore = array(0 => 0, 1 => 0, 2 => 0);

    /**
     * login
     *
     * @var string
     */
    public $login;

    /**
     * nickname
     *
     * @var string
     */
    public $nickName;

    /**
     * best time so far...
     *
     * @var int
     */
    public $bestTime;

}
