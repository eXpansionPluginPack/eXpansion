<?php

namespace ManiaLivePlugins\eXpansion\ManiaExchange;

use ManiaLive\Event\Dispatcher;
use DedicatedApi\Structures\Map;
use ManiaLivePlugins\eXpansion\ManiaExchange\Config;

class ManiaExchange extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $config;
    private $vote;
    private $titleId;
    private $msg_add;

    public function exp_onInit() {
        parent::exp_onInit();

        $this->config = Config::getInstance();

        //Oliverde8 Menu
        if ($this->isPluginLoaded('oliverde8\HudMenu')) {
            Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
        }
    }

    public function exp_onReady() {
        $admGroup = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();
        $this->registerChatCommand("mx", "chatMX", 2, true);
        $this->registerChatCommand("mx", "chatMX", 1, true);

        //if ($this->config->mxVote_enable) {
        //  $this->registerChatCommand('mxqueue', "mxVote", 1, true);
        //}

        if ($this->isPluginLoaded('Standard\Menubar')) {
            $this->buildMenu();
        }

        if ($this->isPluginLoaded('eXpansion\Menu')) {
            $this->callPublicMethod('eXpansion\Menu', 'addSeparator', __('ManiaExchange'), false);
            $this->callPublicMethod('eXpansion\Menu', 'addItem', __('Search Maps'), null, array($this, 'mxSearch'), false);
        }

        $version = $this->connection->getVersion();
        $this->titleId = $version->titleId;
        $this->enableDedicatedEvents();

        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);
    }

    public function onPlayerConnect($login, $isSpectator) {
        $widget = Gui\Widgets\MxWidget::Create($login);
        $widget->setSize(60, 20);
        $widget->setPosition(-160, 60);
        $widget->show();
    }

    public function exp_onLoad() {

        $this->msg_add = exp_getMessage('Map %s $z$s$fff added from MX Succesfully');
    }

    public function onOliverde8HudMenuReady($menu) {

        $parent = $menu->findButton(array('menu', 'Maps'));
        $button["style"] = "UIConstructionSimple_Buttons";
        $button["substyle"] = "Drive";
        $button["plugin"] = $this;
        if (!$parent) {
            $parent = $menu->addButton('menu', "Maps", $button);
        }

        unset($button["style"]);
        unset($button["substyle"]);
        $button["image"] = "http://files.oliver-decramer.com/data/maniaplanet/images/forHud/planet_mx_logo.png";
        $button["plugin"] = $this;
        $parent = $menu->addButton($parent, "ManiaExchange", $button);

        $button["plugin"] = $this;
        $button["function"] = 'chatMX';
        $button["params"] = 'search';
        $menu->addButton($parent, "Search Maps", $button);
        $button["params"] = 'help';
        $menu->addButton($parent, "Help", $button);
    }

    public function onPlayerDisconnect($login, $reason = null) {
        Gui\Windows\MxSearch::Erase($login);
        Gui\Windows\MxWidget::Erase($login);
    }

    public function buildMenu() {
        $this->callPublicMethod('Standard\Menubar', 'initMenu', \ManiaLib\Gui\Elements\Icons128x128_1::Download);
        $this->callPublicMethod('Standard\Menubar', 'addButton', __('Search Maps'), array($this, 'mxSearch'), true);
    }

    public function chatMX($login, $arg, $param = null) {
        switch ($arg) {
            case "add":
                $this->addMap($login, $param);
                break;
            case "search":
                $this->mxSearch($login, $param, "");
                break;
            case "author":
                $this->mxSearch($login, "", $param);
                break;
            case "queue":
                $this->mxVote($login, $param);
                break;
            case "help":
            default:
                $this->connection->chatSendServerMessage(__('Usage.. /mx queue [id] or /mx search "your search terms here"'), $login);
                break;
        }
    }

    public function mxSearch($login, $search = "", $author = "") {
        $window = Gui\Windows\MxSearch::Create($login);
        $window->setTitle('ManiaExchange');
        $window->setPlugin($this);
        $window->search($login, $search, $author);
        $window->centerOnScreen();
        $window->setSize(180, 100);
        $window->show();
    }

    public function addMap($login, $mxId) {
        if (!\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance()->hasPermission($login, 'server_maps')) {
            $this->connection->chatSendServerMessage(__('$iYou don\'t have permission to do that!', $login, $mxId), $login);
            return;
        }

        if ($mxId == 'this') {
            try {
                $this->connection->addMap($this->storage->currentMap->fileName);
                $this->exp_chatSendServerMessage($this->msg_add, null, array($this->storage->currentMap->name));
            } catch (\Exception $e) {
                $this->connection->chatSendServerMessage(__("Error: %s", $login, $e->getMessage()), $login);
            }
            return;
        }

        if (!is_numeric($mxId)) {
            $this->connection->chatSendServerMessage(__('"%s" is not a numeric value.', $login, $mxId), $login);
            return;
        }
        try {
            if ($this->storage->gameInfos->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_SCRIPT) {

                $query = "";
                switch ($this->titleId) {
                    case "SMStorm":
                        $query = 'http://sm.mania-exchange.com/tracks/download/' . $mxId;
                        break;
                    default:
                        $query = 'http://tm.mania-exchange.com/tracks/download/' . $mxId;
                        break;
                }
            } else {
                $query = 'http://tm.mania-exchange.com/tracks/download/' . $mxId;
            }

            $ch = curl_init($query);
            curl_setopt($ch, CURLOPT_USERAGENT, "Manialive/eXpansion MXapi [getter] ver 0.1");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            $status = curl_getinfo($ch);
            curl_close($ch);

            if ($data === false) {
                $this->connection->chatSendServerMessage(__('MX is down'), $login);
                return;
            }

            if ($status["http_code"] !== 200) {
                if ($status["http_code"] == 301) {
                    $this->connection->chatSendServerMessage(__('Map not found for id %s', $login, $mxId), $login);
                    return;
                }

                $this->connection->chatSendServerMessage(__('MX returned http error code: %s', $login, $status["http_code"]), $login);
                return;
            }
            /** @var \DedicatedApi\Structures\Version */
            $game = $this->connection->getVersion();

            $maps = $this->connection->getMapsDirectory();
            $dir = $maps . "/Downloaded/" . $game->titleId;
            $file = $dir . "/" . $mxId . ".Map.Gbx";

            if (!is_dir($dir)) {
                mkdir($dir, 0775);
            }

            if (!touch($file)) {
                $this->connection->chatSendServerMessage(__("Couldn't create mapfile in maps folder, check folder permissions!"), $login);
            }

            file_put_contents($file, $data);
            $this->connection->addMap($file);

            $map = $this->connection->getMapInfo($file);
            $this->exp_chatSendServerMessage($this->msg_add, null, array($map->name));
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__("Error: %s", $login, $e->getMessage()), $login);
        }
    }

    function mxVote($login, $mxId) {
        if (!is_numeric($mxId)) {
            $this->connection->chatSendServerMessage(__('"%s" is not a numeric value.', $login, $mxId), $login);
            return;
        }

        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::isInList($login)) {
            $this->mxQueue($login, $mxId);
            return;
        }

        $queue = $this->callPublicMethod('eXpansion\\Maps', 'returnQueue');
        foreach ($queue as $q) {
            if ($q->player->login == $login) {
                $msg = exp_getMessage('#admin_error# $iYou already have a map in the queue...');
                $this->exp_chatSendServerMessage($msg, $login);
                return;
            }
        }

        try {
            if ($this->storage->gameInfos->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_SCRIPT) {

                $query = "";
                switch ($this->titleId) {
                    case "SMStorm":
                        $query = 'http://sm.mania-exchange.com/api/tracks/get_track_info/id/' . $mxId;
                        break;
                    default:
                        $query = 'http://tm.mania-exchange.com/api/tracks/get_track_info/id/' . $mxId;
                        break;
                }
            } else {
                $query = 'http://tm.mania-exchange.com/api/tracks/get_track_info/id/' . $mxId;
            }


            $ch = curl_init($query);
            curl_setopt($ch, CURLOPT_USERAGENT, "Manialive/eXpansion MXapi [getter] ver 0.1");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            $status = curl_getinfo($ch);
            curl_close($ch);
            $map = json_decode($data, true);

            if (!$map) {
                $this->connection->chatSendServerMessage(__('Unable to retrieve track info from MX..  wrong ID..?'), $login);
                return;
            }

            $version = $this->connection->getVersion();

            if (strtolower(substr($version->titleId, 2)) != strtolower($map['EnvironmentName'])) {
                $this->connection->chatSendServerMessage(__('Wrong environment!'), $login);
                return;
            }

            $this->vote = array();
            $this->vote['login'] = $login;
            $this->vote['mxId'] = $mxId;

            $vote = new \DedicatedApi\Structures\Vote();
            $vote->callerLogin = $login;
            $vote->cmdName = '$0f0add $fff$o' . $map['Name'] . '$o$0f0 by $eee' . $map['Username'] . ' $0f0';
            $vote->cmdParam = array("to the queue from MX?$3f3");
            $this->connection->callVote($vote, $this->config->mxVote_ratio, ($this->config->mxVote_timeout * 1000), $this->config->mxVote_voters);
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__("Error: %s", $login, $e->getMessage()), $login);
        }
    }

    function mxQueue($login, $mxId) {
        try {
            if ($this->storage->gameInfos->gameMode == \DedicatedApi\Structures\GameInfos::GAMEMODE_SCRIPT) {

                $query = "";
                switch ($this->titleId) {
                    case "SMStorm":
                        $query = 'http://sm.mania-exchange.com/tracks/download/' . $mxId;
                        break;
                    default:
                        $query = 'http://tm.mania-exchange.com/tracks/download/' . $mxId;
                        break;
                }
            } else {
                $query = 'http://tm.mania-exchange.com/tracks/download/' . $mxId;
            }

            $ch = curl_init($query);
            curl_setopt($ch, CURLOPT_USERAGENT, "Manialive/eXpansion MXapi [getter] ver 0.1");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            $status = curl_getinfo($ch);
            curl_close($ch);

            if ($data === false) {
                $this->connection->chatSendServerMessage(__('MX is down'), $login);
                return;
            }

            if ($status["http_code"] !== 200) {
                if ($status["http_code"] == 301) {
                    $this->connection->chatSendServerMessage(__('Map not found for id %s', $login, $mxId), $login);
                    return;
                }

                $this->connection->chatSendServerMessage(__('MX returned http error code: %s', $login, $status["http_code"]), $login);
                return;
            }
            /** @var \DedicatedApi\Structures\Version */
            $game = $this->connection->getVersion();

            $maps = $this->connection->getMapsDirectory();
            $dir = $maps . "/Downloaded/" . $game->titleId;
            $file = $dir . "/" . $mxId . ".Map.Gbx";

            if (!is_dir($dir)) {
                mkdir($dir, 0775);
            }

            if (!touch($file)) {
                $this->connection->chatSendServerMessage(__("Couldn't create mapfile in maps folder, check folder permissions!"), $login);
            }

            file_put_contents($file, $data);
            //$this->connection->addMap($file);
            //$map = $this->connection->getMapInfo($file);
            //print_r($map);
            //$map = new \DedicatedApi\Structures\Map($this->connection->getMapInfo($file));
            //print_r($map);
            $this->callPublicMethod('eXpansion\\Maps', 'queueMxMap', $login, $file);
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage(__("Error: %s", $login, $e->getMessage()), $login);
        }
    }

    function onVoteUpdated($stateName, $login, $cmdName, $cmdParam) {
        switch ($cmdParam) {
            case "to the queue from MX?$3f3":
                switch ($stateName) {
                    case "VotePassed":
                        $msg = exp_getMessage('#record# $iVote passed!');
                        $this->exp_chatSendServerMessage($msg, null);
                        $this->mxQueue($this->vote['login'], $this->vote['mxId']);
                        $this->vote = array();
                        break;
                    case "VoteFailed":
                        $msg = exp_getMessage('#admin_error# $iVote failed!');
                        $this->exp_chatSendServerMessage($msg, null);
                        $this->vote = array();
                        break;
                    default:
                        break;
                }
                break;
            default:
                break;
        }
    }

}

?>