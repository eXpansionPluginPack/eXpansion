<?php

namespace ManiaLivePlugins\eXpansion\Menu\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Config;

class PanelItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

	/** @var \ManiaLib\Gui\Elements\Quad */
	protected $bg;

	protected $nick;

	protected $label;

	protected $time;

	protected $frame;

	function __construct()
	{
		$config = Config::getInstance();

		$sizeX = 30;
		$sizeY = 5.5;
		$this->setAlign("left", "top");

		$this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
		$this->bg->setAlign("left", "top");
                $this->bg->setStyle('Bgs1InRace');
                $this->bg->setStyle('BgEmpty');
		$this->bg->setBgcolor($config->windowBackgroundColor);
                $this->bg->setBgcolorFocus($config->windowTitleBackgroundColor);
		$this->bg->setOpacity(0.75);
		$this->bg->setScriptEvents();
		$this->addComponent($this->bg);

		$this->label = new \ManiaLib\Gui\Elements\Label($sizeX, $sizeY);
		$this->label->setPosX($sizeX / 2);
		$this->label->setPosY(-1.5);
		$this->label->setAlign("center", "top");
		$this->label->setTextEmboss();

		$this->addComponent($this->label);
		$this->setSize($sizeX, $sizeY);
	}

	function setText($text)
	{
		$this->label->setText($text);
	}

	function setClass($value)
	{
		$this->bg->setAttribute("class", $value);
		$this->label->setAttribute("class", $value . "_lbl");
	}

	function setId($id)
	{
		$this->bg->setId($id);
		$this->label->setId($id . "_lbl");
	}

	function setAction($action)
	{
		$this->bg->setAction($action);
	}

	function onIsRemoved(\ManiaLive\Gui\Container $target)
	{
		parent::onIsRemoved($target);
		$this->destroy();
	}

	function destroy()
	{
		parent::destroy();
	}

	function setTop()
	{
        // deprecated
	}

	function setBottom()
	{
	// deprecated
	}

}
?>

