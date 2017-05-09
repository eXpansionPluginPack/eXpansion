<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets;

use ManiaLivePlugins\eXpansion\MapRatings\Gui\Controls\RateButton2;

class EndMapRatings extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{

    protected $label;
    protected $xml;
    protected $frame;
    protected $bg;
    protected $titlebg;
    protected $labelMap;

    protected $b0;
    protected $b1;
    protected $b2;
    protected $b3;
    protected $b4;
    protected $b5;

    public static $parentPlugin;

    private $script;

    protected function onConstruct()
    {
        parent::onConstruct();

        $this->setName("Map ratings (endmap)");
        $sizeX = 90;
        $sizeY = 25;

        $bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround($sizeX, $sizeY);
        $bg->setAlign("left", "top");
        $this->bg = $bg;
        $this->addComponent($this->bg);


        $bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetTitle($sizeX, 4.6);
        $bg->setAlign("center", "top");
        $bg->setPosX($sizeX / 2);
        $this->titlebg = $bg;
        $this->addComponent($this->titlebg);


        $this->label = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel($sizeX - 10, 9);
        $this->label->setStyle("TextCardSmallScores2");
        $this->label->setTextSize(2);
        $this->label->setTextEmboss(true);
        $this->label->setAlign("center", "top");
        $this->label->setPosX(($sizeX) / 2);
        $this->label->setPosY(-0.5);
        $this->addComponent($this->label);


        $this->labelMap = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel($sizeX - 10, 9);
        $this->labelMap->setStyle("TextCardSmallScores2");
        $this->labelMap->setTextSize(3);
        $this->labelMap->setTextEmboss(true);
        $this->labelMap->setAlign("center", "top");
        $this->labelMap->setPosX(($sizeX) / 2);
        $this->labelMap->setPosY(-6);
        $this->addComponent($this->labelMap);


        $this->frame = new \ManiaLive\Gui\Controls\Frame(27, -16);
        $this->frame->setAlign("left", "top");
        $line = new \ManiaLib\Gui\Layouts\Line();
        $line->setMargin(8, 0);
        $this->frame->setLayout($line);
        $this->addComponent($this->frame);

        $this->b0 = new RateButton2(0);
        $this->frame->addComponent($this->b0);

        $this->b5 = new RateButton2(5);
        $this->frame->addComponent($this->b5);
        $this->setPosition(-45, -42);

        $this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("MapRatings\Gui\Script");
        $action = $this->createAction(array(self::$parentPlugin, "saveRating"), 0);
        $this->script->setParam("rate_" . 0, $action);

        $action = $this->createAction(array(self::$parentPlugin, "saveRating"), 5);
        $this->script->setParam("rate_" . 5, $action);


        $this->registerScript($this->script);
        $this->setSize($sizeX, $sizeY);
    }

    public function setMap(\Maniaplanet\DedicatedServer\Structures\Map $map)
    {
        $msg = eXpGetMessage("Did you like the map ?");
        $this->labelMap->setText($msg);
        $this->label->setText(\ManiaLib\Utils\Formatting::stripCodes($map->name, "wosn"));
    }
}
