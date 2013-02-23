<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLivePlugins\eXpansion\LocalRecords\Gui\Controls\Recorditem;

class LRPanel extends \ManiaLive\Gui\Window {

    private $connection;
    private $storage;
    private $_windowFrame;
    private $_mainWindow;
    private $_minButton;
    public static $records;

    protected function onConstruct() {
        parent::onConstruct();
        $config = Config::getInstance();

        $dedicatedConfig = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($dedicatedConfig->host, $dedicatedConfig->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->setScriptEvents(true);
        $this->setAlign("left", "top");

        $this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->_windowFrame->setPosY(0);
        $this->_windowFrame->setAlign("left", "top");
        $this->_windowFrame->setId("Frame");
        $this->_windowFrame->setScriptEvents(true);

        $this->_mainWindow = new \ManiaLib\Gui\Elements\Quad(60, 10);
        $this->_mainWindow->setId("myWindow");
        $this->_mainWindow->setStyle("Bgs1InRace");
        $this->_mainWindow->setSubStyle("BgList");
        $this->_mainWindow->setAlign("left", "top");
        $this->_mainWindow->setScriptEvents(true);
        $this->_windowFrame->addComponent($this->_mainWindow);

        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setAlign("left", "top");
        $frame->setPosition(3, -4);
        $frame->setLayout(new \ManiaLib\Gui\Layouts\Column(-1));

        $index = 1;
        $timeDiff = 0;
        $first = 0;

        foreach (self::$records as $record) {
            if ($index == 1) {
                $first = $record->time;
                $timeDiff = 0;
            } else {
                $timeDiff = $record->time - $first;
            }

            $frame->addComponent(new recordItem($index++, $record, $timeDiff));
        }
        $this->_windowFrame->addComponent($frame);

        $this->_minButton = new \ManiaLib\Gui\Elements\Quad(5, 5);
        $this->_minButton->setId("minimizeButton");
        $this->_minButton->setStyle("Icons128x32_1");
        $this->_minButton->setSubStyle("RT_Cup");
        $this->_minButton->setScriptEvents(true);
        $this->_minButton->setAlign("left", "center");

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
        $this->_mainWindow->setSize(60, 60);
        $this->_minButton->setPosition(60 - 6, -30);
    }

    function onShow() {
        
    }

    function destroy() {
        parent::destroy();
    }

}

?>
