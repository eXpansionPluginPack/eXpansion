<?php

namespace ManiaLivePlugins\eXpansion\Players\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLivePlugins\eXpansion\Players\Gui\Controls\Playeritem;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLive\Utilities\Console;

class Playerlist extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    protected $pager;

    /** @var \ManiaLivePlugins\eXpansion\Players\Players */
    public static $mainPlugin;

    /** @var \DedicatedApi\Connection */
    private $connection;

    /** @var \ManiaLive\Data\Storage */
    private $storage;
    private $items = array();
    protected $title_status, $title_login, $title_nickname, $title_actions;
    private $widths;

    protected function onConstruct() {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->pager = new \ManiaLive\Gui\Controls\Pager();
        $this->mainFrame->addComponent($this->pager);
        $this->widths = array(4, 6, 7);
    }

    function ignorePlayer($login, $target) {
        try {
            $login = $this->getRecipient();
            if (!AdminGroups::hasPermission($login, 'player_ignore')) {
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
            } else {
                $this->connection->unignore($target);
                $this->connection->chatSendServerMessage(__('%s$z$s$fff was unignored by admin %s', $login, $player->nickName, $admin->nickName));
            }
        } catch (\Exception $e) {
            //   $this->connection->chatSendServerMessage(__("Error:".$e->getMessage()));
            $this->console("Error:" . $e->getMessage());
        }
    }

    function kickPlayer($login, $target) {
        try {
            AdminGroups::getInstance()->adminCmd($login, "kick " . $target);
        } catch (\Exception $e) {
            //$this->connection->chatSendServerMessage(__("Error:".$e->getMessage()));
            $this->console("Error:" . $e->getMessage());
        }
    }

    function banPlayer($login, $target) {
        try {
            AdminGroups::getInstance()->adminCmd($login, "ban " . $target);
        } catch (\Exception $e) {
            //$this->connection->chatSendServerMessage(__("Error:".$e->getMessage()));
            $this->console("Error:" . $e->getMessage());
        }
    }

    function blacklistPlayer($login, $target) {
        try {
            AdminGroups::getInstance()->adminCmd($login, "black " . $target);
        } catch (\Exception $e) {
            //  $this->connection->chatSendServerMessage(__("Error:".$e->getMessage()));
            $this->console("Error:" . $e->getMessage());
        }
    }

    function toggleSpec($login, $target) {
        try {
            $login = $this->getRecipient();
            if (!AdminGroups::hasPermission($login, 'player_spec')) {
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
            $this->console("Error:" . $e->getMessage());
            //$this->connection->chatSendServerMessage(__("Error:".$login, $e->getMessage()), $login);
        }
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 8);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(4, -4);
    }

    function onShow() {
        $this->populateList();
    }

    function toggleTeam($login, $target) {
        if (AdminGroups::hasPermission($login, "server_admin")) {
            $player = $this->storage->getPlayerObject($target);
            if ($player->teamId === 0)
                $this->connection->forcePlayerTeam($target, 1);
            if ($player->teamId === 1)
                $this->connection->forcePlayerTeam($target, 0);
        }
    }

    function populateList() {

        foreach ($this->items as $item)
            $item->erase();
        $this->pager->clearItems();
        $this->items = array();
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $x = 0;
        $login = $this->getRecipient();
        $isadmin = AdminGroups::hasPermission($login, "server_admin");

        try {
            foreach ($this->storage->players as $player) {
                $this->items[$x] = new Playeritem($x++, $player, $this, $isadmin, $this->getRecipient(), $this->widths, $this->sizeX);
                $this->pager->addItem($this->items[$x]);
            }
            foreach ($this->storage->spectators as $player) {
                $this->items[$x] = new Playeritem($x++, $player, $this, $isadmin, $this->getRecipient(), $this->widths, $this->sizeX);
                $this->pager->addItem($this->items[$x]);
            }
        } catch (\Exception $e) {
            $this->console("Error: " . $e->getMessage());
        }
    }

    function destroy() {
        $this->connection = null;
        $this->storage = null;
        foreach ($this->items as $item)
            $item->erase();

        $this->items = null;


        parent::destroy();
    }

}

?>
