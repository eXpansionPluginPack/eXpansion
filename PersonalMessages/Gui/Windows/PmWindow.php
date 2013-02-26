<?php

namespace ManiaLivePlugins\eXpansion\PersonalMessages\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLive\Gui\ActionHandler;
use ManiaLib\Utils\Formatting;
use ManiaLivePlugins\eXpansion\PersonalMessages\Gui\Controls\Playeritem;
use ManiaLivePlugins\eXpansion\PersonalMessages\PersonalMessages;

class PmWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    private $pager;
    private $connection;
    private $storage;
    private $message;
    private $controller;

    protected function onConstruct() {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->pager = new \ManiaLive\Gui\Controls\Pager();
        $this->mainFrame->addComponent($this->pager);
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
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->pager->clearItems();

        $x = 0;
        $login = $this->getRecipient();
        foreach ($this->storage->players as $player) {
            if ($player->login !== $this->getRecipient())
                $this->pager->addItem(new Playeritem($x++, $player, $this->controller));
        }
        foreach ($this->storage->spectators as $player) {
            if ($player->login !== $this->getRecipient())
                $this->pager->addItem(new Playeritem($x++, $player, $this->controller));
        }
    }

    function setMessage($message) {
        $this->message = $message;
    }

    function setController($obj) {
        $this->controller = $obj;
    }

    function destroy() {
        parent::destroy();
    }

}

?>
