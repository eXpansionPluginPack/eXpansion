<?php

namespace ManiaLivePlugins\eXpansion\Gui\Widgets;

class HudPanel extends \ManiaLive\Gui\Window {
    
    private $_windowFrame;
    private $_mainWindow;
    private $_minButton;
    private $frame;
        
    private $actionEnableMove;
    private $actionDisableMove;
    private $actionReset;
    public static $mainPlugin;
    

    protected function onConstruct() {
        parent::onConstruct();        
        
        $this->actionEnableMove = $this->createAction(array(self::$mainPlugin, 'enableHudMove') );
        $this->actionDisableMove = $this->createAction(array(self::$mainPlugin, 'disableHudMove') );
        $this->actionReset = $this->createAction(array(self::$mainPlugin, 'resetHud') );
        
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
        $this->_mainWindow->setAlign("left", "bottom");
        $this->_windowFrame->addComponent($this->_mainWindow);
        
        $this->frame = new \ManiaLive\Gui\Controls\Frame(3,0);
        $this->frame->setAlign("left", "center");
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        
        $btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(22,5);
        $btn->setAction($this->actionReset);
        $btn->setText("Reset HUD");
        $btn->colorize("a00");
        $btn->setTextColor("ff0");
        $btn->setAlign("left","center");
        $this->frame->addComponent($btn);
        
        $btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(22,5);
        $btn->setAction($this->actionEnableMove);
        $btn->setText("Unlock HUD");
        $btn->colorize("0a0");
        $btn->setAlign("left","center");
        $this->frame->addComponent($btn);
        
        $btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(22,5);
        $btn->setAction($this->actionDisableMove);
        $btn->setText("Lock HUD");
        $btn->colorize("aa0");
        $btn->setAlign("left","center");
        $this->frame->addComponent($btn);
        
       
               
        $this->_windowFrame->addComponent($this->frame);
        
        $this->_minButton = new \ManiaLib\Gui\Elements\Quad(5, 5);
        $this->_minButton->setScriptEvents(true);
        $this->_minButton->setAlign("left","center");
        $this->_minButton->setId("minimizeButton");        
        $this->_minButton->setStyle("Icons128x32_1");        
        $this->_minButton->setSubStyle("Settings");        
        
        $this->_minButton->setAlign("left", "bottom");
        $this->_windowFrame->addComponent($this->_minButton);

        $this->addComponent($this->_windowFrame);

        $xml = new \ManiaLive\Gui\Elements\Xml();
        $xml->setContent('           
        <script><!--
                     main () {
                       
                        declare Window <=> Page.GetFirstChild("' . $this->getId() . '");
                        declare mainWindow <=> Page.GetFirstChild("Frame");
                        declare isMinimized = True;                                          
                        declare lastAction = Now;                           
                        declare autoCloseTimeout = 7500;
                        declare positionMin = -50.0;
                        declare positionMax = -4.0;
                        mainWindow.PosnX = -50.0;    
                        while(True) {
                                
                                if (isMinimized)
                                {
                                     if (mainWindow.PosnX >= positionMin) {                                          
                                          mainWindow.PosnX -= 4;                                          
                                    }
                                }

                                if (!isMinimized)
                                {         
                                    if (Now-lastAction > autoCloseTimeout) {                                          
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

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->_windowFrame->setSize(60, 12);
        $this->_mainWindow->setSize(60, 6);        
        $this->_minButton->setPosition(60 - 6, 0);
        
    }
 

    function onShow() {
        
    }

    function destroy() {   
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}
?>
