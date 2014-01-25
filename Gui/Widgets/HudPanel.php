<?php

namespace ManiaLivePlugins\eXpansion\Gui\Widgets;

class HudPanel extends \ManiaLivePlugins\eXpansion\Gui\Windows\Widget {

    protected $_windowFrame;
    protected $background;
    protected $_minButton;
    protected $frame;
    private $actionEnableMove;
    private $actionDisableMove;
    private $actionOpenConfig;
    private $actionReset;
    public static $mainPlugin;

    protected function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();
        
        $this->actionEnableMove = $this->createAction(array(self::$mainPlugin, 'enableHudMove'));
        $this->actionDisableMove = $this->createAction(array(self::$mainPlugin, 'disableHudMove'));
        $this->actionOpenConfig = $this->createAction(array(self::$mainPlugin, 'showConfigWindow'));
        $this->actionReset = $this->createAction(array(self::$mainPlugin, 'resetHud'));
        $this->setScriptEvents();        

        $this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->_windowFrame->setId("Frame");
        $this->_windowFrame->setSize(90, 6);
        $this->_windowFrame->setPosY(-3);
        $this->_windowFrame->setScriptEvents(true);
        $this->addComponent($this->_windowFrame);

        $this->background = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(74, 10);
        $this->background->setId("MainWindow");        
        $this->background->setAlign("left");
        $this->_windowFrame->addComponent($this->background);


        $this->frame = new \ManiaLive\Gui\Controls\Frame();        
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->_windowFrame->addComponent($this->frame);

        $btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(22, 5);
        $btn->setAction($this->actionReset);
        $btn->setText("Reset HUD");
        $btn->colorize("a00");
        $btn->setTextColor("ff0");        
        $this->frame->addComponent($btn);

        $btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(22, 5);
        $btn->setAction($this->actionEnableMove);
        $btn->setText("Unlock HUD");
        $btn->colorize("0a0");        
        $this->frame->addComponent($btn);

        $btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(22, 5);
        $btn->setAction($this->actionDisableMove);
        $btn->setText("Lock HUD");
        $btn->colorize("aa0");        
        $this->frame->addComponent($btn);


        $btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(22, 5);
        $btn->setAction($this->actionOpenConfig);
        $btn->setText(__("Configure HUD", $login));        
        $this->frame->addComponent($btn);

        $inputbox = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox("widgetStatus");
        $inputbox->setPosition(900, 900);
        $inputbox->setScriptEvents();
        $this->addComponent($inputbox);

        $this->_minButton = new \ManiaLib\Gui\Elements\Quad(5, 5);
        $this->_minButton->setScriptEvents(true);
        $this->_minButton->setAlign("left", "center");
        $this->_minButton->setId("minimizeButton");
        $this->_minButton->setStyle("Icons128x32_1");
        $this->_minButton->setSubStyle("Settings");        
        $this->_windowFrame->addComponent($this->_minButton);

        $this->setSizeX(72);
        
        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\TrayWidget");
        $script->setParam('isMinimized', 'True');
        $script->setParam('autoCloseTimeout', '7500');
        $script->setParam('posXMin',-66);
        $script->setParam('posX', -66);
        $script->setParam('posXMax', 0);
        $this->registerScript($script);        
        $this->setName("Hud Configure Panel");
        $this->setDisableAxis("x");
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->_windowFrame->setSize(80, 7);
        $this->background->setSize(80, 7);        
        $this->background->setPosX(-6);
        $this->_minButton->setPosX(70);
    }


    function destroy() {
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}

?>
