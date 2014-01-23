<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Classes;

//require_once('Webaccess.php');

use \ManiaLive\Event\Dispatcher;
use \ManiaLive\Application\Listener as AppListener;
use \ManiaLive\Features\Tick\Listener as TickListener;
use \ManiaLive\Features\Tick\Event as TickEvent;
use \Maniaplanet\DedicatedServer\Structures\GameInfos;
use ManiaLive\Utilities\Console;
use ManiaLivePlugins\eXpansion\Dedimania\Structures\DediPlayer;
use ManiaLivePlugins\eXpansion\Dedimania\Classes\Request as dediRequest;
use ManiaLivePlugins\eXpansion\Dedimania\Config;
use ManiaLivePlugins\eXpansion\Dedimania\Events\Event as dediEvent;
use \ManiaLivePlugins\eXpansion\Dedimania\Structures\DediMap;
use \ManiaLivePlugins\eXpansion\Core\Classes\Webaccess;

class Connection extends \ManiaLib\Utils\Singleton implements AppListener, TickListener {

    // used for dedimania
    private $version = 0.14;

    /** @var integer */
    public static $serverMaxRank = 15;

    /** @var \ManiaLivePlugins\eXpansion\Dedimania\Structures\DediMap */
    public static $dediMap = null;

    /** @var DediPlayer[] Cached players from dedimania */
    public static $players = array();

    /** @var \Webaccess */
    private $webaccess;

    /** @var \DedicatedApi\Connection */
    private $connection;

    /** @var \ManiaLive\Data\Storage */
    private $storage;

    /** @var string $url dedimania url */
    private $url;

    /** @var string $sessionId dedimania session id */
    private $sessionId = null;
// these are used for async webaccess 
    private $read;
    private $write;
    private $except;
// cached records from dedimania
    private $dediRecords = array();
    private $dediBest = 0;
    private $dediUid = null;
    private $lastUpdate = 0;

    function __construct() {
        parent::__construct();

        $this->webaccess = new Webaccess();

        // if you are developing change port to 8081, othervice use 8082
        $this->url = "http://dedimania.net:8082/Dedimania";
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->read = array();
        $this->write = array();
        $this->except = array();
        $this->lastUpdate = time();
        Dispatcher::register(TickEvent::getClass(), $this);
    }

    function __destruct() {
        $this->webaccess = null;
        Dispatcher::unregister(TickEvent::getClass(), $this);
    }

    function onTick() {
        try {
            $this->webaccess->select($this->read, $this->write, $this->except, 0, 0);

            if ($this->sessionId !== null && (time() - $this->lastUpdate) > 240) {
                $this->debug("Dedimania connection keepalive!");
                $this->updateServerPlayers($this->storage->currentMap);
                $this->lastUpdate = time();
            }
        } catch (\Exception $e) {
            $this->console("[Dedimania] OnTick Update failed: " . $e->getMessage());
        }
    }

    /**
     * dedimania.OpenSession    
     * Should be called when starting the dedimania conversation     
     */
    function openSession() {
        $version = $this->connection->getVersion();

        $serverInfo = $this->connection->getDetailedPlayerInfo($this->storage->serverLogin);
        $config = Config::getInstance();

        if (empty($config->login))
            die("[Dedimania] Server login is not configured!\n");
        if (empty($config->code))
            die("[Dedimania] Server code is not configured! \n");

        if ($serverInfo->login != $config->login)
            die("[Dedimania] Your dedicated server login differs from configured server login, please check your configuration.");

        $packmask = "";
        switch ($version->titleId) {
            case "TMStadium":
                $packmask = "Stadium";
                break;
            case "TMCanyon":
                $packmask = "Canyon";
                break;
            case "TMValley":
                $packmask = "Valley";
        }


        $args = array(array(
                "Game" => "TM2",
                "Login" => $config->login,
                "Code" => $config->code,
                "Tool" => "MLconnector",
                "Version" => $this->version,
                "Packmask" => $packmask,
                "ServerVersion" => $version->version,
                "ServerBuild" => $version->build,
                "Path" => $serverInfo->path
        ));

        $request = new dediRequest("dedimania.OpenSession", $args);
        $this->send($request, array($this, "xOpenSession"));
    }

    /**
     * invokes dedimania.SetChallengeTimes
     * Should be called onEndMatch
     * 
     * @param array $map from dedicated server
     * @param array $ranking from dedicated server
     * 
     */
    public function setChallengeTimes(\Maniaplanet\DedicatedServer\Structures\Map $map, $rankings, $vreplay, $greplay) {

// disabled for relay server
        if ($this->connection->isRelayServer())
            return;

// special rounds mode disabled          
        if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_ROUNDS && (!isset($map->lapRace) || $map->lapRace) && $this->storage->gameInfos->roundsForcedLaps && $this->storage->gameInfos->roundsForcedLaps != 0) {
            $this->console("[Dedimania Warning] Special rounds mode with forced laps ignored!");
            return;
        }

// only special maps under 8 seconds are allowed
        if ($map->authorTime < 8000 && strtolower($map->author) != 'nadeo') {
            $this->console("[Dedimania Notice] Author time under 8 seconds, will not send records.");
            return;
        }

