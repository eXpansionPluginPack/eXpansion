<?php

namespace ManiaLivePlugins\eXpansion\Players\Gui\Windows;

use ManiaLivePlugins\eXpansion\Players\Gui\Controls\Playeritem;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\Gui\Gui;

class Playerlist extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    protected $pager;

    /** @var \ManiaLivePlugins\eXpansion\Players\Players */
    public static $mainPlugin;

    /** @var \DedicatedApi\Connection */
    private $connection;

    /** @var \ManiaLive\Data\Storage */
    private $storage;
    private $items = array();
    private $frame;
    protected $title_status, $title_login, $title_nickname;
    private $widths;

    protected function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->mainFrame->addComponent($this->pager);
        $this->widths = array(1, 8, 6, 5);
        
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($this->sizeX, 4);
        $this->frame->setPosY(0);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->mainFrame->addComponent($this->frame);
        
        $textStyle = "TextCardRaceRank";
        $textColor = "000";
        $textSize = 2.5;
        $scaledSizes = Gui::getScaledSize($this->widths, $this->sizeX/.8);
        
        $this->title_status = new \ManiaLib\Gui\Elements\Label();
        $this->title_status->setText(__("", $login));
        $this->title_status->setStyle($textStyle);
        $this->title_status->setTextColor($textColor);
        $this->title_status->setTextSize($textSize);
        $this->title_status->setScale(0.8);
        $this->title_status->setSizeX(4);
        $this->title_status->setAlign('left', 'center');
        $this->frame->addComponent($this->title_status);
        
        $this->title_nickname = new \ManiaLib\Gui\Elements\Label();
        $this->title_nickname->setText(__("NickName", $login));
        $this->title_nickname->setStyle($textStyle);
        $this->title_nickname->setTextColor($textColor);
        $this->title_nickname->setTextSize($textSize);
        $this->title_nickname->setScale(0.8);
        $this->title_nickname->setSizeX($scaledSizes[1]);
        $this->title_nickname->setAlign('left', 'center');
        $this->frame->addComponent($this->title_nickname);
        
        $this->title_login = new \ManiaLib\Gui\Elements\Label();
        $this->title_login->setText(__("Login", $login));
        $this->title_login->setStyle($textStyle);
        $this->title_login->setTextColor($textColor);
        $this->title_login->setTextSize($textSize);
        $this->title_login->setScale(0.8);
        $this->title_login->setSizeX($scaledSizes[2]);
        $this->title_login->setAlign('left', 'center');
        $this->frame->addComponent($this->title_login);
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
        $this->pager->setSize($this->sizeX - 5, $this->sizeY - 8);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(4, -4);
        
        $scaledSizes = Gui::getScaledSize($this->widths, $this->sizeX/.8);
        $this->title_status->setSizeX($scaledSizes[0]);
        $this->title_nickname->setSizeX($scaledSizes[1]);
        $this->title_login->setSizeX($scaledSizes[2]);
    }

    function onShow() {        
        $this->populateList();
        parent::onShow();
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

        //try {
            foreach ($this->storage->players as $player) {
                $this->items[$x] = new Playeritem($x++, $player, $this, $isadmin, $this->getRecipient(), $this->widths, $this->sizeX);
                $this->pager->addItem($this->items[$x]);
            }
            foreach ($this->storage->spectators as $player) {
                $this->items[$x] = new Playeritem($x++, $player, $this, $isadmin, $this->getRecipient(), $this->widths, $this->sizeX);
                $this->pager->addItem($this->items[$x]);
            }
        /*} catch (\Exception $e) {

        }*/
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
