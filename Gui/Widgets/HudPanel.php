<?php

namespace ManiaLivePlugins\eXpansion\Gui\Widgets;

class HudPanel extends \ManiaLive\Gui\Window {

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
        $this->setAlign("left", "top");

        $this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->_windowFrame->setAlign("left", "top");
        $this->_windowFrame->setId("Frame");
        $this->_windowFrame->setSize(90, 6);
        $this->_windowFrame->setScriptEvents(true);
        $this->addComponent($this->_windowFrame);

        $this->background = new \ManiaLib\Gui\Elements\Quad(70, 6);
        $this->background->setId("MainWindow");
        $this->background->setStyle("Bgs1InRace");
        $this->background->setSubStyle("BgList");
        $this->background->setAlign("left", "center");
        $this->_windowFrame->addComponent($this->background);


        $this->frame = new \ManiaLive\Gui\Controls\Frame(0, -5.5);
        $this->frame->setAlign("left", "center");
        $this->frame->setSize(90, 6);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->_windowFrame->addComponent($this->frame);

        $btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(22, 5);
        $btn->setAction($this->actionReset);
        $btn->setText("Reset HUD");
        $btn->colorize("a00");
        $btn->setTextColor("ff0");
        $btn->setAlign("left", "center");
        $this->frame->addComponent($btn);

        $btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(22, 5);
        $btn->setAction($this->actionEnableMove);
        $btn->setText("Unlock HUD");
        $btn->colorize("0a0");
        $btn->setAlign("left", "center");
        $this->frame->addComponent($btn);

        $btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(22, 5);
        $btn->setAction($this->actionDisableMove);
        $btn->setText("Lock HUD");
        $btn->colorize("aa0");
        $btn->setAlign("left", "center");
        $this->frame->addComponent($btn);


        $btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(22, 5);
        $btn->setAction($this->actionOpenConfig);
        $btn->setText(__("Configure HUD", $login));
        $btn->setAlign("left", "center");
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
        $this->_minButton->setAlign("left", "bottom");
        $this->frame->addComponent($this->_minButton);

        $this->xml = new \ManiaLive\Gui\Elements\Xml();
        $this->setSizeX(90);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->_windowFrame->setSize(90, 6);
        $this->background->setSize(94, 6);
        $this->background->setPosX(-20);
    }

    function onShow() {
        $this->removeComponent($this->xml);
        $this->xml->setContent('           
        <script><!--
	    
		    main () {
                       
                        declare Window <=> Page.GetFirstChild("' . $this->getId() . '");
                        declare mainWindow <=> Page.GetFirstChild("Frame");
                        declare CMlEntry widgetStatus <=> (Page.GetFirstChild("widgetStatus") as CMlEntry);
                        declare isMinimized = True;                                          
                        declare lastAction = Now;                           
                        declare autoCloseTimeout = 7500;
                        declare positionMin = -64.0;
                        declare positionMax = 0.0;
                    	declare Text outText = "";
                        mainWindow.PosnX = -64.0;  
                    	declare persistent Boolean[Text] widgetVisible;


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
				
				outText = "";					   
					   if (widgetVisible.count > 0) {
					   foreach (id => status in widgetVisible) {
						
			    			    declare Text bool = "0";
						    if (status == True) {
							bool = "1";
						    }
						outText = outText ^ id ^ ":" ^ bool ^ "|";
											    
					    }
			
					   widgetStatus.Value = outText;
					  }
                                yield;                        
                        }  
                        
                }
                --></script>');

        $this->addComponent($this->xml);
    }

    function destroy() {
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}

?>
