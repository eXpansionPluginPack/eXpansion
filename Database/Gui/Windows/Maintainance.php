<?php

namespace ManiaLivePlugins\eXpansion\Database\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLivePlugins\eXpansion\Adm\Gui\Controls\MatchSettingsFile;
use ManiaLive\Gui\ActionHandler;

class Maintainance extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

	private $pager;

	/** @var \Maniaplanet\DedicatedServer\Connection */
	private $connection;

	/** @var \ManiaLive\Data\Storage */
	private $storage;

	/** @var \ManiaLivePlugins\eXpansion\Database\Gui\Controls\DbTable[] */
	private $items = array();

	private $frame;

	private $ok;

	private $optimize;

	private $backup;

	private $cancel;

	private $truncate;

	private $actionRepair;

	private $actionOptimize;

	private $actionCancel;

	private $actionBackup;

	private $actionConfirmTruncate;

	private $actionTruncate;

	/** @var  \ManiaLive\Database\Connection */
	private $db;

	protected function onConstruct()
	{
		parent::onConstruct();
		$config = \ManiaLive\DedicatedApi\Config::getInstance();
		$this->connection = \Maniaplanet\DedicatedServer\Connection::factory($config->host, $config->port);
		//$this->storage = \ManiaLive\Data\Storage::getInstance();
		$this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
		$this->addComponent($this->pager);

		$this->actionRepair = $this->createAction(array($this, "Repair"));
		$this->actionOptimize = $this->createAction(array($this, "Optimize"));
		$this->actionBackup = $this->createAction(array($this, "Backup"));
		$this->actionCancel = $this->createAction(array($this, "Cancel"));
		$this->actionTruncate = $this->createAction(array($this, "Truncate"));

		$this->frame = new \ManiaLive\Gui\Controls\Frame();
		$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
		$this->addComponent($this->frame);

		$this->truncate = new OkButton();
		$this->truncate->colorize("d00");
		$this->truncate->setText("Clear Table");
		$this->truncate->setDescription("BEWARE, No confirm, No undo!",60);
		$this->truncate->setAction($this->actionTruncate);
		$this->frame->addComponent($this->truncate);

		$this->ok = new OkButton();

		$this->ok->setText("Repair");
		$this->ok->setAction($this->actionRepair);
		$this->frame->addComponent($this->ok);

		$this->optimize = new OkButton();

		$this->optimize->setText("Optimize");
		$this->optimize->setAction($this->actionOptimize);
		$this->frame->addComponent($this->optimize);

		$this->backup = new OkButton();
		$this->backup->colorize("0d0");
		$this->backup->setText("Backup");
		$this->backup->setAction($this->actionBackup);
		$this->frame->addComponent($this->backup);

		$this->cancel = new OkButton();
		$this->cancel->setText("Cancel");
		$this->cancel->setAction($this->actionCancel);
		$this->frame->addComponent($this->cancel);
	}

	function onResize($oldX, $oldY)
	{
		parent::onResize($oldX, $oldY);
		$this->pager->setSize($this->sizeX - 4, $this->sizeY - 10);
		$this->pager->setPosition(3, 0);
		$this->frame->setPosition(30, -$this->sizeY + 3);
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
		$dbconfig = \ManiaLive\Database\Config::getInstance();
		$dbName = $dbconfig->database;
		$tables = $this->db->execute("SHOW TABLES in " . $dbName . ";")->fetchArrayOfRow();

		foreach ($tables as $table) {
			$this->items[$x] = new \ManiaLivePlugins\eXpansion\Database\Gui\Controls\DbTable($x, $table[0], $this->sizeX);
			$this->pager->addItem($this->items[$x]);
			$x++;
		}
	}

	function init(\ManiaLive\Database\Connection $db)
	{
		$this->db = $db;
	}

	function Backup($login)
	{

		$window = BackupRestore::Create($login);
		$window->init($this->db);
		$window->setTitle(__('Database Backup and Restore'));
		$window->centerOnScreen();
		$window->setSize(160, 100);
		$window->show();
		$this->erase($login);
	}

	function Repair($login, $args)
	{

		foreach ($this->items as $item) {
			// if checkbox checked

			$this->syncCheckboxItem($item, $args);
			if ($item->checkBox->getStatus()) {
				// repair table
				$status = $this->db->execute("REPAIR TABLE " . $item->tableName . ";")->fetchObject();
				$this->connection->chatSendServerMessage("Table " . $status->Table . " repaired with " . $status->Msg_type . ":" . $status->Msg_text, $login);
			}
		}
		//   $this->erase($login);
	}

	function Truncate($login, $args)
	{
		foreach ($this->items as $item) {
			// if checkbox checked
			$this->syncCheckboxItem($item, $args);
			if ($item->checkBox->getStatus()) {
				// repair table
				$status = $this->db->execute("TRUNCATE TABLE " . $item->tableName . ";");
				$this->connection->chatSendServerMessage('Table \'$0d0' . $item->tableName . '$fff\' contents is now $d00CLEARED$fff!', $login);
			}
		}
		//  $this->erase($login);
	}

	function Optimize($login, $args)
	{

		foreach ($this->items as $item) {
			// if checkbox checked
			$this->syncCheckboxItem($item, $args);
			if ($item->checkBox->getStatus()) {
				// repair table
				$status = $this->db->execute("OPTIMIZE TABLE " . $item->tableName . "`;")->fetchObject();
				$this->connection->chatSendServerMessage("Table " . $status->Table . " Optimized with " . $status->Msg_type . ":" . $status->Msg_text, $login);
			}
		}
		//  $this->erase($login);
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
		$this->frame->destroy();
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

	function syncCheckboxItem(&$item, $args)
	{
		foreach ($item->getComponents() as &$component) {
			if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\CheckboxScripted) {
				$component->setArgs($args);
			}
		}
	}

}

?>
