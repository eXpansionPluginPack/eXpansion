<?php

namespace ManiaLivePlugins\eXpansion\Menu\Gui\Widgets;

class Submenu extends \ManiaLive\Gui\Window {

    private $menu, $debug, $bg;
    public $xml;
    private $itemNb = 0;

    public function addItem($text, $action) {
        $item = new \ManiaLib\Gui\Elements\Label();
        $item->setStyle("TextChallengeNameMedium");
        $item->setAlign("left", "center");
        $item->setSize(25, 4.5);

        $item->setFocusAreaColor1("fff");
        $item->setFocusAreaColor2("fff");
        if (!empty($action)) {
            $item->setFocusAreaColor2("4ef");
            $item->setAction($action);
        }
        $item->setText($text);
        $item->setTextColor('000');
        $item->setTextSize(1.75);
        $item->setPosZ(30);
        $item->setId("item_" . $this->itemNb);
        $item->setScriptEvents();
        $this->menu->addComponent($item);
        $this->itemNb++;
    }

    protected function onConstruct() {
        parent::onConstruct();

        $this->bg = new \ManiaLib\Gui\Elements\Quad();
        $this->bg->setBgcolor('$f00');
        $this->bg->setId("menuBg");
        $this->bg->setScriptEvents();
        $this->addComponent($this->bg);

        $this->menu = new \ManiaLive\Gui\Controls\Frame();
        $this->menu->setLayout(new \ManiaLib\Gui\Layouts\Column());
        $this->menu->setId("Submenu");
        $this->menu->setScriptEvents();
        $this->menu->setAttribute("hidden", "true");
        $this->addComponent($this->menu);
        
        $this->debug->setScriptEvents();
        $this->addComponent($this->debug);        

        $this->xml = new \ManiaLive\Gui\Elements\Xml();

        $script = <<<EOD
                       <script><!--      
                        
                 
                        main() {
                        declare CMlFrame Menu <=> (Page.GetFirstChild("Submenu") as CMlFrame);               
                        declare Boolean toggleSubmenu = False;
                        Menu.RelativePosition.Z = 30.0;
                        
                          while (True) {                                  
                            yield;

                            if (!PageIsVisible || InputPlayer == Null) continue;                                                        
                
                            if (MouseRightButton && toggleSubmenu == False)
                            {
                                toggleSubmenu = True;
                                Menu.PosnX = MouseX;
                                Menu.PosnY = MouseY;  
                                
                                    
                            } // mouseRightButton
                
                            if (toggleSubmenu) {
                                Menu.Show(); 
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
        $this->bg->setSize(30, ($this->itemNb * 4.5) + 1);
        $this->bg->setPosition(-2, 2);
    }

}

?>
