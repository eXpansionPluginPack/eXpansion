<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLib\Gui\Elements\Quad;
use ManiaLive\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Config;

class ColorChooser extends Control implements \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer
{

	private $inputbox, $preview, $openButton, $frame, $bg, $ok, $cancel;

	/** @var int */
	private $buttonId;

	/** @var int */
	private static $counter = 0;

	/** @var \ManiaLivePlugins\eXpansion\Gui\Structures\Script */
	private static $script = null;

	/**
	 *
	 * @param string $inputboxName
	 * @param float $sizeX
	 * @param int $output
	 * @param bool $hasPrefix
	 */
	function __construct($inputboxName, $sizeX = 35, $output = 3, $hasPrefix = true)
	{
		$config = Config::getInstance();

		if (self::$script == null) {
			self::$script = new \ManiaLivePlugins\eXpansion\Gui\Scripts\ColorScript();
		}

		$this->buttonId = self::$counter++;

		if (self::$counter > 100000)
			self::$counter = 0;

		$this->setUsePrefix($hasPrefix);
		
		$this->openButton = new Quad(4, 4);
		$this->openButton->setAlign("left", "center");
		$this->openButton->setBgcolor("000");
		$this->openButton->setId("preview_" . $this->buttonId);
		$this->openButton->setAttribute("class", "colorchooser");
		$this->openButton->setScriptEvents();
		$this->addComponent($this->openButton);


		$this->inputbox = new Inputbox($inputboxName, $sizeX - 4, true);
		$this->inputbox->setPosition(4, 0);
		$this->inputbox->setId("output_" . $this->buttonId);
		$this->inputbox->setClass("color_input");
		$this->addComponent($this->inputbox);


		$this->frame = new \ManiaLive\Gui\Controls\Frame(6, 4);
		$this->frame->setPosZ(10);
		$this->addComponent($this->frame);

		$this->bg = new Quad(64, 42);
		$this->bg->setPosition(-2,2);
		$this->bg->setId("bg_" . $this->buttonId);
		$this->bg->setHidden(true);
		$this->bg->setAttribute("class", "colorSelection");
		$this->bg->setBgcolor("222");
		$this->frame->addComponent($this->bg);


		$this->preview = new Quad(32, 32);
		$this->preview->setAlign("left","top");
		$this->preview->setId("chooser_" . $this->buttonId);
		$this->preview->setHidden(true);
		$this->preview->setAttribute("class", "colorSelection");
		$this->preview->setImage($config->getImage("colorchooser", "1.png"), true);
		$this->preview->setScriptEvents();
		$this->frame->addComponent($this->preview);

		$this->color = new Quad(4, 32);
		$this->color->setAlign("left","top");
		$this->color->setId("hue_" . $this->buttonId);
		$this->color->setPosition(36, 0);
		$this->color->setHidden(true);
		$this->color->setAttribute("class", "colorSelection");
		$this->color->setImage($config->getImage("colorchooser", "2.png"), true);
		$this->color->setScriptEvents();
		$this->frame->addComponent($this->color);


		$select = new Quad(2, 2);
		$select->setAlign("center", "center");
		$select->setStyle("Bgs1InRace");
		$select->setSubStyle("BgColorContour");
		$select->setColorize("000");
		$select->setId("selectionBox_" . $this->buttonId);
		$select->setScriptEvents();
		$select->setHidden(true);
		$select->setAttribute("class", "colorSelection");
		$this->frame->addComponent($select);

		$hue = new Quad(8, 3);
		$hue->setPosition(36, 0);
		$hue->setAlign("left", "center");
		$hue->setStyle("Bgs1InRace");
		$hue->setSubStyle("BgColorContour");
		$hue->setScale(0.5);
		$hue->setColorize("fff");
		$hue->setId("selectionBoxHue_" . $this->buttonId);
		$hue->setScriptEvents();
		$hue->setHidden(true);
		$hue->setAttribute("class", "colorSelection");
		$this->frame->addComponent($hue);


		$layout = new \ManiaLib\Gui\Layouts\Column();
		$layout->setMargin(0, 1);

		$helper = new \ManiaLive\Gui\Controls\Frame(48, 0);
		$helper->setLayout($layout);

		$h = new \ManiaLib\Gui\Elements\Entry(8, 6);
		$h->setHidden(true);
		$h->setId("h_" . $this->buttonId);
		$h->setDefault("h");
		$h->setAttribute("class", "colorSelection");
		$h->setScriptEvents();
		$helper->addComponent($h);

		$s = new \ManiaLib\Gui\Elements\Entry(8, 6);
		$s->setId("s_" . $this->buttonId);
		$s->setAttribute("class", "colorSelection");
		$s->setHidden(true);
		$s->setDefault("s");
		$s->setScriptEvents();
		$helper->addComponent($s);

		$v = new \ManiaLib\Gui\Elements\Entry(8, 6);
		$v->setId("v_" . $this->buttonId);
		$v->setHidden(true);
		$v->setDefault("v");
		$v->setScriptEvents();
		$v->setAttribute("class", "colorSelection");
		$helper->addComponent($v);


		$ok = new Quad(8, 8);
		$ok->setStyle("Icons64x64_1");
		$ok->setSubStyle("Save");
		$ok->setId("ok_" . $this->buttonId);
		$ok->setScriptEvents();
		$ok->setHidden(true);
		$ok->setAttribute("class", "colorSelection");
		$helper->addComponent($ok);

		$cancel = new Quad(8, 8);
		$cancel->setStyle("Icons64x64_1");
		$cancel->setSubStyle("Refresh");
		$cancel->setId("cancel_" . $this->buttonId);
		$cancel->setScriptEvents();
		$cancel->setHidden(true);

		$cancel->setAttribute("class", "colorSelection");
		$helper->addComponent($cancel);

		$v = new \ManiaLib\Gui\Elements\Entry(8, 6);
		$v->setId("v_" . $this->buttonId);
		$v->setHidden(true);
		$v->setDefault("v");
		$v->setAttribute("class", "colorSelection");
		$helper->addComponent($v);


		$this->frame->addComponent($helper);
	}

	public function setColor($value)
	{
		$this->inputbox->setText($value);
		$this->openButton->setBgcolor(ltrim($value, '$'));
	}

	public function getScript()
	{
		return self::$script;
	}

	public function setUsePrefix($boolean) {
		self::$script->setParam("usePrefix", \ManiaLivePlugins\eXpansion\Helpers\Maniascript::getBoolean($boolean));


	}




}

?>