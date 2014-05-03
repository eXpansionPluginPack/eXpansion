<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestRuns\Gui\Controls;

class RunPlayerElem extends \ManiaLive\Gui\Control {

    protected $bg;
    protected $nickname;
    protected $totalTime;

    function __construct(\ManiaLive\Data\Player $player) {
	$sizeX = 45;
	$sizeY = 5;

	$this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
	$this->bg->setPosX(-2);
	$this->bg->setStyle("BgsPlayerCard");
	$this->bg->setSubStyle("BgPlayerCardSmall");
	$this->bg->setAlign('left', 'center');
	$this->addComponent($this->bg);

	$this->nickname = new \ManiaLib\Gui\Elements\Label(30, 3);
	$this->nickname->setAlign('left', 'center');
	$this->nickname->setTextSize(1);
	$this->nickname->setPosX(0);
	$this->nickname->setText('$fff' . $player->nickName);
	$this->addComponent($this->nickname);

	$this->totalTime = new \ManiaLib\Gui\Elements\Label(13, 3);
	$this->totalTime->setAlign('left', 'center');
	$this->totalTime->setTextSize(1);
	$this->totalTime->setPosX(32);
	$this->totalTime->setText('$fff' . \ManiaLive\Utilities\Time::fromTM($player->bestTime));
	$this->addComponent($this->totalTime);

	$this->setSize($sizeX + 10, $sizeY);
    }

    public function destroy() {
	parent::destroy();
    }

}
?>

