<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLivePlugins\eXpansion\MapRatings\Gui\Controls\RateButton;

class EndMapRatings extends \ManiaLive\Gui\Window {

    protected $label, $xml, $frame;
    protected $b0, $b1, $b2, $b3, $b4, $b5;
    public static $parentPlugin;

    protected function onConstruct() {
	parent::onConstruct();

	$login = $this->getRecipient();

	$this->xml = new \ManiaLive\Gui\Elements\Xml();
	$this->addComponent($this->xml);


	$this->label = new \ManiaLib\Gui\Elements\Label(70, 9);
	$this->label->setStyle("TextRankingsBig");
	$this->label->setTextEmboss();
	$this->label->setText(__("Please rate the map!", $login));
	$this->label->setAlign("center", "top");
	$this->label->setPosY(9);
	$this->addComponent($this->label);

	$this->frame = new \ManiaLive\Gui\Controls\Frame(0, -3);
	$this->frame->setSize(70, 30);
	$this->frame->setAlign("center", "top");
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
	$this->addComponent($this->frame);

	$this->b0 = new RateButton($login, self::$parentPlugin, 0);
	$this->frame->addComponent($this->b0);

	$this->b1 = new RateButton($login, self::$parentPlugin, 1);
	$this->frame->addComponent($this->b1);

	$this->b2 = new RateButton($login, self::$parentPlugin, 2);
	$this->frame->addComponent($this->b2);

	$this->b3 = new RateButton($login, self::$parentPlugin, 3);
	$this->frame->addComponent($this->b3);

	$this->b4 = new RateButton($login, self::$parentPlugin, 4);
	$this->frame->addComponent($this->b4);

	$this->b5 = new RateButton($login, self::$parentPlugin, 5);
	$this->frame->addComponent($this->b5);

	$move = new \ManiaLib\Gui\Elements\Quad(60, 14);
	$move->setAlign("center", "top");
	$move->setStyle("Icons128x128_Blink");
	$move->setSubStyle("ShareBlink");
	$move->setScriptEvents();
	$move->setId("enableMove");
	$this->addComponent($move);
	$this->setSize(90, 30);
	$this->setAlign("center", "top");
	$this->setPosition(0, -50);
    }

    function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
	$this->frame->setPosX(- ($this->frame->sizeX / 2));
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
                        declare Text id = "MapRatings at podium";      
                        
                        declare persistent Boolean exp_enableHudMove = False;
                        declare persistent Vec3[Text] windowLastPos;
                        declare persistent Vec3[Text] windowLastPosRel;
                        
			declare persistent Boolean[Text] widgetVisible;
			    if (!widgetVisible.existskey(id)) {
				 widgetVisible[id] =  True;
			    }                                          
                         if (!windowLastPos.existskey(id)) {
                                windowLastPos[id] = <0.0, -50.0, 0.0>;
                               }
                         if (!windowLastPosRel.existskey(id)) {
                                windowLastPosRel[id] = <0.0, -50.0, 0.0>;
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

}

?>