        if ($this->dediUid != $map->uId) {
            $this->console("[Dedimania Warning] Map UId mismatch! Map UId differs from dedimania recieved uid for the map. Times are not sent.");
            return;
        }

        $times = array();

        foreach ($rankings as $rank) {
            if (sizeof($rank['BestCheckpoints']) > 0 && $rank['BestTime'] == end($rank['BestCheckpoints'])) {
                if ($rank['BestTime'] > 5000)  // should do sanity checks for more...
                    $times[] = array("Login" => $rank['Login'], "Best" => $rank['BestTime'], "Checks" => implode(',', $rank['BestCheckpoints']));
            }
        }

        usort($times, array($this, "dbsort"));

        if (sizeof($times) == 0) {
            $this->debug("[Dedimania] No new records, skipping dedimania send.");
            return;
        }


        $Vchecks = "";
        if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_LAPS) {
            $Vchecks = implode(',', $times[0]['BestCheckpoints']);
        }

        $base64Vreplay = new IXR_Base64($vreplay);

        $base64Greplay = "";
        if (( $this->dediBest == null && sizeof($this->dediRecords['Records']) == 0) || $times[0]['Best'] < $this->dediBest) {
            $base64Greplay = new IXR_Base64($greplay);
        }


        $replays = array("VReplay" => $base64Vreplay, "VReplayChecks" => $Vchecks, "Top1GReplay" => $base64Greplay);

        $args = array(
            $this->sessionId,
            $this->_getMapInfo($map),
            $this->_getGameMode(),
            $times,
            $replays);

        $request = new dediRequest("dedimania.SetChallengeTimes", $args);
        $this->send($request, array($this, "xSetChallengeTimes"));
    }

    function send(dediRequest $request, $callback) {
        $this->webaccess->request($this->url, array(array($this, '_process'), $callback), $request->getXml(), true, 600, 3, 5);
    }

    function _getSrvInfo() {
        $info = array(
            "SrvName" => $this->storage->server->name,
            "Comment" => $this->storage->server->comment,
            "Private" => ($this->storage->server->password !== ""),
            "NumPlayers" => sizeof($this->storage->players),
            "MaxPlayers" => $this->storage->server->currentMaxPlayers,
            "NumSpecs" => sizeof($this->storage->spectators),
            "MaxSpecs" => $this->storage->server->currentMaxSpectators
        );
        return $info;
    }

    function _getMapInfo($map = null) {
        if ($map == null)
            $map = $this->storage->currentMap;
        if ($map instanceof \Maniaplanet\DedicatedServer\Structures\Map) {
            $mapInfo = array(
                "UId" => $map->uId,
                "Name" => $map->name,
                "Environment" => $map->environnement,
                "Author" => $map->author,
                "NbCheckpoints" => $map->nbCheckpoints,
                "NbLaps" => $map->nbLaps
            );
            return $mapInfo;
        }
        throw new Exception('[Dedimania] error on _getMapInfo, map is in wrong format');
    }

    function checkSession() {
        if ($this->sessionId === null) {
            $this->debug("Session id is null!");
            return;
        }
        $request = new dediRequest("dedimania.CheckSession", array($this->sessionId));
        $this->send($request, array($this, "xCheckSession"));
    }

    /**
     *  getChallengeRecords
     *  should be called onNewMap
     */
    function getChallengeRecords() {
        if ($this->sessionId === null) {
            $this->debug("Session id is null!");
            return;
        }
        $players = array();
        foreach ($this->storage->players as $player) {
            if (is_object($player) && $player->login != $this->storage->serverLogin)
                $players[] = Array("Login" => $player->login, "IsSpec" => false);
        }
        foreach ($this->storage->spectators as $player)
            if (is_object($player)) {
                $players[] = Array("Login" => $player->login, "IsSpec" => true);
            }

        $args = array(
            $this->sessionId,
            $this->_getMapInfo(),
            $this->_getGameMode(),
            $this->_getSrvInfo(),
            $players
        );
        $this->lastUpdate = time();

        $request = new dediRequest("dedimania.GetChallengeRecords", $args);
        $this->send($request, array($this, "xGetRecords"));
    }

    /**
     * PlayerConnect
     * 
     * @param \Maniaplanet\DedicatedServer\Structures\Player $player
     * @param bool $isSpec     
     */
    function playerConnect(\Maniaplanet\DedicatedServer\Structures\Player $player, $isSpec) {

        if ($this->sessionId === null) {
            $this->console("[Dedimania] Error: Session ID is null!");
            return;
        }

        if ($player->login == $this->storage->serverLogin) {
            $this->debug("[Dedimania] Abort. tried to send server login.");
            return;
        }

        $args = array(
            $this->sessionId,
            $player->login,
            $player->nickName,
            $player->path,
            $isSpec
        );

        $request = new dediRequest("dedimania.PlayerConnect", $args);
        $this->send($request, array($this, "xPlayerConnect"));
    }

    /**
     * playerMultiConnect
     * 
     * @param \Maniaplanet\DedicatedServer\Structures\Player $player
     * @param bool $isSpec     
     */
    function playerMultiConnect($players) {

        if ($this->sessionId === null) {
            $this->debug("Session id is null!");
            return;
        }
        if (!is_array($players))
            return;

        $x = 0;
        $request = "";

        foreach ($players as $player) {

            if (is_a($player[0], "\Maniaplanet\DedicatedServer\Structures\Player")) {

                if ($player[0]->login == $this->storage->serverLogin) {
                    $this->debug("[Dedimania Warning] Tried to send server login.");
                    continue;
                }

                $args = array(
                    $this->sessionId,
                    $player[0]->login,
                    $player[0]->nickName,
                    $player[0]->path,
                    $player[1]
                );

                if ($x == 0) {
                    $request = new dediRequest("dedimania.PlayerConnect", $args);
                    $x++;
                } else {
                    $request->add("dedimania.PlayerConnect", $args);
                    $x++;
                }
            }
        }
        if (is_object($request))
            $this->send($request, array($this, "xPlayerMultiConnect"));
    }

    /**
     * playerDisconnect     
     * @param string $login     
     */
    function playerDisconnect($login) {
        if ($this->sessionId === null) {
            $this->debug("Session id is null!");
            return;
        }
        $args = array(
            $this->sessionId,
            $login,
            "");
        $request = new dediRequest("dedimania.PlayerDisconnect", $args);
        $this->send($request, array($this, "xPlayerDisconnect"));
    }

    /**
     * UpdateServerPlayers
     * Should be called Every 3 minutes + onEndChallenge.
     * 
     * @param array,\Maniaplanet\DedicatedServer\Structures\Map $map
     * @return type
     */
    function updateServerPlayers($map) {
        if ($this->sessionId === null) {
            $this->debug("Session id is null!");
            return;
        }

        if (is_array($map))
            $uid = $map['UId'];
        if (is_object($map))
            $uid = $map->uId;

        $players = array();
        foreach ($this->storage->players as $player) {
            if (is_object($player) && $player->login != $this->storage->serverLogin)
                $players[] = Array("Login" => $player->login, "IsSpec" => false, "Vote" => -1);
        }
        foreach ($this->storage->spectators as $player) {
            if (is_object($player))
                $players[] = Array("Login" => $player->login, "IsSpec" => true, "Vote" => -1);
        }
        $gamemode = $this->_getGameMode();

        $args = array(
            $this->sessionId,
            $this->_getSrvInfo(),
            array("UId" => $uid, "GameMode" => $gamemode),
            $players
        );

        $request = new dediRequest("dedimania.UpdateServerPlayers", $args);
        $this->send($request, array($this, "xUpdateServerPlayers"));
    }

    function _getGameMode() {
        switch ($this->storage->gameInfos->gameMode) {
            case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT:
                $gamemode = "";
                break;
            case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK:
                $gamemode = "TA";
                break;
            case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS:
                $gamemode = "TA";
                break;
            case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS:
                $gamemode = "Rounds";
                break;
            case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM:
                $gamemode = "Rounds";
                break;
            case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP:
                $gamemode = "Rounds";
                break;
            case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_STUNTS:
                $gamemode = "";
                break;
            default:
                $gamemode = "";
                break;
        }
        return $gamemode;
    }

    function _process($dedires, $callback) {
        try {
            $msg = new \DedicatedApi\Xmlrpc\Message($dedires['Message']);
            $msg->parse();
            $errors = end($msg->params[0]);

            // print_r($errors);
            // print "Actual Data\n";

            $array = $msg->params[0];
            unset($array[count($array) - 1]);


            //  print_r($array);

            if (array_key_exists("faultString", $array[0])) {
                // $this->connection->chatSendServerMessage("[Dedimania] " . $array[0]['faultString']);
                $this->console("[Dedimania] Fault from dedimania server: " . $array[0]['faultString']);
                return;
            }

            if (!empty($array[0][0]['Error'])) {
                // $this->connection->chatSendServerMessage("Dedimania error: " . $array[0][0]['Error']);
                $this->console("[Dedimania] Error from dedimania server: " . $array[0][0]['Error']);
                return;
            }

            if (is_callable($callback)) {
                call_user_func_array($callback, array($array));
            } else {
                // $this->connection->chatSendServerMessage("Dedimania error: Callback not valid");
                $this->console("[Dedimania Error] Callback-function is not valid!");
            }
        } catch (\Exception $e) {
            $this->console("[Dedimania Error] connection to dedimania server failed." . $e->getMessage());
        }
    }

    public function dbsort($a, $b) {
        if ($b['Best'] <= 0)
            return -1;
// other best valid
        elseif ($a['Best'] <= 0)
            return 1;
// best a better than best b
        elseif ($a['Best'] < $b['Best'])
            return -1;
// best b better than best a
        elseif ($a['Best'] > $b['Best'])
            return 1;
    }

    function xOpenSession($data) {
        if (isset($data[0][0]['SessionId'])) {
            $this->sessionId = $data[0][0]['SessionId'];
            $this->console("[Dedimania] Authentication success to dedimania server!");
            $this->debug("recieved Session key:" . $this->sessionId);
            Dispatcher::dispatch(new dediEvent(dediEvent::ON_OPEN_SESSION, $this->sessionId));
            return;
        }
        if (!empty($data[0][0]['Error'])) {
            $this->console("[Dedimania] Authentication Error occurred: " . $data[0][0]['Error']);
            return;
        }
    }

    function xGetRecords($data) {
        $data = $data[0];

        $this->dediRecords = array();
        $this->dediUid = null;
        $this->dediBest = null;
        self::$dediMap = null;

        if (!empty($data[0]['Error'])) {
            $this->console("[Dedimania] Error from dediserver: " . $data[0]['Error']);
            return;
        }


        $this->dediUid = $data[0]['UId'];
        $this->dediRecords = $data[0];
        self::$serverMaxRank = $data[0]['ServerMaxRank'];
        $maplimit = intval($data[0]['ServerMaxRank']);

        if (count($data[0]['Records']) > 0) {
            $maplimit = count($data[0]['Records']);
        }

        self::$dediMap = new DediMap($data[0]['UId'], $maplimit, $data[0]['AllowedGameModes']);


        if (!$data[0]['Records']) {
            $this->debug("Dedimania: No records found.");
            return;
        }

        if (!empty($data[0]['Records'][0]['Best']))
            $this->dediBest = $data[0]['Records'][0]['Best'];

        Dispatcher::dispatch(new dediEvent(dediEvent::ON_GET_RECORDS, $data[0]));
    }

    function xUpdateServerPlayers($data) {
//   print_r($data);
    }

    function xSetChallengeTimes($data) {
        //print_r($data);
        $this->console("[Dedimania] Sending times new times: Success");
    }

    function xCheckSession($data) {
// print_r($data);
    }

    function xPlayerConnect($data) {
        $dediplayer = DediPlayer::fromArray($data[0][0]);
        self::$players[$dediplayer->login] = $dediplayer;
        if ($dediplayer->banned) {
            try {
                $player = $this->storage->getPlayerObject($dediplayer->login);
                $this->connection->chatSendServerMessage("Player" . $player->nickName . '$z$s$fff[' . $player->login . '] is $f00BANNED$fff from dedimania.');
            } catch (\Exception $e) {
                
            }
        }
        Dispatcher::dispatch(new dediEvent(dediEvent::ON_PLAYER_CONNECT, $dediplayer));
    }

    function xPlayerMultiConnect($data) {
        foreach ($data as $player) {
            $player = $player[0];
            $dediPlayer = DediPlayer::fromArray($player);
            self::$players[$dediPlayer->login] = $dediPlayer;

            if ($dediPlayer->banned) {
                try {
                    $pla = $this->storage->getPlayerObject($dediPlayer->login);
                    $this->connection->chatSendServerMessage("Player" . $pla->nickName . '$z$s$fff[' . $pla->login . '] is $f00BANNED$fff from dedimania.');
                } catch (\Exception $e) {
                    
                }
            }
            Dispatcher::dispatch(new dediEvent(dediEvent::ON_PLAYER_CONNECT, $dediPlayer));
        }
//print_r(self::$players);
    }

    function xPlayerDisconnect($data) {
        //print_r($data);
        Dispatcher::dispatch(new dediEvent(dediEvent::ON_PLAYER_DISCONNECT, $data[0][0]['Login']));
    }

    function debug($message) {
        if (DEBUG)
            $this->console($message);
    }

    function console($message) {
        Console::println($message);
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
