<?php

namespace ManiaLivePlugins\eXpansion\Notifications\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Notifications\Gui\Controls\Item;

class Panel extends \ManiaLive\Gui\Window
{

    protected $_windowFrame;
    protected $_mainWindow;
    protected $_minButton;
    protected $frame;
    public static $menuPlugin;

    protected function onConstruct()
    {
        parent::onConstruct();

        $this->setScriptEvents(true);
        $this->setAlign("center", "bottom");

        $this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->_windowFrame->setAlign("left", "bottom");
        $this->_windowFrame->setId("Frame");
        $this->_windowFrame->setScriptEvents(true);

        $this->_mainWindow = new \ManiaLib\Gui\Elements\Quad(60, 10);
        $this->_mainWindow->setId("myWindow");
        $this->_mainWindow->setStyle("BgsPlayerCard");
        $this->_mainWindow->setSubStyle("BgPlayerCardBig");
        $this->_mainWindow->setAlign("left", "bottom");
        $this->_mainWindow->setScriptEvents(true);

        $this->_windowFrame->addComponent($this->_mainWindow);

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
                        declare autoCloseTimeout = 5000;
                        declare positionMin = -1.0;
                        declare positionMax = -25.0;
                        mainWindow.PosnY = -1.0;                        
                                              
                        while(True) {
                                
                                if (isMinimized)
                                {
                                     if (mainWindow.PosnY <= positionMin) {                                          
                                          mainWindow.PosnY += 4;                                          
                                    }
                                }

                                if (!isMinimized)
                                {         
                                    if (Now-lastAction > autoCloseTimeout) {                                          
                                        if (mainWindow.PosnY <= positionMin) {                                                 
                                                mainWindow.PosnY += 4;                                      
                                        } 
                                        if (mainWindow.PosnY >= positionMin)  {
                                                isMinimized = True;
                                        }
                                    }
                                    
                                    else {
                                        if ( mainWindow.PosnY >= positionMax) {                                                      
                                                  mainWindow.PosnY -= 4;
                                        }                                                                                                                                             
                                    }
                                }
                                    
                                foreach (Event in PendingEvents) {                                                
                                    if (Event.Type == CMlEvent::Type::MouseClick && ( Event.ControlId == "myWindow" || Event.ControlId == "minimizeButton" )) {
                                           isMinimized = !isMinimized;    
                                           lastAction = Now;                                           
                                    }                                       
                                }
                                yield;                        
                        }  
                        
                } 
                --></script>');
        $this->addComponent($xml);
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->_windowFrame->setSize(120, 40);
        $this->_mainWindow->setSize(120, 45);
    }

    public function setItems(array $menuItems)
    {
        if ($this->frame != null) {
            $this->_windowFrame->removeComponent($this->frame);
        }
        $this->frame = new \ManiaLive\Gui\Controls\Frame(100, 40);
        $this->frame->setAlign("left", "bottom");
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(1));
        // $menu = array_reverse($menuItems);
        foreach ($menuItems as $menuItem) {
            $item = new Item($menuItem);
            $this->frame->addComponent($item);
        }

        $posY = abs(count($menuItems) * 6);

        $this->frame->setPosition(6, $posY);
        $this->_windowFrame->addComponent($this->frame);
    }
}
