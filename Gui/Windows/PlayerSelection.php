<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Controls\Playeritem;

class PlayerSelection extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    private $pager;
    private $connection;
    private $storage;
    private $message;
    private $controller;
    private $items = array();

    protected function onConstruct()
    {
        parent::onConstruct();

        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \ManiaLivePlugins\eXpansion\Helpers\Singletons::getInstance()->getDediConnection();
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->mainFrame->addComponent($this->pager);
    }

    protected function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 2, $this->sizeY);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(0, 4);
    }

    protected function onShow()
    {

    }

    public function populateList($callback, $text = "")
    {
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        foreach ($this->items as $item)
            $item->erase();

        $this->pager->clearItems();

        $x = 0;
        $login = $this->getRecipient();
        //for($i =0; $i < 100; $i++){
        foreach ($this->storage->players as $player) {

            if ($player->login != $this->getRecipient()) {
                $this->items[$x] = new Playeritem($x, $player, $callback, $text);
                $this->pager->addItem($this->items[$x]);
                $x++;
            }
        }
        //}
        foreach ($this->storage->spectators as $player) {
            if ($player->login != $this->getRecipient()) {
                $this->items[$x] = new Playeritem($x, $player, $callback, $text);
                $this->pager->addItem($this->items[$x]);
                $x++;
            }
        }
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setController($obj)
    {
        $this->controller = $obj;
    }

    public function destroy()
    {
        foreach ($this->items as $item)
            $item->erase();

        parent::destroy();
    }
}
