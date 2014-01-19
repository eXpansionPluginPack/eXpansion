<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Times\Gui\Widgets;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;

class TimePanel extends \ManiaLive\Gui\Window {

    const Mode_BestOfAll = 1;
    const Mode_PersonalBest = 2;
    const Mode_None = 3;
    const Mode_All = 4;

    protected $checkpoint;
    protected $time;
    protected $audio;
    protected $xml;
    protected $frame;
    protected $position;
    protected $top1;
    private $totalCp = 0;
    private $bestRun = array();
    private $currentRun = array();
    private $lastFinish = -1;
    private $counter = 1;

    /** @var \ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record[] */
    public static $localrecords = array();
    public static $dedirecords = array();

    protected function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();
        $this->setAlign("center", "center");
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setAlign("center", "center");
        $this->frame->setSize(40, 7);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line(40, 7));
        $this->addComponent($this->frame);

        $this->checkpoint = new \ManiaLib\Gui\Elements\Label(8, 4);
        $this->checkpoint->setTextColor("fff");
        $this->checkpoint->setAlign("left", "center");
        $this->checkpoint->setText('');
        $this->checkpoint->setId("Cp");
        $this->checkpoint->setScriptEvents();
        $this->frame->addComponent($this->checkpoint);

        $this->time = new \ManiaLib\Gui\Elements\Label(20, 4);
        $this->time->setAlign("left", "center");
        $this->time->setStyle("TextTitle2");
        $this->time->setText('');
        $this->time->setId("Label");
        $this->time->setScriptEvents();
        $this->frame->addComponent($this->time);

        $this->position = new \ManiaLib\Gui\Elements\Label(40, 4);
        $this->position->setAlign("left", "center");
        $this->position->setStyle("TextTitle2");
        $this->setPosX(-40);
        $this->addComponent($this->position);

        $this->top1 = new \ManiaLib\Gui\Elements\Label(40, 4);
        $this->top1->setAlign("left", "center");
        $this->top1->setStyle("TextTitle2");
        $this->setPosX(-80);
        $this->addComponent($this->top1);

        $this->audio = new \ManiaLib\Gui\Elements\Audio();
        $this->audio->setPosY(260);
        $this->addComponent($this->audio);

        $move = new \ManiaLib\Gui\Elements\Quad(45, 7);
        $move->setAlign("center", "center");
        $move->setStyle("Icons128x128_Blink");
        $move->setSubStyle("ShareBlink");
        $move->setScriptEvents();
        $move->setId("enableMove");
        $this->addComponent($move);

        $this->xml = new \ManiaLive\Gui\Elements\Xml();
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    function setMapInfo(\DedicatedApi\Structures\Map $map) {
        $this->totalCp = $map->nbCheckpoints;
    }

    function onDraw() {
        parent::onDraw();
        $login = $this->getRecipient();
        $this->removeComponent($this->xml);        
        $record = \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::getObjbyPropValue(self::$localrecords, "login", $login);
        $checkpoints = "[ -1 ]";
        if ($record instanceof \ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record) {
            $checkpoints = "[" . implode(",", $record->ScoreCheckpoints) . "]";
        }


        $this->xml->setContent('    
        <script><!--
               #Include "MathLib" as MathLib
               #Include "TextLib" as TextLib

                Text pad(Integer number, Integer pad) {
                    declare Text out = "";
                    out = "" ^ number;
                    if (number < 10 && pad == 3) {
                            out = "00" ^ number;
                            }
                    if (number < 10 && pad == 2) {
                            out = "0" ^ number;
                            }
                    if (number < 100 && pad == 3) {
                            out = "0" ^ number;
                            }
                    
                    return out;
                }


                Text TimeToText(Integer intime, Boolean test) {
                            declare time = MathLib::Abs(intime);
                            declare Integer cent = time % 1000;                           
                            time = time / 1000;
                            declare Integer sec = time % 60;
                            declare Integer min = time / 60;
                            declare Text sign = "";
                            if (intime < 0)  {
                                sign = "-";
                            }
                            return sign ^ pad(min,2) ^ ":" ^ pad(sec,2) ^ "." ^ pad(cent,3);                                                         
                }   
                
                
                            
                       main () {     
                        declare Window <=> Page.GetFirstChild("' . $this->getId() . '");                 
                        declare MoveWindow = False;                      
                        declare CMlQuad  quad <=> (Page.GetFirstChild("enableMove") as CMlQuad);      
                        declare Vec3 LastDelta = <Window.RelativePosition.X, Window.RelativePosition.Y, 0.0>;
                        declare Vec3 DeltaPos = <0.0, 0.0, 0.0>;
                        declare Real lastMouseX = 0.0;
                        declare Real lastMouseY =0.0;                           
                        declare Text id = "Checkpoints Tracker (panel)";      
                        
                        declare persistent Boolean exp_enableHudMove = False;
                        declare persistent Vec3[Text] windowLastPos;
                        declare persistent Vec3[Text] windowLastPosRel;			
			declare persistent Boolean[Text] widgetVisible;

                        declare CMlLabel Label <=> (Page.GetFirstChild("Label") as CMlLabel);                
                        declare CMlLabel Cp <=> (Page.GetFirstChild("Cp") as CMlLabel);
                        declare Integer[] Checkpoints = ' . $checkpoints . ';
                        declare Integer curCp = 0;
                        declare Integer totalCp = ' . $this->totalCp . ';

		        if (!widgetVisible.existskey(id)) {
				 widgetVisible[id] =  True;
			    }            
                         if (!windowLastPos.existskey(id)) {
                                windowLastPos[id] = <0.00, 40.00, 0.0>;
                               }
                         if (!windowLastPosRel.existskey(id)) {
                                windowLastPosRel[id] = <0.00, 40.00, 0.0>;
                              }
                        Window.PosnX = windowLastPos[id][0];
                        Window.PosnY = windowLastPos[id][1];
                        LastDelta = windowLastPosRel[id];
                        Window.RelativePosition = windowLastPosRel[id];                                                
                        
                          while (True) {
                            
                            yield;
                            if (!PageIsVisible || InputPlayer == Null) continue;
                            
                
                            foreach (Player in Players) {
                                if (Player.Login == InputPlayer.Login) {
                                    //    log(Player.RaceState);
                                    
                                    declare PrevCheckpointCount for Player = -1;
                                    if (PrevCheckpointCount != Player.CurRace.Checkpoints.count) {
                                            PrevCheckpointCount = Player.CurRace.Checkpoints.count;
                                            curCp = Player.CurRace.Checkpoints.count;
                                            
                                            if (curCp > 0 && curCp != totalCp) {
                                                if (Checkpoints.count > curCp && Checkpoints[curCp] != -1) {
                                                declare Integer diff = (Player.CurRace.Checkpoints[curCp-1] - Checkpoints[curCp-1]);
                                                log(diff);
                                                    declare Text color = "$f00";
                                                     if (diff <= 0) {
                                                        color = "$00f";
                                                    }
                                                    Label.SetText(color ^ TimeToText(diff, True));
                                               }
                                               else {                                               
                                                Label.SetText("$fff$s" ^ TimeToText(Player.CurRace.Checkpoints[curCp-1], True));
                                                }
                                                Cp.SetText(curCp ^ "/" ^ totalCp);
                                            } else {
                                                Label.SetText("");
                                                Cp.SetText("");
                                                }
                                             
                                        }
                                    }
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
                           
                           }                  
                } 
                --></script>');
        $this->addComponent($this->xml);
    }

    /*   public function onCheckpoint($login, $time, $cpIndex, $lap, $cpTotal, $mode, $playAudio) {
      $this->currentRun[$cpIndex] = $time;
      $this->checkpoint->setText("cp " . (($cpIndex % $cpTotal) + 1) . "/" . $cpTotal . "");

      $dedicp = array();
      $localcp = array();
      $dediTotal = 0;
      $localTotal = 0;

      $this->time->setTextColor('fffa');
      $this->time->setText(\ManiaLive\Utilities\Time::fromTM($time, false));



      if ($mode == self::Mode_BestOfAll) {
      $dedicp = array();
      $localcp = array();
      if (isset(self::$dedirecords[0]))
      if (array_key_exists('Checks', self::$dedirecords[0])) {
      $dedicp = explode(",", self::$dedirecords[0]['Checks']);
      $dediTotal = end($dedicp);
      }
      if (isset(self::$localrecords[0])) {
      $localcp = self::$localrecords[0]->ScoreCheckpoints;
      $localTotal = self::$localrecords[0]->time;
      }
      }

      if ($mode == self::Mode_PersonalBest) {
      $dedicp = array();
      foreach (self::$dedirecords as $dedirec) {
      if ($dedirec['Login'] == $login) {
      if (array_key_exists('Checks', $dedirec)) {
      $dedicp = explode(",", $dedirec['Checks']);
      $dediTotal = end($dedicp);
      }
      break;
      }
      }
      $record = \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::getObjbyPropValue(self::$localrecords, "login", $login);
      $localcp = array();
      if ($record) {
      $localcp = $record->ScoreCheckpoints;
      $localTotal = $record->time;
      }
      }

      // determin whenever to use deditime or localtime from the checkpoints
      $deditime = 0;
      $localtime = 0;
      $diff = null;


      if (sizeof($dedicp) > 0) {
      if (array_key_exists($cpIndex, $dedicp)) {
      $deditime = $dedicp[$cpIndex];
      }
      }

      if (sizeof($localcp) > 0) {
      if (array_key_exists($cpIndex, $localcp)) {
      $localtime = $localcp[$cpIndex];
      }
      }
      // use dedimania times in firstplace
      if ($dediTotal != 0) {
      $diff = $deditime;
      // except if localrecord is set and is faster than dedimania time
      if (($localTotal != 0) && ($localTotal < $dediTotal)) {
      $diff = $localtime;
      }
      // if no dedimania record, try local record instead
      } elseif ($localTotal != 0) {
      $diff = $localtime;
      }

      // if diff is still not set, check for this rounds best time
      if ($diff === null) {
      if (isset($this->bestRun[$cpIndex])) {
      $diff = $this->bestRun[$cpIndex];
      }
      }
      // set colors and play sound if diffenential is found
      if ($diff !== null) {
      // if no records found for dedimania or local, fallback to personal best
      $this->time->setText(\ManiaLive\Utilities\Time::fromTM($time - $diff, true));
      $this->time->setTextColor('a00a');
      $this->audio->setData("");

      if ($diff > $time) {
      $this->time->setTextColor('00aa');
      if ($playAudio) {
      $this->audio->setData("http://reaby.kapsi.fi/ml/ding.ogg", true);
      $this->audio->autoPlay();
      }
      }
      }
      }

      public function onFinish($time) {
      if ($time < $this->lastFinish || $this->lastFinish == -1) {
      $this->lastFinish = $time;
      $this->bestRun = $this->currentRun;
      $this->counter++;
      }
      }

      public function onStart() {
      $this->currentRun = array();
      }
     */

    function destroy() {
        $this->clearComponents();
        parent::destroy();
    }

}

?>
