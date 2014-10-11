<?php

namespace ManiaLivePlugins\eXpansion\ChatBackground\Gui\Windows;

use ManiaLivePlugins\eXpansion\ChatBackground\Config;

class BoxWindow extends \ManiaLive\Gui\Window
{

	private $quad, $quad2;

	private $config;

	protected function onConstruct()
	{
		$this->config = Config::getInstance();

		$this->quad = new \ManiaLib\Gui\Elements\Quad($this->config->width, $this->config->height);
		$this->quad->setStyle("BgsPlayerCard");
		$this->quad->setSubStyle("BgRacePlayerName");
		$this->quad->setPosition(0, $this->config->posY, $this->config->posZ);
		$this->quad->setColorize($this->config->color);
		$this->quad->setAlign("center", "bottom");
		$this->addComponent($this->quad);

		$this->quad2 = new \ManiaLib\Gui\Elements\Quad($this->config->width*2, $this->config->height);
		$this->quad2->setPosition(0, $this->config->posY, $this->config->posZ);
		$this->quad2->setColorize("3af");
		$this->quad2->setStyle("BgsPlayerCard");
		$this->quad2->setSubStyle("BgRacePlayerLine");
		$this->quad2->setAttribute("rot", 180);
		$this->quad2->setAlign("center", "top");
		$this->addComponent($this->quad2);
	}

}

?>
