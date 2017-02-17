<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Widgets;

use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Layouts\Column;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround;
use Maniaplanet\DedicatedServer\Structures\Map;

class NextMapWidget extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{
    /** @var  WidgetBackGround */
    protected $bg;
    /** @var  Frame */
    protected $leftFrame;
    /** @var  Frame */
    protected $rightFrame;

    /** @var  DicoLabel */
    protected $labelName;
    /** @var  DicoLabel */
    protected $labelAuthor;
    /** @var  DicoLabel */
    protected $environment;

    /** @var  DicoLabel */
    protected $time;
    /** @var  Quad */
    protected $country;

    /** @var Map */
    protected $map;

    protected function eXpOnBeginConstruct()
    {
        $this->setName("Next Map");
        $this->bg = new WidgetBackGround(60, 15);
        $this->addComponent($this->bg);

        $column = new Column();

        $this->leftFrame = new Frame(4, -1);
        $this->leftFrame->setAlign("left", "top");
        $this->leftFrame->setLayout(clone $column);
        $this->addComponent($this->leftFrame);

        $this->rightFrame = new Frame(20, -3);
        $this->rightFrame->setLayout(clone $column);
        $this->addComponent($this->rightFrame);

        $biglabel = new DicoLabel(40, 4);
        $biglabel->setStyle("TextRankingsBig");
        $biglabel->setTextSize(2);
        $biglabel->setAlign("left", "center");

        $label = new DicoLabel(40, 4);
        $label->setStyle("TextRaceMessage");
        $label->setAlign("left", "center");
        $label->setTextSize(2);
        $label->setTextEmboss();

        $nowPlaying = clone $biglabel;
        $nowPlaying->setText(eXpGetMessage("Next Map"));
        $nowPlaying->setPosition(30, 3);
        $nowPlaying->setAlign("center", "center");
        $this->addComponent($nowPlaying);

        $this->country = new Quad(14, 9);
        $this->country->setId("authorZone");
        $this->country->setImage("http://reaby.kapsi.fi/ml/flags/Other%20Countries.dds", true);
        $this->country->setAlign("left", "top");
        $this->leftFrame->addComponent($this->country);

        $this->labelAuthor = clone $biglabel;
        $this->labelAuthor->setAlign("left", "top");
        $this->leftFrame->addComponent($this->labelAuthor);

        $this->environment = clone $label;
        $this->environment->setText("unknown");
        $this->rightFrame->addComponent($this->environment);

        $this->labelName = clone $biglabel;
        $this->rightFrame->addComponent($this->labelName);

        $this->time = clone $label;
        $this->rightFrame->addComponent($this->time);
    }

    protected function eXpOnEndConstruct()
    {
        $this->setSize(60, 15);
    }

    public function setAction($action)
    {
        $this->bg->setAction($action);
    }

    public function setMap(Map $map)
    {
        $this->map = $map;
        $this->labelName->setText($this->map->name);
        $this->labelAuthor->setText($this->map->author);
        $this->environment->setText($map->environnement);

        if ($map->author == "Nadeo") {
            $this->country->setImage("http://reaby.kapsi.fi/ml/flags/France.dds", true);
        }
    }

    public function destroy()
    {
        $this->destroyComponents();
        parent::destroy();
    }
}
