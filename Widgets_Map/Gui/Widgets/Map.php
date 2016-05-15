<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Map\Gui\Widgets;

class Map extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{
    protected $clockBg;
    protected $frame, $players, $specs, $map, $author;

    protected function eXpOnBeginConstruct()
    {
        $this->setName("Mapinfo Widget");
        $clockBg = new \ManiaLib\Gui\Elements\Quad(45, 15);
        $clockBg->setAlign("right", "top");
        $clockBg->setPosition(58, 0);
        $clockBg->setStyle("Bgs1InRace");
        $clockBg->setSubStyle("Empty");
        $clockBg->setBgcolor("0000");
        $clockBg->setAction($this->createAction(array($this, "showMapInfo")));
        $this->addComponent($clockBg);

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

        $this->author = new \ManiaLib\Gui\Elements\Label(60, 6);
        $this->author->setId('authorTime');
        $this->author->setAlign("right", "top");
        $this->author->setStyle(\ManiaLib\Gui\Elements\Format::TextRaceMessageBig);
        $this->author->setTextSize(2);
        $this->author->setPosition(58, -9);
        $this->author->setTextColor('fff');
        $this->author->setTextPrefix('$s');
        $this->addComponent($this->author);

        $line = new \ManiaLive\Gui\Controls\Frame(36, -14.5);
        $line->setAlign("left", "top");
        $layout = new \ManiaLib\Gui\Layouts\Line();
        $layout->setMargin(1);
        $line->setLayout($layout);
        $icon = new \ManiaLib\Gui\Elements\Quad(5, 5);
        $icon->setStyle("Icons128x32_1");
        $icon->setAlign("left", "center");
        $icon->setSubStyle(\ManiaLib\Gui\Elements\Icons128x32_1::RT_TimeAttack);
        $line->addComponent($icon);

        $clock = new \ManiaLib\Gui\Elements\Label(20, 8);
        $clock->setAlign("left", "center");
        $clock->setId('clock');
        $clock->setTextColor('fff');
        $clock->setTextSize(2);
        $clock->setStyle('TextRaceMessageBig');
        $clock->setTextPrefix('$s');
        $line->addComponent($clock);


        $this->frame = $line;
        // 	$this->addComponent($this->frame);
        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Widgets_Map\Gui\Scripts_Map");
        $this->registerScript($script);
    }

    public function setServerName($name)
    {
        // $this->server->setText($name);
    }

    public function showMapInfo($login)
    {
        $window = \ManiaLivePlugins\eXpansion\Maps\Gui\Windows\MapInfo::create($login);
        $window->setMap(null);
        $window->setSize(160, 90);
        $window->show($login);
    }

    public function destroy()
    {
        $this->destroyComponents();
        parent::destroy();
    }
}
