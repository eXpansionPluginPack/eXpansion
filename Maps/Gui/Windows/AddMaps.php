<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use \ManiaLivePlugins\eXpansion\Maps\Gui\Controls\Mapitem;
use \ManiaLivePlugins\eXpansion\Maps\Gui\Controls\Additem;

use ManiaLive\Gui\ActionHandler;

class AddMaps extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    private $pager;
    private $connection;
    private $storage;

    protected function onConstruct() {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->pager = new \ManiaLive\Gui\Controls\Pager();
        $this->mainFrame->addComponent($this->pager);
    }

    function addMap($login, $filename) {
        try {
            $this->connection->addMap($filename);
            $mapinfo = $this->connection->getMapInfo($filename);
            $this->connection->chatSendServerMessage(_('Map %s $z$s$fffadded to playlist.', $mapinfo->name));
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(_('Error:', $e->getMessage()));
        }
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 14);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(8, -10);
    }

    function onShow() {
        $this->populateList();
    }

    function populateList() {     
        $this->pager->clearItems();


        $login = $this->getRecipient();
        $path = $this->connection->getMapsDirectory() . "/Downloaded/*.Map.Gbx";
        
        $maps = glob($path);
        $x = 0;
        if (count($maps) >= 1) {
        foreach ($maps as $map)
            $this->pager->addItem(new Additem($x++, $map, $this));
        }
    }

    function destroy() {
        parent::destroy();
    }

}

?>
