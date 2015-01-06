<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Gui\Controls;

use Exception;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Utils\Formatting;
use ManiaLive\Gui\Container;
use ManiaLive\Gui\Control;
use ManiaLive\Utilities\Time;
use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Structures\Checkpoint;

class CheckpointElem extends Control
{

	private $bg;

	private $label;

	private $nick;

	private $time;

	function __construct($x, Checkpoint $cp = null)
	{
		$sizeX = 35;
		$sizeY = 5;

		$config = Config::getInstance();
		$this->bg = new Quad($sizeX, $sizeY);
		$this->bg->setPosX(-2);
		$this->bg->setId("Bg" . $x);
		$this->bg->setStyle("BgsPlayerCard");
		$this->bg->setSubStyle("BgRacePlayerName");
		$this->bg->setAlign('left', 'center');
		$this->bg->setColorize($config->style_widget_bgColorize); // tämä
		$this->bg->setHidden(1);
		$this->addComponent($this->bg);


		$this->label = new Label(10, 3);
		$this->label->setAlign('left', 'center');
		$this->label->setTextSize(1);
		$this->label->setId("CpTime" . $x);
		$this->label->setPosX(0);
		if ($cp != null && $cp->time != 0)
			$this->label->setText('$ff0' . ($cp->index + 1 ) . ' $fff' . Time::fromTM($cp->time));

		$this->addComponent($this->label);


		$this->nick = new Label(20, 4);
		$this->nick->setAlign('left', 'center');
		$this->nick->setTextSize(1);
		$this->nick->setPosX(11);
		$this->nick->setId("CpNick_" . $x);
		if ($cp != null) {
			$nickname = Formatting::stripCodes($cp->nickname, "wosnm");
			$this->nick->setText('$fff' . $nickname);
		}
		$this->addComponent($this->nick);

		$this->sizeX = $sizeX;
		$this->sizeY = $sizeY;
		$this->setSize($sizeX, $sizeY);
	}

	function onIsRemoved(Container $target)
	{
		parent::onIsRemoved($target);
		$this->destroy();
	}

	public function destroy()
	{
		try {
			$this->clearComponents();
		} catch (Exception $e) {

		}
		parent::destroy();
	}

}
?>

