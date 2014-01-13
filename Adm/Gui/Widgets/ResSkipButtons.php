<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Widgets;

class ResSkipButtons extends \ManiaLive\Gui\Window {

    protected $xml;
    public $btn_res;
    public $btn_skip;

    protected function onConstruct() {
        parent::onConstruct();

        $this->btn_res = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetButton();
        $this->btn_res->setAlign("left", "center2");
        $this->btn_res->setText(array('$ff0Pay', '$fffto', '$ff0Restart'));        
        $this->addComponent($this->btn_res);

        $this->btn_skip = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetButton();
        $this->btn_skip->setAlign("left", "center2");
        $this->btn_skip->setText(array('$ff0Pay', '$fffto', '$ff0Skip'));
        $this->btn_skip->setPosX(16);
        $this->addComponent($this->btn_skip);

        $move = new \ManiaLib\Gui\Elements\Quad(36, 18);
        $move->setAlign("left", "top");
        $move->setPosition(-10,6);
        $move->setStyle("Icons128x128_Blink");
        $move->setSubStyle("ShareBlink");
        $move->setScriptEvents();
        $move->setId("enableMove");
        $this->addComponent($move);

        $this->setAlign("left", "top");
        $this->setScale(0.8);
        $this->xml = new \ManiaLive\Gui\Elements\Xml();
        $this->xml->setContent('    
        <script><!--
        #Include "TextLib" as TextLib
        
                       main () {     
                        declare Window <=> Page.GetFirstChild("' . $this->getId() . '");                 
                        declare MoveWindow = False;                        
                        declare CMlQuad  quad <=> (Page.GetFirstChild("enableMove") as CMlQuad);      
                        declare Vec3 LastDelta = <Window.RelativePosition.X, Window.RelativePosition.Y, 0.0>;
                        declare Vec3 DeltaPos = <0.0, 0.0, 0.0>;
                        declare Real lastMouseX = 0.0;
                        declare Real lastMouseY = 0.0;                           
                        declare Text id = "Skip and Res Buttons";      
                        
                        declare persistent Boolean exp_enableHudMove = False;
                        declare persistent Vec3[Text] windowLastPos;
                        declare persistent Vec3[Text] windowLastPosRel;
                        
			declare persistent Boolean[Text] widgetVisible;
			    if (!widgetVisible.existskey(id)) {
				 widgetVisible[id] =  True;
			    }
			 
                         if (!windowLastPos.existskey(id)) {
                                windowLastPos[id] = <116.0, -65.0, 0.0>;
                               }
                         if (!windowLastPosRel.existskey(id)) {
                                windowLastPosRel[id] = <116.0, -65.0, 0.0>;
                              }
                        //Window.PosnX = windowLastPos[id][0];
                        //Window.PosnY = windowLastPos[id][1];
                        LastDelta = windowLastPosRel[id];
                        Window.RelativePosition = windowLastPosRel[id];                                                
                        
                        while(True) {    
			 if (!widgetVisible.existskey(id)) {
				 widgetVisible[id] =  True;
			    }   
                        if (exp_enableHudMove == True) {
			    Window.Show();
                            quad.Show();                            
                            }
                        else {
                            quad.Hide();
                        }
			if (widgetVisible[id] == True) {
			    Window.Show();
			}
			else {
			    Window.Hide();
			    yield;
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
                                    DeltaPos.X = MouseX - lastMouseX;
                                    DeltaPos.Y = MouseY - lastMouseY;
                                                                      
                                    LastDelta += DeltaPos;
                                    LastDelta.Z = 3.0;
                                    Window.RelativePosition = LastDelta;
                                    windowLastPos[id] = Window.AbsolutePosition;
                                    windowLastPosRel[id] = Window.RelativePosition;
                                    
                                    lastMouseX = MouseX;
                                    lastMouseY = MouseY;                            
                                    }
                                    
                               yield;                        
                            }
                  
                  
                } 
                --></script>');
       $this->addComponent($this->xml);
    }

    public function setActions($res, $skip) {
        $this->btn_res->setAction($res);
        $this->btn_skip->setAction($skip);
    }

    function destroy() {
        $this->clearComponents();
        parent::destroy();
    }

}

?>
