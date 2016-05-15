<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Windows;

use ManiaLivePlugins\eXpansion\Helpers\Helper;
use ManiaLivePlugins\eXpansion\Maps\Gui\Controls\Additem;

class AddMaps extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    protected $pager;

    public static $mapsPlugin = null;

    /** @var  \Maniaplanet\DedicatedServer\Connection */
    protected $connection;

    /** @var  \ManiaLive\Data\Storage */
    protected $storage;

    protected $items = array();

    protected $gbx;

    protected $btnAddAll;

    protected $actionAddAll;

    protected $label;

    protected function onConstruct()
    {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \ManiaLivePlugins\eXpansion\Helpers\Singletons::getInstance()->getDediConnection();
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->gbx = new \ManiaLivePlugins\eXpansion\Helpers\GbxReader\Map();
        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->mainFrame->addComponent($this->pager);

        $this->actionAddAll = $this->createAction(array($this, "addAllMaps"));

        $this->btnAddAll = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->btnAddAll->setText(__("Add all", $this->getRecipient()));
        $this->btnAddAll->setAction($this->actionAddAll);
        $this->mainFrame->addComponent($this->btnAddAll);
    }

    public function addMap($login, $array)
    {
        try {
            $this->connection->addMap($array[0]);
            $this->connection->chatSendServerMessage(__('Map %s $z$s$fffadded to playlist.', $this->getRecipient(), $array[1]));
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage("Error: " . $e->getMessage(), $login);
        }
    }

    public function onResize($oldX, $oldY)
    {
        foreach ($this->items as $item) {
            $item->setSize($this->sizeX, $this->sizeY);
        }
        $this->pager->setSize($this->sizeX, $this->sizeY - 12);
        $this->btnAddAll->setPosition(4, -$this->sizeY + 6);
        parent::onResize($oldX, $oldY);
    }

    public function onShow()
    {
        $this->populateList();
    }

    public function populateList()
    {
        foreach ($this->items as $item) {
            $item->erase();
        }

        $this->pager->clearItems();
        $this->items = array();

        $login = $this->getRecipient();


        if (\ManiaLivePlugins\eXpansion\Helpers\Storage::getInstance()->isRemoteControlled) {

            $this->items[0] = new \ManiaLivePlugins\eXpansion\Adm\Gui\Controls\InfoItem(1, __("File listing disabled since you are running eXpansion remote", $this->getRecipient()), $this->sizeX);
            $this->pager->addItem($this->items[0]);

            return;
        }
        /** @var \Maniaplanet\DedicatedServer\Structures\Version */
        $game = $this->connection->getVersion();
        $path = Helper::getPaths()->getDownloadMapsPath() . $game->titleId . "/*.Map.Gbx";

        $maps = glob($path);
        $x = 0;
        if (count($maps) >= 1) {
            foreach ($maps as $map) {
                $this->items[$x] = new Additem($x, $map, $this, $this->gbx, $login, $this->sizeX);
                $this->pager->addItem($this->items[$x]);
                $x++;
            }
        }
    }

    public function deleteMap($login, $filename)
    {
        if (\ManiaLivePlugins\eXpansion\Helpers\Storage::getInstance()->isRemoteControlled) {
            $this->connection->chatSendServerMessage(__("#admin_error#This instance of eXpansion is runnin remotelly! Can't delete file #variable#'%s'", $this->getRecipient(), end($file)));

            return;
        }
        try {
            unlink($filename);
            $file = explode("/", $filename);
            $this->connection->chatSendServerMessage(__("File '%s' deleted from filesystem!", $this->getRecipient(), end($file)));
            $this->populateList();
            $this->RedrawAll();
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__('$f00$oError $z$s$fff%s', $this->getRecipient(), $e->getMessage()));
        }
    }

    public function addAllMaps($login)
    {
        $game = $this->connection->getVersion();
        $path = Helper::getPaths()->getDownloadMapsPath() . $game->titleId . "/*.Map.Gbx";

        $mapsAtDisk = glob($path);
//        $mapsAtServer = array();
//        foreach ($this->storage->maps as $map) {
//            $mapsAtServer[] = str_replace("\\", "/", $mapsDir . $map->fileName);
//        }
//        $mapDiff = array_diff($mapsAtServer, $mapsAtDisk);

        $this->connection->addMapList($mapsAtDisk);
        $this->connection->chatSendServerMessage("Added " . count($mapsAtDisk) . " maps to playlist.", $login);
    }

    public function destroy()
    {
        $this->gbx = null;
        foreach ($this->items as $item) {
            $item->erase();
        }
        $this->items = array();
        $this->btnAddAll->destroy();
        $this->connection = null;
        $this->storage = null;
        $this->pager->destroy();
        $this->destroyComponents();

        parent::destroy();
    }

}
