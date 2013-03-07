<?php

namespace ManiaLivePlugins\eXpansion\Emotes\Gui\Windows;

use ManiaLivePlugins\eXpansion\Emotes\Config;

class EmotePanel extends \ManiaLive\Gui\Window {

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

        $dedicatedConfig = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($dedicatedConfig->host, $dedicatedConfig->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->actionGG = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'actions'), "GG");
        $this->actionBG = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'actions'), "BG");
        $this->actionAfk = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'actions'), "Afk");
        $this->actionLol = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'actions'), "Lol");
        

        $this->setScriptEvents(true);
        $this->setAlign("left", "top");

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

        $xml = new \ManiaLive\Gui\Elements\Xml();
        $xml->setContent('
        <timeout>0</timeout>            
        <script><!--
                      main () {
                       
                        declare Window <=> Page.GetFirstChild("' . $this->getId() . '");
                        declare mainWindow <=> Page.GetFirstChild("Frame");
                        declare isMinimized = True;                                          
                        declare lastAction = Now;
                        declare autoCloseTimeout = 3500;
                        declare positionMin = -50.0;
                        declare positionMax = -4.0;
                        mainWindow.PosnX = -50.0;                        
                        declare blink = True;
                        declare blinkDuration = 2000;
                        declare blinkStartTime = Now;
                        declare isMouseOver = False;
                            
                      

                        while(True) {
                              /*
                              // Blink cannot be implemented since CMlControl doesnt have opacity :(((
                              if (blink) {
                                     if (Now-blinkStartTime < blinkDuration) {
                                     declare seed =(Now-blinkStartTime)/1000;
                                     Window.O
                                     
                                    } else {
                                    blink = False;
                                    }                                        
                                } */
                                
                                if (isMinimized)
                                {
                                     if (mainWindow.PosnX >= positionMin) {                                          
                                          mainWindow.PosnX -= 4;                                          
                                    }
                                }

                            
                                if (!isMinimized)
                                {         
                                    if (!isMouseOver && Now-lastAction > autoCloseTimeout) {                                          
                                        if (mainWindow.PosnX <= positionMin) {                                                 
                                                mainWindow.PosnX -= 4;                                      
                                        } 
                                        if (mainWindow.PosnX >= positionMin)  {
                                                isMinimized = True;
                                        }
                                    }
                                    
                                    else {
                                        if ( mainWindow.PosnX <= positionMax) {                                                      
                                                  mainWindow.PosnX += 4;
                                        }                                                                                                                                             
                                    }
                                }
                                    
                                foreach (Event in PendingEvents) {                                                
                                    if (Event.Type == CMlEvent::Type::MouseOver && (Event.ControlId == "myWindow" || Event.ControlId == "minimizeButton" )) {
                                           isMinimized = False;
                                           isMouseOver = True;
                                           lastAction = Now;
                                    }
                                    if (Event.Type == CMlEvent::Type::MouseOut) {
                                        isMouseOver = False;
                                    }
                                    
                                    if (!isMinimized && Event.Type == CMlEvent::Type::MouseClick && ( Event.ControlId == "myWindow" || Event.ControlId == "minimizeButton" )) {
                                        isMinimized = True;
                                    }
                                }
                                yield;                        
                        }  
                        
                }
                --></script>');
        $this->addComponent($xml);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->_windowFrame->setSize(60, 12);
        $this->_mainWindow->setSize(60, 6);        
        $this->_minButton->setPosition(60 - 6, -2.5);
        
    }

    function actions($login, $action) {
      self::$emotePlugin->sendEmote($login, $action);
    }

    function onShow() {
        
    }

    function destroy() {
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->actionAfk);
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->actionGG);
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->actionBG);
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->actionLOL);
        $this->btnAfk->destroy();
        $this->btnBG->destroy();
        $this->btnGG->destroy();
        $this->btnLOL->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}
?>
