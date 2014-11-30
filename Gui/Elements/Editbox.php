<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLib\Gui\Elements\Entry;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLive\Gui\Container;
use ManiaLive\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Config;

class Editbox extends Control
{

	private $label;

	private $button;

	private $name;

	private $bgleft, $bgcenter, $bgright;

	private $bg;

	function __construct($name, $sizeX = 100, $sizeY = 30, $editable = true)
	{

		$config = Config::getInstance();
		$this->name = $name;

		$this->createButton($editable);

		$this->bg = new WidgetBackGround(100, 30);
	//	$this->addComponent($this->bg);
		
		$this->label = new Label($sizeX, 4);
		$this->label->setAlign('left', 'top');
		$this->label->setTextSize(1);
		$this->label->setStyle("TextCardScores2");
		$this->label->setTextEmboss();
		$this->addComponent($this->label);

		/* 	$this->bgleft = new Quad(3, 6);
		  $this->bgleft->setAlign("right", "top");
		  $this->bgleft->setImage($config->getImage("inputbox", "left.png"), true);
		  $this->addComponent($this->bgleft);

		  $this->bgcenter = new Quad(3, 6);
		  $this->bgcenter->setAlign("left", "top");
		  $this->bgcenter->setImage($config->getImage("inputbox", "center.png"), true);
		  $this->addComponent($this->bgcenter);

		  $this->bgright = new Quad(3, 6);
		  $this->bgright->setAlign("left", "top");
		  $this->bgright->setImage($config->getImage("inputbox", "right.png"), true);
		  $this->addComponent($this->bgright); */

		$this->sizeX = $sizeX;
		$this->sizeY = $sizeY;

		$this->setSize($sizeX, $sizeY);
	}

	protected function onResize($oldX, $oldY)
	{
		$this->button->setSize($this->getSizeX(), $this->getSizeY() - 5);
		$this->button->setPosition(0, 0);

		/* 	$this->bgleft->setSize(3, $this->getSizeY());
		  $this->bgleft->setPosX(3);

		  $this->bgcenter->setSize($this->getSizeX() - 6, $this->getSizeY());
		  $this->bgcenter->setPosX(3);

		  $this->bgright->setSize(3, $this->getSizeY());
		  $this->bgright->setPosX($this->getSizeX() - 3); */

		$this->label->setSize($this->getSizeX(), 3);
		$this->label->setPosition(1, 5);
		$this->bg->setSize($this->sizeX, $this->sizeY);

		parent::onResize($oldX, $oldY);
	}

	protected function createButton($editable)
	{
		$text = "";
		if ($this->button != null) {
			$this->removeComponent($this->button);
			$text = $this->getText();
		}

		if ($editable) {
			$this->button = new TextEdit($this->name, $this->sizeX, $this->sizeY);
			$this->button->setAttribute("class", "isTabIndex isEditable");
			$this->button->setName($this->name);
			$this->button->setId($this->name);
			$this->button->setText($text);

			$this->button->setScriptEvents(true);
		}
		else {
			$this->button = new Label($this->sizeX, 5);
			$this->button->setText($text);
			$this->button->setTextColor('fff');
			$this->button->setTextSize(1.5);
		}

		$this->button->setAlign('left', 'top');
		$this->button->setPosX(2);
		$this->addComponent($this->button);
	}

	public function setEditable($state)
	{
		if ($state && $this->button instanceof Label) {
			$this->createButton($state);
		}
		elseif (!$state && $this->button instanceof Entry) {
			$this->createButton($state);
		}
	}

	function getLabel()
	{
		return $this->label->getText();
	}

	function setLabel($text)
	{
		$this->label->setText($text);
	}

	function getText()
	{
		if ($this->button instanceof Entry)
			return $this->button->getDefault();
		else
			return $this->button->getText();
	}

	function setText($text)
	{
		if ($this->button instanceof Entry)
			$this->button->setDefault($text);
		else
			$this->button->setText($text);
	}

	function getName()
	{
		return $this->button->getName();
	}

	function setName($text)
	{
		$this->button->setName($text);
	}

	function setId($id)
	{
		$this->button->setId($id);
		$this->button->setScriptEvents();
	}

	function setClass($class)
	{
		$this->button->setAttribute("class", "isTabIndex isEditable " . $class);
	}

	function onIsRemoved(Container $target)
	{
		parent::onIsRemoved($target);
		parent::destroy();
	}

}

?>