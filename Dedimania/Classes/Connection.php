<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Classes;

use \ManiaLive\Event\Dispatcher;
use \ManiaLive\Application\Listener as AppListener;
use \ManiaLive\Application\Event as AppEvent;
use \ManiaLive\Features\Tick\Listener as TickListener;
use \ManiaLive\Features\Tick\Event as TickEvent;
use \DedicatedApi\Xmlrpc\Client;
use \ManiaLivePlugins\eXpansion\Dedimania\Classes\Webaccess;
use ManiaLivePlugins\eXpansion\Dedimania\Classes\Request as myRequest;
use ManiaLivePlugins\eXpansion\Dedimania\Config;
use ManiaLivePlugins\eXpansion\Dedimania\Events\Event;

class Connection extends \ManiaLib\Utils\Singleton implements AppListener, TickListener {

    /** @var Webaccess */
    private $webaccess;

    /** @var \DedicatedApi\Connection */
    private $connection;

    /** @var \ManiaLive\Data\Storage */
    private $storage;
    private $url;
    private $_callbacks = array();
    private $_requests = array();
    private $sessionId = "";

    function __construct() {
        parent::__construct();
        Dispatcher::register(TickEvent::getClass(), $this);
        $this->webaccess = new Webaccess();
        $this->url = "http://dedimania.net:8081/Dedimania";
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();
    }

    function __destruct() {
        $this->webaccess = null;
        Dispatcher::unregister(TickEvent::getClass(), $this);
    }

    function onTick() {
        $this->webaccess->select();
    }

    function openSession() {

        $version = $this->connection->getVersion();

        $serverInfo = $this->connection->getDetailedPlayerInfo($this->storage->serverLogin);
        $config = Config::getInstance();
        $packmask = "";
        switch ($version->titleId) {
            case "TMStadium":
                $packmask = "Stadium";
                break;
            case "TMCanyon":
                $packmask = "Canyon";
                break;
        }


        $args = array(array(
                "Game" => "TM2",
                "Login" => $config->login,
                "Code" => $config->code,
                "Tool" => "eXpansion",
                "Version" => "0.11",
                "Packmask" => $packmask,
                "ServerVersion" => $version->version,
                "ServerBuild" => $version->build,
                "Path" => $serverInfo->path
                ));

        $request = new myRequest("dedimania.OpenSession", $args);
        $this->send($request, array($this, "xOpenSession"));
    }

    function send(myRequest $request, callable $callback) {
        $this->webaccess->request($this->url, array(array($this, '_process'), $callback), $request->getXml(), true);
    }

    function _getSrvInfo() {
        $info = array(
            "SrvName" => $this->storage->server->name,
            "Comment" => $this->storage->server->comment,
            "Private" => !$this->storage->server->hideServer,
            "NumPlayers" => sizeof($this->storage->players),
            "MaxPlayers" => $this->storage->server->currentMaxPlayers,
            "NumSpecs" => sizeof($this->storage->spectators),
            "MaxSpecs" => $this->storage->server->currentMaxSpectators
        );
        return $info;
    }

    function _getMapInfo() {
        $mapInfo = array(
            "UId" => $this->storage->currentMap->uId,
            "Name" => $this->storage->currentMap->name,
            "Environment" => $this->storage->currentMap->environnement,
            "Author" => $this->storage->currentMap->author,
            "NbCheckpoints" => $this->storage->currentMap->nbCheckpoints,
            "NbLaps" => $this->storage->currentMap->nbLaps
        );
        return $mapInfo;
    }

    function checkSession() {
        $request = new myRequest("dedimania.CheckSession", array($this->sessionId));
        $this->send($request, array($this, "xCheckSession"));
    }

    function getChallengeRecords() {
        $players = array();
        foreach ($this->storage->players as $player) {
            if ($player->login != $this->storage->serverLogin)
                $players[] = Array("Player" => (string) $player->login, "IsSpec" => false);
        }
        foreach ($this->storage->spectators as $player)
            $players[] = Array("Player" => (string) $player->login, "IsSpec" => true);

        $gamemode = "";

        switch ($this->storage->gameInfos->gameMode) {
            case \DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK:
                $gamemode = "TA";
                break;
            case \DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS:
                $gamemode = "Rounds";
                break;
            default:
                break;
        }

        $args = array(
            $this->sessionId,
            $this->_getMapInfo(),
            $gamemode,
            $this->_getSrvInfo(),
            $players
        );

        $request = new myRequest("dedimania.GetChallengeRecords", $args);
        $this->send($request, array($this, "xGetRecords"));
    }

    /**
     * Player Connect
     * @param string $login
     * @param string $nickname
     * @param string $path
     * @param bool $isSpec
     */
    function playerConnect(\DedicatedApi\Structures\Player $player, $isSpec) {
        if ($this->sessionId === null) {
            echo "Session id is null!";
            return;
        }

        $args = array(
            $this->sessionId,
            $player->login,
            $player->nickName,
            $player->path,
            $isSpec
        );

        $request = new myRequest("dedimania.PlayerConnect", $args);

        print $request->getXml();

        $this->send($request, array($this, "xPlayerConnect"));
    }

    function updateServerPlayers($map) {
        $players = array();
        foreach ($this->storage->players as $player) {
            if ($player->login != $this->storage->serverLogin)
                $players[] = Array("Player" => (string) $player->login, "IsSpec" => false, "Vote" => -1);
        }
        foreach ($this->storage->spectators as $player)
            $players[] = Array("Player" => (string) $player->login, "IsSpec" => true, "Vote" => -1);

        $gamemode = "";

        switch ($this->storage->gameInfos->gameMode) {
            case \DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK:
                $gamemode = "TA";
                break;
            case \DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS:
                $gamemode = "Rounds";
                break;
            default:
                break;
        }

        $args = array(
            $this->sessionId,
            $this->_getSrvInfo(),
            array("UId" => $map->uId, "GameMode" => $gamemode),
            $players
        );

        $request = new myRequest("dedimania.UpdateServerPlayers", $args);
        $this->send($request, null);
    }

    function _process($dedires, $callback) {
        $msg = new \DedicatedApi\Xmlrpc\Message($dedires['Message']);
        $msg->parse();
        $errors = end($msg->params[0]);

        print_r($errors);

        print "Actual Data\n";

        $array = $msg->params[0][0];

        if (array_key_exists("faultString", $array)) {
            $this->connection->chatSendServerMessage("Dedimania error: " . $array['faultString']);
            \ManiaLive\Utilities\Console::println("Dedimania error: " . $array['faultString']);
            return;
        }

        if (is_callable($callback)) {
            call_user_func_array($callback, array($array));
        }
    }

    function xOpenSession($data) {
        $this->sessionId = $data[0]['SessionId'];
        echo "recieved Session key:" . $this->sessionId . "\n";
        Dispatcher::dispatch(new Event(Event::ON_OPEN_SESSION, $this->sessionId));
    }

    function xGetRecords($data) {
        Dispatcher::dispatch(new Event(Event::ON_GET_RECORDS, $data));
    }

    function xCheckSession($data) {
        print_r($data);
    }

    function xPlayerConnect($data) {
        print_r($data);
    }

    function onInit() {
        
    }

    function onRun() {
        
    }

    function onPostLoop() {
        
    }

    function onTerminate() {
        
    }

    function onPreLoop() {
        
    }

}

?>
