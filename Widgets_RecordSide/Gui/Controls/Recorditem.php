<?php

namespace ManiaLivePlugins\eXpansion\Widgets_RecordSide\Gui\Controls;

use ManiaLivePlugins\eXpansion\Widgets_Record\Config;
use ManiaLivePlugins\eXpansion\LocalRecords\LocalRecords;
use ManiaLivePlugins\eXpansion\Helpers\Countries;

class Recorditem extends \ManiaLive\Gui\Control {

    private $bg, $bg2;
    private $nick;
    private $label;
    private $time;
    private $frame;

    function __construct($index, $highlite, $moreInfo=false) {
	$sizeX = 40;
	$sizeY = 4;


	$this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX+2, $sizeY);
	$this->bg->setStyle("Icons128x128_Blink");
	$this->bg->setSubStyle(\ManiaLib\Gui\Elements\Icons128x128_Blink::ShareBlink); 
	$this->bg->setAlign('left', 'center');
	//$this->bg->setBgcolor('6af5');
	$this->bg->setHidden(1);
	$this->bg->setPosX(-1);
	$this->bg->setId("RecBgBlink_" . $index);
	$this->addComponent($this->bg);

	$this->bg2 = new \ManiaLib\Gui\Elements\Quad($sizeX+2, $sizeY+1);
	$this->bg2->setStyle("Bgs1");
	$this->bg2->setSubStyle("NavButtonBlink"); 
	$this->bg2->setAlign('left', 'center');
	//$this->bg->setBgcolor('6af5');
	$this->bg2->setHidden(1);
	$this->bg2->setPosX(-1);
	$this->bg2->setId("RecBg_" . $index);
	$this->addComponent($this->bg2);

	
	
	
	$this->label = new \ManiaLib\Gui\Elements\Label(4, 4);
	$this->label->setAlign('right', 'center');
	$this->label->setPosition(3, 0);
	$this->label->setStyle("TextRaceChat");
	$this->label->setId("RecRank_" . $index);
	$this->label->setTextSize(1);
	//$this->label->setText($index);
	$this->label->setTextColor('ff0');
	$this->addComponent($this->label);

	$this->label = new \ManiaLib\Gui\Elements\Label(11, 5);
	$this->label->setPosition(3.7, 0);
	$this->label->setAlign('left', 'center');
	$this->label->setStyle("TextRaceChat");
	$this->label->setTextSize(1);
	$this->label->setId("RecTime_" . $index);
	$this->label->setTextColor('fff');		
	$this->addComponent($this->label);

	$this->nick = new \ManiaLib\Gui\Elements\Label(30, 4);
	$this->nick->setPosition(15.5, 0);
	$this->nick->setAlign('left', 'center');
	$this->nick->setStyle("TextRaceChat");
	$this->nick->setTextSize(1);
	$this->nick->setTextColor('fff');
	$this->nick->setId("RecNick_" . $index);	
	$this->addComponent($this->nick);

	if($moreInfo){
	    $this->label = new \ManiaLib\Gui\Elements\Label(6, 4);
	    $this->label->setAlign('right', 'center');
	    $this->label->setPosition(59, 0);
	    $this->label->setStyle("TextRaceChat");
	    $this->label->setId("RecCp2_" . $index);
	    $this->label->setTextSize(1);
	    $this->label->setTextColor('ff0');
	    //$this->label->setText("+1Cp");
	    $this->addComponent($this->label);

	    $this->label = new \ManiaLib\Gui\Elements\Label(6, 4);
	    $this->label->setPosition(-18, 0);
	    $this->label->setAlign('left', 'center');
	    $this->label->setStyle("TextRaceChat");
	    $this->label->setTextSize(1);
	    $this->label->setId("RecCp1_" . $index);
	    $this->label->setTextColor('ff0');
	    //$this->label->setText("+1Cp");
	    $this->addComponent($this->label);

	    $this->label = new \ManiaLib\Gui\Elements\Label(11, 4);
	    $this->label->setAlign('right', 'center');
	    $this->label->setPosition(53, 0);
	    $this->label->setStyle("TextRaceChat");
	    $this->label->setId("RecInfo2_" . $index);
	    $this->label->setTextSize(1);
	    $this->label->setTextColor('fff');
	    //$this->label->setText("+00:00:00");
	    $this->addComponent($this->label);

	    $this->label = new \ManiaLib\Gui\Elements\Label(11, 4);
	    $this->label->setPosition(-12, 0);
	    $this->label->setAlign('left', 'center');
	    $this->label->setStyle("TextRaceChat");
	    $this->label->setTextSize(1);
	    $this->label->setId("RecInfo1_" . $index);
	    $this->label->setTextColor('fff');
	    //$this->label->setText("+00:00:00");
	    $this->addComponent($this->label);
	    
	    $this->bg->setPosX(-19);
	    $this->bg->setSizeX($sizeX+2+37);
	}
	
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

