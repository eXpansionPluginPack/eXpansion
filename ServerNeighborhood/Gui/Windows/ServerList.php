<?php

namespace ManiaLivePlugins\eXpansion\ServerNeighborhood\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Elements\Pager;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;
use ManiaLivePlugins\eXpansion\ServerNeighborhood\Gui\Controls\ServerItem;

/**
 * Description of ServerList
 *
 * @author oliverde8
 */
class ServerList extends Window{
    
    private $pager;
    private $items = array();
    
    protected function onConstruct(){
        parent::onConstruct();
        $this->pager = new Pager($this->getSizeX()-2, $this->getSizeY());
        $this->pager->setPosY(0);
        $this->pager->setPosX(0);
        $this->mainFrame->addComponent($this->pager);
    }
    
    public function setServers($servers){
        
        $this->pager->clearItems();
        foreach ($this->items as $item) {
            $item->destroy();            
        }        
        $this->items = array();
        
        $i = 1;
        foreach($servers as $server){
            if($server->isOnline()){
                $component = new ServerItem($i, $this, $server);
                $component->setSizeX($this->getSizeX()-2);
                $this->items[$i-1] = $component;
                $this->pager->addItem($this->items[$i-1]);
                $i++;
            }
        }
    }

    
    public function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->getSizeX()-2, $this->getSizeY()-18);
    }
    
    public function destroy() {
        foreach ($this->items as $item) {
            $item->destroy();            
        }        
        $this->items = null;
        $this->pager->destroy();
        parent::destroy();
    }
    
    public function showServerPlayers($login, $server){
        \ManiaLivePlugins\eXpansion\ServerNeighborhood\Gui\Windows\PlayerList::Erase($login);
        $w = \ManiaLivePlugins\eXpansion\ServerNeighborhood\Gui\Windows\PlayerList::Create($login);
        $w->setTitle('ServerNeighborhood - Server Players');
        $w->setSize(120, 105);
        $w->setServer($server);
        $w->centerOnScreen();
		$w->show();
    }
}

?>
