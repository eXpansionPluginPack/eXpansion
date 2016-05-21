<?php

namespace ManiaLivePlugins\eXpansion\Widgets_TeamRoundScores\Gui\Controls;

use ManiaLib\Gui\Elements\Label;
use ManiaLive\Gui\Container;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Widgets_TeamRoundScores\Structures\RoundScore;

class ScoreItem extends Control
{

    protected $bg;
    protected $score1, $score2, $winner, $team1, $team2, $roundNumber;

    public function __construct(RoundScore $score)
    {
        $sizeX = 52;
        $sizeY = 4;

        $color = array(-1 => "fff", 0 => "3af", 1 => "d00");

        $this->roundNumber = new Label(5, 4);
        $this->roundNumber->setAlign('right', 'center');
        $this->roundNumber->setPosition(3, 0);
        $this->roundNumber->setStyle("TextCardSmallScores2");
        $this->roundNumber->setTextSize(1);
        $this->roundNumber->setTextColor("fff");
        $this->roundNumber->setText("r" . ($score->roundNumber + 1));
        $this->addComponent($this->roundNumber);

        $label = new Label(7, 5);
        $label->setAlign('center', 'center');
        $label->setStyle("TextCardSmallScores2");
        $label->setTextSize(1);

        // total score
        $this->score1 = clone $label;
        $this->score1->setPosX(6);
        $this->score1->setTextColor($color[0]);
        $this->score1->setText($score->totalScore[0]);
        $this->addComponent($this->score1);

        $this->score2 = clone $label;
        $this->score2->setPosX(11);
        $this->score2->setTextColor($color[1]);
        $this->score2->setText($score->totalScore[1]);
        $this->addComponent($this->score2);

        // winner team
        $this->winner = clone $label;
        $this->winner->setSizeX(16);
        $this->winner->setPosX(22);
        $text = "";
        $textColor = "fff";
        switch ($score->winningTeamId) {
            case 0:
                $text = "Blue";
                break;
            case 1:
                $text = "Red";
                break;
            default:
                $text = "Draw";
                $textColor = "aaa";
                break;
        }
        $this->winner->setTextColor($textColor);
        $this->winner->setText($text);
        $this->addComponent($this->winner);

        $this->team1 = clone $label;
        $this->team1->setPosX(32);
        $this->team1->setTextColor($color[0]);
        $this->team1->setText($score->score[0]);
        $this->addComponent($this->team1);

        $this->team2 = clone $label;
        $this->team2->setPosX(38);
        $this->team2->setTextColor($color[1]);
        $this->team2->setText($score->score[1]);
        $this->addComponent($this->team2);

        $this->setSize($sizeX, $sizeY);
        $this->setAlign("left", "top");
    }

    public function onIsRemoved(Container $target)
    {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    public function destroy()
    {
        $this->destroyComponents();
        parent::destroy();
    }
}

