<?php

namespace ManiaLivePlugins\eXpansion\ExtendTime\Gui\Widgets;

use ManiaLib\Gui\Elements\Label;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;
use ManiaLivePlugins\eXpansion\MapRatings\Gui\Controls\RateButton2;

class TimeExtendVote extends Widget
{
    protected $script;

    protected $xml;
    protected $frame;
    protected $bg;
    protected $titlebg;
    protected $label;

    protected $btnNo;
    protected $btnYes;

    public static $parentPlugin;

    protected function onConstruct()
    {
        parent::onConstruct();

        $this->setName("Extend Timelimit");
        $sizeX = 90;
        $sizeY = 25;

        $this->label = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel($sizeX - 10, 9);
        $this->label->setStyle("TextCardSmallScores2");
        $this->label->setTextSize(3);
        $this->label->setTextEmboss(true);
        $this->label->setText("Extend Timelimit ?");
        $this->label->setAlign("center", "top");
        $this->label->setPosX(($sizeX) / 2);
        $this->label->setPosY(-6);
        $this->addComponent($this->label);

        $this->frame = new \ManiaLive\Gui\Controls\Frame(27, -16);
        $this->frame->setAlign("left", "top");
        $line = new \ManiaLib\Gui\Layouts\Line();
        $line->setMargin(8, 0);
        $this->frame->setLayout($line);
        $this->addComponent($this->frame);

        $this->btnNo = new RateButton2(0);
        $this->frame->addComponent($this->btnNo);

        $this->btnYes = new RateButton2(5);
        $this->frame->addComponent($this->btnYes);
        $this->setPosition(-45, -42);

        $this->script = new Script("ExtendTime/Gui/Script");

        $action = $this->createAction(array(self::$parentPlugin, "vote"), "no");
        $this->script->setParam("actionNo", $action);

        $action = $this->createAction(array(self::$parentPlugin, "vote"), "yes");
        $this->script->setParam("actionYes", $action);

        $action = $this->createAction(array(self::$parentPlugin, "calcVotes"));
        $this->script->setParam("calcVotes", $action);

        $this->registerScript($this->script);
        $this->setSize($sizeX, $sizeY);
    }

    public function eXpOnEndConstruct()
    {
        $this->setSize(36, 12);
        $this->setPosition(120, 88);
    }
}
