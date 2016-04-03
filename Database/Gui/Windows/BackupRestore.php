<?php

namespace ManiaLivePlugins\eXpansion\Database\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLivePlugins\eXpansion\Adm\Gui\Controls\MatchSettingsFile;
use ManiaLive\Gui\ActionHandler;

class BackupRestore extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    private $pager;

    /** @var \Maniaplanet\DedicatedServer\Connection */
    private $connection;

    /** @var \ManiaLive\Data\Storage */
    private $storage;
    private $items = array();
    private $ok;
    private $cancel;
    private $inputbox;
    private $actionBackup;
    private $actionCancel;
    private $fileHandler;
    private $fileName = "c:/temp/test.sql";

    /** @var  \ManiaLive\Database\Connection */
    private $db;

    protected function onConstruct()
    {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \ManiaLivePlugins\eXpansion\Helpers\Singletons::getInstance()->getDediConnection();
        //$this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->mainFrame->addComponent($this->pager);
        $this->actionBackup = $this->createAction(array($this, "Backup"));
        $this->actionCancel = $this->createAction(array($this, "Cancel"));
        $this->inputbox = new Inputbox("filename", 60);
        $this->inputbox->setLabel("Backup filename");
        $this->mainFrame->addComponent($this->inputbox);
        $this->ok = new OkButton();
        $this->ok->colorize("0d0");
        $this->ok->setText("Create Backup");
        $this->ok->setAction($this->actionBackup);
        $this->mainFrame->addComponent($this->ok);

        $this->cancel = new OkButton();
        $this->cancel->setText("Cancel");
        $this->cancel->setAction($this->actionCancel);
        $this->mainFrame->addComponent($this->cancel);
    }

    function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX, $this->sizeY - 8);
        $this->pager->setStretchContentX($this->sizeX);
        $this->inputbox->setPosition(4, -$this->sizeY + 6);
        $this->ok->setPosition($this->sizeX - 38, -$this->sizeY + 6);
        $this->cancel->setPosition($this->sizeX - 20, -$this->sizeY + 6);
    }

    function onShow()
    {
        $this->populateList();
    }

    function populateList()
    {

        foreach ($this->items as $item)
            $item->erase();
        $this->pager->clearItems();
        $this->items = array();

        $login = $this->getRecipient();
        $x = 0;

        $path = $this->getPath();
        if (!$path)
            return;
        $files = glob($path . "/*.sql");

        foreach ($files as $file) {
            $this->items[$x] = new \ManiaLivePlugins\eXpansion\Database\Gui\Controls\SqlFile($x, $this, $file, $this->sizeX);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    function init(\ManiaLive\Database\Connection $db)
    {
        $this->db = $db;
    }

    function write($string)
    {
        if ($this->fileHandler === false)
            return;

        if (fwrite($this->fileHandler, $string) === false) {
            throw new \Exception("Writting to file failed!", 4);
        }
    }

    function getPath()
    {
        $path = $this->connection->GameDataDirectory();
        $path = dirname($path) . "/backup";
        if (!is_dir($path)) {
            if (!mkdir($path, 0777)) {
                $this->connection->chatSendServerMessage("Error while creating folder: " . $path, $login);
                return false;
            }
        }
        return $path;
    }

    function Backup($login, $inputboxes = "")
    {
        if (empty($inputboxes['filename'])) {
            $this->connection->chatSendServerMessage("No backup filename given, canceling backup!", $login);
        }
        $path = $this->getPath();
        if (!$path)
            return;
        $this->fileName = $path . "/" . $inputboxes['filename'] . ".sql";

        $this->fileHandler = fopen($this->fileName, "wb");
        $this->connection->chatSendServerMessage("Creating database backup...", $login);
        $dbconfig = \ManiaLive\Database\Config::getInstance();
        $dbName = $dbconfig->database;
        $tables = $this->db->execute("SHOW TABLES in " . $dbName . ";")->fetchArrayOfRow();
        foreach ($tables as $table) {
            $create = $this->db->execute("SHOW CREATE TABLE `" . $table[0] . "`;")->fetchAssoc();

            $this->write("-- --------------------------------------------------------\n\n");
            $this->write("--\n-- Table structure for table `" . $table[0] . "`\n--\n\n");
            $this->write("DROP TABLE IF EXISTS `" . $table[0] . "`;\n\n");
            $this->write($create['Create Table'] . ";\n\n");

            $this->write("--\n-- Dumping data for table `" . $table[0] . "`\n--\n\n");
            $data = $this->db->execute("SELECT * FROM `" . $table[0] . "`;")->fetchArrayOfRow();
            foreach ($data as $row) {
                $vals = array();
                foreach ($row as $val) {
                    $vals[] = is_null($val) ? "NULL" : $this->db->quote($val);
                }
                $this->write("INSERT INTO `" . $table[0] . "` VALUES(" . implode(", ", $vals) . ");\n");
            }
            $this->write("\n");
        }
        fclose($this->fileHandler);
        $this->fileHandler = false;
        $this->connection->chatSendServerMessage("Backup Complete!", $login);
        $this->populateList();
        $this->RedrawAll();
    }

    /** @todo imlement restore from .sql file */
    function restoreFile($login, $file)
    {
        $this->connection->chatSendServerMessage("Not implemented yet...");

    }

    function deleteFile($login, $file)
    {

        unlink($file);
        $this->connection->chatSendServerMessage("Deleted.", $login);
        $this->populateList();
        $this->RedrawAll();
    }

    function Cancel($login)
    {
        $this->erase($login);
    }

    function destroy()
    {
        foreach ($this->items as $item)
            $item->erase();

        $this->db = null;
        $this->items = array();
        $this->pager->destroy();
        $this->ok->destroy();
        $this->cancel->destroy();
        $this->inputbox->destroy();
        $this->connection = null;
        $this->storage = null;
        $this->destroyComponents();
        parent::destroy();
    }

}

?>
