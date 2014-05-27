<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Clock\Gui\Widgets;

class Clock extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget {

    protected $clockBg;
    private $frame, $players, $specs, $server;

    protected function exp_onBeginConstruct() {	
	$clockBg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(58, 11.5);	
	$clockBg->setAction(\ManiaLivePlugins\eXpansion\Core\Core::$action_serverInfo);
	$this->addComponent($clockBg);

	$this->server = new \ManiaLib\Gui\Elements\Label(58, 6);
	$this->server->setId('serverName');
	$this->server->setAlign("left", "top");
	$this->server->setStyle(\ManiaLib\Gui\Elements\Format::TextRaceMessageBig);
	$this->server->setTextSize(2);
	$this->server->setPosition(2, -1);
	$this->server->setTextColor('fff');
	$this->server->setTextPrefix('$s');		
	$this->addComponent($this->server);

	/* $this->nameBg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(57, 5);
	  $this->addComponent($this->nameBg);
	  $this->nameBg->setPosition(0, -3); */

	$line = new \ManiaLive\Gui\Controls\Frame(2, -8.5);
	$layout = new \ManiaLib\Gui\Layouts\Line();
	$layout->setMargin(1);
	$line->setLayout($layout);
	$icon = new \ManiaLib\Gui\Elements\Quad(5, 5);
	$icon->setAlign("left", "center2");
	$icon->setStyle("Icons128x128_1");
	$icon->setSubStyle(\ManiaLib\Gui\Elements\Icons128x128_1::Buddies);
	$line->addComponent($icon);

	$this->players = new \ManiaLib\Gui\Elements\Label(9, 6);
	$this->players->setAlign("left", "center");
	$this->players->setId('playersCount');
	$this->players->setTextColor('fff');
	$this->players->setScale(0.8);
	$this->players->setStyle('TextCardScores2');
	$this->players->setId("nbPlayer");
	//$this->players->setTextPrefix('$s');
	$line->addComponent($this->players);



	$icon = new \ManiaLib\Gui\Elements\Quad(5, 5);
	$icon->setStyle("Icons64x64_1");
	$icon->setSubStyle(\ManiaLib\Gui\Elements\Icons64x64_1::TV);
	$icon->setAlign("left", "center2");
	$line->addComponent($icon);

	$this->specs = new \ManiaLib\Gui\Elements\Label(9, 6);
	$this->specs->setAlign("left", "center");
	$this->specs->setId('specsCount');
	$this->specs->setTextColor('fff');
	$this->specs->setScale(0.8);
	$this->specs->setStyle('TextCardScores2');
	$this->specs->setId("nbSpec");
	//$this->specs->setTextPrefix('$s');
	$line->addComponent($this->specs);


	$icon = new \ManiaLib\Gui\Elements\Quad(5, 5);
	$icon->setStyle("Icons128x32_1");
	$icon->setAlign("left", "center");
	$icon->setSubStyle(\ManiaLib\Gui\Elements\Icons128x32_1::RT_TimeAttack);
	$line->addComponent($icon);

	$clock = new \ManiaLib\Gui\Elements\Label(24, 6);
	$clock->setAlign("left", "center");
	$clock->setId('clock');
	$clock->setTextColor('fff');
	$clock->setScale(0.8);
	$clock->setStyle('TextCardScores2');
	//$clock->setTextPrefix('$s');
	$line->addComponent($clock);

	$this->frame = $line;
	$this->addComponent($this->frame);
	$script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Widgets_Clock\Gui\Scripts_Clock");
	$this->registerScript($script);
	$script->setParam("serverLogin", \ManiaLive\Data\Storage::getInstance()->serverLogin);

	$this->setName("Clock & Server Name Widget");
    }

    public function setPlayersCount($players, $specs) {
	$this->players->setText($players);
	$this->specs->setText($specs);
    }

    public function setServerName($name) {
	$this->server->setText($name);
    }
    
    function destroy() {
	$this->clearComponents();
	parent::destroy();
    }

}

?>
