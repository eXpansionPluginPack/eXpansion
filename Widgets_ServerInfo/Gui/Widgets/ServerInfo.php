<?php

namespace ManiaLivePlugins\eXpansion\Widgets_ServerInfo\Gui\Widgets;

class ServerInfo extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{
    protected $clockBg;
    private $frame, $ladderMin, $ladderMax, $serverName, $maxPlayers, $script;

    protected function exp_onBeginConstruct()
    {

        $bg = new \ManiaLib\Gui\Elements\Quad(60, 15);
        $bg->setAlign("left", "top");
        $bg->setStyle("Bgs1InRace");
        $bg->setSubStyle("Empty");
        $bg->setBgcolor("0000");
        $bg->setAction(\ManiaLivePlugins\eXpansion\Core\Core::$action_serverInfo);
        $this->addComponent($bg);

        $this->serverName = new \ManiaLib\Gui\Elements\Label(60, 6);
        $this->serverName->setId('serverName');
        $this->serverName->setAlign("left", "top");
        $this->serverName->setStyle(\ManiaLib\Gui\Elements\Format::TextRaceMessageBig);
        $this->serverName->setTextSize(2);
        $this->serverName->setPosition(2, 0);
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


        $line   = new \ManiaLive\Gui\Controls\Frame(1, -6.5);
        $layout = new \ManiaLib\Gui\Layouts\Line();
        $layout->setMargin(1);
        $line->setLayout($layout);

        $icon = new \ManiaLib\Gui\Elements\Quad(5, 5);
        $icon->setAlign("left", "center2");
        $icon->setStyle("Icons128x128_1");
        $icon->setSubStyle(\ManiaLib\Gui\Elements\Icons128x128_1::Buddies);
        $line->addComponent($icon);

        $this->players = new \ManiaLib\Gui\Elements\Label(12, 6);
        $this->players->setAlign("left", "center");
        $this->players->setId('playersCount');
        $this->players->setTextColor('fff');
        $this->players->setTextSize(2);
        $this->players->setStyle('TextCardScores2');
        $this->players->setId("nbPlayer");
        //$this->players->setTextPrefix('$s');
        $line->addComponent($this->players);

        $icon = new \ManiaLib\Gui\Elements\Quad(5, 5);
        $icon->setStyle("Icons64x64_1");
        $icon->setPosY(-0.5);
        $icon->setSubStyle(\ManiaLib\Gui\Elements\Icons64x64_1::TV);
        $icon->setAlign("left", "center");
        $line->addComponent($icon);

        $this->specs = new \ManiaLib\Gui\Elements\Label(12, 6);
        $this->specs->setAlign("left", "center");
        $this->specs->setId('specsCount');
        $this->specs->setTextColor('fff');
        $this->specs->setTextSize(2);
        $this->specs->setStyle('TextCardScores2');
        $this->specs->setId("nbSpec");
        //$this->specs->setTextPrefix('$s');
        $line->addComponent($this->specs);

        $icon = new \ManiaLib\Gui\Elements\Quad(5, 5);
        $icon->setAlign("left", "center2");
        $icon->setStyle("Icons128x128_1");
        $icon->setSubStyle(\ManiaLib\Gui\Elements\Icons128x128_1::LadderPoints);
        $line->addComponent($icon);

        $this->ladderMin = new \ManiaLib\Gui\Elements\Label(16, 6);
        $this->ladderMin->setAlign("left", "center");
        $this->ladderMin->setTextColor('fff');
        $this->ladderMin->setTextSize(2);
        $this->ladderMin->setStyle('TextCardScores2');
        //$this->players->setTextPrefix('$s');
        $line->addComponent($this->ladderMin);

        $this->frame = $line;
        $this->addComponent($this->frame);

        $this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Widgets_ServerInfo\Gui\Scripts_Infos");
        $this->script->setParam("maxPlayers", \ManiaLive\Data\Storage::getInstance()->server->currentMaxPlayers);
        $this->script->setParam("maxSpec", \ManiaLive\Data\Storage::getInstance()->server->currentMaxSpectators);
        $this->registerScript($this->script);
        $this->setName("Server info Widget");
    }

    public function setLadderLimits($min, $max)
    {
        $this->ladderMin->setText(($min / 1000)." - ".($max / 1000)."k");
        $this->script->setParam("maxPlayers", \ManiaLive\Data\Storage::getInstance()->server->currentMaxPlayers);
        $this->script->setParam("maxSpec", \ManiaLive\Data\Storage::getInstance()->server->currentMaxSpectators);
    }

    function destroy()
    {
        $this->destroyComponents();
        unset($this->script);
        parent::destroy();
    }
}
?>
