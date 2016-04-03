<?php

namespace ManiaLivePlugins\eXpansion\DonatePanel\Gui;

use ManiaLivePlugins\eXpansion\DonatePanel\DonatePanel;

class DonatePanelWindow extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{

    private $_windowFrame;
    private $_mainWindow;
    private $_minButton;
    private $container;
    public static $donatePlugin;
    private $items = array();

    protected function exp_onBeginConstruct()
    {
        $this->setName("Donate Panel");
        $login = $this->getRecipient();
        $this->setScriptEvents();
        $this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->_windowFrame->setId("Frame");
        $this->_windowFrame->setScriptEvents(true);
        $this->addComponent($this->_windowFrame);

        $this->_mainWindow = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(72, 6);
        $this->_mainWindow->setId("MainWindow");
        $this->_windowFrame->addComponent($this->_mainWindow);

        $frame = new \ManiaLive\Gui\Controls\Frame(2, -3);
        $frame->setAlign("left", "center");
        $frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $text = new \ManiaLib\Gui\Elements\Label(10, 6);
        $text->setText('$fff' . "Donate");
        $text->setAlign("left", "center2");
        $text->setTextSize(1);
        $frame->addComponent($text);

        $donations = array(50, 100, 500, 1000, 2000);
        $x = 0;
        foreach ($donations as $text) {
            $this->items[$x] = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(18, 6);
            $this->items[$x]->setText($text . "p");
            $this->items[$x]->setScale(0.55);
            $this->items[$x]->setAction($this->createAction(array($this, "Donate"), $text));
            $frame->addComponent($this->items[$x]);
        }

        $this->_windowFrame->addComponent($frame);

        $this->_minButton = new \ManiaLib\Gui\Elements\Quad(5, 5);
        $this->_minButton->setScriptEvents(true);
        $this->_minButton->setAlign("left", "top");
        $this->_minButton->setId("minimizeButton");
        $this->_minButton->setStyle("ManiaPlanetLogos");
        $this->_minButton->setSubStyle("IconPlanetsSmall");
        $this->_minButton->setPosition(70 - 4, -0.5);
        $this->_windowFrame->addComponent($this->_minButton);
    }

    protected function exp_onSettingsLoaded()
    {

        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\TrayWidget");
        $script->setParam('isMinimized', 'True');
        $script->setParam('autoCloseTimeout', $this->getParameter('autoCloseTimeout'));
        $script->setParam('posXMin', -62);
        $script->setParam('posX', -62);
        $script->setParam('posXMax', -2);
        $this->registerScript($script);
    }

    function Donate($login, $amount)
    {
        self::$donatePlugin->Donate($login, $amount);
    }

    function destroy()
    {
        foreach ($this->items as $item)
            $item->destroy();
        parent::destroy();
    }

}

?>