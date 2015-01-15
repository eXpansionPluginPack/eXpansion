<?php

namespace ManiaLivePlugins\eXpansion\Emotes\Gui\Windows;

use ManiaLivePlugins\eXpansion\Emotes\Config;

class EmotePanel extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget {

    private $connection;
    private $storage;
    private $_windowFrame;
    private $_mainWindow;
    private $_minButton;
    private $servername;
    
    private $btnBG;
    private $btnGG;
    private $btnLOL;
    private $btnAfk;    
    private $actionGG;
    private $actionBG;
    private $actionLOL;
    private $actionAfk;
    public static $emotePlugin;
    

    protected function onConstruct() {
        parent::onConstruct();
        
        $config = Config::getInstance();

        $this->setName("Emote Panel");
        $this->setDisableAxis("x");
        
        $dedicatedConfig = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \Maniaplanet\DedicatedServer\Connection::factory($dedicatedConfig->host, $dedicatedConfig->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->actionGG = \ManiaLivePlugins\eXpansion\Emotes\Emotes::$action_GG;
        $this->actionBG = \ManiaLivePlugins\eXpansion\Emotes\Emotes::$action_Bg;
        $this->actionAfk = \ManiaLivePlugins\eXpansion\Emotes\Emotes::$action_Afk;
        $this->actionLol = \ManiaLivePlugins\eXpansion\Emotes\Emotes::$action_Lol;

        $this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->_windowFrame->setAlign("left", "top");
        $this->_windowFrame->setId("Frame");
        $this->_windowFrame->setScriptEvents(true);

        $this->_mainWindow = new \ManiaLib\Gui\Elements\Quad(60, 10);
        $this->_mainWindow->setId("MainWindow");
        $this->_mainWindow->setStyle("Bgs1InRace");
        $this->_mainWindow->setSubStyle("BgList");
        $this->_mainWindow->setAlign("left", "center");
        $this->_windowFrame->addComponent($this->_mainWindow);
        
        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setAlign("left", "top");
        $frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $frame->setPosition(6,4);
        
        $this->btnLol = new \ManiaLib\Gui\Elements\Quad(7,7);
        $this->btnLol->setImage($config->iconLol);
        $this->btnLol->setAction($this->actionLol);
        $frame->addComponent($this->btnLol);

        $this->btnBG = new \ManiaLib\Gui\Elements\Quad(7,7);
        $this->btnBG->setImage($config->iconBG);
        $this->btnBG->setAction($this->actionBG);
        $frame->addComponent($this->btnBG);
        
        $this->btnGG = new \ManiaLib\Gui\Elements\Quad(7,7);
        $this->btnGG->setImage($config->iconGG);
        $this->btnGG->setAction($this->actionGG);
        $frame->addComponent($this->btnGG);
        
        $this->btnAfk = new \ManiaLib\Gui\Elements\Quad(7,7);
        $this->btnAfk->setImage($config->iconAfk);
        $this->btnAfk->setAction($this->actionAfk);
        $frame->addComponent($this->btnAfk);
       
        $this->_windowFrame->addComponent($frame);
        
        $this->_minButton = new \ManiaLib\Gui\Elements\Quad(5, 5);
        $this->_minButton->setScriptEvents(true);
        $this->_minButton->setId("minimizeButton");        
        $this->_minButton->setImage($config->iconMenu);       
        $this->_minButton->setAlign("left", "bottom");
        $this->_windowFrame->addComponent($this->_minButton);

        $this->addComponent($this->_windowFrame);

        $script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\TrayWidget");
        $script->setParam('isMinimized', 'True');
        $script->setParam('autoCloseTimeout', '3500');
        $script->setParam('posXMin',-50);
        $script->setParam('posX', -50);
        $script->setParam('posXMax', -4);
        $this->registerScript($script);
        
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->_windowFrame->setSize(60, 12);
        $this->_mainWindow->setSize(60, 6);        
        $this->_minButton->setPosition(60 - 6, -2.5);
        
    }

    function destroy() {
        $this->destroyComponents();
        parent::destroy();
    }

}
?>
