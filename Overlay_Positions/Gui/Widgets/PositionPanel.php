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

    protected $frame;

    protected function onConstruct() {

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column());
        $this->addComponent($this->frame);
    }

    /**
     * @param \ManiaLivePlugins\eXpansion\Core\Structures\ExpPlayer[] $expPlayer
     */
    public function setData($expPlayer) {
        $this->frame->clearComponents();
        $x = 0;
        foreach ($expPlayer as $player) {
            $this->frame->addComponent(new \ManiaLivePlugins\eXpansion\Overlay_Positions\Gui\Controls\PlayerItem($x, $player, $this->getRecipient(), $this->getSizeX()));
            $x++;
        }
    }

}
