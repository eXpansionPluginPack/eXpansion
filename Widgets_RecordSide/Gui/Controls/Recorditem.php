<?php

namespace ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Controls;

use ManiaLivePlugins\eXpansion\Widgets_Record\Config;
use ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords;
use ManiaLivePlugins\eXpansion\Helpers\Countries;

class Recorditem extends \ManiaLive\Gui\Control {

    private $bg;
    private $nick;
    private $label;
    private $time;
    private $frame;

    function __construct($index, $highlite) {
        $sizeX = 40;
        $sizeY = 3;


		$this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
		/* $this->bg->setStyle("Bgs1InRace");
		  $this->bg->setStyle("NavButtonBlink"); */
		$this->bg->setAlign('left', 'center');
		$this->bg->setBgcolor('6af5');
		$this->bg->setHidden(1);
		$this->bg->setId("RecBg_".$index);
		$this->addComponent($this->bg);
        

        $this->label = new \ManiaLib\Gui\Elements\Label(4, 4);
        $this->label->setAlign('right', 'center');
        $this->label->setPosition(3, 0);
        $this->label->setStyle("TextRaceChat");
		$this->label->setId("RecRank_".$index);
        $this->label->setScale(0.75);
        $this->label->setTextSize(1);
        $this->label->setTextColor('ff0');
        $this->addComponent($this->label);

        $this->label = new \ManiaLib\Gui\Elements\Label(11, 5);
        $this->label->setPosX(3.7);
        $this->label->setAlign('left', 'center');
        $this->label->setStyle("TextRaceChat");
        $this->label->setScale(0.75);
        $this->label->setTextSize(1);
        $this->label->setId("RecTime_".$index);
        $this->label->setTextColor('fff');
        $this->addComponent($this->label);

        $this->nick = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->nick->setPosition(12.5, 0);
        $this->nick->setAlign('left', 'center');
        $this->nick->setStyle("TextRaceChat");
        $this->nick->setScale(0.75);
        $this->nick->setTextSize(1);
        $this->nick->setTextColor('fff');
		$this->nick->setId("RecNick_".$index);
        $this->addComponent($this->nick);

        // $this->addComponent($this->frame);

        $this->setSize($sizeX, $sizeY);
        $this->setAlign("center", "top");
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    public function destroy() {
        // $this->frame->clearComponents();
        // $this->frame->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}
?>

