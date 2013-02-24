<?php

namespace ManiaLivePlugins\eXpansion\Players\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLivePlugins\eXpansion\Players\Gui\Controls\Playeritem;
use ManiaLive\Gui\ActionHandler;

class Playerlist extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    private $pager;
    private $connection;
    private $storage;

    protected function onConstruct() {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->pager = new \ManiaLive\Gui\Controls\Pager();
        $this->mainFrame->addComponent($this->pager);
    }

    function kickPlayer($login, $target) {
        try {
            $login = $this->getRecipient();
            $player = $this->storage->getPlayerObject($target);
            $admin = $this->storage->getPlayerObject($login);
            $this->connection->kick($target, _("Please behave next time you visit the server!"));
            $this->connection->chatSendServerMessage(_('$%sz were kicked from the server by admin.', $player->nickName));
            // can't use notice...since $this->storage->players too slow.
            // $this->connection->sendNotice($this->storage->players, $player->nickName . '$z were kicked from the server by admin.');
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(_("Error: %s",$e->getMessage()));
        }
    }

    function banPlayer($login, $target) {
        try {
            $login = $this->getRecipient();
            $player = $this->storage->getPlayerObject($target);
            $admin = $this->storage->getPlayerObject($login);
            $this->connection->ban($target, _("You are now banned from the server."));
            $this->connection->chatSendServerMessage(_('%s$z has been banned from the server.',$player->nickName));
            //$this->connection->sendNotice($this->storage->players, $player->nickName . '$z has been banned from the server.');
        } catch (\Exception $e) {
             $this->connection->chatSendServerMessage(_("Error: %s",$e->getMessage()));
        }
    }

    function toggleSpec($login, $target) {
        try {
            $player = $this->storage->getPlayerObject($target);

            if ($player->forceSpectator == 0) {
                $this->connection->forceSpectator($target, 1);
                $this->connection->sendNotice($target, _('Admin has forced you to specate!'));
            }

            if ($player->forceSpectator == 1) {
                $this->connection->forceSpectator($target, 2);
                $this->connection->forceSpectator($target, 0);
                $this->connection->sendNotice($target, _("Admin has released you from specate to play."));
            }
        } catch (\Exception $e) {
             $this->connection->chatSendServerMessage(_("Error: %s",$e->getMessage()));
        }
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 14);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(8, -10);
    }

    function onShow() {
        $this->populateList();
    }

    function populateList() {
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->pager->clearItems();

        $x = 0;
        $login = $this->getRecipient();
        foreach ($this->storage->players as $player)
            $this->pager->addItem(new Playeritem($x++, $player, $this, \ManiaLive\Features\Admin\AdminGroup::contains($login)));
        foreach ($this->storage->spectators as $player)
            $this->pager->addItem(new Playeritem($x++, $player, $this, \ManiaLive\Features\Admin\AdminGroup::contains($login)));
    }

    function destroy() {
        parent::destroy();
    }

}

?>
