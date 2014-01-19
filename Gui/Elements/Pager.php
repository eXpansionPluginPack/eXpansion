<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

/**
 * Description of Pager
 *
 * @author Petri
 */
class Pager extends \ManiaLive\Gui\Control {

    private $pager;
    private $items = array();
    private $scroll;
    private $scrollBg, $scrollUp, $scrollDown;
    private $itemSizeY = 6;

    public function __construct() {
        $this->pager = new \ManiaLive\Gui\Controls\Frame();
        $this->pager->setId("Pager");
        $this->pager->setScriptEvents();
        $this->addComponent($this->pager);

        $this->scrollBg = new \ManiaLib\Gui\Elements\Quad(4, 40);
        $this->scrollBg->setAlign("center", "top");
        $this->scrollBg->setStyle("Bgs1");
        $this->scrollBg->setSubStyle(\ManiaLib\Gui\Elements\Bgs1::BgTitle3_3);
        $this->scrollBg->setId("ScrollBg");
        $this->scrollBg->setScriptEvents();
        $this->addComponent($this->scrollBg);

        $this->scroll = new \ManiaLib\Gui\Elements\Quad(3, 15);
        $this->scroll->setAlign("center", "top");
        $this->scroll->setStyle("Bgs1");
        $this->scroll->setSubStyle(\ManiaLib\Gui\Elements\Bgs1::BgCard1);
        $this->scroll->setId("ScrollBar");
        $this->scroll->setScriptEvents();
        $this->addComponent($this->scroll);
    }

    public function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 6, $this->sizeY);
        $this->scroll->setPosition($this->sizeX - 3, 0);

        $this->scrollBg->setPosition($this->sizeX - 3);
        $this->scrollBg->setSizeY($this->sizeY);
    }

    public function setStretchContentX($value) {
        // do nothing xD 
    }

    public function addItem(\ManiaLib\Gui\Component $component) {
        $component->setSizeX($this->sizeX - 4);
        $component->setAlign("left", "top");
        if ($component->getSizeY() > 0) {
            $this->itemSizeY = $component->getSizeY();
        }
        $item = new \ManiaLive\Gui\Controls\Frame();
        $item->setAlign("left", "top");
        $item->setScriptEvents();
        $item->addComponent($component);
        $hash = spl_object_hash($item);
        $this->items[$hash] = $item;
        $this->pager->addComponent($this->items[$hash]);
    }

    public function clearItems() {
        foreach ($this->items as $item) {
            $this->pager->removeComponent($item);
            $item->destroy();
        }
        $this->items = array();
    }

    public function removeItem(\ManiaLib\Gui\Component $item) {
        $hash = spl_object_hash($item);
        $this->pager->removeComponent($this->items[$hash]);
        $this->items[$hash]->destroy();
        unset($this->items[$hash]);
    }

    public function destroy() {
        $this->clearItems();
        $this->pager->destroy();
        parent::destroy();
    }

    private function getNumber($number) {
        return number_format((float) $number, 2, '.', '');
    }

    public function getScriptDeclares() {
        $sizeY = $this->itemSizeY;

        $script = 'declare Real itemSizeY = ' . $this->getNumber($sizeY) . ';';

        $script .= <<<EOD
                    
                    declare CMlFrame Pager <=> (Page.GetFirstChild("Pager") as CMlFrame);
                    declare CMlQuad ScrollBar <=> (Page.GetFirstChild("ScrollBar") as CMlQuad);
                    declare CMlQuad ScrollBg <=> (Page.GetFirstChild("ScrollBg") as CMlQuad);
                    declare Real itemCount = Pager.Size.Y / itemSizeY;
                    declare Integer itemsPerPage = MathLib::NearestInteger(itemCount) - 4;
                    declare Real pagerMouseY;                    
                    declare moveScroll = False;
                    declare Real pagerStartPos = ScrollBar.RelativePosition.Y;
                    declare Real pagerDelta = 0.0;
                    declare CMlFrame item;
                    declare Real nb = 1.0;
                    foreach (item in Pager.Controls) {                        
                    item.RelativePosition.Y = -itemSizeY * nb;                    
                        if(item.RelativePosition.Y < -itemSizeY * itemsPerPage) { 
                           item.Hide();                            
                        }
                    nb +=1;
                    }
                    if (Pager.Controls.count < itemsPerPage) {
                        ScrollBar.Hide();
                        ScrollBg.Hide();
                    }
                
                
                
EOD;
        return $script;
    }

    public function getScriptMainLoop() {
        $script = <<<EOD
        if (moveScroll) {                                                                                                    
                    pagerDelta += MouseY - pagerMouseY;
                        
                    declare max = (-itemSizeY * itemsPerPage) + 13 ;

                    if (pagerDelta >= 0.0) {
                            pagerDelta = 0.0;
                            pagerMouseY = MouseY;
                    }
                    if (pagerDelta < max) {
                            pagerDelta = max;
                            pagerMouseY = MouseY;        
                    }
                
                    ScrollBar.RelativePosition.Y = pagerDelta;            
                    declare Real percent = 1 - (MathLib::Abs(max) - MathLib::Abs(pagerDelta)) / MathLib::Abs(max);               
                    nb = 1.0;                    
                    foreach (item in Pager.Controls) {
                        item.RelativePosition.Y = (-itemSizeY * nb) - percent * (-itemSizeY * (Pager.Controls.count - itemsPerPage));
                        if(item.RelativePosition.Y > -3.0 || item.RelativePosition.Y < -itemSizeY * itemsPerPage) { 
                          item.Hide();
                        }
                        else {
                          item.Show();
                        } 
                        nb +=1;
                    }                                                  
                        pagerMouseY = MouseY;                         
                   }
             
   
   
   
   
            if (MouseLeftButton == True) {                   
                  foreach (Event in PendingEvents) {
                       if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "ScrollBar")  {
                              pagerMouseY = MouseY;                                            
                              moveScroll = True;
                      }                                   
                  }                                                                                                                                    
                } else {
                        
                            moveScroll = False;
                }
                

EOD;
        return $script;
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        $this->destroy();
    }

}
