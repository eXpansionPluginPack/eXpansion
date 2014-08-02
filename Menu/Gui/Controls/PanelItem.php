<?php

namespace ManiaLivePlugins\eXpansion\Menu\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;

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
		$sizeX = 25;
		$sizeY = 4.5;
		$this->setAlign("left", "top");

		$this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
		$this->bg->setAlign("left", "top");
		$this->bg->setBgcolor("000b");
		$this->bg->setBgcolorFocus("3afb");
		$this->bg->setScriptEvents();
		$this->addComponent($this->bg);

		$this->label = new \ManiaLib\Gui\Elements\Label($sizeX, $sizeY);
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

}
?>

