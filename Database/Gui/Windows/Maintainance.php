<?php

namespace ManiaLivePlugins\eXpansion\Database\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLivePlugins\eXpansion\Adm\Gui\Controls\MatchSettingsFile;
use ManiaLive\Gui\ActionHandler;

class Maintainance extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    private $pager;

    /** @var \DedicatedApi\Connection */
    private $connection;

    /** @var \ManiaLive\Data\Storage */
    private $storage;
    private $items = array();
    private $ok;
    private $optimize;
    private $backup;
    private $cancel;
    private $actionRepair;
    private $actionOptimize;
    private $actionCancel;
    private $actionBackup;

    /** @var  \ManiaLive\Database\Connection */
    private $db;

    protected function onConstruct() {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
        //$this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->pager = new \ManiaLive\Gui\Controls\Pager();
        $this->mainFrame->addComponent($this->pager);
        $this->actionRepair = $this->createAction(array($this, "Repair"));
        $this->actionOptimize = $this->createAction(array($this, "Optimize"));

        $this->actionBackup = $this->createAction(array($this, "Backup"));
        $this->actionCancel = $this->createAction(array($this, "Cancel"));

        $this->ok = new OkButton();
        $this->ok->colorize("0d0");
        $this->ok->setText("Repair");
        $this->ok->setAction($this->actionRepair);
        $this->mainFrame->addComponent($this->ok);

        $this->optimize = new OkButton();
        $this->optimize->colorize("0d0");
        $this->optimize->setText("Optimize");
        $this->optimize->setAction($this->actionOptimize);
        $this->mainFrame->addComponent($this->optimize);

        $this->backup = new OkButton();
        $this->backup->colorize("d00");
        $this->backup->setText("Backup");
        $this->backup->setAction($this->actionBackup);
        $this->mainFrame->addComponent($this->backup);

        $this->cancel = new OkButton();
        $this->cancel->setText("Cancel");
        $this->cancel->setAction($this->actionCancel);
        $this->mainFrame->addComponent($this->cancel);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX, $this->sizeY - 8);
        $this->pager->setStretchContentX($this->sizeX);
        $this->ok->setPosition($this->sizeX - 38, -$this->sizeY + 6);
        $this->optimize->setPosition($this->sizeX - 58, -$this->sizeY + 6);
        $this->cancel->setPosition($this->sizeX - 20, -$this->sizeY + 6);
        $this->backup->setPosition(4, -$this->sizeY + 6);
    }

    function onShow() {
        $this->populateList();
    }

    function populateList() {
        foreach ($this->items as $item)
            $item->destroy();
        $this->pager->clearItems();
        $this->items = array();

        $login = $this->getRecipient();
        $x = 0;
        $dbconfig = \ManiaLive\Database\Config::getInstance();
        $dbName = $dbconfig->database;
        $tables = $this->db->query("SHOW TABLES in " . $dbName . ";")->fetchArrayOfRow();

        foreach ($tables as $table) {
            $this->items[$x] = new \ManiaLivePlugins\eXpansion\Database\Gui\Controls\DbTable($x, $table[0], $this->sizeX);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    function init(\ManiaLive\Database\Connection $db) {
        $this->db = $db;
    }

    function Backup($login) {

        $window = BackupRestore::Create($login);
        $window->init($this->db);
        $window->setTitle(__('Database Backup and Restore'));
        $window->centerOnScreen();
        $window->setSize(160, 100);
        $window->show();
        $this->erase($login);
    }

    function Repair($login) {

        foreach ($this->items as $item) {
            // if checkbox checked
            if ($item->checkBox->getStatus()) {
                // repair table
                $status = $this->db->query("REPAIR TABLE " . $item->tableName . ";")->fetchObject();
                $this->connection->chatSendServerMessage("Table " . $status->Table . " repaired with " . $status->Msg_type . ":" . $status->Msg_text, $login);
            }
        }
        //   $this->erase($login);
    }

    function Optimize($login) {

        foreach ($this->items as $item) {
            // if checkbox checked
            if ($item->checkBox->getStatus()) {
                // repair table
                $status = $this->db->query("OPTIMIZE TABLE " . $item->tableName . ";")->fetchObject();
                $this->connection->chatSendServerMessage("Table " . $status->Table . " Optimized with " . $status->Msg_type . ":" . $status->Msg_text, $login);
            }
        }
        //  $this->erase($login);
    }

    function Cancel($login) {
        $this->erase($login);
    }

    function destroy() {
        foreach ($this->items as $item)
            $item->destroy();

        $this->db = null;
        $this->items = array();
        $this->pager->destroy();
        $this->ok->destroy();
        $this->optimize->destroy();
        $this->backup->destroy();

        $this->cancel->destroy();
        $this->connection = null;
        $this->storage = null;
        $this->clearComponents();
        parent::destroy();
    }

}

?>
