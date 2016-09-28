<?php

namespace ManiaLivePlugins\eXpansion\ServerNeighborhood\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Elements\Pager;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;
use ManiaLivePlugins\eXpansion\ServerNeighborhood\Gui\Controls\PlayerItem;
use ManiaLivePlugins\eXpansion\ServerNeighborhood\Gui\Controls\ServerItem;
use ManiaLivePlugins\eXpansion\ServerNeighborhood\Server;

/**
 * Description of ServerList
 *
 * @author oliverde8
 */
class PlayerList extends Window
{

    protected $pager;
    protected $items = array();
    protected $serverItem;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->pager = new Pager($this->getSizeX() - 2, $this->getSizeY());
        $this->pager->setPosY(-12);
        $this->pager->setPosX(1);
        $this->mainFrame->addComponent($this->pager);
    }

    public function setServer(Server $server)
    {

        $this->serverItem = new ServerItem(0, null, $server);
        $this->serverItem->setPosY(-8);
        $this->serverItem->setSizeX($this->getSizeX() - 2);
        $this->addComponent($this->serverItem);


        $this->pager->clearItems();
        $this->items = array();

        $i = 1;
        foreach ($server->getServer_data()->current->players->player as $player) {

            $pitem = new PlayerItem($i, $this, $player);
            $pitem->setSizeX($this->getSizeX() - 2);
            $this->items[] = $pitem;
            $this->pager->addItem($pitem);
        }

    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->getSizeX() - 2, $this->getSizeY() - 18);
        foreach ($this->items as $item) {
            $item->setSizeX($this->getSizeX() - 2);
        }
        if ($this->serverItem != null) {
            $this->serverItem->setSizeX($this->getSizeX() - 2);
        }
    }

    public function destroy()
    {
        $this->items = null;
        $this->pager->destroy();
        $this->serverItem->destroy();
        parent::destroy();
    }
}
