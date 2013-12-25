<?php

namespace ManiaLivePlugins\eXpansion\ESportsManager\Gui\Controls;

use \ManiaLivePlugins\eXpansion\ESportsManager\Structures\PlayerStatus;

class PlayerStatusItem extends \ManiaLive\Gui\Control {

    protected $bg;
    protected $nickname;
    protected $team;
    protected $status;
    protected $frame;

    /**
     * 
     * @param int $indexNumber
     * @param \DedicatedApi\Structures\Player $player
     * @param int $score
     * @param type $controller
     * @param int $sizeX
     */
    function __construct($indexNumber, \ManiaLivePlugins\eXpansion\ESportsManager\Structures\PlayerStatus $player, $sizeX) {
        $sizeY = 4;

        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());



        $this->team = new \ManiaLib\Gui\Elements\Quad();
        $this->team->setSize(4, 4);
        $this->team->setAlign("center", "center2");
        $this->team->setStyle("Icons64x64_1");
        $this->team->setSubStyle("Empty");
        if (\ManiaLivePlugins\eXpansion\ESportsManager\ESportsManager::$matchSettings->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM) {

            if ($player->player->teamId === 0) {
                $this->team->setStyle("BgRaceScore2");
                $this->team->setSubStyle("HandleBlue");
                $this->team->setAction($this->toggleTeam);
            }
            if ($player->player->teamId === 1) {
                $this->team->setStyle("BgRaceScore2");
                $this->team->setSubStyle("HandleRed");
                $this->team->setAction($this->toggleTeam);
            }
        }
        $this->frame->addComponent($this->team);


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        //$this->frame->addComponent($spacer);

        $this->label = new \ManiaLib\Gui\Elements\Label(50, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText($player->nickName);
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

        $this->status = new \ManiaLib\Gui\Elements\Label();
        $text = '$d00Waiting...';
        switch ($player->status) {
            case PlayerStatus::NotReady:
                $text = '$dd0Not Ready';
                break;
            case PlayerStatus::Ready:
                $text = '$0d0Ready';
                break;
            case PlayerStatus::Timeout:
                $text = '$d00Waiting...';
                break;
        }
        $this->status->setText($text);
        $this->status->setAlign('left', 'center');
        $this->status->setScale(0.8);
        $this->frame->addComponent($this->status);

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY) {
        $this->bg->setSize($this->sizeX, $this->sizeY);
        $this->bg->setPosX(-2);
        $this->frame->setSize($this->sizeX, $this->sizeY);
    }

}
?>
