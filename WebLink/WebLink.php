<?php

namespace ManiaLivePlugins\eXpansion\WebLink;

/**
 * Description of WebLink
 *
 * @author Reaby
 */
class WebLink extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    /** @var string $url address of nodejs relay server */
    private $url = "";

    /** @var Config */
    private $config;

    /** @var \ManiaLivePlugins\eXpansion\Core\DataAccess */
    private $access;

    /** @var array $buffer */
    private $dataBuffer = array();
    private $eventBuffer = array();

    /** @var integer */
    public $lastTick = 0;

    /** @var boolean */
    private $connectionFailed = false;

    /** @var \Maniaplanet\DedicatedServer\Structures\PlayerRanking[] */
    private $rankings = array();

    /** @var \Maniaplanet\DedicatedServer\Structures\Map */
    private $map = array();

    /** @var \Maniaplanet\DedicatedServer\Structures\PlayerRanking[] holds the players who finished this round in order of arrival */
    private $roundFinish = array();

    public function exp_onLoad() {
        $this->enableDedicatedEvents();
        $this->config = Config::getInstance();
    }

    public function exp_onReady() {
        $this->lastTick = time();
        $this->enableTickerEvent();
        $this->setPublicMethod("sendEvent");
        $this->setPublicMethod("sendData");
        $this->access = \ManiaLivePlugins\eXpansion\Core\DataAccess::getInstance();
        $this->syncDatas();
        $outServerData = array();
        $outServerData["rankings"] = $this->rankings;
        $outServerData["players"] = $this->storage->players;
        $outServerData["spectators"] = $this->storage->spectators;
        $outServerData["map"] = $this->map;
        $outServerData["gameinfos"] = $this->storage->gameInfos;
        $outServerData["server"] = $this->getServerData();
        $this->sendData("serverData", $outServerData);
    }

    /**
     * syncDatas()
     *
     * will update the buffers for rankings, players + spectators and current map
     */
    public function syncDatas() {
        $this->rankings = $this->connection->getCurrentRanking(-1, 0);
        $this->players = $this->storage->players;
        $this->spectators = $this->storage->spectators;
        $this->map = $this->storage->currentMap;
    }

    public function onTick() {

        if ($this->connectionFailed) {

            if (time() >= $this->lastTick + 10) {

                $outServerData = array();
                $outServerData["rankings"] = $this->rankings;
                $outServerData["players"] = $this->storage->players;
                $outServerData["spectators"] = $this->storage->spectators;
                $outServerData["map"] = $this->map;
                $outServerData["server"] = $this->getServerData();
                $outServerData['gameinfos'] = $this->storage->gameInfos;
                $outServerData["roundFinish"] = $this->roundFinish;
                $status = $this->sendData("serverData", $outServerData);
                if ($status) {
                    foreach ($this->dataBuffer as $type => $datas) {
                        $this->sendData($type, $datas);
                    }
                    foreach ($this->eventBuffer as $event) {
                        foreach ($event as $type => $datas) {
                            $this->sendEvent($type, $datas);
                        }
                    }
                }
                $this->dataBuffer = array();
                $this->eventBuffer = array();
                $this->lastTick = time();
            }
        } else {
            if (time() >= $this->lastTick + 2) {
                foreach ($this->dataBuffer as $type => $datas) {
                    if (!$this->sendData($type, $datas)) {
                        break;
                    }
                }
                foreach ($this->eventBuffer as $event) {
                    foreach ($event as $type => $datas) {
                        if (!$this->sendEvent($type, $datas))
                            break 2;
                    }
                }

                $this->dataBuffer = array();
                $this->eventBuffer = array();
                $this->lastTick = time();
            }
        }
    }

    public function onPlayerChat($playerUid, $login, $text, $isRegistredCmd) {
        if ($playerUid == 0)
            return;

        if (substr($text, 0, 1) == "/")
            return;

        $this->eventBuffer[]["onPlayerChat"] = array("login" => $login, "nickName" => $this->storage->getPlayerObject($login)->nickName, "text" => $text);
    }

    public function onBeginRound() {
        $this->eventBuffer[]['onBeginRound'] = array();
    }

    public function onPlayerFinish($playerUid, $login, $timeOrScore) {
        if ($timeOrScore == 0)
            return;
        $player = new \Maniaplanet\DedicatedServer\Structures\PlayerRanking();
        $player->playerId = $playerUid;
        $player->login = $login;
        $player->nickName = $this->storage->getPlayerObject($login)->nickName;
        $player->score = $timeOrScore;
        $this->roundFinish[$login] = $player;

        \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortAsc($this->roundFinish, "score");
        $newArray = array();

        foreach ($this->rankings as $rindex => $ranking) {
            foreach ($this->roundFinish as $index => $player) {
                if ($ranking->login == $player->login) {
                    $this->rankings[$rindex]->score = $player->score;
                    $this->rankings[$rindex]->rank = $index + 1;
                }
            }
            $player->rank = $index + 1;
            $newArray[$player->login] = $player;
        }
        $this->roundFinish = $newArray;

        \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortAsc($this->rankings, "rank");

        $this->eventBuffer[]['onPlayerFinish'] = array("login" => $login, "time" => $timeOrScore);
        $this->dataBuffer["roundFinish"] = $this->roundFinish;
        $this->dataBuffer["rankings"] = $this->rankings;
    }

    public function getServerData() {
        $server = $this->storage->server;
        $server->password = "*not allowed*";
        $server->passwordForSpectator = "*not allowed*";
        $server->refereePassword = "*not allower*";
        return $server;
    }

    public function onBeginMap($map, $warmUp, $matchContinuation) {
        $this->map = $this->storage->currentMap;
        $this->dataBuffer["map"] = $this->storage->currentMap;
        $this->dataBuffer["gameinfos"] = $this->storage->gameInfos;
        $this->dataBuffer["server"] = $this->getServerData();
        $this->dataBuffer['players'] = $this->storage->players;
        $this->dataBuffer['spectators'] = $this->storage->spectators;
        $this->rankings = $this->connection->getCurrentRanking(-1, 0);
        $this->dataBuffer['rankings'] = $this->rankings;
        $this->eventBuffer[]['onBeginMap'] = array("warmup" => $warmUp, "matchContinuation" => $matchContinuation);
    }

    public function onEndRound() {
        $this->roundFinish = null;
        $this->roundFinish = array();
        $this->roundFinish = array();
        $this->rankings = $this->connection->getCurrentRanking(-1, 0);
        $this->dataBuffer["rankings"] = $this->rankings;
        $this->eventBuffer[]['onEndRound'] = array();
    }

    public function onEndMatch($rankings, $winnerTeamOrMap) {
        $this->roundFinish = null;
        $this->roundFinish = array();
        $this->dataBuffer["rankings"] = $this->rankings;
        $this->eventBuffer[]['onEndMatch'] = array();
    }

    public function onEndMap($rankings, $map, $wasWarmUp, $matchContinuesOnNextMap, $restartMap) {
        $this->roundFinish = null;
        $this->roundFinish = array();
        $this->dataBuffer["rankings"] = $this->rankings;
        $this->eventBuffer[]['onEndMap'] = array();
    }

    public function onPlayerConnect($login, $isSpectator) {
        $this->spectators = $this->storage->spectators;
        $this->eventBuffer[]["onPlayerConnect"] = array("login" => $login);
        $this->dataBuffer['players'] = $this->storage->players;
        $this->dataBuffer['spectators'] = $this->storage->spectators;
    }

    public function onPlayerDisconnect($login, $disconnectionReason = null) {
        $this->dataBuffer['players'] = $this->storage->players;
        $this->dataBuffer['spectators'] = $this->storage->spectators;
        $this->eventBuffer[]["onPlayerDisconnect"] = array("login" => $login);
    }

    public function sendEvent($event, $data) {
        $out = array("secret" => $this->config->secret,
            "event" => $event,
            "data" => $data);
        $params = urlencode(base64_encode(json_encode($out)));
        try {
            $ctx = stream_context_create(array("http" => array("method" => "GET", "timeout" => floatval($this->config->timeout))));
            file_get_contents(rtrim($this->config->url, "/") . "/onDedicatedEvent?data=" . $params, false, $ctx, 0, 1);
            $this->connectionFailed = false;
            return true;
        } catch (\Exception $e) {
           $this->console("connection problem.");
            $this->connectionFailed = true;
            return false;
        }
    }

    public function sendData($type, $data) {
        $out = array("secret" => $this->config->secret,
            "type" => $type,
            "data" => $data);
        $params = urlencode(base64_encode(json_encode($out)));
        try {
            $ctx = stream_context_create(array("http" => array("method" => "GET", "timeout" => floatval($this->config->timeout))));
            file_get_contents(rtrim($this->config->url, "/") . "/onDedicatedData?data=" . $params, false, $ctx, 0, 1);
            $this->connectionFailed = false;
            return true;
        } catch (\Exception $e) {
           $this->console("connection problem.");
            $this->connectionFailed = true;
            return false;
        }
    }

}
