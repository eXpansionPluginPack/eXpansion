<?php

namespace ManiaLivePlugins\eXpansion\Players\Gui\Windows;

use ManiaLivePlugins\eXpansion\Players\Gui\Controls\Playeritem;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use \ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Helpers\Helper;

class Playerlist extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

	protected $pager;

	/** @var \ManiaLivePlugins\eXpansion\Players\Players */
	public static $mainPlugin;

	/** @var \Maniaplanet\DedicatedServer\Connection */
	private $connection;

	/** @var \ManiaLive\Data\Storage */
	private $storage;

	private $items = array();

	private $frame;

	protected $title_status, $title_login, $title_nickname;

	private $widths;

	protected function onConstruct()
	{
		parent::onConstruct();
		$login = $this->getRecipient();
		$config = \ManiaLive\DedicatedApi\Config::getInstance();
		$this->connection = \Maniaplanet\DedicatedServer\Connection::factory($config->host, $config->port);
		$this->storage = \ManiaLive\Data\Storage::getInstance();

		$this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
		$this->mainFrame->addComponent($this->pager);
		$this->widths = array(1, 8, 6, 6);

		$line = new \ManiaLive\Gui\Controls\Frame(18, 0);
		$line->setLayout(new \ManiaLib\Gui\Layouts\Line());
		if (AdminGroups::hasPermission($login, Permission::player_ignore)) {
			$btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
			$btn->setText(__("Ignore List", $login));
			$btn->setAction(\ManiaLivePlugins\eXpansion\Chat_Admin\Chat_Admin::$showActions['ignore']);
			$line->addComponent($btn);
		}

		if (AdminGroups::hasPermission($login, Permission::game_settings)) {
			$btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
			$btn->setText(__("Guest List", $login));
			$btn->setAction(\ManiaLivePlugins\eXpansion\Chat_Admin\Chat_Admin::$showActions['guest']);
			$line->addComponent($btn);
		}

		if (AdminGroups::hasPermission($login, Permission::player_unban)) {
			$btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
			$btn->setText(__("Ban List", $login));
			$btn->setAction(\ManiaLivePlugins\eXpansion\Chat_Admin\Chat_Admin::$showActions['ban']);
			$line->addComponent($btn);
		}
		if (AdminGroups::hasPermission($login, Permission::player_black)) {
			$btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
			$btn->setText(__("Black List", $login));
			$btn->setAction(\ManiaLivePlugins\eXpansion\Chat_Admin\Chat_Admin::$showActions['black']);
			$line->addComponent($btn);
		}


		$this->mainFrame->addComponent($line);


		$textStyle = "TextCardRaceRank";
		$textColor = "fff";
		$textSize = 2.5;
		$scaledSizes = Gui::getScaledSize($this->widths, $this->sizeX);
	}

	function ignorePlayer($login, $target)
	{
		try {
			$login = $this->getRecipient();
			if (!AdminGroups::hasPermission($login, Permission::player_ignore)) {
				$this->connection->chatSendServerMessage(__('$ff3$iYou are not allowed to do that!', $login), $login);
			}
			$player = $this->storage->getPlayerObject($target);
			$admin = $this->storage->getPlayerObject($login);
			$list = $this->connection->getIgnoreList(-1, 0);
			$ignore = true;
			foreach ($list as $test) {
				if ($target == $test->login) {
					$ignore = false;
					break;
				}
			}
			if ($ignore) {
				$this->connection->ignore($target);
				$this->connection->chatSendServerMessage(__('%s$z$s$fff was ignored by admin %s', $login, $player->nickName, $admin->nickName));
			}
			else {
				$this->connection->unignore($target);
				$this->connection->chatSendServerMessage(__('%s$z$s$fff was unignored by admin %s', $login, $player->nickName, $admin->nickName));
			}

			$this->show($login);
		} catch (\Exception $e) {
			//   $this->connection->chatSendServerMessage(__("Error:".$e->getMessage()));
			Helper::logError("Error:" . $e->getMessage());
		}
	}

	function kickPlayer($login, $target)
	{
		try {
			AdminGroups::getInstance()->adminCmd($login, "kick " . $target);
		} catch (\Exception $e) {
			//$this->connection->chatSendServerMessage(__("Error:".$e->getMessage()));
			Helper::logError("Error:" . $e->getMessage());
		}
	}

	function banPlayer($login, $target)
	{
		try {
			AdminGroups::getInstance()->adminCmd($login, "ban " . $target);
		} catch (\Exception $e) {
			//$this->connection->chatSendServerMessage(__("Error:".$e->getMessage()));
			Helper::logError("Error:" . $e->getMessage());
		}
	}

	function blacklistPlayer($login, $target)
	{
		try {
			AdminGroups::getInstance()->adminCmd($login, "black " . $target);
		} catch (\Exception $e) {
			//  $this->connection->chatSendServerMessage(__("Error:".$e->getMessage()));
			Helper::logError("Error:" . $e->getMessage());
		}
	}

	function toggleSpec($login, $target)
	{
		try {
			$login = $this->getRecipient();
			if (!AdminGroups::hasPermission($login, Permission::player_forcespec)) {
				$this->connection->chatSendServerMessage(__('$ff3$iYou are not allowed to do that!', $login), $login);
			}
			$player = $this->storage->getPlayerObject($target);

			if ($player->spectatorStatus == 0) {
				$this->connection->forceSpectator($target, 1);
				$this->connection->chatSendServerMessage(__('Admin has forced you to specate!', $target), $target);
				return;
			}
			if ($player->spectator == 1) {
				$this->connection->forceSpectator($target, 2);
				$this->connection->forceSpectator($target, 0);
				$this->connection->chatSendServerMessage(__("Admin has released you from specate to play.", $target), $target);
				return;
			}
		} catch (\Exception $e) {
			Helper::logError("Error:" . $e->getMessage());
			//$this->connection->chatSendServerMessage(__("Error:".$login, $e->getMessage()), $login);
		}
	}

	function onResize($oldX, $oldY)
	{
		parent::onResize($oldX, $oldY);
		$this->pager->setSize($this->sizeX, $this->sizeY - 10);
	
	}

	function onDraw()
	{
		$this->populateList();
		parent::onDraw();
	}

	function toggleTeam($login, $target)
	{
		if (AdminGroups::hasPermission($login, Permission::player_changeTeam)) {
			$player = $this->storage->getPlayerObject($target);
			if ($player->teamId === 0)
				$this->connection->forcePlayerTeam($target, 1);
			if ($player->teamId === 1)
				$this->connection->forcePlayerTeam($target, 0);
		}
	}

	private function populateList()
	{

		foreach ($this->items as $item)
			$item->erase();

		$this->pager->clearItems();
		$this->items = array();
		$this->storage = \ManiaLive\Data\Storage::getInstance();
		$x = 0;
		$login = $this->getRecipient();
		$isadmin = AdminGroups::hasPermission($login, Permission::player_forcespec);

		$list = $this->connection->getIgnoreList(-1, 0);
		$ignoreList = array();
		foreach ($list as $player) {
			$ignoreList[$player->login] = true;
		}

		foreach ($this->storage->players as $player) {
			$this->items[$x] = new Playeritem($x++, $player, $this, $isadmin, $this->getRecipient(), $this->widths, $this->sizeX, isset($ignoreList[$player->login]));
			$this->pager->addItem($this->items[$x]);
		}
		foreach ($this->storage->spectators as $player) {
			$this->items[$x] = new Playeritem($x++, $player, $this, $isadmin, $this->getRecipient(), $this->widths, $this->sizeX, isset($ignoreList[$player->login]));
			$this->pager->addItem($this->items[$x]);
		}
	}

	function destroy()
	{
		$this->connection = null;
		$this->storage = null;
		foreach ($this->items as $item)
			$item->erase();

		$this->items = null;


		parent::destroy();
	}

}

?>
