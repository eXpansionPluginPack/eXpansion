<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;

class RatingsWidget extends \ManiaLive\Gui\Window {

    protected $xml, $frame, $starFrame, $move, $gauge;
    protected $stars = array();

    protected function onConstruct() {
        parent::onConstruct();
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setAlign("left", "top");
        // $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(20, 20));
        $this->addComponent($this->frame);

        $bg = new \ManiaLib\Gui\Elements\Quad(34, 11);
        $bg->setAlign("left", "center");
        $bg->setStyle("Bgs1InRace");
        $bg->setSubStyle("NavButtonBlink");
        $bg->setPosition(-30, -7);
        $this->addComponent($bg);

        $label = new \ManiaLib\Gui\Elements\Label(30);
        $label->setText('$s' . __('Map Rating'));
        $label->setTextColor("ffff");
        $label->setStyle("TextRaceMessage");
        $label->setAlign("center", "top");
        $label->setPosition(-12, -3.5);
        $label->setTextSize(1.5);
        $this->addComponent($label);

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
        parent::onDraw();
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
                                windowLastPos[id] = <157.0, 76.0, 0.0>;
                               }
                         if (!windowLastPosRel.existskey(id)) {
                                windowLastPosRel[id] = <157.0, 76.0, 0.0>;
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
    }

    function destroy() {
        parent::destroy();
    }

    function setStars($number, $total) {
        $this->frame->clearComponents();
        $login = $this->getRecipient();

        $test = ($number / 6) * 100;
        $color = "fff";
        if ($test < 30)
            $color = "0ad";
        if ($test >= 30)
            $color = "2af";
        if ($test > 60)
            $color = "0cf";

        $this->gauge->setContent('<gauge scale="0.7" sizen="45 15" drawblockbg="1" color="' . $color . '" drawbg="0" rotation="0" posn="-29 -3" grading="1" ratio="' . ($number / 5) . '" centered="0" />');
        $this->frame->addComponent($this->gauge);

        $score = ($number / 5) * 100;
        $score = round($score);


        $info = new \ManiaLib\Gui\Elements\Label();
        $info->setTextSize(1);
        $info->setTextColor('fff');
        $info->setAlign("center", "center");
        $info->setTextEmboss();
        $info->setText($score . "% (" . $total . ")");
        $info->setPosition(-12, -8.5);
        $this->frame->addComponent($info);
        $this->redraw();
    }

}

?>
