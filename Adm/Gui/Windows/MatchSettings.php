<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Adm\Gui\Controls\MatchSettingsFile;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Helpers\Helper;

class MatchSettings extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    private $pager;

    private $connection;

    private $storage;

    private $items = array();

    private $inputboxSaveAs;
    private $inputboxLoadAs;

    private $actionSave;
    private $actionLoad;

    private $saveButton;
    private $loadButton;

    private $frame;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->connection = \ManiaLivePlugins\eXpansion\Helpers\Singletons::getInstance()->getDediConnection();
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $layout = new \ManiaLib\Gui\Layouts\Line();
        $layout->setMargin(2, 0);
        $this->frame->setLayout($layout);


        $login = $this->getRecipient();
        $this->inputboxSaveAs = new Inputbox("SaveAs", 40);
        $this->inputboxSaveAs->setLabel(__("Save MatchSettings as", $login));
        $this->frame->addComponent($this->inputboxSaveAs);

        $this->actionSave = $this->createAction(array($this, "saveAs"));

        $this->saveButton = new OkButton();
        $this->saveButton->setText('$fff' . __("Save", $login));
        $this->saveButton->colorize("d00");
        $this->saveButton->setAction($this->actionSave);
        $this->frame->addComponent($this->saveButton);

        // Load
        $this->inputboxLoadAs = new Inputbox("LoadAs", 40);
        $this->inputboxLoadAs->setLabel(__("Load MatchSettings by name", $login));
        $this->frame->addComponent($this->inputboxLoadAs);

        $this->actionLoad = $this->createAction(array($this, "loadAs"));

        $this->loadButton = new OkButton();
        $this->loadButton->setText('$fff' . __("Load", $login));
        $this->loadButton->colorize("0d0");
        $this->loadButton->setAction($this->actionLoad);
        $this->frame->addComponent($this->loadButton);

        $this->mainFrame->addComponent($this->frame);

        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->mainFrame->addComponent($this->pager);
    }

    public function saveAs($login, $entries)
    {

        try {
            if (empty($entries['SaveAs'])) {
                $this->connection->chatSendServerMessage(__("Error in filename", $login), $login);

                return;
            }
            $appendTxt = ".txt";
            if (substr($entries['SaveAs'], -4, 4) == ".txt") {
                $appendTxt = "";
            }

            $filename = Helper::getPaths()->getMatchSettingPath() . $entries['SaveAs'] . $appendTxt;
            $this->saveSettings($login, $filename);
            $this->populateList();
            $this->RedrawAll();
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__('$f00$oError $z$s$fff%s', $login, $e->getMessage()), $login);
        }
    }

    public function loadAs($login, $entries)
    {

        try {
            if (empty($entries['LoadAs'])) {
                $this->connection->chatSendServerMessage(__("Error in filename", $login), $login);

                return;
            }
            $appendTxt = ".txt";
            if (substr($entries['LoadAs'], -4, 4) == ".txt") {
                $appendTxt = "";
            }

            $filename = Helper::getPaths()->getMatchSettingPath() . $entries['LoadAs'] . $appendTxt;
            $this->loadSettings($login, $filename);
            $this->populateList();
            $this->RedrawAll();
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__('$f00$oError $z$s$fff%s', $login, $e->getMessage()), $login);
        }
    }

    public function deleteSetting($login, $filename)
    {

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

    public function saveSettings($login, $filename)
    {

        try {
            $this->connection->saveMatchSettings($filename);
            $file = explode("/", $filename);
            $this->connection->chatSendServerMessage(__("Saved MatchSettings to file: %s", $login, end($file)), $login);
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__('$f00$oError $z$s$fff%s', $login, $e->getMessage()), $login);
        }
    }

    public function loadSettings($login, $filename)
    {
        try {
            $this->connection->loadMatchSettings($filename);
            $file = explode("/", $filename);
            $this->connection->chatSendServerMessage(__("Loaded MatchSettings from file: %s", $login, end($file)), $login);
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__('$f00$oError $z$s$fff%s', $login, $e->getMessage()), $login);
        }
    }

    protected function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);

        $this->frame->setPosition(4, -4);
        $this->pager->setPosY(-6);
        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 16);
        $this->pager->setStretchContentX($this->sizeX);
    }

    protected function onShow()
    {
        $this->populateList();
    }

    protected function onDraw()
    {
        parent::onDraw();
        $this->frame->setVisibility(AdminGroups::hasPermission($this->getRecipient(), Permission::game_matchSave));
    }

    public function populateList()
    {

        foreach ($this->items as $item) {
            $item->erase();
        }

        $this->pager->clearItems();
        $this->items = array();

        if (\ManiaLivePlugins\eXpansion\Helpers\Storage::getInstance()->isRemoteControlled) {
            $this->items[0] = new \ManiaLivePlugins\eXpansion\Adm\Gui\Controls\InfoItem(1, __("File listing disabled since you are running remote", $this->getRecipient()), $this->sizeX);
            $this->pager->addItem($this->items[0]);
            $this->items[0] = new \ManiaLivePlugins\eXpansion\Adm\Gui\Controls\InfoItem(1, __("You can tho save and load files from server by the filename!", $this->getRecipient()), $this->sizeX);
            $this->pager->addItem($this->items[0]);
        } else {
            $path = Helper::getPaths()->getMatchSettingPath() . "*.txt";

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
    }

    public function destroy()
    {
        foreach ($this->items as $item) {
            $item->erase();
        }

        $this->items = array();

        $this->saveButton->destroy();
        $this->inputboxSaveAs->destroy();
        $this->frame->destroy();

        $this->pager->destroy();
        $this->connection = null;
        $this->storage = null;
        $this->destroyComponents();
        parent::destroy();
    }

}
