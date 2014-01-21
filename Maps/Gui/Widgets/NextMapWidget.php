<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Widgets;

class NextMapWidget extends \ManiaLive\Gui\Window {

    private $bg;
    private $mapName;
    private $mapAuthor;
    private $labelName;
    private $labelAuthor;
    private $xml;

    /** @var \DedicatedApi\Structures\Map */
    private $map;

    protected function onConstruct() {
	$frame = new \ManiaLive\Gui\Controls\Frame();
	$frame->setPosY(0);
	// $frame->setLayout(new \ManiaLib\Gui\Layouts\Column());
	$login = $this->getRecipient();

    $bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(42, 15);
    $bg->setPosition(-38, -7);
    $this->addComponent($bg);
    
	$label = new \ManiaLib\Gui\Elements\Label(30);
	$label->setText('$ddd' . __('Next map', $login));
	$label->setTextEmboss(true);
	$label->setAlign("right", "top");
	$label->setPosX(0);
	$this->addComponent($label);

	$row = new \ManiaLive\Gui\Controls\Frame(0, -4);
	$this->labelName = new \ManiaLib\Gui\Elements\Label(23, 7);
	$this->labelName->setText('$ddd' . $this->mapName);
	$this->labelName->setAlign("right", "top");
	$this->labelName->setPosX(-4);
	$this->labelName->setPosY(-1);
	$row->addComponent($this->labelName);

	$icon = new \ManiaLib\Gui\Elements\Quad(6, 6);
	$icon->setStyle("UIConstructionSimple_Buttons");
	$icon->setSubStyle("Challenge");
	$icon->setAlign("left", "top");
	$icon->setPosX(-4);
	$row->addComponent($icon);
	$frame->addComponent($row);

	$row = new \ManiaLive\Gui\Controls\Frame(0, -8);
	$this->labelAuthor = new \ManiaLib\Gui\Elements\Label(23, 7);
	$this->labelAuthor->setText('$ddd' . $this->mapAuthor);
	$this->labelAuthor->setAlign("right", "top");
	$this->labelAuthor->setPosX(-4);
	$this->labelAuthor->setPosY(-1);
	$row->addComponent($this->labelAuthor);

	$icon = new \ManiaLib\Gui\Elements\Quad(6, 6);
	$icon->setStyle("UIConstructionSimple_Buttons");
	$icon->setSubStyle("Author");
	$icon->setAlign("left", "top");
	$icon->setPosX(-4);
	$row->addComponent($icon);
	$frame->addComponent($row);

	$this->addComponent($frame);

	$move = new \ManiaLib\Gui\Elements\Quad(60, 14);
	$move->setAlign("right", "top");
	$move->setStyle("Icons128x128_Blink");
	$move->setSubStyle("ShareBlink");
	$move->setScriptEvents();
	$move->setId("enableMove");
	$this->addComponent($move);

	$this->setScale(0.8);
	$this->xml = new \ManiaLive\Gui\Elements\Xml();
    }

    function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
    }

    function onShow() {
	
    }

    public function onDraw() {
	$this->removeComponent($this->xml);
	$this->xml->setContent('    
        <script><!--
               
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
                        declare Text id = "NextMapWidget";      
                        
                        declare persistent Boolean exp_enableHudMove = False;
                        declare persistent Vec3[Text] windowLastPos;
                        declare persistent Vec3[Text] windowLastPosRel;
                        
			declare persistent Boolean[Text] widgetVisible;
			    if (!widgetVisible.existskey(id)) {
				 widgetVisible[id] =  True;
			    }                                          
                         if (!windowLastPos.existskey(id)) {
                                windowLastPos[id] = <158.0, 62.0, 0.0>;
                               }
                         if (!windowLastPosRel.existskey(id)) {
                                windowLastPosRel[id] = <158.0, 62.0, 0.0>;
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

    function setMap(\DedicatedApi\Structures\Map $map) {
	$this->map = $map;
	$this->labelName->setText('$ddd' . $this->map->name);
	$this->labelAuthor->setText('$ddd' . $this->map->author);
    }

    function destroy() {
	$this->clearComponents();
	parent::destroy();
    }

}

?>
