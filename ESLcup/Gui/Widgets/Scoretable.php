<?php

namespace ManiaLivePlugins\eXpansion\ESLcup\Gui\Widgets;

/**
 * Description of Scoretable
 *
 * @author Reaby
 */
class Scoretable extends \ManiaLive\Gui\Window {

    protected $background, $rankingslabel, $pointslimit, $gamemode;
    protected $frame;

    protected function onConstruct() {
        $this->sizeX = 165;
        $this->sizeY = 90;

        $this->background = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->sizeY);
        $this->background->setStyle("Bgs1InRace");
        $this->background->setSubStyle("BgList");
        $this->addComponent($this->background);
        $this->background->setPosY(4);

        $this->rankingslabel = new \ManiaLib\Gui\Elements\Label(120, 6);
        $this->rankingslabel->setText(__("Score Rankings"));
        $this->rankingslabel->setTextColor("fff");
        $this->rankingslabel->setTextSize(4);
        $this->rankingslabel->setPosition($this->sizeX / 2, 2);
        $this->rankingslabel->setAlign("center");
        $this->addComponent($this->rankingslabel);

        $this->pointslimit = new \ManiaLib\Gui\Elements\Label(30, 6);
        $this->pointslimit->setTextColor("fff");
        $this->pointslimit->setTextSize(1);
        $this->pointslimit->setPosition($this->sizeX / 2, -$this->sizeY);
        $this->pointslimit->setAlign("center");
        $this->addComponent($this->pointslimit);

        $this->gamemode = new \ManiaLib\Gui\Elements\Label(30, 6);
        $this->gamemode->setTextColor("fff");
        $this->gamemode->setTextSize(1);
        $this->gamemode->setText('$sGame Mode: ESL cup');
        $this->gamemode->setPosition($this->sizeX / 2, -$this->sizeY - 4);
        $this->gamemode->setAlign("center");
        $this->addComponent($this->gamemode);


        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\VerticalFlow());
        $this->frame->setSize(120, 80);
        $this->frame->setPosition(2, -2);
        $this->addComponent($this->frame);
    }

    public function populate($scores, $limit, $winners) {
        $this->pointslimit->setText('$sPoints Limit: ' . $limit);
        $this->frame->clearComponents();
        $x = 1;
        foreach ($scores as $scoreitem) {
            if ($x > 16)
                continue;
            $place = -1;
            $i = 1;
            foreach ($winners as $winner) {
                if ($winner->login == $scoreitem->login) {
                    $place = $i;
                }
                $i++;
            }
            $this->frame->addComponent(new \ManiaLivePlugins\eXpansion\ESLcup\Gui\Controls\CupScoreTableItem($x, $scoreitem, $place));
            $x++;
        }
    }

}
