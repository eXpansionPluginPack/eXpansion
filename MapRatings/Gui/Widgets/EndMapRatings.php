<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLivePlugins\eXpansion\MapRatings\Gui\Controls\RateButton2;

class EndMapRatings extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{

	protected $label, $xml, $frame, $bg, $titlebg, $labelMap;

	protected $b0, $b1, $b2, $b3, $b4, $b5;

	public static $parentPlugin;

	private $script;

	protected function onConstruct()
	{
		parent::onConstruct();

		$this->setName("Map ratings (endmap)");
		$sizeX = 90;
		$sizeY = 25;
		
		$bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround($sizeX, $sizeY);
		$bg->setAlign("left", "top");
		$this->bg = $bg;
		$this->addComponent($this->bg);
		

		$bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetTitle($sizeX, 4.6);
		$bg->setAlign("center", "top");
		$bg->setPosX($sizeX/2);
		$this->titlebg = $bg;
		$this->addComponent($this->titlebg);

		
		$this->label = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel($sizeX-10, 9);
		$this->label->setStyle("TextCardSmallScores2");
		$this->label->setTextSize(2);
		$this->label->setTextEmboss(true);
		$this->label->setAlign("center", "top");
		$this->label->setPosX(($sizeX)/2);
		$this->label->setPosY(-0.5);
		$this->addComponent($this->label);


		$this->labelMap = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel($sizeX-10, 9);
		$this->labelMap->setStyle("TextCardSmallScores2");
		$this->labelMap->setTextSize(3);
		$this->labelMap->setTextEmboss(true);
		$this->labelMap->setAlign("center", "top");
		$this->labelMap->setPosX(($sizeX)/2);
		$this->labelMap->setPosY(-6);
		$this->addComponent($this->labelMap);



		$this->frame = new \ManiaLive\Gui\Controls\Frame(27, -16);
		$this->frame->setAlign("left", "top");
		$line = new \ManiaLib\Gui\Layouts\Line();
		$line->setMargin(8,0);
		$this->frame->setLayout($line);
		$this->addComponent($this->frame);

		$this->b0 = new RateButton2(0);
		$this->frame->addComponent($this->b0);

		/* $this->b1 = new RateButton(1);
		$this->frame->addComponent($this->b1);

		$this->b2 = new RateButton(2);
		$this->frame->addComponent($this->b2);

		$this->b3 = new RateButton(3);
		$this->frame->addComponent($this->b3);

		$this->b4 = new RateButton(4);
		$this->frame->addComponent($this->b4); */

		$this->b5 = new RateButton2(5);
		$this->frame->addComponent($this->b5);


		$this->setPosition(-45, -42);

		$this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("MapRatings\Gui\Script");
		$action = $this->createAction(array(self::$parentPlugin, "saveRating"), 0);
		$this->script->setParam("rate_" . 0, $action);

		$action = $this->createAction(array(self::$parentPlugin, "saveRating"), 5);
		$this->script->setParam("rate_" . 5, $action);


		$this->registerScript($this->script);
		$this->setSize($sizeX, $sizeY);
	}

	function setMap(\Maniaplanet\DedicatedServer\Structures\Map $map)
	{
		$msg = exp_getMessage("Did you like the map ?");
		$this->label->setText($msg);
		$this->labelMap->setText(\ManiaLib\Utils\Formatting::stripCodes($map->name, "wosn"));
	}

}
?>
    
