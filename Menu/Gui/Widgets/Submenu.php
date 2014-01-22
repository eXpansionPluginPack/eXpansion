<?php

namespace ManiaLivePlugins\eXpansion\Menu\Gui\Widgets;

class Submenu extends \ManiaLive\Gui\Window {

    private $menu, $debug, $bg;
    public $xml;
    private $item = array();
    private $submenu = array();

    public function addItem(&$menu, $text, $action = null, $submenuNb = false) {
        $nb = count($this->item);
        $this->item[$nb] = new \ManiaLib\Gui\Elements\Label();

        $this->item[$nb]->setStyle("TextChallengeNameMedium");
        $this->item[$nb]->setAlign("left", "center");
        $this->item[$nb]->setSize(25, 4.5);
        $this->item[$nb]->setFocusAreaColor1("0008");
        $this->item[$nb]->setFocusAreaColor2("0008");

        if (!empty($action)) {
            $this->item[$nb]->setFocusAreaColor2("fff8");
            $this->item[$nb]->setAction($action);
        }

        $this->item[$nb]->setText("  " . $text);
        $this->item[$nb]->setTextColor('fff');
        $this->item[$nb]->setTextSize(1.75);
        $this->item[$nb]->setPosZ(30);

        if ($submenuNb !== false) {
            $this->item[$nb]->setId("sub_" . $submenuNb);
            $this->item[$nb]->setFocusAreaColor2("fff8");
        } else {

            $snb = false;
            foreach ($this->submenu as $subNb => $sub) {
                if ($sub === $menu) {
                    $snb = $subNb;
                    break;
                }
            }
            if ($snb) {
                $this->item[$nb]->setId("sub_" . $snb . "_item_" . $nb);
                $this->item[$nb]->setFocusAreaColor2("fff8");
                $this->item[$nb]->setAction($action);
            } else {
                $this->item[$nb]->setId("item_" . $nb);
            }
        }
        $this->item[$nb]->setScriptEvents();
        $menu->addComponent($this->item[$nb]);
    }

    public function addSubMenu(&$menu, $text) {
        $mb = count($this->submenu) + 1;
        $this->submenu[$mb] = new \ManiaLive\Gui\Controls\Frame(25, 4.5);
        $this->submenu[$mb]->setLayout(new \ManiaLib\Gui\Layouts\Column());
        $this->submenu[$mb]->setId("submenu_" . $mb);
        $this->submenu[$mb]->setScriptEvents();
        // add item to menu
        $this->addItem($menu, $text . " » ", null, $mb);
        // add component to menu
        $menu->addComponent($this->submenu[$mb]);

        return $this->submenu[$mb];
    }

    public function getMenu() {
        return $this->menu;
    }

    protected function onConstruct() {
        parent::onConstruct();
        $this->menu = new \ManiaLive\Gui\Controls\Frame();
        $this->menu->setLayout(new \ManiaLib\Gui\Layouts\Column());
        $this->menu->setId("Submenu");
        $this->menu->setScriptEvents();
        $this->addComponent($this->menu);
        
        $inputbox = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox("widgetStatus");
        $inputbox->setPosition(900, 900);
        $inputbox->setScriptEvents();
        $this->addComponent($inputbox);
        
        
        $this->xml = new \ManiaLive\Gui\Elements\Xml();
    }

    protected function onDraw() {
        parent::onDraw();

        $this->removeComponent($this->xml);
        $count = count($this->submenu);
        $script = <<<EOD
                       <script><!--      
                        #Include "MathLib" as MathLib
                        #Include "TextLib" as TextLib
                 
                        main() {                        
                        declare CMlFrame Menu <=> (Page.GetFirstChild("Submenu") as CMlFrame);   
                        declare CMlEntry widgetStatus <=> (Page.GetFirstChild("widgetStatus") as CMlEntry);
                        declare Text outText = "";
                        declare Boolean toggleSubmenu = False;
                        declare CMlFrame currentButton = Null; 
                        declare CMlFrame previousButton = Null; 
                        declare persistent Boolean[Text] widgetVisible;    
                
                        for(i, 1, $count) {
                                    Page.GetFirstChild("submenu_"^i).Hide();
                                }
                
                        Menu.RelativePosition.Z = 30.0;                                        
                          while (True) {                                  
                            yield;                           
                
                            if (MouseRightButton && !IsKeyPressed(8060928) )
                            {
                                toggleSubmenu = True;
                                Menu.PosnX = MouseX-1;
                                Menu.PosnY = MouseY+.5;  
                                
                                    
                            } // mouseRightButton
                
                            if (toggleSubmenu) {
                                 Menu.Show();     
                                     outText = "";					   
					   if (widgetVisible.count > 0) {
					   foreach (id => status in widgetVisible) {
						
			    			    declare Text bool = "0";
						    if (status == True) {
							bool = "1";
						    }
						outText = outText ^ id ^ ":" ^ bool ^ "|";
											    
					    }
			
					   widgetStatus.Value = outText;
					  }
                
                                    foreach (Event in PendingEvents) {
                                        if (Event.Type == CMlEvent::Type::MouseOver && Event.ControlId != "Unassigned")  {
                                            if(Page.GetFirstChild("submenu_"^ TextLib::SubText(Event.ControlId,4,1)) != Null ) {                                                                                            
                                                    if (currentButton != Null && currentButton.ControlId != "submenu_"^ TextLib::SubText(Event.ControlId,4,1)) {        
                                                        log ("ControlId changed");
                                                        currentButton.Hide();
                                                    } 
                                            log ("hovering: submenu_"^ TextLib::SubText(Event.ControlId,4,1));
                                            currentButton = (Page.GetFirstChild("submenu_"^ TextLib::SubText(Event.ControlId,4,1)) as CMlFrame);
                                            currentButton.Show();                                                                                                                                                              
                                        } else {                            
                                            if (currentButton != Null) {                
                                                log ("hiding:" ^ currentButton.ControlId);
                                                currentButton.Hide();        
                                                currentButton = Null;
                                            }
                                       }                                                  
                                    } 
                                }
                            }
                            else { 
                                Menu.Hide();
                            }
                
                           if (MouseLeftButton) {                           
                                 toggleSubmenu = False;
                            }   
                
                           } // while
                        
                       } // main
                        
                        --></script>
EOD;


        $this->xml->setContent($script);
        $this->addComponent($this->xml);
    }

    protected function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->bg->setSize(30, ($this->itemNb * 4.5) + 1);
        $this->bg->setPosition(-2, 2);
    }

}

?>
