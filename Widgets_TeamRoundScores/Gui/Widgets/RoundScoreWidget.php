<?php

namespace ManiaLivePlugins\eXpansion\Widgets_TeamRoundScores\Gui\Widgets;

use ManiaLib\Gui\Layouts\Column;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;
use ManiaLivePlugins\eXpansion\Widgets_TeamRoundScores\Gui\Controls\ScoreItem;
use ManiaLivePlugins\eXpansion\Widgets_TeamRoundScores\Structures\RoundScore;

/**
 * Description of RoundScoreWidget
 *
 * @author Petri
 */
class RoundScoreWidget extends Widget
{

    private $frame, $bg, $lbl_title, $bgTitle;

    protected function exp_onBeginConstruct()
    {
        parent::exp_onBeginConstruct();
        $this->setName("Round Scores for team mode");
        $sizeX = 42;
        $sizeY = 56;

        $this->bg = new WidgetBackGround($sizeX, $sizeY);
        $this->addComponent($this->bg);

        $this->bgTitle = new \ManiaLib\Gui\Elements\Quad($sizeX, 4.2);
        $this->bgTitle->setStyle("UiSMSpectatorScoreBig");
        $this->bgTitle->setSubStyle("PlayerSlotCenter");
        $this->bgTitle->setColorize("3af");
        $this->addComponent($this->bgTitle);

        $this->lbl_title = new \ManiaLib\Gui\Elements\Label(30, 5);
        $this->lbl_title->setTextSize(1);
        $this->lbl_title->setTextColor("fff");
        $this->lbl_title->setStyle("TextCardScores2");
        $this->lbl_title->setText('Round Points');
        $this->lbl_title->setAlign("center", "center");
        $this->addComponent($this->lbl_title);

        $this->frame = new Frame(2, -6.5);
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setAlign("left", "top");
        $this->frame->setLayout(new Column());
        $this->addComponent($this->frame);
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->lbl_title->setPosition(($this->sizeX / 2), -2);
    }

    public function setScores($roundScores)
    {
        foreach ($roundScores as $score) {
            if ($score->roundNumber >= 12)
                return;
            $this->frame->addComponent(new ScoreItem($score));
        }
    }

}
