<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\Widgets_TeamRoundScores\Structures;

/**
 * Description of RoundScore
 *
 * @author Petri
 */
class RoundScore extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

    /**
     * Summed score of the round for teams
     * $score[teamId] = value
     *
     * @var int[]
     */
    public $score = array(0 => 0, 1 => 0);

    /**
     * Winner team id, zero based
     *
     * @var int 0 = blue, 1 = red, -1 = no winning team.
     */
    public $winningTeamId = -1;

    /**
     * The overall score of the teams
     * $totalScore[teamId] = value;
     *
     * @var int[]
     */
    public $totalScore = array(0 => 0, 1 => 0);

    /**
     * round number, 0 based
     *
     * @var int
     */
    public $roundNumber;

}
