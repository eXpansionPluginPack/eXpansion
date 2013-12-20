<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\Overlay_Positions\Gui\Widgets;

/**
 * Description of PositionPanel
 *
 * @author Reaby
 */
class PositionPanel extends \ManiaLive\Gui\Window {

    protected $frame, $label;

    protected function onConstruct() {

        $this->label = new \ManiaLib\Gui\Elements\Label(40, 6);
        $this->label->setPosY(6);
        $this->addComponent($this->label);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column());
        $this->addComponent($this->frame);
    }

    /**
     * @param \ManiaLivePlugins\eXpansion\Core\Structures\ExpPlayer[] $expPlayer
     */
    public function setData($expPlayer, $gamemode) {
        $this->frame->clearComponents();
        $x = 0;
        $total = count($expPlayer);
        $teamPoints = array(0 => 0, 1 => 0);
        // $teamPointsDisplay = array(0 => 0, 1 => 0);
        $cpCount = 0;
        foreach ($expPlayer as $player) {
            $cpindex = $player->curCpIndex;
            if ($cpindex < 0)
                $cpindex = 0;
            $cpCount += $cpindex;
            if ($player->teamId >= 0 && !$player->hasRetired) {
                $teamPoints[$player->teamId] += ($total + 1) - ($player->position + 1);
                //   $teamPointsDisplay[$player->teamId] += ($total + 1) - ($player->position + 1);
            }
            if ($x < 8) {
                $this->frame->addComponent(new \ManiaLivePlugins\eXpansion\Overlay_Positions\Gui\Controls\PlayerItem($x, $player, $this->getRecipient(), $this->getSizeX()));
            }
            $x++;
        }
        if ($gamemode == \DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM) {

            if ($teamPoints[0] == $teamPoints[1] || $cpCount == 0) {
                $this->label->setText('$fffTeams Score is Draw');
            } elseif ($teamPoints[1] < $teamPoints[0]) {
                $this->label->setText('$00fTeam Blue is Winning  $fff' . $teamPoints[0] . " vs " . $teamPoints[1]);
            } else {
                $this->label->setText('$f00Team Red is Winning $fff' . $teamPoints[1] . "vs" . $teamPoints[0]);
            }
        } else {
            $this->label->setText("");
        }
    }

}
