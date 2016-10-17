<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel;
use ManiaLivePlugins\eXpansion\Helpers\Helper;
use ManiaLivePlugins\eXpansion\Maps\Gui\Controls\DirectoryItem;
use ManiaLivePlugins\eXpansion\Maps\Gui\Controls\NewAddItem;

class AddMaps extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    protected $pager;

    /** @const integer limits the number of fetched maps at the list */
    const MAPLIMIT = 150;

    public static $mapsPlugin = null;

    /** @var  \Maniaplanet\DedicatedServer\Connection */
    protected $connection;

    /** @var  \ManiaLive\Data\Storage */
    protected $storage;

    protected $items = array();

    protected $infoLabel;

    protected $gbx;

    protected $btnAddAll;

    protected $actionAddAll;

    protected $label;

    protected $allMapsPath = "";


    protected function onConstruct()
    {
        parent::onConstruct();
        $this->connection = \ManiaLivePlugins\eXpansion\Helpers\Singletons::getInstance()->getDediConnection();
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();

        $label = new DicoLabel(90, 6);
        $label->setPosY(4);
        $label->setText(eXpGetMessage('Display is limiting to first %1$s maps.'), array(self::MAPLIMIT));
        $this->mainFrame->addComponent($label);

        $this->mainFrame->addComponent($this->pager);

        $this->actionAddAll = $this->createAction(array($this, "addAllMaps"));

        $this->btnAddAll = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->btnAddAll->setText(__("Add all", $this->getRecipient()));
        $this->btnAddAll->setAction($this->actionAddAll);
        $this->mainFrame->addComponent($this->btnAddAll);
        $this->allMapsPath = Helper::getPaths()->getDownloadMapsPath();
    }

    public function addMap($login, $filename)
    {
        try {
            $this->connection->addMap($filename);
            $info = $this->connection->getMapInfo($filename);
            $this->connection->chatSendServerMessage(__('Map %s $z$s$fffadded to playlist.', $this->getRecipient(), $info->name));
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

    public function populateList($folder = "")
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

        $mapPath = Helper::getPaths()->getDownloadMapsPath() . $folder;
        if ($folder) {
            $mapPath = $folder;
            $this->allMapsPath = $folder;
        }

        $x = 0;

        foreach (new \DirectoryIterator($mapPath) as $dir) {
            if ($dir->isDir()) {
                $file = $dir->getPathname();
                $label = str_replace(DIRECTORY_SEPARATOR, "", str_replace($mapPath, "", $dir->getPathname()));
                if ((\realpath(Helper::getPaths()->getDownloadMapsPath()) == $dir->getPath())) {
                    if ($dir->isDot()) {
                        continue;
                    }
                } else {
                    if ($dir->isDot()) {
                        if ($dir->getBasename() == "..") {
                            $label = "Parent directory (..)";
                            $file = $dir->getRealPath();
                        } else {
                            continue;
                        }
                    }
                }
                $this->items[$x] = new DirectoryItem($x, $label, $file, $this, $login, $this->sizeX);
                $this->pager->addItem($this->items[$x]);
                $x++;
            }
        }


        $x = 0;
        foreach (new \DirectoryIterator($mapPath) as $dir) {
            if ($dir->isFile()) {
                if ($x > self::MAPLIMIT) return;
                $file = str_replace($mapPath, "", $dir->getBasename());

                $path = $dir->getRealPath();
                $this->items[$x] = new NewAddItem($x, $file, $path, $this, $login, $this->sizeX);
                $this->pager->addItem($this->items[$x]);
                $x++;
            }
        }
    }

    public function deleteMap($login, $filename)
    {
        if (\ManiaLivePlugins\eXpansion\Helpers\Storage::getInstance()->isRemoteControlled) {
            $this->connection->chatSendServerMessage(__("#admin_error#This instance of eXpansion is running remote! Can't delete file #variable#'%s'", $this->getRecipient(), end($file)));
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
        $mapsAtDisk = glob($this->allMapsPath . "/*.Map.Gbx");
        $this->connection->addMapList($mapsAtDisk);
        $this->connection->chatSendServerMessage("Added " . count($mapsAtDisk) . " maps to playlist.", $login);
    }

    public function changeDirectory($login, $dir)
    {
        $this->populateList($dir);
        $this->redraw($login);
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
