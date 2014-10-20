<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLib\Gui\Elements\Entry;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLive\Gui\Container;
use ManiaLive\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Config;

class InputboxMasked extends Control
{

	private $label;

	private $button;

	/** @var Button */
	private $nonHidden;

	private $name;

	private $bgleft, $bgcenter, $bgright;

	function __construct($name, $sizeX = 35, $editable = true)
	{
		$config = Config::getInstance();
		$this->name = $name;

		$this->createButton($editable);

		$this->label = new Label(30, 3);
		$this->label->setAlign('left', 'center');
		$this->label->setTextSize(1);
		$this->label->setStyle("TextCardMediumWhite");
		$this->label->setTextEmboss();
		$this->addComponent($this->label);

		$this->bgleft = new Quad(3, 6);
		$this->bgleft->setAlign("right", "center");
		$this->bgleft->setImage($config->getImage("inputbox", "left.png"), true);
		$this->addComponent($this->bgleft);

		$this->bgcenter = new Quad(3, 6);
		$this->bgcenter->setAlign("left", "center");
		$this->bgcenter->setImage($config->getImage("inputbox", "center.png"), true);
		$this->addComponent($this->bgcenter);

		$this->bgright = new Quad(3, 6);
		$this->bgright->setAlign("left", "center");
		$this->bgright->setImage($config->getImage("inputbox", "right.png"), true);
		$this->addComponent($this->bgright);

		$this->setSize($sizeX, 12);
	}

	protected function onResize($oldX, $oldY)
	{
		parent::onResize($oldX, $oldY);
		$this->button->setSize($this->getSizeX() - 8, 4);
		$this->button->setPosX(2);

		$this->bgleft->setSize(3, 6);
		$this->bgleft->setPosX(3);

		$this->bgcenter->setSize($this->getSizeX() - 6, 6);
		$this->bgcenter->setPosX(3);

		$this->bgright->setSize(3, 6);
		$this->bgright->setPosX($this->getSizeX() - 3);

		$this->label->setSize($this->getSizeX(), 3);
		$this->label->setPosition(1, 5);
	}

	protected function createButton($editable)
	{
		$text = "";
		if ($this->button != null) {
			$this->removeComponent($this->button);
			$text = $this->getText();
		}

		if ($editable) {
			$this->button = new Entry($this->sizeX, 4.5);
			$this->button->setAttribute("class", "isTabIndex isEditable");
			$this->button->setAttribute("textformat", "password");
			$this->button->setName($this->name);
			$this->button->setId($this->name);
			$this->button->setDefault($text);
			$this->button->setScriptEvents(true);
			$this->button->setStyle("TextValueMedium");
			$this->button->setTextSize(1);
			$this->button->setFocusAreaColor1("0000");
			$this->button->setFocusAreaColor2("0000");
		}
		else {
			$this->button = new Label($this->sizeX, 5);
			$this->button->setText($text);
			$this->button->setTextSize(1.5);
		}

		$this->button->setAlign('left', 'center');
		$this->button->setTextColor('fff');

		$this->button->setPosX(2);
		$this->button->setSize($this->getSizeX() - 3, 4);
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

	function setShowClearText()
	{
		if ($this->nonHidden == null) {
			$this->nonHidden = New Button(3, 3);
			$this->nonHidden->setIcon("Icons64x64_1", "ClipPause");
			$this->nonHidden->setPosition(-4, 0);
			$this->nonHidden->setId($this->name . "_1");
			$this->nonHidden->setDescription($this->getText());
			$this->addComponent($this->nonHidden);
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