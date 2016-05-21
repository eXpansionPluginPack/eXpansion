<?php

namespace ManiaLivePlugins\eXpansion\Gui\Widgets;

use ManiaLib\Gui\Elements\Quad;
use ManiaLive\Gui\Container;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;

/**
 *
 * @author reaby
 */
class Edge extends Widget
{

    protected $quad, $quad2;
    protected $label, $label2;
    protected $orientation;
    protected $background;
    protected $_mainWindow, $_windowFrame, $bg;
    protected $sscript;
    protected $widgetSize;

    public function onConstruct()
    {
        parent::onConstruct();

        $sizeX = 60;
        $sizeY = 6;
        $config = Config::getInstance();

        $this->setName("Autohide Switcher");
        $this->setDisableAxis("x");

        $this->_windowFrame = new Frame();
        $this->_windowFrame->setId("Frame");
        $this->_windowFrame->setScriptEvents(true);
        $this->addComponent($this->_windowFrame);

        $this->bg = new WidgetBackGround(60, 6);
        $this->_windowFrame->addComponent($this->bg);

        $this->label = new DicoLabel(20, 6);
        $this->label->setTextColor("fff");
        $this->label->setPosition(5, -3);
        $this->label->setAlign("left", "center");
        $msg = eXpGetMessage("Auto hide");
        $this->label->setText($msg);
        $this->_windowFrame->addComponent($this->label);

        $this->quad = new Quad(6, 6);
        $this->quad->setPosY(-0.5);
        $this->quad->setPosX(20);
        $this->quad->setStyle('Icons64x64_1');
        $this->quad->setSubStyle('GenericButton');
        $this->quad->setColorize("f00");
        $this->quad->setId("Edge");
        $this->quad->setAlign("left", "top");
        $this->quad->setScriptEvents();
        $this->_windowFrame->addComponent($this->quad);


        $this->label2 = new DicoLabel(20, 6);
        $this->label2->setTextColor("fff");
        $this->label2->setPosition(26, -3);
        $this->label2->setAlign("left", "center");
        $msg = eXpGetMessage("Show Diff");
        $this->label2->setText($msg);
        $this->_windowFrame->addComponent($this->label2);

        $this->quad2 = new Quad(6, 6);
        $this->quad2->setPosY(-0.5);
        $this->quad2->setPosX(44);
        $this->quad2->setStyle('Icons64x64_1');
        $this->quad2->setSubStyle('GenericButton');
        $this->quad2->setColorize("f00");
        $this->quad2->setId("Diff");
        $this->quad2->setAlign("left", "top");
        $this->quad2->setScriptEvents();
        $this->_windowFrame->addComponent($this->quad2);


        $this->_minButton = new Quad(5.5, 5.5);
        $this->_minButton->setAlign("left", "top");
        $this->_minButton->setId("minimizeButton");
        $this->_minButton->setStyle("Icons128x32_1");
        $this->_minButton->setSubStyle("Settings");
        $this->_minButton->setScriptEvents(true);
        $this->_minButton->setPosition(60 - 6, -0.5);
        $this->_windowFrame->addComponent($this->_minButton);

        $script = new Script("Gui\Scripts\TrayWidget");
        $script->setParam('isMinimized', 'True');
        $script->setParam('autoCloseTimeout', 30000);
        $script->setParam('posXMin', -50);
        $script->setParam('posX', -50);
        $script->setParam('posXMax', -4);
        $this->registerScript($script);

        $this->sscript = new Script("Gui\Scripts\EdgeScript");
        $this->sscript->setParam("imageOff", "<1.,0.,0.>");
        $this->sscript->setParam("imageOn", "<0.,1.,0.>");
        $this->registerScript($this->sscript);

        $this->setSize($sizeX, $sizeY);
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->bg->setSize($this->sizeX, $this->sizeY);
    }

    public function onIsRemoved(Container $target)
    {
        parent::onIsRemoved($target);
        $this->destroy();
    }

}
