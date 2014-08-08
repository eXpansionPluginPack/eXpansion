<?php

namespace ManiaLivePlugins\eXpansion\Menu\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Config;

class PanelItem extends \ManiaLive\Gui\Control
{

	/** @var \ManiaLib\Gui\Elements\Quad */
	private $bg;

	private $nick;

	private $label;

	private $time;

	private $frame;

	function __construct()
	{
		$config = Config::getInstance();

		$sizeX = 30;
		$sizeY = 5.5;
		$this->setAlign("left", "top");

		$this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
		$this->bg->setAlign("left", "top");
		$this->bg->setImage($config->getImage("menu", "middle_off.png"), true);
		$this->bg->setImageFocus($config->getImage("menu", "middle_on.png"), true);
		$this->bg->setOpacity(0.8);
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
		$config = Config::getInstance();
		$this->bg->setImage($config->getImage("menu", "top_off.png"), true);
		$this->bg->setImageFocus($config->getImage("menu", "top_on.png"), true);
	}

	function setBottom()
	{
		$config = Config::getInstance();
		$this->bg->setImage($config->getImage("menu", "bottom_off.png"), true);
		$this->bg->setImageFocus($config->getImage("menu", "bottom_on.png"), true);
	}

}
?>

