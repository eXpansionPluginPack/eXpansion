<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Clock\Gui\Widgets;

class Clock extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{

	protected $clockBg;
	private $frame, $players, $specs, $map, $author;

	protected function exp_onBeginConstruct()
	{
		$clockBg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(60, 6);
		$clockBg->setAction(\ManiaLivePlugins\eXpansion\Core\Core::$action_serverInfo);
		//$this->addComponent($clockBg);

		$this->map = new \ManiaLib\Gui\Elements\Label(60, 6);
		$this->map->setId('mapName');
		$this->map->setAlign("right", "top");
		$this->map->setStyle(\ManiaLib\Gui\Elements\Format::TextRaceMessageBig);
		$this->map->setTextSize(2);
		$this->map->setPosition(58, 0);
		$this->map->setTextColor('fff');
		$this->map->setTextPrefix('$s');
		$this->addComponent($this->map);

		$this->author = new \ManiaLib\Gui\Elements\Label(60, 6);
		$this->author->setId('mapAuthor');
		$this->author->setAlign("right", "top");
		$this->author->setStyle(\ManiaLib\Gui\Elements\Format::TextRaceMessageBig);
		$this->author->setTextSize(2);
		$this->author->setPosition(58, -4.5);
		$this->author->setTextColor('fff');
		$this->author->setTextPrefix('$s');
		$this->addComponent($this->author);


		$line = new \ManiaLive\Gui\Controls\Frame(40, -10.5);
		$line->setAlign("right", "top");
		$layout = new \ManiaLib\Gui\Layouts\Line();
		$layout->setMargin(1);
		$line->setLayout($layout);
		$icon = new \ManiaLib\Gui\Elements\Quad(5, 5);

		$clock = new \ManiaLib\Gui\Elements\Label(14, 6);
		$clock->setAlign("left", "center");
		$clock->setId('clock');
		$clock->setTextColor('fff');
		$clock->setTextSize(2);
		$clock->setStyle('TextCardScores2');
		//$clock->setTextPrefix('$s');
		$line->addComponent($clock);

		$icon->setStyle("Icons128x32_1");
		$icon->setAlign("left", "center");
		$icon->setSubStyle(\ManiaLib\Gui\Elements\Icons128x32_1::RT_TimeAttack);
		$line->addComponent($icon);


		$this->frame = $line;
		$this->addComponent($this->frame);
		$script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Widgets_Clock\Gui\Scripts_Clock");
		$this->registerScript($script);

		$this->setName("Clock And Mapinfo Widget");
	}

	public function setServerName($name)
	{
		// $this->server->setText($name);
	}

	function destroy()
	{
		$this->clearComponents();
		parent::destroy();
	}

}

?>
