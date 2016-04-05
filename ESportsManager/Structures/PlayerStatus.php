<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\ESportsManager\Structures;

/**
 * Description of PlayerStatus
 *
 * @author Reaby
 */
class PlayerStatus extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

    const Ready = "ready";
    const NotReady = "notReady";
    const Timeout = "timeout";
    const Other = "other";

    /** @var \ManiaLive\Data\Player */
    public $player;

    /** @var String */
    public $login;

    /** @var String */
    public $nickName;

    /** @var Integer */
    public $voteStartTime;

    /** @var Integer */
    public $voteAnswerTime;

    /** @var mixed */
    public $status;

    /**
     *
     * @param \ManiaLive\Data\Player $player
     * @param mixed                  $status
     */
    public function __construct(\ManiaLive\Data\Player $player)
    {
        $player->skins = null;

        $this->player = $player;
        $this->login = $player->login;
        $this->nickName = $player->nickName;
        $this->voteStartTime = time();
        $this->status = self::NotReady;
    }

}
