<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Classes;

require_once('Webaccess.php');

use \ManiaLive\Event\Dispatcher;
use \ManiaLive\Application\Listener as AppListener;
use \ManiaLive\Features\Tick\Listener as TickListener;
use \ManiaLive\Features\Tick\Event as TickEvent;
use \DedicatedApi\Structures\GameInfos;
use ManiaLive\Utilities\Console;
use ManiaLivePlugins\eXpansion\Dedimania\Structures\DediPlayer;
use ManiaLivePlugins\eXpansion\Dedimania\Classes\Request as dediRequest;
use ManiaLivePlugins\eXpansion\Dedimania\Config;
use ManiaLivePlugins\eXpansion\Dedimania\Events\Event;

class Connection extends \ManiaLib\Utils\Singleton implements AppListener, TickListener {

    /** @var integer */
    static public $serverMaxRank = 30;

    /** @var array("login" => DediPlayer) */
    static public $players = array();

    /** @var Webaccess */
    private $webaccess;

    /** @var \DedicatedApi\Connection */
    private $connection;

    /** @var \ManiaLive\Data\Storage */
    private $storage;
    private $url; // dedimania url
    private $sessionId = null; // dedimania session id
    // these are used for async webaccess 
    private $read;
    private $write;
    private $except;
    // cached records from dedimania
    private $dediRecords = array();
    private $dediBest = 0;
    private $dediUid = null;
    // cached players from dedimania

    private $lastUpdate;

