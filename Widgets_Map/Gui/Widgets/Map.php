<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Map\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Maps\Structures\DbMap;

class Map extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{
    protected $clockBg;
    protected $frame;
    protected $players;
    protected $specs;
    protected $map;
    protected $author;
    protected $style;

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
        $this->map->setTextSize(3);
        $this->map->setPosition(58, 1.5);
        $this->map->setTextColor('fff');
        $this->map->setTextPrefix('$s');
        $this->addComponent($this->map);

        $this->author = new \ManiaLib\Gui\Elements\Label(60, 6);
        $this->author->setId('mapAuthor');
        $this->author->setAlign("right", "center2");
        $this->author->setStyle(\ManiaLib\Gui\Elements\Format::TextRaceMessageBig);
        $this->author->setTextSize(2);
        $this->author->setPosition(51, -6);
        $this->author->setTextColor('fff');
        $this->author->setTextPrefix('$s');
        $this->addComponent($this->author);

        $icon = new \ManiaLib\Gui\Elements\Quad(6, 6);
        $icon->setId("country");
        $icon->setAlign("left", "center");
        $icon->setPosition(52, -6);
        $this->addComponent($icon);

        $this->style = new \ManiaLib\Gui\Elements\Label(60, 6);
        $this->style->setAlign("right", "center");
        $this->style->setId('style');
        $this->style->setTextColor('fff');
        $this->style->setTextSize(2);
        $this->style->setStyle('TextRaceMessageBig');
        $this->style->setPosition(58, -10);
        $this->style->setTextPrefix('$s');
        $this->addComponent($this->style);

        $this->author = new \ManiaLib\Gui\Elements\Label(60, 6);
        $this->author->setId('authorTime');
        $this->author->setAlign("right", "top");
        $this->author->setStyle(\ManiaLib\Gui\Elements\Format::TextRaceChrono);
        $this->author->setTextSize(3);
        $this->author->setPosition(58, -13);
        $this->author->setTextColor('fff');
        $this->author->setTextPrefix('$s');
        $this->addComponent($this->author);

        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Widgets_Map\Gui\Scripts_Map");
        $this->registerScript($script);
    }

    public function setMap($map)
    {
        if ($map instanceof DbMap) {
            $this->style->setText($map->difficultyName . " / " . $map->styleName);
        } else {
            $this->style->setText("");
        }
    }

    public function showMapInfo($login)
    {
        $window = \ManiaLivePlugins\eXpansion\Maps\Gui\Windows\MapInfo::create($login);
        $window->setMap(null);
        $window->setSize(160, 90);
        $window->show($login);
    }
}
