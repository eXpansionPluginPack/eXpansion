<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Adm\Gui\Controls\MatchSettingsFile;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

class MatchSettings extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    private $pager;
    private $connection;
    private $storage;
    private $items = array();
    private $inputboxSaveAs;
    private $actionSave;
    private $saveButton;
    private $frame;

    protected function onConstruct() {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line(120));
        $login = $this->getRecipient();
        $this->inputboxSaveAs = new Inputbox("SaveAs", 60);
        $this->inputboxSaveAs->setLabel(__("Save MatchSettings as", $login));

        $this->frame->addComponent($this->inputboxSaveAs);

        $this->actionSave = $this->createAction(array($this, "saveAs"));

        $this->saveButton = new OkButton(26, 5);
        $this->saveButton->setText('$fff' . __("Save", $login));
        $this->saveButton->colorize("a22");
        $this->saveButton->setAction($this->actionSave);
        $this->saveButton->setScale(0.8);
        $this->frame->addComponent($this->saveButton);

        $this->mainFrame->addComponent($this->frame);

        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->mainFrame->addComponent($this->pager);
    }

    function saveAs($login, $entries) {

        try {
            if (empty($entries['SaveAs'])) {
                $this->connection->chatSendServerMessage(__("Error in filename", $login), $login);
                return;
            }
            $filename = $this->connection->getMapsDirectory() . "/MatchSettings/" . $entries['SaveAs'] . ".txt";
            $this->saveSettings($login, $filename);
            $this->populateList();
            $this->RedrawAll();
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__('$f00$oError $z$s$fff%s', $login, $e->getMessage()), $login);
        }
    }

    function deleteSetting($login, $filename) {

        try {
            unlink($filename);
            $file = explode("/", $filename);
            $this->connection->chatSendServerMessage(__("File '%s' deleted from filesystem!", $this->getRecipient(), end($file)), $login);
            $this->populateList();
            $this->RedrawAll();
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__('$f00$oError $z$s$fff%s', $this->getRecipient(), $e->getMessage()), $login);
        }
    }

    function saveSettings($login, $filename) {

        try {
            $this->connection->saveMatchSettings($filename);
            $file = explode("/", $filename);
            $this->connection->chatSendServerMessage(__("Saved MatchSettings to file: %s", $login, end($file)), $login);
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__('$f00$oError $z$s$fff%s', $login, $e->getMessage()), $login);
        }
    }

    function loadSettings($login, $filename) {
        try {
            $this->connection->loadMatchSettings($filename);
            $file = explode("/", $filename);
            $this->connection->chatSendServerMessage(__("Loaded MatchSettings from file: %s", $login, end($file)), $login);
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__('$f00$oError $z$s$fff%s', $login, $e->getMessage()), $login);
        }
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->frame->setPosition(4, -4);
        $this->pager->setPosY(-12);
        $this->pager->setSize($this->sizeX, $this->sizeY - 16);
        $this->pager->setStretchContentX($this->sizeX);
    }

    function onShow() {
        $this->populateList();
    }

    public function onDraw() {
        parent::onDraw();
        $this->frame->setVisibility(AdminGroups::hasPermission($this->getRecipient(), 'game_matchSave'));
    }
    
    function populateList() {
        foreach ($this->items as $item)
            $item->destroy();
        $this->pager->clearItems();
        $this->items = array();

        $login = $this->getRecipient();
        $path = $this->connection->getMapsDirectory() . "/MatchSettings/*.txt";

        $settings = glob($path);
        $x = 0;
        if (count($settings) > 1) {
            foreach ($settings as $file) {
                $this->items[$x] = new MatchSettingsFile($x, $file, $this, $this->getRecipient(), $this->sizeX);
                $this->pager->addItem($this->items[$x]);
                $x++;
            }
        }
    }

    function destroy() {
        foreach ($this->items as $item)
            $item->destroy();

        $this->items = array();

        $this->saveButton->destroy();
        $this->inputboxSaveAs->destroy();
        $this->frame->destroy();

        $this->pager->destroy();
        $this->connection = null;
        $this->storage = null;
        $this->clearComponents();
        parent::destroy();
    }

}

?>
