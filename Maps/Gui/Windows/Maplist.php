<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use \ManiaLivePlugins\eXpansion\Maps\Gui\Controls\Mapitem;
use ManiaLive\Gui\ActionHandler;

class Maplist extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    private $pager;
    private $connection;
    private $storage;
    public static $records = array();
    public static $mapsPlugin = null;

    protected function onConstruct() {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->pager = new \ManiaLive\Gui\Controls\Pager();
        $this->mainFrame->addComponent($this->pager);
       
    }

    function gotoMap($login, $mapNumber) {
        try {
            $this->hide();
            $this->connection->jumpToMapIndex($mapNumber);
            $map = $this->connection->getNextMapInfo();
            $player = $this->storage->players[$login];
            $this->connection->chatSendServerMessage(__('Speedjump to map %s $z$s$fff by %s', $map->name, $map->author));
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__('Error:', $e->getMessage()));
        }
    }

    function removeMap($login, $mapNumber) {
        self::$mapsPlugin->removeMap($login, $mapNumber);
    }

    function chooseNextMap($login, $mapNumber) {
        try {
            $this->hide();
            $this->connection->setNextMapIndex($mapNumber);
            $map = $this->connection->getNextMapInfo();
            $player = $this->storage->players[$login];           
            $this->connection->chatSendServerMessage(__('Next map will be %s $z$s$fff by %s', $map->name, $map->author));        
        } catch (\Exception $e) {
           $this->connection->chatSendServerMessage(__('Error:', $e->getMessage()));
        }
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);        
         $this->populateList();
        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 14);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(4, -10);
    }

    function onShow() {
     
    }    

    function populateList() {       
        $this->pager->clearItems();
        $x = 0;
        $login = $this->getRecipient();
        foreach ($this->storage->maps as $map)
            $this->pager->addItem(new Mapitem($x++, $login, $map, $this, \ManiaLive\Features\Admin\AdminGroup::contains($login)));
    }

    function destroy() {           
        parent::destroy();
    }

}

?>
