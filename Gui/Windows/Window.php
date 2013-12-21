<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Config;

/**
 * @abstract
 */
class Window extends \ManiaLive\Gui\Window {

    protected $_titlebar;
    protected $_title;
    protected $_mainWindow;
    protected $mainFrame;
    protected $_mainText;
    protected $_closebutton;
    protected $_minbutton;
    protected $_closeAction;
    protected $_showCoords = 'False';
    protected $_windowFrame;
    private $dDeclares = "";
    private $dLoop = "";
    private $dCount = 0;

    protected function onConstruct() {
        parent::onConstruct();
        $config = Config::getInstance();
        $this->_closeAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'closeWindow'));

        $this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->_windowFrame->setScriptEvents(true);
        $this->_windowFrame->setAlign("left", "top");

        $this->_mainWindow = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->sizeY);
        $this->_mainWindow->setId("MainWindow");
        $this->_mainWindow->setStyle("Bgs1");
        $this->_mainWindow->setSubStyle("BgCard");
        $this->_mainWindow->setBgcolor("eeef");
        // $this->_mainWindow->setStyle("Bgs1InRace");
        // $this->_mainWindow->setSubStyle("BgEmpty");
        // $this->_mainWindow->setBgcolor("fff");
        $this->_mainWindow->setScriptEvents(true);
        $this->_windowFrame->addComponent($this->_mainWindow);

        $this->_titlebar = new \ManiaLib\Gui\Elements\Quad($this->sizeX, 6);
        $this->_titlebar->setId("Titlebar");
        $this->_titlebar->setStyle("Bgs1");
        $this->_titlebar->setSubStyle("ProgressBar");
        // $this->_titlebar->setBgcolor("6bf");
        //$this->_titlebar->setImage($config->windowTitlebar);
        $this->_titlebar->setScriptEvents(true);
        $this->_windowFrame->addComponent($this->_titlebar);


        $this->_title = new \ManiaLib\Gui\Elements\Label(60, 4);
        $this->_title->setId("TitlebarText");
        $this->_title->setStyle("TextStaticSmall");
        $this->_title->setTextColor('000');
        $this->_title->setTextSize(1);
        $this->_windowFrame->addComponent($this->_title);

        $this->_closebutton = new \ManiaLib\Gui\Elements\Quad(7, 3);
        $this->_closebutton->setAlign('center', 'top');
        $this->_closebutton->setStyle("Icons64x64_1");
        $this->_closebutton->setSubStyle("Close");

        /*   $this->_closebutton->setStyle("TextChallengeNameMedium");
          $this->_closebutton->setScriptEvents(true);
          $this->_closebutton->setFocusAreaColor1("fff");
          $this->_closebutton->setFocusAreaColor2("000");
          $this->_closebutton->setId("Close");
          $this->_closebutton->setText(' x ');
          $this->_closebutton->setTextColor('000');
          $this->_closebutton->setTextSize(1); */
        $this->_closebutton->setScriptEvents(true);
        $this->_closebutton->setAction($this->_closeAction);
        $this->_windowFrame->addComponent($this->_closebutton);

        $this->_minbutton = new \ManiaLib\Gui\Elements\Label(7, 3);
        $this->_minbutton->setAlign('center', 'top');
        $this->_minbutton->setStyle("TextChallengeNameMedium");
        $this->_minbutton->setScriptEvents(true);
        $this->_minbutton->setText('$000-');

        $this->_minbutton->setFocusAreaColor1("fff0");
        $this->_minbutton->setFocusAreaColor2("0000");
        $this->_minbutton->setScriptEvents(true);
        $this->_minbutton->setId("Minimize");
        // $this->_windowFrame->addComponent($this->_minbutton);

        $this->mainFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->mainFrame->setPosY(-3);
        $this->_windowFrame->addComponent($this->mainFrame);

        $this->addComponent($this->_windowFrame);
        $this->xml = new \ManiaLive\Gui\Elements\Xml();
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->_windowFrame->setSize($this->sizeX, $this->sizeY);
        $this->_mainWindow->setSize($this->sizeX + 0.6, $this->sizeY + 2);
        $this->_mainWindow->setPosY(1);

        $this->_title->setSize($this->sizeX, 4);
        $this->_title->setPosition(($this->_title->sizeX / 2), 3.5);
        $this->_title->setHalign("center");

        $this->_titlebar->setPosX(-4);
        $this->_titlebar->setPosY(6);
        $this->_titlebar->setSize($this->sizeX + 8, 7);

        $this->_closebutton->setSize(5, 5);
        $this->_closebutton->setPosition($this->sizeX - 3, 5.5);

        $this->_minbutton->setSize(5, 5);
        $this->_minbutton->setPosition($this->sizeX - 8, 5);

        $this->mainFrame->setSize($this->sizeX - 4, $this->sizeY - 8);
        $this->mainFrame->setPosition(2, -2);
    }

    protected function onDraw() {
        foreach ($this->getComponents() as $index => $component) {
            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\LinePlotter) {
                $this->addScriptToMain($component->getScript());
            }
        }



        $this->removeComponent($this->xml);
        // fixes the window to be center of the screen for first open. 
        $startPosX = (-1 * intval($this->getSizeX() / 2)) . ".0";
        $startPosY = intval($this->getSizeY() / 2) . ".0";

        $this->xml->setContent('    
        <script><!--
        #Include "MathLib" as MathLib
        
                       main () {     
                        declare Window <=> Page.GetFirstChild("' . $this->getId() . '");    
                        declare CMlLabel TitlebarText <=> (Page.GetFirstChild("TitlebarText") as CMlLabel);
                        declare showCoords = ' . $this->_showCoords . ';
                        
                        declare MoveWindow = False;
                        declare Scroll = False;
                        declare CloseWindow = False;   
                        declare isMinimized = False;   
                        declare Real CloseCounter = 1.0;
                        declare Real OpenCounter = 0.0;                        
                        declare CenterWindow = False;                      
                        
                        declare Vec3 LastDelta = <Window.RelativePosition.X, Window.RelativePosition.Y, 0.0>;
                        declare Vec3 DeltaPos = <0.0, 0.0, 0.0>;
                        declare Real lastMouseX = 0.0;
                        declare Real lastMouseY =0.0;         
                        declare active = False;
                        declare Text id = "' . $this->_title->getText() . '";        
                        declare persistent Vec3[Text] windowLastPos;
                        declare persistent Vec3[Text] windowLastPosRel;
			declare persistent Text windowActive = "";
			
                        ' . $this->dDeclares . '                          
                        
                         if (!windowLastPos.existskey(id)) {
                                windowLastPos[id] = <' . $startPosX . ', ' . $startPosY . ', 0.0>;
                                }
                         if (!windowLastPosRel.existskey(id)) {
                               windowLastPosRel[id] = <' . $startPosX . ', ' . $startPosY . ', 0.0>;
                                }
                        Window.PosnX = windowLastPos[id][0];
                        Window.PosnY = windowLastPos[id][1];
                        LastDelta = windowLastPosRel[id];
                        Window.RelativePosition = windowLastPosRel[id];                                                
                        windowActive = id;
			
                        while(True) {                                                               
                               if (windowActive == id) {
                                declare temp = Window.RelativePosition;
                                temp.Z = 20.0;
                                Window.RelativePosition = temp;
                            //    TitlebarText.SetText("true");
                                } else {
                                declare temp = Window.RelativePosition;
                                temp.Z = -50.0;
                                Window.RelativePosition = temp;				
                              //  TitlebarText.SetText("false");
                                }
                                
                               if (showCoords) {                               
                                    declare coords = "$fffX:" ^ (MouseX - Window.PosnX) ^ " Y:" ^ (MouseY - Window.PosnY + 3 );                                   
                                    TitlebarText.Value = coords;
                                }
                                 
				//TitlebarText.SetText( "X:" ^ LastDelta.X ^ "  Y:" ^ LastDelta.Y);
				       
				    
                                if (MoveWindow) {                                                                                                    
                                    DeltaPos.X = MouseX - lastMouseX;
                                    DeltaPos.Y = MouseY - lastMouseY;
                                   
				    if (Window.PosnX < -140.0) {                                    
					LastDelta.X = -140.0;	
					
				    } 
                                    if (Window.PosnX > 110.0) {                
					LastDelta.X = 110.0;
				       
                                    }
                                    if (Window.PosnY > 78.0) {                
				        LastDelta.Y = 78.0;
					
                                    }
				    if (Window.PosnY < -80.0) {                               
					LastDelta.Y = -80.0;
					
                                    }                          
				    
                                    LastDelta += DeltaPos;         
				     if (windowActive == id) {
					    LastDelta.Z = 20.0; 
				    }
                                    Window.RelativePosition = LastDelta;                                
                                    windowLastPos[id] = Window.AbsolutePosition;
                                    windowLastPosRel[id] = Window.RelativePosition;
                                    
                                    lastMouseX = MouseX;
                                    lastMouseY = MouseY; 
				    yield;
                                    }
				    
				   
                                                          
                                                  
                               if (MouseLeftButton == True) {
                                     
                       
                                        foreach (Event in PendingEvents) {
                                                
                                               
                                                       if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "Titlebar")  {                                                          
                                                            lastMouseX = MouseX;
                                                            lastMouseY = MouseY;   
                                                            MoveWindow = True;                                                      
							    windowActive = id;
                                                        } 

                                           
                                                        if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "Close") {
                                                          Window.Hide();
                                                        }                                                             
                                                        
							if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "MainWindow") {                                            
                                                             isMinimized = False;   
							      windowActive = id;
                                                         }                                  
                                     ' . $this->dLoop . ' 
                                         
                                                }
                                        }
                                        
                                else {
                                        MoveWindow = False;                                      
                                } 
                                
                                
                                yield;                        
                        }
                  
                  
                } 
                --></script>');
        $this->addComponent($this->xml);
        parent::onDraw();
    }

    function setDebug($bool) {
        if ($bool) {
            $this->_showCoords = 'True';
        }
    }

    function setText($text) {
        $this->_mainText->setText($text);
    }

    function setTitle($text) {
        $this->_title->setText($text);
    }

    function closeWindow() {
        $this->erase($this->getRecipient());
    }

    function addScriptToMain($script) {
        $this->dDeclares .= $script;
    }

    function addScriptToLoop($script) {
        $this->dLoop .= $script;
    }

    function addDropdown($name, $items) {


        $this->dDeclares .= '           
                            declare CMlFrame Frame' . $this->dCount . ' <=> (Page.GetFirstChild("' . $name . 'f") as CMlFrame);
                            declare CMlLabel Label' . $this->dCount . ' <=> (Page.GetFirstChild("' . $name . 'l") as CMlLabel);
                            declare CMlEntry Output' . $this->dCount . ' <=> (Page.GetFirstChild("' . $name . 'e") as CMlEntry);
                            Frame' . $this->dCount . '.Hide();
     ';


        $this->dLoop .= ' 
                            if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "' . $name . 'l") { 
                                    Frame' . $this->dCount . '.Show();
                           }
            ';





        $x = 0;
        foreach ($items as $item) {
            $this->dLoop .= '
                             if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "' . $name . $x . '") {                           
                                           Label' . $this->dCount . '.Value = "' . $item . '";
                                           Output' . $this->dCount . '.Value = "' . $x . '";
                                           Frame' . $this->dCount . '.Hide();
                            }      
                      ';
            $x++;
        }
        $this->dCount++;
    }

    function destroy() {
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->_closeAction);
        $this->_windowFrame->clearComponents();
        $this->_windowFrame->destroy();
        $this->mainFrame->destroy();

        $this->clearComponents();
        $this->_closeAction = null;
        parent::destroy();
    }

}

?>
