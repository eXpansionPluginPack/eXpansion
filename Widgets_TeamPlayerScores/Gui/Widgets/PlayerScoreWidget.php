<?php

namespace ManiaLivePlugins\eXpansion\Widgets_TeamPlayerScores\Gui\Widgets;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Layouts\Column;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;
use ManiaLivePlugins\eXpansion\Widgets_TeamPlayerScores\Gui\Controls\ScoreItem;
use ManiaLivePlugins\eXpansion\Widgets_TeamPlayerScores\Structures\PlayerScore;

/**
 * Description of RoundScoreWidget
 *
 * @author Petri
 */
class PlayerScoreWidget extends Widget
{

    private $frame, $bg, $lbl_title, $bgTitle;

    protected function exp_onBeginConstruct()
    {
        parent::exp_onBeginConstruct();
        $this->setName("Player Scores for team mode");
        $sizeX = 42;
        $sizeY = 56;

        $this->bg = new WidgetBackGround($sizeX, $sizeY);
        $this->addComponent($this->bg);

        $this->bgTitle = new Quad($sizeX, 4.2);
        $this->bgTitle->setStyle("UiSMSpectatorScoreBig");
        $this->bgTitle->setSubStyle("PlayerSlotCenter");
        $this->bgTitle->setColorize("3af");
        $this->addComponent($this->bgTitle);

        $this->lbl_title = new Label(30, 5);
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

    /**
     *
     * @param PlayerScore $roundScores
     * @return null
     */
    public function setScores($playerScores)
    {
        $x = 0;
        foreach ($playerScores as $score) {
            if ($x >= 12)
                return;
            $this->frame->addComponent(new ScoreItem($score));
            $x++;
        }
    }

}
