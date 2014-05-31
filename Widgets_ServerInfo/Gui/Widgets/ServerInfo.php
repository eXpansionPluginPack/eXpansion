<?php

namespace ManiaLivePlugins\eXpansion\Widgets_ServerInfo\Gui\Widgets;

class ServerInfo extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget {

    protected $clockBg;
    private $frame, $ladderMin, $ladderMax, $serverName, $maxPlayers;

    protected function exp_onBeginConstruct() {
	$this->setAlign("right", "top");

	$bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(60, 6);
	$bg->setAction(\ManiaLivePlugins\eXpansion\Core\Core::$action_serverInfo);
	$this->addComponent($bg);

	$this->serverName = new \ManiaLib\Gui\Elements\Label(60, 6);
	$this->serverName->setId('serverName');
	$this->serverName->setAlign("left", "top");
	$this->serverName->setStyle(\ManiaLib\Gui\Elements\Format::TextRaceMessageBig);
	$this->serverName->setTextSize(2);
	$this->serverName->setPosition(2, -6.5);
	$this->serverName->setTextColor('fff');
	$this->serverName->setTextPrefix('$s');
	$this->addComponent($this->serverName);

//	$this->author = new \ManiaLib\Gui\Elements\Label(60, 6);
//	$this->author->setId('mapAuthor');
//	$this->author->setAlign("right", "top");
//	$this->author->setStyle(\ManiaLib\Gui\Elements\Format::TextRaceMessageBig);
//	$this->author->setTextSize(2);
//	$this->author->setPosition(58, -11.5);
//	$this->author->setTextColor('fff');
//	$this->author->setTextPrefix('$s');		
//	$this->addComponent($this->author);


	$line = new \ManiaLive\Gui\Controls\Frame(8, -3);
	$layout = new \ManiaLib\Gui\Layouts\Line();
	$layout->setMargin(1);
	$line->setLayout($layout);

	$icon = new \ManiaLib\Gui\Elements\Quad(5, 5);
	$icon->setAlign("left", "center2");
	$icon->setStyle("Icons128x128_1");
	$icon->setSubStyle(\ManiaLib\Gui\Elements\Icons128x128_1::LadderPoints);
	$line->addComponent($icon);

	$this->ladderMin = new \ManiaLib\Gui\Elements\Label(24, 6);
	$this->ladderMin->setAlign("left", "center");
	$this->ladderMin->setTextColor('fff');
	$this->ladderMin->setScale(0.8);
	$this->ladderMin->setStyle('TextCardScores2');
	//$this->players->setTextPrefix('$s');
	$line->addComponent($this->ladderMin);

	$icon = new \ManiaLib\Gui\Elements\Quad(5, 5);
	$icon->setStyle("Icons128x32_1");
	$icon->setAlign("left", "center");
	$icon->setStyle("Icons128x128_1");
	$icon->setSubStyle(\ManiaLib\Gui\Elements\Icons128x128_1::Hotseat);
	$line->addComponent($icon);

	$this->maxPlayers = new \ManiaLib\Gui\Elements\Label(24, 6);
	$this->maxPlayers->setAlign("left", "center");
	$this->maxPlayers->setId('clock');
	$this->maxPlayers->setTextColor('fff');
	$this->maxPlayers->setScale(0.8);
	$this->maxPlayers->setStyle('TextCardScores2');
	//$this->maxPlayers->setTextPrefix('$s');
	$line->addComponent($this->maxPlayers);

	$this->frame = $line;
	$this->addComponent($this->frame);
	/*$script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Widgets_\Gui\Scripts_Clock");
	$this->registerScript($script);
	$script->setParam("serverLogin", \ManiaLive\Data\Storage::getInstance()->serverLogin); */

	$this->setName("Server info Widget");
    }

    public function setPlayersCount($maxPlayers) {
	$this->maxPlayers->setText($maxPlayers);	
    }

    public function setServerName($name) {
	$this->serverName->setText($name);
    }
    public function setLadderLimits($min, $max) {
	$this->ladderMin->setText(($min / 1000) . " - " . ($max / 1000) . "k");	
    }

    function destroy() {
	$this->clearComponents();
	parent::destroy();
    }

}

?>
