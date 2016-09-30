<?php

namespace ManiaLivePlugins\eXpansion\Players\Gui\Windows;

use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Helpers\Helper;

class Playerlist extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{
    protected $pager;

    /** @var \ManiaLivePlugins\eXpansion\Players\Players */
    public static $mainPlugin;

    /** @var \Maniaplanet\DedicatedServer\Connection */
    protected $connection;

    /** @var \ManiaLive\Data\Storage */
    protected $storage;
    protected $items = array();
    protected $frame;
    protected $title_status;
    protected $title_login;
    protected $title_nickname;

    public static $widths = array(1, 8, 6, 6);

    protected function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();
        \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \ManiaLivePlugins\eXpansion\Helpers\Singletons::getInstance()->getDediConnection();
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->setScriptEvents();
        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\OptimizedPager();
        $this->mainFrame->addComponent($this->pager);
        $this->setName("Players on server");

        $line = new \ManiaLive\Gui\Controls\Frame(18, 2);
        $line->setLayout(new \ManiaLib\Gui\Layouts\Line());
        if (AdminGroups::hasPermission($login, Permission::PLAYER_IGNORE)) {
            $btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
            $btn->setText(__("Ignore List", $login));
            $btn->setAction(\ManiaLivePlugins\eXpansion\Chat_Admin\Chat_Admin::$showActions['ignore']);
            $line->addComponent($btn);
        }

        if (AdminGroups::hasPermission($login, Permission::GAME_SETTINGS)) {
            $btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
            $btn->setText(__("Guest List", $login));
            $btn->setAction(\ManiaLivePlugins\eXpansion\Chat_Admin\Chat_Admin::$showActions['guest']);
            $line->addComponent($btn);
        }

        if (AdminGroups::hasPermission($login, Permission::PLAYER_UNBAN)) {
            $btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
            $btn->setText(__("Ban List", $login));
            $btn->setAction(\ManiaLivePlugins\eXpansion\Chat_Admin\Chat_Admin::$showActions['ban']);
            $line->addComponent($btn);
        }
        if (AdminGroups::hasPermission($login, Permission::PLAYER_BLACK)) {
            $btn = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
            $btn->setText(__("Black List", $login));
            $btn->setAction(\ManiaLivePlugins\eXpansion\Chat_Admin\Chat_Admin::$showActions['black']);
            $line->addComponent($btn);
        }

        $this->mainFrame->addComponent($line);

