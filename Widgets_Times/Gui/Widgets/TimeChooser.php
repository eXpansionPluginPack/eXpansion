<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Times\Gui\Widgets;

class TimeChooser extends \ManiaLive\Gui\Window {

    public static $plugin = null;
    protected $frame;
    protected $btnBest, $btnPersonal, $btnNone, $btnAudio;

    protected function onConstruct() {
	parent::onConstruct();
	$login = $this->getRecipient();
	$this->setAlign("center", "center");

	$this->frame = new \ManiaLive\Gui\Controls\Frame(0, -3);
	$this->frame->setAlign("center", "top");
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(20, 40));
	$this->addComponent($this->frame);

	$this->btnBest = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(50, 6);
	$this->btnBest->setAction($this->createAction(array(self::$plugin, "setMode"), TimePanel::Mode_BestOfAll));
	$this->btnBest->setText(__("Top1", $login));
	$this->btnBest->setScale(0.4);
	$this->btnBest->colorize('aaaa');
	$this->btnBest->setAlign("center");
	$this->frame->addComponent($this->btnBest);

	$this->btnPersonal = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(50, 6);
	$this->btnPersonal->setAction($this->createAction(array(self::$plugin, "setMode"), TimePanel::Mode_PersonalBest));
	$this->btnPersonal->setText(__("Personal Best", $login));
	$this->btnPersonal->colorize('fff8');
	$this->btnPersonal->setScale(0.4);
	$this->btnPersonal->setAlign("center");
	$this->frame->addComponent($this->btnPersonal);

	$this->btnNone = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(50, 6);
	$this->btnNone->setAction($this->createAction(array(self::$plugin, "setMode"), TimePanel::Mode_None));
	$this->btnNone->setText(__("Off", $login));
	$this->btnNone->colorize('aaaa');
	$this->btnNone->setScale(0.4);
	$this->btnNone->setAlign("center");
	$this->frame->addComponent($this->btnNone);

	$this->btnAudio = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(50, 6);
	$this->btnAudio->setText(__("Audio: off", $login));
	$this->btnAudio->colorize('a00a');
	$this->btnAudio->setScale(0.4);
	$this->btnAudio->setAlign("center");
	$this->frame->addComponent($this->btnAudio);

	$move = new \ManiaLib\Gui\Elements\Quad(24, 20);
	$move->setAlign("center", "top");
	$move->setStyle("Icons128x128_Blink");
	$move->setSubStyle("ShareBlink");
	$move->setScriptEvents();
	$move->setId("enableMove");
	$this->addComponent($move);

	$this->xml = new \ManiaLive\Gui\Elements\Xml();
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
                        declare Text id = "CheckpointsTracker_Chooser";      
                        
                        declare persistent Boolean exp_enableHudMove = False;
                        declare persistent Vec3[Text] windowLastPos;
                        declare persistent Vec3[Text] windowLastPosRel;			
			declare persistent Boolean[Text] widgetVisible;
			
		                
                         if (!windowLastPos.existskey(id)) {
                                windowLastPos[id] = <26.0, -74.00, 0.0>;
                               }
                         if (!windowLastPosRel.existskey(id)) {
                                windowLastPosRel[id] = <26.00, -74.00, 0.0>;
                              }
                        Window.PosnX = windowLastPos[id][0];
                        Window.PosnY = windowLastPos[id][1];
                        LastDelta = windowLastPosRel[id];
                        Window.RelativePosition = windowLastPosRel[id];                                                
                        
                        while(True) { 
			 if (!widgetVisible.existskey(id)) {
				 widgetVisible[id] =  True;
			    }   
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

    function updatePanelMode($mode, $audiomode) {
	$login = $this->getRecipient();
	$this->btnBest->colorize('aaa8');
	$this->btnPersonal->colorize('aaa8');
	$this->btnNone->colorize('aaa8');

	if ($mode == TimePanel::Mode_BestOfAll)
	    $this->btnBest->colorize('fffe');

	if ($mode == TimePanel::Mode_PersonalBest)
	    $this->btnPersonal->colorize('fffe');

	if ($mode == TimePanel::Mode_None)
	    $this->btnNone->colorize('fffe');

	if ($audiomode) {
	    $this->btnAudio->setAction($this->createAction(array(self::$plugin, "setAudioMode"), false));
	    $this->btnAudio->setText(__("Audio: on", $login));
	    $this->btnAudio->colorize('0a0a');
	} else {
	    $this->btnAudio->setAction($this->createAction(array(self::$plugin, "setAudioMode"), true));
	    $this->btnAudio->setText(__("Audio: off", $login));
	    $this->btnAudio->colorize('a00a');
	}
	$this->redraw($this->getRecipient());
    }

    function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
    }

    function onShow() {
	
    }

    function destroy() {
	$this->clearComponents();
	parent::destroy();
    }

}

?>
