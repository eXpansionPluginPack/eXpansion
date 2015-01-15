<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Gui\Controls;

class RateButton extends \ManiaLivePlugins\eXpansion\Gui\Control
{

	protected $label;

	protected $frame;

	protected $quad;

	/**
	 * Button
	 * 
	 * @param int $sizeX = 24
	 * @param intt $sizeY = 6
	 */
	function __construct($number)
	{
		$sizeX = 22;
		$sizeY = 8;
		$this->setAlign("left");

		/* $this->quad = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
		  $this->quad->setStyle("Bgs1InRace");
		  $this->quad->setSubStyle("BgCard");
		  $this->quad->setAlign('left', 'center');
		  $this->quad->setAttribute("class","rateButton");
		  $this->quad->setId("rate_".$number);
		  $this->quad->setScriptEvents();
		  $this->addComponent($this->quad);

		  $this->label = new \ManiaLib\Gui\Elements\Label();
		  $this->label->setAlign('center', 'center');
		  $this->label->setStyle("TextCardRaceRank");
		  $this->label->setTextSize(1);
		  $this->label->setTextColor("000");
		  $this->label->setText($number . " / 5");

		  $this->label->setPosition(0, 3);
		  //$this->addComponent($this->label);

		  $starSize = 3.5 * $number;
		  $correction = ($sizeX - $starSize) / 2;
		  $this->frame = new \ManiaLive\Gui\Controls\Frame($correction, ($sizeY - 3.5) / 2);
		  $this->frame->setAlign("left", "center");
		  $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
		  $this->addComponent($this->frame);

		  for ($x = 0; $x < $number; $x++) {
		  $star = new \ManiaLib\Gui\Elements\Quad(3.5, 3.5);
		  $star->setStyle("BgRaceScore2");
		  $star->setSubStyle("Fame");
		  $star->setColorize("dd0");
		  $this->frame->addComponent($star);
		  }
		 */
		$this->label = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
		//$this->label->setIcon("BgRaceScore2", "Fame");
		$this->label->$this->label->setAlign('center', 'center');
		$this->label->setText("+" . $number);
		$this->label->setId("rate_" . $number);
		$this->label->setAttribute("class", "rateButton");
		
		$this->addComponent($this->label);
		
		$this->setSize($sizeX, $sizeY);
	}

	protected function onResize($oldX, $oldY)
	{
		//$this->label->setSize($this->sizeX - 2, $this->sizeY - 1);
		$this->label->setPosX(($this->sizeX - 2) / 2);
		$this->label->setPosZ($this->posZ);

		parent::onResize($oldX, $oldY);
	}

}

?>