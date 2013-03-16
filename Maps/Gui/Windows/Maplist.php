<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Maps\Gui\Controls\Mapitem;
use ManiaLive\Gui\ActionHandler;

class Maplist extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    
    public static $records = array();
    
    public static $mapsPlugin = null;    
    
    private $items = array();
    
    /** @var \ManiaLive\Gui\Controls\Pager */   
    private $pager;
    
    protected function onConstruct() {
        parent::onConstruct();
        $this->pager = new \ManiaLive\Gui\Controls\Pager();
        $this->mainFrame->addComponent($this->pager);
    }

    static function Initialize($mapsPlugin) {
        self::$mapsPlugin = $mapsPlugin;
    }

    function gotoMap($login, \DedicatedApi\Structures\Map $map) {
        self::$mapsPlugin->gotoMap($login, $map);
        $this->Erase($this->getRecipient());
    }

    function removeMap($login, \DedicatedApi\Structures\Map $map) {
        self::$mapsPlugin->removeMap($login, $map);
        $this->RedrawAll();
    }

    function chooseNextMap($login, \DedicatedApi\Structures\Map $map) {
        self::$mapsPlugin->chooseNextMap($login, $map);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 14);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(4, -10);
    }

    protected function onDraw() {
        $login = $this->getRecipient();      
        foreach ($this->items as $item) {
            $item->destroy();            
        }
        
        $this->pager->clearItems();
        $this->items = array();


        $isAdmin = \ManiaLive\Features\Admin\AdminGroup::contains($login);
        $x = 0;
        foreach (\ManiaLive\Data\Storage::getInstance()->maps as $map) {
            $this->items[$x] = new Mapitem($x, $login, $map, $this, $isAdmin, $this->sizeX);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }

        parent::onDraw();
    }

    function destroy() {
        foreach ($this->items as $item) {
            $item->destroy();            
        }        
        $this->items = null;
        $this->pager->destroy();
        $this->clearComponents();                
        parent::destroy();
    }

}

?>
