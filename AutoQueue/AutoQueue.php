<?php
/*
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace ManiaLivePlugins\eXpansion\AutoQueue;

use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\AutoQueue\Classes\Queue;
use ManiaLivePlugins\eXpansion\AutoQueue\Gui\Widgets\EnterQueueWidget;
use ManiaLivePlugins\eXpansion\AutoQueue\Gui\Widgets\QueueList;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use Maniaplanet\DedicatedServer\Structures\PlayerInfo;
use Maniaplanet\DedicatedServer\Structures\Status;

/**
 * Description of AutoQueue
 *
 * @author Reaby
 */
class AutoQueue extends ExpPlugin
{
    /** @var Queue */
    private $queue;
    public static $enterAction;
    public static $leaveAction;

    public function eXpOnReady()
    {
        $this->enableDedicatedEvents();
        $this->queue = new Queue();

        $ah = ActionHandler::getInstance();
        self::$enterAction = $ah->createAction(array($this, "enterQueue"));
        self::$leaveAction = $ah->createAction(array($this, "leaveQueue"));

        foreach ($this->storage->spectators as $login => $player) {
            $this->connection->forceSpectator($login, 1);
            $this->showEnterQueue($login);
        }
        $this->widgetSyncList();

//$this->registerChatCommand("next", "queueReleaseNext", 0, true);
    }

    function onPlayerConnect($login, $isSpectator)
    {
        if ($isSpectator) {
            $this->connection->forceSpectator($login, 1);
            $this->showEnterQueue($login);
            $this->widgetSyncList();
        }
    }

    public function onPlayerInfoChanged($info)
    {
        if ($this->storage->serverStatus->code != Status::PLAY) return;

        $player = PlayerInfo::fromArray($info);
        $login = $player->login;

        if (in_array($login, $this->queue->getLogins())) return;

        if ($player->spectator) {
            $this->showEnterQueue($login);
            $this->widgetSyncList();

            try {
                $this->connection->forceSpectator($login, 1);
            } catch (\Exception $ex) {

            }
            if ($player->hasPlayerSlot) {
                try {
                    $this->connection->spectatorReleasePlayerSlot($login);
                } catch (\Exception $e) {

                }
            }

            if ($this->storage->server->currentMaxPlayers > count($this->storage->players)) {
                $this->queueReleaseNext();
            }
        } else {
            $this->widgetSyncList();
            EnterQueueWidget::Erase($login);
        }
    }

    public function onPlayerDisconnect($login, $disconnectionReason = null)
    {
        if (in_array($login, $this->queue->getLogins())) {
            $this->queue->remove($login);
        }
        $this->queueReleaseNext();
    }

    function onBeginMatch()
    {
        $this->queRealeseAvailable();
    }

    function onBeginRound()
    {
        $this->queRealeseAvailable();
    }

    public function queRealeseAvailable()
    {
        for ($i = 0; $i < $this->storage->server->currentMaxPlayers; $i++) {
            $this->queueReleaseNext();
        }
    }

    public function queueReleaseNext()
    {
        echo count($this->storage->players). "<". $this->storage->server->currentMaxPlayers ."\n";
        
        if (count($this->storage->players) <= $this->storage->server->currentMaxPlayers) {
            $player = $this->queue->getNextPlayer();
            if ($player) {
                $this->connection->forceSpectator($player->login, 2);
                $this->connection->forceSpectator($player->login, 0);
                $msg = exp_getMessage('You got free spot, good luck and have fun!');
                $this->exp_chatSendServerMessage($msg, $player->login);
            }
        }
        $this->widgetSyncList();
    }

    public function admRemoveQueue($login, $target)
    {
        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login,
            \ManiaLivePlugins\eXpansion\AdminGroups\Permission::server_admin)
        ) {
            if (in_array($target, $this->queue->getLogins())) {
                $this->queue->remove($target);
                $this->exp_chatSendServerMessage(exp_getMessage("Admin has removed you from queue!", $target));
                $this->exp_chatSendServerMessage(exp_getMessage('Removed player %s $z$ffffrom queue'), $login,
                    array($this->storage->getPlayerObject($target)->nickName));
            }
        }
        EnterQueueWidget::Erase($login);
        $this->widgetSyncList();
    }

    public function enterQueue($login)
    {
        $this->queue->add($login);

        if ($this->storage->server->currentMaxPlayers > count($this->storage->players)) {
            $this->queueReleaseNext();
        }

        EnterQueueWidget::Erase($login);
        $this->widgetSyncList();
    }

    public function leaveQueue($login)
    {
        $this->queue->remove($login);
        $this->showEnterQueue($login);
        $this->widgetSyncList();
    }

    public function exp_onUnload()
    {
        $ah = ActionHandler::getInstance();
        $ah->deleteAction(self::$enterAction);
        $ah->deleteAction(self::$leaveAction);
        self::$enterAction = null;
        self::$leaveAction = null;
        EnterQueueWidget::EraseAll();
        QueueList::EraseAll();
        $this->queue = null;
    }

    public function widgetSyncList()
    {
        $this->queue->syncPlayers(array_keys($this->storage->players));

        QueueList::EraseAll();

        foreach ($this->storage->spectators as $login => $player) {
            $widget = QueueList::Create($login);
            $widget->setPlayers($this->queue->getQueuedPlayers(), $this);
            $widget->show();
        }
    }

    public function showEnterQueue($login)
    {
        $widget = EnterQueueWidget::Create($login);
        $widget->show($login);
    }
}