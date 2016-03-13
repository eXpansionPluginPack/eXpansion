<?php

namespace ManiaLivePlugins\eXpansion\Widgets_PlainLocalRecords\Gui\Controls;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLive\Gui\Container;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record;


class Recorditem extends Control {

    private $bg, $bg2;
    private $nick;
    private $label;
    private $time;
    private $frame;

    function __construct($index, Record $record, $highlite = false) {
	$sizeX = 38;
	$sizeY = 4;

	// hilight own record
	$this->bg = new Quad($sizeY, $sizeX * 1.2);
	$this->bg->setStyle("BgsPlayerCard");
	$this->bg->setSubStyle("BgRacePlayerLine");
	$this->bg->setColorize("0f0");
	$this->bg->setAlign('left', 'top');
	//$this->bg->setBgcolor('6af5');
	$this->bg->setHidden(1);
	$this->bg->setPosY(1.5);
	$this->bg->setOpacity(0.75);
	$this->bg->setAttribute("rot", 90);
	$this->bg->setId("RecBgBlink_" . $index);
	$this->bg->setPosX($sizeX);
	$this->addComponent($this->bg);

	// hilight of server record
	$this->bg2 = new Quad($sizeY, $sizeX * 1.5);
	$this->bg2->setStyle("BgsPlayerCard");
	$this->bg2->setSubStyle("BgRacePlayerLine");
	$this->bg2->setOpacity(0.75);
	$this->bg2->setColorize("3af");
	$this->bg2->setAttribute("rot", 270);
	$this->bg2->setAlign('left', 'top');
	//$this->bg->setBgcolor('6af5');
	$this->bg2->setHidden(1);
	//$this->bg2->setPosX(-1);
	$this->bg2->setPosY(-2.25);
	//$this->bg2->setPosX($sizeX);

	$this->bg2->setId("RecBg_" . $index);
	$this->addComponent($this->bg2);

	$this->label = new Label(4, 4);
	$this->label->setAlign('right', 'center');
	$this->label->setPosition(3, 0);
	$this->label->setStyle("TextCardSmallScores2");
	$this->label->setId("RecRank_" . $index);
	$this->label->setTextSize(1);
	$this->label->setText($index);
	$this->label->setTextColor('ff0');
	$this->addComponent($this->label);

	$this->label = new Label(11, 5);
	$this->label->setPosition(3.7, 0);
	$this->label->setAlign('left', 'center');
	$this->label->setStyle("TextCardSmallScores2");
	$this->label->setTextSize(1);
	$this->label->setId("RecTime_" . $index);
	$this->label->setText(\ManiaLive\Utilities\Time::fromTM($record->time));
	$this->label->setTextColor('fff');
	$this->addComponent($this->label);

	$this->nick = new Label(22, 4);
	$this->nick->setPosition(15.5, 0);
	$this->nick->setAlign('left', 'center');
	$this->nick->setStyle("TextCardSmallScores2");
	$this->nick->setTextSize(1);
	$this->nick->setTextColor('fff');
	$this->nick->setId("RecNick_" . $index);
	$this->nick->setAttribute("class", "nickLabel");
	$this->nick->setScriptEvents();
	$this->nick->setText($record->nickName);
	$this->addComponent($this->nick);

	// $this->addComponent($this->frame);

	$this->setSize($sizeX, $sizeY);
	$this->setAlign("center", "top");
    }

    function onIsRemoved(Container $target) {
	parent::onIsRemoved($target);
	$this->destroy();
    }

    public function destroy() {
	// $this->frame->clearComponents();
	// $this->frame->destroy();
	$this->destroyComponents();
	parent::destroy();
    }

}

?>