    function __construct() {
        parent::__construct();

        $this->webaccess = new \Webaccess();

        // if you are developing change port to 8081
        $this->url = "http://dedimania.net:8081/Dedimania";
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

        $this->webaccess->select($this->read, $this->write, $this->except, 0, 0);

        if ($this->sessionId !== null && (time() - $this->lastUpdate) > 180) {
            echo "Dediconnection Keepalive!";
            $this->updateServerPlayers($this->storage->currentMap);
            $this->lastUpdate = time();
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
            die("Dedimania server login is not configured!");
        if (empty($config->code))
            die("Dedimania server code is not configured!");
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
    public function setChallengeTimes($map, $rankings, $vreplay, $greplay) {

        // disabled for relay server
        if ($this->connection->isRelayServer())
            return;
        // cup mode disabled
        if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_CUP)
            return;
        // special rounds mode disabled             
        if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_ROUNDS && (!$map['LapRace'] || $map['LapRace'] && !empty($this->storage->gameInfos->roundsForcedLaps) && $this->storage->gameInfos->roundsForcedLaps != 0 ))
            return;

        // only special maps under 8 seconds are allowed
        if ($map['AuthorTime'] < 8000 && strtolower($map['Author']) != 'nadeo')
            return;

        if ($this->dediUid != $map['UId']) {
            Console::println("Challenge UId mismatch on dedimania plugin");
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
            Console::println("No records, skipping dedimania send");
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
            $this->_getMapInfo(),
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
        if ($this->sessionId === null) {
            echo "Session id is null!";
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
            echo "Session id is null!";
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
     * @param \DedicatedApi\Structures\Player $player
     * @param bool $isSpec     
     */
    function playerConnect(\DedicatedApi\Structures\Player $player, $isSpec) {

        if ($this->sessionId === null) {
            echo "Session id is null!";
            return;
        }

        if ($player->login == $this->storage->serverLogin) {
            echo "Abort. tried to send server login.";
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
     * @param \DedicatedApi\Structures\Player $player
     * @param bool $isSpec     
     */
    function playerMultiConnect($players) {

        if ($this->sessionId === null) {
            echo "Session id is null!";
            return;
        }
        if (!is_array($players))
            return;

        $x = 0;
        $request = "";

        foreach ($players as $player) {

            if (is_a($player[0], "\DedicatedApi\Structures\Player")) {

                if ($player[0]->login == $this->storage->serverLogin) {
                    echo "Abort. tried to send server login.";
                    return;
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
            echo "Session id is null!";
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
     * @param array,\DedicatedApi\Structures\Map $map
     * @return type
     */
    function updateServerPlayers($map) {
        if ($this->sessionId === null) {
            echo "Session id is null!";
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
            case \DedicatedApi\Structures\GameInfos::GAMEMODE_SCRIPT:
                $gamemode = "";
                break;
            case \DedicatedApi\Structures\GameInfos::GAMEMODE_TIMEATTACK:
                $gamemode = "TA";
                break;
            case \DedicatedApi\Structures\GameInfos::GAMEMODE_LAPS:
                $gamemode = "TA";
                break;
            case \DedicatedApi\Structures\GameInfos::GAMEMODE_ROUNDS:
                $gamemode = "Rounds";
                break;
            case \DedicatedApi\Structures\GameInfos::GAMEMODE_TEAM:
                $gamemode = "Rounds";
                break;
            case \DedicatedApi\Structures\GameInfos::GAMEMODE_CUP:
                $gamemode = "Rounds";
                break;
            case \DedicatedApi\Structures\GameInfos::GAMEMODE_STUNTS:
                $gamemode = "";
                break;
            default:
                $gamemode = "";
                break;
        }
        return $gamemode;
    }

    function _process($dedires, $callback) {
        $msg = new \DedicatedApi\Xmlrpc\Message($dedires['Message']);
        $msg->parse();
        $errors = end($msg->params[0]);

        // print_r($errors);
        //print "Actual Data\n";

        $array = $msg->params[0];
        unset($array[count($array)-1]);
        
        
        //print_r($array);

        if (array_key_exists("faultString", $array[0])) {
            $this->connection->chatSendServerMessage("Dedimania error: " . $array[0]['faultString']);
            \ManiaLive\Utilities\Console::println("Dedimania error: " . $array[0]['faultString']);
            return;
        }

        if (!empty($array[0][0]['Error'])) {
            $this->connection->chatSendServerMessage("Dedimania error: " . $array[0][0]['Error']);
            \ManiaLive\Utilities\Console::println("Dedimania error: " . $array[0][0]['Error']);
            return;
        }

        if (is_callable($callback)) {
            call_user_func_array($callback, array($array));
        } else {
            $this->connection->chatSendServerMessage("Dedimania error: Callback not valid");
            \ManiaLive\Utilities\Console::println("Dedimania error: Callback not valid");
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
        $this->sessionId = $data[0][0]['SessionId'];
        echo "recieved Session key:" . $this->sessionId . "\n";
        Dispatcher::dispatch(new Event(Event::ON_OPEN_SESSION, $this->sessionId));
    }

    function xGetRecords($data) {
        $data = $data[0];
        
        $this->dediRecords = $data[0];
        $this->dediUid = $data[0]['UId'];
        self::$serverMaxRank = $data[0]['ServerMaxRank'];


        if (!empty($data[0]['Records'][0]['Best'])) {
            $this->dediBest = $data[0]['Records'][0]['Best'];
        } else {
            $this->dediBest = null;
        }
        Dispatcher::dispatch(new Event(Event::ON_GET_RECORDS, $data[0]));
    }

    function xUpdateServerPlayers($data) {
        //   print_r($data);
    }

    function xSetChallengeTimes($data) {
        // print_r($data);        
    }

    function xCheckSession($data) {
        // print_r($data);
    }

    function xPlayerConnect($data) {
        self::$players[$data[0][0]['Login']] = DediPlayer::fromArray($data[0][0]);
        if ($data[0][0]['Banned']) {
            $player = $this->storage->getPlayerObject($data[0][0]['Login']);
            $this->connection->chatSendServerMessage("Player" . $player->nickName . '$z$s$fff[' . $player->login . '] is $f00BANNED$fff from dedimania.');
        }
        Dispatcher::dispatch(new Event(Event::ON_PLAYER_CONNECT, $data[0][0]));
    }

    function xPlayerMultiConnect($data) {                
        foreach ($data as $player) {                        
            $player = $player[0];                        
            $dediPlayer = DediPlayer::fromArray($player);
            self::$players[$player['Login']] = $dediPlayer;
            
            if ($player['Banned']) {
                $pla = $this->storage->getPlayerObject($player['Login']);
                $this->connection->chatSendServerMessage("Player" . $player->nickName . '$z$s$fff[' . $player->login . '] is $f00BANNED$fff from dedimania.');
            }
        }        
        //print_r(self::$players);
    }

    function xPlayerDisconnect($data) {
        //print_r($data);
        Dispatcher::dispatch(new Event(Event::ON_PLAYER_DISCONNECT, null));
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
