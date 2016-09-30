<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Widgets;

class CurrentMapWidget extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{

    protected $bg;
    protected $authorTime;
    protected $logo;
    protected $leftFrame;
    protected $centerFrame;
    protected $rightFrame;
    protected $environment;
    protected $country;

    protected function eXpOnBeginConstruct()
    {
        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(90, 15);
        $this->addComponent($this->bg);

        $column = new \ManiaLib\Gui\Layouts\Column();

        $this->leftFrame = new \ManiaLive\Gui\Controls\Frame(4, -1);
        $this->leftFrame->setAlign("left", "top");
        $this->leftFrame->setLayout(clone $column);
        $this->addComponent($this->leftFrame);

        $this->centerFrame = new \ManiaLive\Gui\Controls\Frame(45, -2);
        $column2 = clone $column;
        $column2->setMargin(0, 1);
        $this->centerFrame->setAlign("center", "top");
        $this->centerFrame->setLayout($column2);
        $this->addComponent($this->centerFrame);

        $this->rightFrame = new \ManiaLive\Gui\Controls\Frame(88, -2);
        $this->rightFrame->setLayout(clone $column);
        $this->rightFrame->setAlign("right", "top");
        $this->addComponent($this->rightFrame);

        $biglabel = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel(45, 4);
        $biglabel->setStyle("TextRankingsBig");
        $biglabel->setTextSize(2);
        $biglabel->setAlign("center", "center");

        $label = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel(45, 4);
        $label->setStyle("TextRaceMessage");
        $label->setAlign("center", "center");
        $label->setTextSize(2);
        $label->setTextEmboss();

        $nowPlaying = clone $biglabel;
        $nowPlaying->setText(eXpGetMessage("Now Playing"));
        $nowPlaying->setPosition(45, 3);
        $this->addComponent($nowPlaying);

        $this->country = new \ManiaLib\Gui\Elements\Quad(14, 9);
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

        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Maps\Gui\Scripts_CurrentMap");
        $this->registerScript($script);

        $this->setName("Current Map Widget");
    }

    protected function eXpOnEndConstruct()
    {
        $this->setSize(90, 15);
    }

    public function setMap(\Maniaplanet\DedicatedServer\Structures\Map $map)
    {
        $playerModel = "";
        if (isset($map->playerModel)) {
            $playerModel = '/' . $map->playerModel;
        }
        $this->environment->setText($map->environnement . $playerModel);
        if ($map->author == "Nadeo") {
            $this->country->setImage("http://reaby.kapsi.fi/ml/flags/France.dds", true);
        }
    }

    public function setAction($action)
    {
        $this->bg->setAction($action);
    }
}
