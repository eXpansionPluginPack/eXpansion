<?php

namespace ManiaLivePlugins\eXpansion\Widgets_TeamPlayerScores\Gui\Controls;

use ManiaLib\Gui\Elements\Label;
use ManiaLive\Gui\Container;
use ManiaLive\Gui\Control;
use ManiaLivePlugins\eXpansion\Widgets_TeamPlayerScores\Structures\PlayerScore;

class ScoreItem extends Control {

    private $bg;
    private $score, $nick, $time, $win1, $win2, $win3;

    function __construct(PlayerScore $playerScore) {
	$sizeX = 52;
	$sizeY = 4;

	$color = array(0 => "ff0", 1 => "aaa", 2 => "f80");

	$label = new Label(7, 5);
	$label->setAlign('center', 'center');
	$label->setStyle("TextCardSmallScores2");
	$label->setTextColor('fff');
	$label->setTextSize(1);

	// total score
	$this->score = clone $label;
	$this->score->setPosition(3, 0);
	$this->score->setTextColor("ff0");
	$this->score->setAlign('right', 'center');
	$this->score->setText($playerScore->score);
	$this->addComponent($this->score);

	// Nickname
	$this->nick = clone $label;
	$this->nick->setPosX(4);
	$this->nick->setSize(13, 4);
	$this->nick->setAlign('left', 'center');	
	$this->nick->setText(\ManiaLib\Utils\Formatting::stripColors($playerScore->nickName));
	$this->addComponent($this->nick);
	
	// BestTime
	$this->time = clone $label;
	$this->time->setPosX(23);
	$this->time->setSize(16, 4);	
	$this->time->setText(\ManiaLive\Utilities\Time::fromTM($playerScore->bestTime));
	$this->addComponent($this->time);

	// wins
	$this->win1 = clone $label;
	$this->win1->setPosX(30);
	$this->win1->setTextColor($color[0]);
	$this->win1->setText($playerScore->winScore[0]);
	$this->addComponent($this->win1);
	
	$this->win2 = clone $label;
	$this->win2->setPosX(34);
	$this->win2->setTextColor($color[1]);
	$this->win2->setText($playerScore->winScore[1]);
	$this->addComponent($this->win2);
	
	$this->win3 = clone $label;
	$this->win3->setPosX(38);
	$this->win3->setTextColor($color[2]);
	$this->win3->setText($playerScore->winScore[2]);
	$this->addComponent($this->win3);

	$this->setSize($sizeX, $sizeY);
	$this->setAlign("left", "top");
    }

    function onIsRemoved(Container $target) {
	parent::onIsRemoved($target);
	$this->destroy();
    }

    public function destroy() {
	$this->clearComponents();
	parent::destroy();
    }

}

?>
