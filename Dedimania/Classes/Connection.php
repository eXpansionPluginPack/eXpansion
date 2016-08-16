<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Classes;

//require_once('Webaccess.php');

use ManiaLive\Application\Listener as AppListener;
use ManiaLive\Event\Dispatcher;
use ManiaLive\Features\Tick\Event as TickEvent;
use ManiaLive\Features\Tick\Listener as TickListener;
use ManiaLivePlugins\eXpansion\Core\Classes\Webaccess;
use ManiaLivePlugins\eXpansion\Dedimania\Classes\Request as dediRequest;
use ManiaLivePlugins\eXpansion\Dedimania\Events\Event as dediEvent;
use ManiaLivePlugins\eXpansion\Dedimania\Structures\DediMap;
use ManiaLivePlugins\eXpansion\Dedimania\Structures\DediPlayer;
use ManiaLivePlugins\eXpansion\Helpers\Helper;
use Maniaplanet\DedicatedServer\Structures\GameInfos;

class Connection extends \ManiaLib\Utils\Singleton implements AppListener, TickListener
{
    // used for dedimania
    private $version = 0.15;

    /** @var integer */
    public static $serverMaxRank = 15;

    /** @var \ManiaLivePlugins\eXpansion\Dedimania\Structures\DediMap */
    public static $dediMap = null;

    /** @var \ManiaLivePlugins\eXpansion\Dedimania\Structures\DediPlayer[] Cached players from dedimania */
    public static $players = array();

    /** @var \Webaccess */
    private $webaccess;

    /** @var \Maniaplanet\DedicatedServer\Connection */
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

    public function __construct()
    {
        parent::__construct();

        $this->webaccess = new Webaccess();

        // if you are developing change port to 8081, othervice use 8082
        $this->url = "http://dedimania.net:8082/Dedimania";
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \ManiaLivePlugins\eXpansion\Helpers\Singletons::getInstance()->getDediConnection();
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->read = array();
        $this->write = array();
        $this->except = array();
        $this->lastUpdate = time();
        Dispatcher::register(TickEvent::getClass(), $this);
    }

    public function __destruct()
    {
        $this->webaccess = null;
        Dispatcher::unregister(TickEvent::getClass(), $this);
    }

    public function onTick()
    {
        try {
            $this->webaccess->select($this->read, $this->write, $this->except, 0, 0);

            if ($this->sessionId !== null && (time() - $this->lastUpdate) > 240) {
                $this->debug("Dedimania connection keepalive!");
                $this->updateServerPlayers($this->storage->currentMap);
                $this->lastUpdate = time();
            }
        } catch (\Exception $e) {
            $this->console("OnTick Update failed: " . $e->getMessage());
        }
    }