        Gui::getScaledSize(self::$widths, $this->sizeX);
    }

    public function ignorePlayer($login, $target)
    {
        try {
            $login = $this->getRecipient();
            if (!AdminGroups::hasPermission($login, Permission::PLAYER_IGNORE)) {
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
                $this->connection->chatSendServerMessage(
                    __('%s$z$s$fff was ignored by admin %s', $login, $player->nickName, $admin->nickName)
                );
            } else {
                $this->connection->unignore($target);
                $this->connection->chatSendServerMessage(
                    __('%s$z$s$fff was unignored by admin %s', $login, $player->nickName, $admin->nickName)
                );
            }

            $this->show($login);
        } catch (\Exception $e) {
            Helper::logError("Error:" . $e->getMessage());
        }
    }

    public function kickPlayer($login, $target)
    {
        try {
            AdminGroups::getInstance()->adminCmd($login, "kick " . $target);
        } catch (\Exception $e) {
            Helper::logError("Error:" . $e->getMessage());
        }
    }

    public function banPlayer($login, $target)
    {
        try {
            AdminGroups::getInstance()->adminCmd($login, "ban " . $target);
        } catch (\Exception $e) {
            Helper::logError("Error:" . $e->getMessage());
        }
    }

    public function blacklistPlayer($login, $target)
    {
        try {
            AdminGroups::getInstance()->adminCmd($login, "black " . $target);
        } catch (\Exception $e) {
            Helper::logError("Error:" . $e->getMessage());
        }
    }

    public function guestlistPlayer($login, $target)
    {
        try {
            AdminGroups::getInstance()->adminCmd($login, "guest " . $target);
        } catch (\Exception $e) {
            Helper::logError("Error:" . $e->getMessage());
        }
    }

    public function toggleSpec($login, $target)
    {
        try {
            $login = $this->getRecipient();
            if (!AdminGroups::hasPermission($login, Permission::PLAYER_FORCESPEC)) {
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
                $this->connection->chatSendServerMessage(
                    __("Admin has released you from specate to play.", $target),
                    $target
                );

                return;
            }
        } catch (\Exception $e) {
            Helper::logError("Error:" . $e->getMessage());
        }
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setPosition(0, -6);
        $this->pager->setSize($this->sizeX, $this->sizeY - 10);
    }

    public function onDraw()
    {
        $this->populateList();
        parent::onDraw();
    }

    public function toggleTeam($login, $target)
    {
        if (AdminGroups::hasPermission($login, Permission::PLAYER_CHANGE_TEAM)) {
            $player = $this->storage->getPlayerObject($target);
            if ($player->teamId === 0) {
                $this->connection->forcePlayerTeam($target, 1);
            }
            if ($player->teamId === 1) {
                $this->connection->forcePlayerTeam($target, 0);
            }
        }
    }

    private function populateList()
    {

        $this->pager->clearItems();
        $this->items = array();
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $login = $this->getRecipient();
        $isadmin = AdminGroups::hasPermission($login, Permission::PLAYER_FORCESPEC);

        $list = $this->connection->getIgnoreList(-1, 0);
        $ignoreList = array();
        foreach ($list as $player) {
            $ignoreList[$player->login] = true;
        }

        foreach ($this->storage->players as $player) {
            $ignoreAction = $this->createAction(array($this, 'ignorePlayer'), $player->login);
            $kickAction = $this->createAction(array($this, 'kickPlayer'), $player->login);
            $banAction = $this->createAction(array($this, 'banPlayer'), $player->login);
            $blacklistAction = $this->createAction(array($this, 'blacklistPlayer'), $player->login);
            $forceAction = $this->createAction(array($this, 'toggleSpec'), $player->login);
            $guestAction = $this->createAction(array($this, 'guestlistPlayer'), $player->login);

            $this->pager->addSimpleItems(array(Gui::fixString($player->nickName) . " " => -1,
                Gui::fixString($player->login) => -1,
                "ignore" => $ignoreAction,
                "kick" => $kickAction,
                "ban" => $banAction,
                "blacklist" => $blacklistAction,
                "force" => $forceAction,
                "guest" => $guestAction,
            ));
        }
        foreach ($this->storage->spectators as $player) {

            $ignoreAction = $this->createAction(array($this, 'ignorePlayer'), $player->login);
            $kickAction = $this->createAction(array($this, 'kickPlayer'), $player->login);
            $banAction = $this->createAction(array($this, 'banPlayer'), $player->login);
            $blacklistAction = $this->createAction(array($this, 'blacklistPlayer'), $player->login);
            $forceAction = $this->createAction(array($this, 'toggleSpec'), $player->login);
            $guestAction = $this->createAction(array($this, 'guestlistPlayer'), $player->login);

            $this->pager->addSimpleItems(array(Gui::fixString($player->nickName) . " " => -1,
                Gui::fixString($player->login) => -1,
                "ignore" => $ignoreAction,
                "kick" => $kickAction,
                "ban" => $banAction,
                "blacklist" => $blacklistAction,
                "force" => $forceAction,
                "guest" => $guestAction,
            ));
        }

        $this->pager->setContentLayout('\\ManiaLivePlugins\\eXpansion\\Players\\Gui\\Controls\\Playeritem');
        $this->pager->update($this->getRecipient());
    }

    public function destroy()
    {
        $this->connection = null;
        $this->storage = null;
        foreach ($this->items as $item) {
            $item->erase();
        }

        $this->items = null;


        parent::destroy();
    }
}
