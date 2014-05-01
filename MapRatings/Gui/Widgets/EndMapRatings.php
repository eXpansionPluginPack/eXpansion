<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLivePlugins\eXpansion\MapRatings\Gui\Controls\RateButton;

class EndMapRatings extends \ManiaLivePlugins\eXpansion\Gui\Windows\Widget {

    protected $label, $xml, $frame, $bg, $titlebg;
    protected $b0, $b1, $b2, $b3, $b4, $b5;
    public static $parentPlugin;
    private $script;
    
    protected function onConstruct() {
	parent::onConstruct();

	$bg = new \ManiaLib\Gui\Elements\Quad(140, 20);
	$bg->setStyle("UiSMSpectatorScoreBig");
	$bg->setSubStyle("PlayerSlot");
	$bg->setAlign("center", "top");
	$this->bg = $bg;
	$this->addComponent($this->bg);

	$bg = new \ManiaLib\Gui\Elements\Quad(100, 8);
	$bg->setStyle("UiSMSpectatorScoreBig");
	$bg->setSubStyle("PlayerSlotCenter");
	$bg->setColorize('0f0');
	$bg->setAlign("center", "top");
	$bg->setPosY(2);
	$this->titlebg = $bg;
	$this->addComponent($this->titlebg);


	$this->label = new \ManiaLib\Gui\Elements\Label(90, 9);
	$this->label->setStyle("TextCardSmallScores2");
	$this->label->setTextSize(3);
	$this->label->setTextEmboss(true);
	$this->label->setText("mapname goes here!");
	$this->label->setAlign("center", "top");
	$this->label->setPosY(0);
	$this->addComponent($this->label);

	$this->frame = new \ManiaLive\Gui\Controls\Frame(0, -10);
	$this->frame->setSize(70, 30);
	$this->frame->setAlign("center", "top");
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
	$this->addComponent($this->frame);

	$this->b0 = new RateButton(0);
	$this->frame->addComponent($this->b0);

	$this->b1 = new RateButton(1);
	$this->frame->addComponent($this->b1);

	$this->b2 = new RateButton(2);
	$this->frame->addComponent($this->b2);

	$this->b3 = new RateButton(3);
	$this->frame->addComponent($this->b3);

	$this->b4 = new RateButton(4);
	$this->frame->addComponent($this->b4);

	$this->b5 = new RateButton(5);
	$this->frame->addComponent($this->b5);

	$this->setSize(120, 20);
	$this->setAlign("left", "top");
	$this->setPosition(0, -56);
	$this->setScale(0.7);

	$this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("MapRatings\Gui\Script");		
	for ($x = 0; $x < 6; $x++) {
	    $action = $this->createAction(array(self::$parentPlugin, "saveRating"), $x);
	    $this->script->setParam("rate_" . $x, $action);
	}
	$this->registerScript($this->script);
	
	
	$this->setName("Map ratings (endmap)");
    }

    function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
	$this->frame->setPosX(-($this->frame->sizeX / 2) + 4);
    }

    function setMap(\Maniaplanet\DedicatedServer\Structures\Map $map) {
	$this->label->setText($map->author . " - " . $map->name);
    }

}
?>
    