    /**
     * dedimania.OpenSession
     * Should be called when starting the dedimania conversation
     */
    public function openSession($packmask = "", $config = null)
    {
        $version = $this->connection->getVersion();

        $serverInfo = $this->connection->getDetailedPlayerInfo($this->storage->serverLogin);
        if (is_null($config)) {
            $config = \ManiaLivePlugins\eXpansion\Dedimania\Config::getInstance();
        }

        if (empty($config->login)) {
            throw new \Exception("Server login is not configured!\n");
        }
        if (empty($config->code)) {
            throw new \Exception("Server code is not configured! \n");
        }

        if (strtolower($serverInfo->login) != strtolower($config->login)) {
            throw new \Exception("Your dedicated server login differs from configured server login, please check your configuration.");
        }

        if ($packmask == "") {
            $packmask = $version->titleId;
        }

        switch ($packmask) {
            case "TMStadium":
            case 'BaF1@mcrobert':
            case 'Dirt_World_TM2@bernatf':
            case 'Dirt_@mr.dvd':
            case 'edenia@nexxusdrako':
            case 'ESLTitlePack@nilakite2':
            case 'Nations_Forever@citiroller':
            case 'Nations_ESWC@tm-jinzo':
            case 'Minimalize@mvv0105':
            case 'only_stadium_car@adamkooo':
            case 'RPG@tmrpg':
            case 'SRE@tm-nascar':
            case 'StadiumPlatform@darkpuddle_':
            case 'Ultimate_Challenge_2@mr.dvd':
            case 'esl_comp@lt_forever':
                $packmask = "Stadium";
                break;
            case "TMCanyon":
            case "Acrobatic@mr.dvd":
            case "_f00Canyon_00fStar@mcmart1":
            case 'CanyonCity@darkpuddle_':
            case 'Canyon_Inverted_edk@edk':
            case 'DD_RailwaySystem@divingduck':
            case 'Glide@darmaya':
            case 'LEGO_Racing@macio6':
            case 'POLSO@darmaya':
            case 'Raid@meuh21':
            case 'TM2_Canyon_Sparkstedition@sparkster':
            case 'Mr.DvDCanyon_dvd@mr.dvd':
            case 'Wastelands@mpmandark':
            case 'wtc@woutre':
                $packmask = "Canyon";
                break;
            case "TMValley":
            case 'adrenalin@flighthigh':
            case 'endless_Valley@flighthigh':
            case 'F1_Abu_Dhabi@darkpuddle_':
            case 'miniahoy3@kaeptniglu':
            case 'RaidValley@meuh21':
            case 'ValleyCity@darkpuddle_':
            case 'Valley_Extensions@dag_bert':
                $packmask = "Valley";
                break;

            // Trust eXpansion that the plugin won't run on Shootmania :D.
            case "Trackmania_2@nadeolabs" :
            default :
                $packmask = "Trackmania_2@nadeolabs";
                break;
        }

        $args = array(array(
            "Game" => "TM2",
            "Login" => strtolower($config->login),
            "Code" => $config->code,
            "Tool" => "eXpansion",
            "Version" => \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION,
            "Packmask" => $packmask,
            "ServerVersion" => $version->version,
            "ServerBuild" => $version->build,
            "Path" => $serverInfo->path,
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
    public function setChallengeTimes(\Maniaplanet\DedicatedServer\Structures\Map $map, $rankings, $vreplay, $greplay)
    {

// disabled for relay server
        if ($this->connection->isRelayServer()) {
            return;
        }

// special rounds mode disabled
        if (\ManiaLivePlugins\eXpansion\Core\Core::eXpGetCurrentCompatibilityGameMode() == GameInfos::GAMEMODE_ROUNDS && (!isset($map->lapRace)
                || $map->lapRace) && $this->storage->gameInfos->roundsForcedLaps && $this->storage->gameInfos->roundsForcedLaps != 0
        ) {
            $this->console("[Warning] Special rounds mode with forced laps ignored!");

            return;
        }

// only special maps under 8 seconds are allowed
        if ($map->authorTime < 8000 && strtolower($map->author) != 'nadeo') {
            $this->console("[Notice] Author time under 8 seconds, will not send records.");

            return;
        }

        if ($this->dediUid != $map->uId) {
            $this->console("[Warning] Map UId mismatch! Map UId differs from dedimania recieved uid for the map. Times are not sent.");

            return;
        }

        $times = array();

        foreach ($rankings as $rank) {
            if (sizeof($rank['BestCheckpoints']) > 0 && $rank['BestTime'] == end($rank['BestCheckpoints'])) {
                if ($rank['BestTime'] > 5000) // should do sanity checks for more...
                {
                    $times[] = array("Login" => $rank['Login'], "Best" => intval($rank['BestTime']), "Checks" => implode(',',
                        $rank['BestCheckpoints']));
                }
            }
        }

        usort($times, array($this, "dbsort"));

        if (sizeof($times) == 0) {
            $this->debug("No new records, skipping dedimania send.");

            return;
        }

        $Vchecks = "";
        if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_LAPS) {
            $Vchecks = implode(",", $rankings[0]['AllCheckpoints']);
        }

        if (empty($vreplay)) {
            $this->console("Validation replay is empty, cancel sending times.");

            return;
        }
        $base64Vreplay = new IXR_Base64($vreplay);

        $base64Greplay = "";
        if (($this->dediBest == null && sizeof($this->dediRecords['Records']) == 0) || $times[0]['Best'] < $this->dediBest) {
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

    public function send(dediRequest $request, $callback)
    {
        $this->webaccess->request($this->url, array(array($this, '_process'), $callback), $request->getXml(), true, 600, 3, 5);
    }

    private function _getSrvInfo()
    {
        $info = array(
            "SrvName" => $this->storage->server->name,
            "Comment" => $this->storage->server->comment,
            "Private" => ($this->storage->server->password !== ""),
            "NumPlayers" => sizeof($this->storage->players),
            "MaxPlayers" => $this->storage->server->currentMaxPlayers,
            "NumSpecs" => sizeof($this->storage->spectators),
            "MaxSpecs" => $this->storage->server->currentMaxSpectators,
        );

        return $info;
    }

    private function _getMapInfo($map = null)
    {
        if ($map == null) {
            $map = $this->storage->currentMap;
        }
        if ($map instanceof \Maniaplanet\DedicatedServer\Structures\Map) {
            $mapInfo = array(
                "UId" => $map->uId,
                "Name" => $map->name,
                "Environment" => $map->environnement,
                "Author" => $map->author,
                "NbCheckpoints" => $map->nbCheckpoints,
                "NbLaps" => $map->nbLaps,
            );

            return $mapInfo;
        }
        throw new Exception('error on _getMapInfo, map is in wrong format');
    }

    public function checkSession()
    {
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
    public function getChallengeRecords()
    {
        if ($this->sessionId === null) {
            $this->debug("Session id is null!");

            return;
        }
        $players = array();
        foreach ($this->storage->players as $player) {
            if (is_object($player) && $player->login != $this->storage->serverLogin) {
                $players[] = Array("Login" => $player->login, "IsSpec" => false);
            }
        }
        foreach ($this->storage->spectators as $player) {
            if (is_object($player)) {
                $players[] = Array("Login" => $player->login, "IsSpec" => true);
            }
        }

        $args = array(
            $this->sessionId,
            $this->_getMapInfo(),
            $this->_getGameMode(),
            $this->_getSrvInfo(),
            $players,
        );
        $this->lastUpdate = time();

        $request = new dediRequest("dedimania.GetChallengeRecords", $args);
        $this->send($request, array($this, "xGetRecords"));
    }

    /**
     * PlayerConnect
     *
     * @param \ManiaLive\Data\Player $player
     * @param bool $isSpec
     */
    public function playerConnect(\ManiaLive\Data\Player $player, $isSpec)
    {

        if ($this->sessionId === null) {
            $this->console("Error: Session ID is null!");

            return;
        }

        if ($player->login == $this->storage->serverLogin) {
            $this->debug("Abort. tried to send server login.");

            return;
        }

        $args = array(
            $this->sessionId,
            $player->login,
            $player->nickName,
            $player->path,
            $isSpec,
        );

        $request = new dediRequest("dedimania.PlayerConnect", $args);
        $this->send($request, array($this, "xPlayerConnect"));
    }

    /**
     * playerMultiConnect
     *
     * @param \ManiaLive\Data\Player[] $players
     * @param bool $isSpec
     */
    public function playerMultiConnect($players)
    {

        if ($this->sessionId === null) {
            $this->debug("Session id is null!");

            return;
        }
        if (!is_array($players)) {
            return;
        }

        $x = 0;
        $request = "";

        foreach ($players as $player) {

            if (is_a($player[0], "\ManiaLive\Data\Player")) {

                if ($player[0]->login == $this->storage->serverLogin) {
                    $this->debug("[Dedimania Warning] Tried to send server login.");
                    continue;
                }

                $args = array(
                    $this->sessionId,
                    $player[0]->login,
                    $player[0]->nickName,
                    $player[0]->path,
                    $player[1],
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
        if (is_object($request)) {
            $this->send($request, array($this, "xPlayerMultiConnect"));
        }
    }

    /**
     * playerDisconnect
     *
     * @param string $login
     */
    public function playerDisconnect($login)
    {
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
     * @param array ,\Maniaplanet\DedicatedServer\Structures\Map $map
     *
     * @return type
     */
    public function updateServerPlayers($map)
    {
        if ($this->sessionId === null) {
            $this->debug("Session id is null!");

            return;
        }

        if (is_array($map)) {
            $uid = $map['UId'];
        }
        if (is_object($map)) {
            $uid = $map->uId;
        }

        $players = array();
        foreach ($this->storage->players as $player) {
            if (is_object($player) && $player->login != $this->storage->serverLogin) {
                $players[] = Array("Login" => $player->login, "IsSpec" => false, "Vote" => -1);
            }
        }
        foreach ($this->storage->spectators as $player) {
            if (is_object($player)) {
                $players[] = Array("Login" => $player->login, "IsSpec" => true, "Vote" => -1);
            }
        }
        $gamemode = $this->_getGameMode();

        $args = array(
            $this->sessionId,
            $this->_getSrvInfo(),
            array("UId" => $uid, "GameMode" => $gamemode),
            $players,
        );

        $request = new dediRequest("dedimania.UpdateServerPlayers", $args);
        $this->send($request, array($this, "xUpdateServerPlayers"));
    }

    private function _getGameMode()
    {
        switch ($this->storage->gameInfos->gameMode) {
            case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT:
                $gamemode = $this->detectScriptName();
                break;
            case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS:
            case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK:
                $gamemode = "TA";
                break;
            case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP:
            case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM:
            case \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS:
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

    public function detectScriptName()
    {
        $name = $this->connection->getScriptName();

        $scriptNameArr = $this->connection->getScriptName();
        $scriptName = $scriptNameArr['CurrentValue'];

        // Workaround for a 'bug' in setModeScriptText.
        if ($scriptName === '<in-development>') {
            $scriptName = $scriptNameArr['NextValue'];
        }

        $scriptName = strtolower($scriptName);
        switch ($scriptName) {
            case "timeattack.script.txt":
                return "TA";
                break;
            case "laps.script.txt":
                return "TA";
                break;
            case "rounds.script.txt":
                return "Rounds";
                break;
            case "team.script.txt":
                return "Rounds";
                break;
            case "cup.script.txt":
                return "Rounds";
                break;
            default:
                return "";
                break;
        }

        // shoud never happen, but just for failsafe :)
        return "";
    }

    public function _process($dedires, $callback)
    {

        try {

            $msg = \Maniaplanet\DedicatedServer\Xmlrpc\Request::decode($dedires['Message']);

            $errors = end($msg[1]);

            $triggerError = false;

            if (count($errors) > 0 && array_key_exists('methods', $errors[0])) {
                foreach ($errors[0]['methods'] as $error) {
                    if (!empty($error['errors'])) {
                        $this->console('[Dedimania service return error] Method:' . $error['methodName']);
                        $this->console('Error string:' . $error['errors']);
                    }
                }
            }
            // print "Actual Data\n";

            $array = $msg[1];
            unset($array[count($array) - 1]);


            if (array_key_exists("faultString", $array[0])) {
                // $this->connection->chatSendServerMessage("" . $array[0]['faultString']);
                $this->console("Fault from dedimania server: " . $array[0]['faultString']);

                return;
            }

            if (!empty($array[0][0]['Error'])) {
                // $this->connection->chatSendServerMessage("Dedimania error: " . $array[0][0]['Error']);
                $this->console("Error from dedimania server: " . $array[0][0]['Error']);

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

    public function dbsort($a, $b)
    {
        if ($b['Best'] <= 0) {
            return -1;
        } // other best valid
        elseif ($a['Best'] <= 0) {
            return 1;
        } // best a better than best b
        elseif ($a['Best'] < $b['Best']) {
            return -1;
        } // best b better than best a
        elseif ($a['Best'] > $b['Best']) {
            return 1;
        }
    }

    public function xOpenSession($data)
    {
        if (isset($data[0][0]['SessionId'])) {
            $this->sessionId = $data[0][0]['SessionId'];
            $this->console("Authentication success to dedimania server!");
            $this->debug("recieved Session key:" . $this->sessionId);
            Dispatcher::dispatch(new dediEvent(dediEvent::ON_OPEN_SESSION, $this->sessionId));

            return;
        }
        if (!empty($data[0][0]['Error'])) {
            $this->console("Authentication Error occurred: " . $data[0][0]['Error']);

            return;
        }
    }

    public function xGetRecords($data)
    {
        $data = $data[0];

        $this->dediRecords = array();
        $this->dediUid = null;
        $this->dediBest = null;
        self::$dediMap = null;

        if (!empty($data[0]['Error'])) {
            $this->console("Error from dediserver: " . $data[0]['Error']);
            return;
        }


        $this->dediUid = $data[0]['UId'];
        $this->dediRecords = $data[0];
        self::$serverMaxRank = intval($data[0]['ServerMaxRank']);
        $maplimit = intval($data[0]['ServerMaxRank']);

        if (count($data[0]['Records']) > 0) {
            $maplimit = count($data[0]['Records']);
        }

        self::$dediMap = new DediMap($data[0]['UId'], $maplimit, $data[0]['AllowedGameModes']);


        if (!$data[0]['Records']) {
            $this->debug("No records found.");

            return;
        }

        if (!empty($data[0]['Records'][0]['Best'])) {
            $this->dediBest = $data[0]['Records'][0]['Best'];
        }

        Dispatcher::dispatch(new dediEvent(dediEvent::ON_GET_RECORDS, $data[0]));
    }

    public function xUpdateServerPlayers($data)
    {

    }

    public function xSetChallengeTimes($data)
    {
        $this->console("Sending times new times: \$0f0Success");
    }

    public function xCheckSession($data)
    {

    }

    public function xPlayerConnect($data)
    {
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

    public function xPlayerMultiConnect($data)
    {
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
    }

    public function xPlayerDisconnect($data)
    {
        Dispatcher::dispatch(new dediEvent(dediEvent::ON_PLAYER_DISCONNECT, $data[0][0]['Login']));
    }

    public function debug($message)
    {
        if (DEBUG) {
            $this->console($message);
        }
    }

    public function console($message)
    {
        Helper::log("$message", array('Dedimania/Connection'));
    }

    public function onInit()
    {

    }

    public function onRun()
    {

    }

    public function onPostLoop()
    {

    }

    public function onTerminate()
    {

    }

    public function onPreLoop()
    {

    }

}
