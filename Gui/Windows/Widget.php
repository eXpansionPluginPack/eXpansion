<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Config;

/**
 * @abstract
 */
class Widget extends \ManiaLive\Gui\Window {

    private $dDeclares = "";
    private $dLoop = "";
    private $wLoop = "";
    private $_name = "widget";
    private $move;
    private $axisDisabled = "";

    protected function onConstruct() {
        parent::onConstruct();

        $this->move = new \ManiaLib\Gui\Elements\Quad(45, 7);
        $this->move->setAlign("left", "center");
        $this->move->setStyle("Icons128x128_Blink");
        $this->move->setSubStyle("ShareBlink");
        $this->move->setScriptEvents();
        $this->move->setId("enableMove");        
        $this->addComponent($this->move);

        $this->xml = new \ManiaLive\Gui\Elements\Xml();
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->move->setSize($this->sizeX, $this->sizeY);
        $this->move->setPosZ(20);
    }

    private $nbButton = 0;
    private $minIdButton = 999999;
    private $maxIdButton = 0;
    private $aButton = null;

    private function detectElements($components) {
        $buttonScript = null;
        foreach ($components as $index => $component) {
            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\LinePlotter) {
                $this->addScriptToMain($component->getScript());
            }

            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\Pager) {
                $this->addScriptToMain($component->getScriptDeclares());
                $this->addScriptToWhile($component->getScriptMainLoop());
            }

            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\Button) {

                $decl = $component->getScriptDeclares();
                if ($this->nbButton == 0) {
                    if (!empty($decl)) {
                        $this->addScriptToMain($decl);
                        $this->addScriptToWhile($component->getScriptMainLoop());
                        $this->nbButton++;
                        $this->aButton = $component;
                    }
                }
                if (!empty($decl)) {
                    if ($this->maxIdButton < $component->getButtonId())
                        $this->maxIdButton = $component->getButtonId();
                    if ($this->minIdButton > $component->getButtonId())
                        $this->minIdButton = $component->getButtonId();
                }
            }
            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\Dropdown) {
                $this->addScriptToMain($component->getScriptDeclares($this->dIndex));
                $this->addScriptToLoop($component->getScriptMainLoop($this->dIndex));
                $this->dIndex++;
            }

            if ($component instanceof \ManiaLive\Gui\Container) {
                $this->detectElements($component->getComponents());
            }
        }
    }

    private function getNumber($number) {
        return number_format((float) $number, 2, '.', '');
    }

    protected function onDraw() {
        $this->nbButton = 0;
        $this->dIndex = 0;
        //$this->dDeclares = "";
        //$this->dLoop = "";

        $this->detectElements($this->getComponents());
        if ($this->aButton != null) {
            $this->addScriptToMain($this->aButton->getHideMainLoop($this->minIdButton, $this->maxIdButton));
        }

        $this->removeComponent($this->xml);
        $deltaX = "DeltaPos.X = MouseX - lastMouseX;";
        $deltaY = "DeltaPos.Y = MouseY - lastMouseY;";

        if ($this->axisDisabled == "x")
            $deltaX = "";
        if ($this->axisDisabled == "y")
            $deltaY = "";


        $this->xml->setContent('    
        <script><!--
        #Include "MathLib" as MathLib
        
                       main () {     
                        declare Window <=> Page.GetFirstChild("' . $this->getId() . '");                 
                        declare MoveWindow = False;                      
                        declare CMlQuad  quad <=> (Page.GetFirstChild("enableMove") as CMlQuad);      
                        declare Vec3 LastDelta = <Window.RelativePosition.X, Window.RelativePosition.Y, 0.0>;
                        declare Vec3 DeltaPos = <0.0, 0.0, 0.0>;
                        declare Real lastMouseX = 0.0;
                        declare Real lastMouseY =0.0;                                                   
                        
                        declare persistent Boolean exp_enableHudMove = False;
                        declare persistent Vec3[Text] windowLastPos;
                        declare persistent Vec3[Text] windowLastPosRel;			
			declare persistent Boolean[Text] widgetVisible;      
                        
                        declare Text id = "' . $this->_name . '";        
                        
                        // external declares
                        ' . $this->dDeclares . '    
                        // external declares ends

                        if (!widgetVisible.existskey(id)) {
				 widgetVisible[id] =  True;
			}            
                        if (!windowLastPos.existskey(id)) {
                                windowLastPos[id] = <' . $this->getNumber($this->getPosX()) . ', ' . $this->getNumber($this->getPosY()) . ', 0.0>;
                               }
                        if (!windowLastPosRel.existskey(id)) {
                                windowLastPosRel[id] = <' . $this->getNumber($this->getPosX()) . ', ' . $this->getNumber($this->getPosY()) . ', 0.0>;
                        }
                        Window.PosnX = windowLastPos[id][0];
                        Window.PosnY = windowLastPos[id][1];
                        LastDelta = windowLastPosRel[id];
                        Window.RelativePosition = windowLastPosRel[id];    
                        
                                              
			
                        while(True) {                                                               
                        yield;

                        // external loop stuff
                        '
                . $this->wLoop .
                '
                        // external loop ends
                        
                        if (!widgetVisible.existskey(id)) {
				 widgetVisible[id] =  True;
			    }   
			    if (widgetVisible[id] == True) {
				Window.Show();
			    }
			    else {
			        Window.Hide();
			    }
			    
                        if (exp_enableHudMove == True) {
                                quad.Show();  
                            }
                        else {
                            quad.Hide();    
			   
                        }		    			    			    
                        if (exp_enableHudMove == True && MouseLeftButton == True) {
                                     
                                foreach (Event in PendingEvents) {
                                        if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "enableMove")  {
                                            lastMouseX = MouseX;
                                            lastMouseY = MouseY;                                                            
                                            MoveWindow = True;                                                           
                                       }                                                                                                  
                               }
                        }
                        else {
                            MoveWindow = False;                                                                          
                        }
                                        
                        if (MoveWindow) {                            
                            ' . $deltaX . "\n" . $deltaY . '                                           
                            LastDelta += DeltaPos;
                            LastDelta.Z = 3.0;
                            Window.RelativePosition = LastDelta;
                            windowLastPos[id] = Window.AbsolutePosition;
                            windowLastPosRel[id] = Window.RelativePosition;

                            lastMouseX = MouseX;
                            lastMouseY = MouseY;                            
                       }
                  }        


           }
                        
                --></script>');
        $this->addComponent($this->xml);
        parent::onDraw();
    }

    function setName($text, $parameter = "") {
        $this->_name = $text;
    }

    function closeWindow() {
        $this->erase($this->getRecipient());
    }

    function addScriptToMain($script) {
        $this->dDeclares .= $script;
    }

    function addScriptToWhile($script) {
        $this->wLoop .= $script;
    }

    function addScriptToLoop($script) {
        $this->dLoop .= $script;
    }

    function destroy() {        
        $this->clearComponents();
        parent::destroy();
    }

    function setDisableAxis($axis) {
        $this->axisDisabled = $axis;
    }

}

?>
