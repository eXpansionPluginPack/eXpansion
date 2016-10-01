<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Clock\Gui\Widgets;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;

class Clock extends Widget
{
    protected $script;

    public function eXpOnBeginConstruct()
    {
        $frame = new Frame();
        $frame->setId("Frame");
        $this->setName("Local time");
        $bg = new WidgetBackGround(28, 6);
        $bg->setPosX(-6);
        $frame->addComponent($bg);

        $label = new Label(20, 6);
        $label->setPosition(0, -1);
        $label->setAlign("left", "top");
        $label->setId("Time");
        $label->setStyle("TextValueSmallSm");
        $label->setTextSize(3);
        $frame->addComponent($label);

        $script = new Script("Widgets_Clock/Gui/Script");
        $this->registerScript($script);


        $quad = new Quad(5, 5);
        $quad->setPosY(2);
        $quad->setStyle("BgRaceScore2");
        $quad->setSubStyle("SendScore");
        $quad->setAlign("left", "center");
        $quad->setPosition(16, -3);
        $quad->setId("minimizeButton");
        $quad->setScriptEvents();
        $frame->addComponent($quad);
        $this->addComponent($frame);

    }

    public function eXpOnEndConstruct()
    {
        $this->setSize(36, 6);
        $this->setPosition(-160, 74);
        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\TrayWidget");
        $script->setParam('isMinimized', 'False');
        $script->setParam('autoCloseTimeout', 0);
        $script->setParam('posXMin', -12);
        $script->setParam('posX', -12);
        $script->setParam('posXMax', -2);
        $this->registerScript($script);
    }
}
