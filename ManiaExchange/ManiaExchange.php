<?php

namespace ManiaLivePlugins\eXpansion\ManiaExchange;

class ManiaExchange extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {


    public function exp_onReady() {
        $this->registerChatCommand("mx", "chatMX", 2, true);
        $this->registerChatCommand("mx", "chatMX", 1, true);
        Gui\Windows\MxSearch::$mxPlugin = $this;

        if ($this->isPluginLoaded('Standard\Menubar'))
            $this->buildMenu();

        if ($this->isPluginLoaded('eXpansion\Menu')) {
            $this->callPublicMethod('eXpansion\Menu', 'addSeparator', 'ManiaExchange', true);
            $this->callPublicMethod('eXpansion\Menu', 'addItem', 'Search Maps', null, array($this, 'mxSearch'), true);
        }

        $this->enableDedicatedEvents();
    }

    public function onPlayerDisconnect($login) {
        Gui\Windows\MxSearch::Erase($login);
    }

    public function buildMenu() {
        $this->callPublicMethod('Standard\Menubar', 'initMenu', \ManiaLib\Gui\Elements\Icons128x128_1::Download);
        $this->callPublicMethod('Standard\Menubar', 'addButton', 'Search', array($this, 'mxSearch'), true);
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
            case "help":
            default:
                $this->connection->chatSendServerMessage('Usage /mx add [id] or /mx search "your search terms here"', $login);
                break;
        }
    }

    public function mxSearch($login, $search = "", $author = "") {
        $window = Gui\Windows\MxSearch::Create($login);
        $window->setTitle('ManiaExchange');
        $window->search("", $search, $author);
        $window->centerOnScreen();
        $window->setSize(140, 100);
        $window->show();
    }

    public function addMap($login, $mxId) {
        if (!is_numeric($mxId)) {
            $this->connection->chatSendServerMessage('"' . $mxId . '" is not a numeric value.', $login);
            return;
        }
        try {
            $script = $this->connection->getModeScriptInfo();
            $query = "";
            switch ($script->name) {
                case "ShootMania\Royal":
                case "ShootMania\Melee":
                case "ShootMania\Battle":
                case "ShootMania\Elite":
                    $query = 'http://sm.mania-exchange.com/tracks/download/' . $mxId;
                    break;
                default:
                    $query = 'http://tm.mania-exchange.com/tracks/download/' . $mxId;
                    break;
            }
            
            $ch = curl_init($query);
            curl_setopt($ch, CURLOPT_USERAGENT, "Manialive/eXpansion MXapi [getter] ver 0.1");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            $status = curl_getinfo($ch);
            curl_close($ch);

            if ($data === false) {
                $this->connection->chatSendServerMessage('$f00$oError $z$s$fffMX is down', $login);
                return;
            }

            if ($status["http_code"] !== 200) {
                if ($status["http_code"] == 301) {
                    $this->connection->chatSendServerMessage('$f00$oError $z$s$fffMap not found for id ' . $mxId, $login);
                    return;
                }

                $this->connection->chatSendServerMessage('$f00$oError $z$s$fffMX returned http error code:' . $status["http_code"], $login);
                return;
            }

            $file = $this->connection->getMapsDirectory() . "/Downloaded/" . $mxId . ".Map.Gbx";

            if (!touch($file)) {
                $this->connection->chatSendServerMessage("Couldn't create mapfile in maps folder, check folder permissions!", $login);
            }
            file_put_contents($file, $data);
            $this->connection->addMap($file);

            $map = $this->connection->getMapInfo($file);
            $this->connection->chatSendServerMessage("Map " . $map->name . '$z$s$fff added from MX Succesfully.', $login);
        } catch (\Exception $e) {
            $this->connection->chatSendServerMessage('$f00$oError! $z$s$fff' . $e->getMessage(), $login);
        }
    }

}

?>
