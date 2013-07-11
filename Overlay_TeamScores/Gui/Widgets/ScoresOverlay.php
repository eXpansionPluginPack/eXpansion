<?php

namespace ManiaLivePlugins\eXpansion\Overlay_TeamScores\Gui\Widgets;

class ScoresOverlay extends \ManiaLive\Gui\Window {

    private $background;
    private $team1;
    private $team2;
    private $score1;
    private $score2;

    protected function onConstruct() {
        $this->setPosition(0, 86);
        $this->setAlign("center", "top");

        $this->background = new \ManiaLib\Gui\Elements\Quad();
        $this->background->setImage("http://chris92.tm-betmania.com/images/scoreboard.png", true);
        //$this->background->setImage("http://tm.mania-exchange.com/tracks/screenshot/normal/39208/?i.jpg", false);

        $this->background->setSize(110 * 1.5, 12 * 1.5);
        $this->background->setAlign("center", "top");
        $this->addComponent($this->background);

        $this->team1 = new \ManiaLib\Gui\Elements\Label(60, 8);
        $this->team1->setStyle("TextRankingsBig");
        $this->team1->setAlign("left", "center");
        $this->team1->setTextSize(3);
        $this->team1->setTextPrefix('$s');
        $this->team1->setPosition(25, -7);
        $this->addComponent($this->team1);

        $this->team2 = new \ManiaLib\Gui\Elements\Label(60, 8);
        $this->team2->setStyle("TextRankingsBig");
        $this->team2->setAlign("right", "center");
        $this->team2->setTextSize(3);
        $this->team2->setTextPrefix('$s');
        $this->team2->setPosition(-25, -7);
        $this->addComponent($this->team2);

        $this->score1 = new \ManiaLib\Gui\Elements\Label(20, 7);
        $this->score1->setStyle("TextRaceChrono");
        $this->score1->setAlign("left", "top");
        $this->score1->setPosition(3, -5);
        $this->addComponent($this->score1);

        $this->score2 = new \ManiaLib\Gui\Elements\Label(20, 7);
        $this->score2->setStyle("TextRaceChrono");
        $this->score2->setAlign("right", "top");
        $this->score2->setPosition(-3, -5);
        $this->addComponent($this->score2);        
    }

    /**
     * 
     * @param \ManiaLivePlugins\eXpansion\Overlay_TeamScores\Structures\Team[] $teams 
     */
    function setData($teams) {
        $this->team1->setText($teams[1]->name);
        $this->team2->setText($teams[0]->name);

        $this->score1->setText($teams[1]->score);
        $this->score2->setText($teams[0]->score);
    }

    function onShow() {
        
    }

}

?>
