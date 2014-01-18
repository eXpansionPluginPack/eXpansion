<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;

class RatingsWidget extends \ManiaLive\Gui\Window {

    protected $xml, $frame, $starFrame, $move, $gauge;
    protected $stars = array();

    protected function onConstruct() {
        parent::onConstruct();
        $this->frame = new \ManiaLive\Gui\Controls\Frame(2, 0);
        $this->frame->setAlign("left", "top");
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(20, 20));
        $this->addComponent($this->frame);

        $this->starFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->starFrame->setAlign("left", "top");
        $this->starFrame->setSize(40, 4);
        $this->frame->addComponent($this->starFrame);


        $this->move = new \ManiaLib\Gui\Elements\Quad(30, 14);
        $this->move->setAlign("right", "center");
        $this->move->setStyle("Icons128x128_Blink");
        $this->move->setSubStyle("ShareBlink");
        $this->move->setScriptEvents();
        $this->move->setId("enableMove");
        $this->move->setPosition(2, -3);
        $this->addComponent($this->move);
        $this->gauge = new \ManiaLive\Gui\Elements\Xml();

        $this->xml = new \ManiaLive\Gui\Elements\Xml();
        $this->addComponent($this->xml);
        $this->setAlign("center", "center");
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    function onDraw() {

        $this->removeComponent($this->xml);
        $this->xml->setContent('    
        <script><!--
               
                       main () {     
                        declare Window <=> Page.GetFirstChild("' . $this->getId() . '");                 
                        declare MoveWindow = False;                                     
                        declare CMlQuad  quad <=> (Page.GetFirstChild("enableMove") as CMlQuad);      
                        declare Vec3 LastDelta = <Window.RelativePosition.X, Window.RelativePosition.Y, 0.0>;
                        declare Vec3 DeltaPos = <0.0, 0.0, 0.0>;
                        declare Real lastMouseX = 0.0;
                        declare Real lastMouseY =0.0;                           
                        declare Text id = "Map Ratings";      
                        
                        declare persistent Boolean exp_enableHudMove = False;
                        declare persistent Vec3[Text] windowLastPos;
                        declare persistent Vec3[Text] windowLastPosRel;
                        declare persistent Boolean[Text] widgetVisible;
			
			if (!widgetVisible.existskey(id)) {
				 widgetVisible[id] =  True;
			}                                                                       
                         if (!windowLastPos.existskey(id)) {
                                windowLastPos[id] = <157.0, 52.0, 0.0>;
                               }
                         if (!windowLastPosRel.existskey(id)) {
                                windowLastPosRel[id] = <157.0, 52.0, 0.0>;
                              }
                        Window.PosnX = windowLastPos[id][0];
                        Window.PosnY = windowLastPos[id][1];
                        LastDelta = windowLastPosRel[id];
                        Window.RelativePosition = windowLastPosRel[id];                                                
                        
                        while(True) {    
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
        parent::onDraw();
    }

    function destroy() {
        parent::destroy();
    }

    function setStars($number, $total) {
        $this->frame->clearComponents();
        $login = $this->getRecipient();

        $test = ($number / 6) * 100;
        $color = "fff";
        if ($test < 50)
            $color = "f00";
        if ($test >= 50)
            $color = "ff0";
        if ($test > 70)
            $color = "0f0";

        $this->gauge->setContent('<gauge scale="0.7" sizen="35 10" drawblockbg="1" color="' . $color . '" drawbg="0" rotation="0" posn="-24 0" grading="1" ratio="' . ($number / 5) . '" centered="0" />');
        $this->frame->addComponent($this->gauge);

        $score = ($number / 6) * 100;
        $score = round($score);


        $info = new \ManiaLib\Gui\Elements\Label();
        $info->setTextSize(1);
        $info->setTextColor('fff');
        $info->setAlign("right");
        $info->setTextEmboss();
        $info->setText($score . "% (" . $total . " " . __("votes", $login) . ")");
        $info->setScale(0.8);
        $info->setPosY(-7);
        $this->frame->addComponent($info);
        $this->redraw();
    }

}

?>
