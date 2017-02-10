<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Widgets;

use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Layouts\Column;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use Maniaplanet\DedicatedServer\Structures\Map;

class CurrentMapWidget extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{
    /** @var  WidgetBackGround */
    protected $bg;

    /** @var  DicoLabel */
    protected $authorTime;
    /** @var  Frame */
    protected $leftFrame;
    /** @var  Frame */
    protected $centerFrame;
    /** @var  Frame */
    protected $rightFrame;
    /** @var  DicoLabel */
    protected $environment;
    /** @var  Quad */
    protected $country;

    protected function eXpOnBeginConstruct()
    {
        $this->bg = new WidgetBackGround(90, 15);
        $this->addComponent($this->bg);

        $column = new Column();

        $this->leftFrame = new Frame(4, -1);
        $this->leftFrame->setAlign("left", "top");
        $this->leftFrame->setLayout(clone $column);
        $this->addComponent($this->leftFrame);

        $this->centerFrame = new Frame(45, -2);
        $column2 = clone $column;
        $column2->setMargin(0, 1);
        $this->centerFrame->setAlign("center", "top");
        $this->centerFrame->setLayout($column2);
        $this->addComponent($this->centerFrame);

        $this->rightFrame = new Frame(88, -2);
        $this->rightFrame->setLayout(clone $column);
        $this->rightFrame->setAlign("right", "top");
        $this->addComponent($this->rightFrame);

        $biglabel = new DicoLabel(45, 4);
        $biglabel->setStyle("TextRankingsBig");
        $biglabel->setTextSize(2);
        $biglabel->setAlign("center", "center");

        $label = new DicoLabel(45, 4);
        $label->setStyle("TextRaceMessage");
        $label->setAlign("center", "center");
        $label->setTextSize(2);
        $label->setTextEmboss();

        $nowPlaying = clone $biglabel;
        $nowPlaying->setText(eXpGetMessage("Now Playing"));
        $nowPlaying->setPosition(45, 3);
        $this->addComponent($nowPlaying);

        $this->country = new Quad(14, 9);
        $this->country->setId("authorZone");
        $this->country->setImage("", true);
        $this->country->setImage("http://reaby.kapsi.fi/ml/flags/Other%20Countries.dds", true);
        $this->country->setAlign("left", "top");
        $this->leftFrame->addComponent($this->country);

        $author = clone $biglabel;
        $author->setSizeX(40);
        $author->setId("authorName");
        $author->setAlign("left", "top");
        $this->leftFrame->addComponent($author);


        $this->environment = clone $label;
        $this->environment->setId("environment");
        $this->environment->setText("unknown");
        $this->centerFrame->addComponent($this->environment);

        $mapname = clone $biglabel;
        $mapname->setId("mapName");
        $this->centerFrame->addComponent($mapname);

        $blank = clone $label;
        $this->rightFrame->addComponent($blank);
        $blank = clone $label;
        $this->rightFrame->addComponent($blank);
        $time = clone $label;
        $time->setId("authorTime");
        $time->setAlign("right");
        $this->rightFrame->addComponent($time);

        $script = new Script("Maps\\Gui\\Scripts_CurrentMap");
        $this->registerScript($script);

        $this->setName("Current Map Widget");
    }

    protected function eXpOnEndConstruct()
    {
        $this->setSize(90, 15);
    }

    public function setMap(Map $map)
    {
        $playerModel = "";
        if (isset($map->playerModel)) {
            $playerModel = '/' . $map->playerModel;
        }
        $this->environment->setText($map->environnement . $playerModel);
        if ($map->author == "Nadeo") {
            $this->country->setImage("file://Media/Manialinks/flags/France.dds", true);
        }
    }

    public function setAction($action)
    {
        $this->bg->setAction($action);
    }
}
