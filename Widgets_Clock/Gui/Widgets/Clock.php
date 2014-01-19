<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Clock\Gui\Widgets;

class Clock extends \ManiaLive\Gui\Window {

    protected $xml;
    protected $clock;
    protected $clockBg;
    
    protected $date;
    protected $nameBg;

    protected function onConstruct() {
        parent::onConstruct();
        
        $this->clockBg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(22, 5);
        $this->addComponent($this->clockBg);
        $this->clockBg->setPosition(-6, -6);
        
        $this->clock = new \ManiaLib\Gui\Elements\Label();
        $this->clock->setId('clock');
        $this->clock->setAlign("left", "top");
        $this->clock->setPosition(0, -4);
        $this->clock->setTextColor('fff');
        $this->clock->setScale(0.8);
        $this->clock->setStyle('TextCardScores2');
        //$this->clock->setTextPrefix('$s');
        $this->addComponent($this->clock);

        
        $this->nameBg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(57, 5);
        $this->addComponent($this->nameBg);
        $this->nameBg->setPosition(-6, -1);
        
        $this->date = new \ManiaLib\Gui\Elements\Label(50, 6);
        $this->date->setId('date');
        $this->date->setAlign("left", "top");
        $this->date->setPosition(0, 1);
        $this->date->setTextColor('fff');
        $this->date->setTextPrefix('$s');
        $this->addComponent($this->date);

        $move = new \ManiaLib\Gui\Elements\Quad(60, 12);
        $move->setStyle("Icons128x128_Blink");
        $move->setSubStyle("ShareBlink");
        $move->setScriptEvents();
        $move->setId("enableMove");
        $this->addComponent($move);

        $this->setAlign("left", "top");
        $this->xml = new \ManiaLive\Gui\Elements\Xml();
        $this->xml->setContent('    
        <script><!--
        #Include "TextLib" as TextLib
        
                       main () {     
                        declare Window <=> Page.GetFirstChild("' . $this->getId() . '");                 
                        declare MoveWindow = False;                       
                        declare CMlLabel lbl_clock <=> (Page.GetFirstChild("clock") as CMlLabel);
                        declare CMlLabel lbl_date <=> (Page.GetFirstChild("date") as CMlLabel);                        
                        declare CMlQuad  quad <=> (Page.GetFirstChild("enableMove") as CMlQuad);      
                        declare Vec3 LastDelta = <Window.RelativePosition.X, Window.RelativePosition.Y, 0.0>;
                        declare Vec3 DeltaPos = <0.0, 0.0, 0.0>;
                        declare Real lastMouseX = 0.0;
                        declare Real lastMouseY =0.0;                           
                        declare Text id = "Clock";      
                        
                        declare persistent Boolean exp_enableHudMove = False;
                        declare persistent Vec3[Text] windowLastPos;
                        declare persistent Vec3[Text] windowLastPosRel;
                        
			declare persistent Boolean[Text] widgetVisible;
			    if (!widgetVisible.existskey(id)) {
				 widgetVisible[id] =  True;
			    }
			 
                         if (!windowLastPos.existskey(id)) {
                                windowLastPos[id] = <-159.0, 89.0, 0.0>;
                               }
                         if (!windowLastPosRel.existskey(id)) {
                                windowLastPosRel[id] = <-159.0, 89.0, 0.0>;
                              }
                        Window.PosnX = windowLastPos[id][0];
                        Window.PosnY = windowLastPos[id][1];
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
                                    
                                    
                                lbl_clock.SetText(""^TextLib::SubString(CurrentLocalDateText, 11, 2)^":"^TextLib::SubString(CurrentLocalDateText, 14, 2)^":"^TextLib::SubString(CurrentLocalDateText, 17, 2));
                                yield;                        
                            }
                  
                  
                } 
                --></script>');
        $this->addComponent($this->xml);
    }

    public function setServername($name) {
        $this->date->setText($name);
    }

    function destroy() {
        $this->clearComponents();
        parent::destroy();
    }

}

?>
