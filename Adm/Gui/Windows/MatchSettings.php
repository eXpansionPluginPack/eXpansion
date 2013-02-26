<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLivePlugins\eXpansion\Adm\Gui\Controls\MatchSettingsFile;
use ManiaLive\Gui\ActionHandler;

class MatchSettings extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

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

    function saveSettings($login, $filename, $args) {
        
        try {
            $this->connection->saveMatchSettings($filename);
            $file = explode("/", $filename);
            $this->connection->chatSendServerMessage(__("Saved MatchSettings to file: %s", end($file)));
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__('$f00$oError $z$s$fff%s', $e->getMessage()));
        }
    }

    function loadSettings($login, $filename, $args) {
        try {
            $this->connection->loadMatchSettings($filename);
            $file = explode("/", $filename);
            $this->connection->chatSendServerMessage(__("Loaded MatchSettings from file: %s", end($file)));
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__('$f00$oError $z$s$fff', $e->getMessage()));
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
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->pager->clearItems();


        $login = $this->getRecipient();
        $path = $this->connection->getMapsDirectory() . "/MatchSettings/*.txt";

        $settings = glob($path);
        $x = 0;
        if (count($settings) > 1) {
            foreach ($settings as $file)
                $this->pager->addItem(new MatchSettingsFile($x++, $file, $this));
        }
    }

    function destroy() {
        $this->pager->destroy();
        parent::destroy();
    }

}

?>
