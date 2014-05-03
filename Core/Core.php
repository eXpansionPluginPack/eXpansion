<?php

namespace ManiaLivePlugins\eXpansion\Core;

use ManiaLive\Event\Dispatcher;
use ManiaLive\Utilities\Console;
use ManiaLivePlugins\eXpansion\Core\Events\GameSettingsEvent;
use ManiaLivePlugins\eXpansion\Core\Events\ServerSettingsEvent;
use \Maniaplanet\DedicatedServer\Structures\ServerOptions;
use ManiaLivePlugins\eXpansion\Core\Events\ScriptmodeEvent as Event;
use \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

/**
 * Description of Core
 *
 * @author oliverde8
 * @author reaby
 * 
 */
class Core extends types\ExpPlugin {

    const EXP_VERSION = "0.9.5";
    const EXP_REQUIRE_MANIALIVE = "4.0.0";
    const EXP_REQUIRE_DEDIATED = "2014.4.2";  // replace dedicated 2013-7-30 to 2013.7.30

    /**
     * Last used game mode
     * @var \Maniaplanet\DedicatedServer\Structures\GameInfos
     */

    private $lastGameMode;
    private $lastGameSettings;
    private $lastServerSettings;

    /** private variable to hold players infos 
     * @var Structures\ExpPlayer[] */
    private $expPlayers = array();

    /** @var array() */
    private $teamScores = array();

    /**
     * public variable to export player infos 
     * @var Structures\ExpPlayer[] */
    public static $playerInfo = array();

    /**
     * @var Structures\NetStat[] 
     */
    public static $netStat = array();

    /** @var string[int] */
    public static $roundFinishOrder = array();

    /** @var string[string][int] */
    public static $checkpointOrder = array();

    /** @var int */
    private $giveupCount = 0;

    /** @var bool $update flag to force calculate player positions */
    private $update = true;

    /** @var bool $enableCalculation marks if player positions should be calculated */
    private $enableCalculation = true;
    private $loopTimer = 0;
    private $lastTick = 0;
    public static $action_serverInfo = -1;

    /** @var Config */
    private $config;

    /**
     *
     * @var ConfigManager
     */
    private $configManager;
    public static $gameModeDisabledPlugins = array();
    public static $isTMServer = false;
    public static $isSMServer = false;
    public static $titleId = null;

    /**
     * Is Manialive running on a Relay server or not
     * @var type Boolean
     */
    public static $isRelay = false;

    /**
     * 
     */
    function exp_onInit() {
	$logFile = "manialive-" . $this->storage->serverLogin . ".console.log";
	if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $logFile)) {
	    unlink(__DIR__ . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $logFile);
	}
	$logFile = "manialive-" . $this->storage->serverLogin . ".error.log";
	if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $logFile)) {
	    unlink(__DIR__ . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $logFile);
	}

	Dispatcher::register(\ManiaLivePlugins\eXpansion\Core\Events\ServerSettingsEvent::getClass(), $this);

	$aHandler = \ManiaLive\Gui\ActionHandler::getInstance();
	self::$action_serverInfo = $aHandler->createAction(array($this, 'showInfo'));

	$this->configManager = ConfigManager::getInstance($this);
	$this->configManager->loadSettings();
    }

    /**
     * 
     */
    function exp_onLoad() {

	$this->enableDedicatedEvents();
	$config = Config::getInstance();
	i18n::getInstance()->start();
	DataAccess::getInstance()->start();

	foreach ($config->disableGameMode as $pluginName => $exception) {
	    self::$gameModeDisabledPlugins[$pluginName] = explode(',', $exception);
	}


	$expansion = <<<'EOT'
   
   

                     __   __                      _             
                     \ \ / /                     (_)            
                  ___ \ ' / _ __   __ _ _ __  ___ _  ___  _ __  
                 / _ \ > < | '_ \ / _` | '_ \/ __| |/ _ \| '_ \ 
                |  __// . \| |_) | (_| | | | \__ \ | (_) | | | |
                 \___/_/ \_\ .__/ \__,_|_| |_|___/_|\___/|_| |_|
...........................| |.........Plugin Pack for Manialive...............
                           |_|                                                  
EOT;

	$this->console($expansion);
	$server = $this->connection->getVersion();
	$d = (object) date_parse_from_format("Y-m-d_H_i", $server->build);
	$this->console('Dedicated Server running for title: ' . $server->titleId);

	$this->connection->setApiVersion($config->API_Version); // For SM && TM
	$this->console('Dedicated Server api version in use: ' . $config->API_Version);
	$this->console('eXpansion version: ' . $this->getVersion());

	$bExitApp = false;
	$dedicatedVersion = $d->year . "." . $d->month . "." . $d->day;
	if (version_compare($dedicatedVersion, self::EXP_REQUIRE_DEDIATED, "lt")) {
	    $this->console('Dedicated Server: ' . $d->year . "-" . $d->month . "-" . $d->day);
	    $this->console('Minimum Dedicated version ' . self::EXP_REQUIRE_DEDIATED . ': Fail (' . $dedicatedVersion . ')');
	    $bExitApp = true;
	} else {
	    $this->console('Minimum Dedicated version ' . self::EXP_REQUIRE_DEDIATED . ': Pass (' . $dedicatedVersion . ')');
	}



	if (version_compare(PHP_VERSION, '5.3.3') >= 0) {
	    $this->console('Minimum PHP version 5.3.3: Pass (' . PHP_VERSION . ')');
	} else {
	    $this->console('Minimum PHP version 5.3.3: Fail (' . PHP_VERSION . ')');
	    $bExitApp = true;
	}

	if (gc_enabled()) {
	    $this->console('Garbage Collector enabled: Pass ');
	} else {
	    $this->console('Garbage Collector enabled: Fail )');
	    $bExitApp = true;
	}
	$this->console('');
	$this->console('Language support detected for: ' . implode(",", i18n::getInstance()->getSupportedLocales()) . '!');
	$this->console('Enabling default locale: ' . $config->defaultLanguage . '');
	i18n::getInstance()->setDefaultLanguage($config->defaultLanguage);

	$this->console('');
	$this->console('-------------------------------------------------------------------------------');
	$this->console('');
	if (DEBUG) {
	    $this->console('                        RUNNING IN DEVELOPMENT MODE  ');
	    $this->console('');
	    $this->console('-------------------------------------------------------------------------------');
	    $this->console('');
	}

	if ($bExitApp) {
	    $this->connection->chatSendServerMessage("Failed to init eXpansion, see consolelog for more info!");
	    die();
	}

	$this->lastGameMode = \ManiaLive\Data\Storage::getInstance()->gameInfos->gameMode;

	$this->connection->chatSendServerMessage('$fff$w$oe$3afΧ$fffpansion');
	$this->connection->chatSendServerMessage('$000P L U G I N   P A C K  ');
	$this->connection->chatSendServerMessage('Version ' . \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION . '  $n build ' . date("Y-m-d", filemtime(__FILE__)) . '');
	if (DEBUG) {
	    $this->connection->chatSendServerMessage('$f00$w DEBUG MODE enabled');
	}
    }

    public function onTerminate() {
	$this->connection->chatSendServerMessage("[Notice] eXpansion closed succesfully.");
	$this->connection->sendHideManialinkPage();
    }

    /**
     * 
     */
    public function exp_onReady() {
	$this->lastTick = time();
	$this->config = Config::getInstance();
	$this->registerChatCommand("server", "showInfo", 0, true);
	$this->registerChatCommand("serverlogin", "serverlogin", 0, true);
	
	$this->setPublicMethod("showInfo");
	$this->setPublicMethod("showExpSettings");
	$window = new Gui\Windows\QuitWindow();
	$this->connection->customizeQuitDialog($window->getXml(), "", true, 0);

	// this is a fix for servers with a password, if player chooses to spectate, he can now enter back to play
	$this->connection->keepPlayerSlots(true);
	$this->onBeginMap(null, null, null);
	$this->resetExpPlayers(true);
	$this->update = true;
	$this->loopTimer = round(microtime(true));

	// disable netStats Widget, comment next line
	//$this->enableTickerEvent();

	if ($this->config->enableRanksCalc == true) {
	    $this->enableApplicationEvents();
	} else {
	    $this->enableCalculation = false;
	}
	$this->lastServerSettings = clone $this->storage->server;
	$this->connection->dedicatedEcho("ManiaLive\\eXpansion", (string) getmypid());
	$this->connection->setForcedMusic(false, "");
    }

    public function onEcho($internal, $public) {
	if (($public == "ManiaLive\\eXpansion") && ($internal != (string) getmypid() )) {
	    exit("\n\nManiaLive will now exit due new eXpansion process has been initialized.");
	}
    }

    /**
     * Fixes error message on chat command /serverlogin
     * @param type $login
     */
    public function serverlogin($login) {
	
    }

    /**
     * 
     * @param array $map
     * @param bool $warmUp
     * @param bool $matchContinuation
     */
    function onBeginMap($map, $warmUp, $matchContinuation) {

	//Check if reload or save of configurations needed
	$this->configManager->check();

	//echo "Begin Map. Memory usage : ".$this->echo_memory_usage()."\n";

	$gameSettings = \ManiaLive\Data\Storage::getInstance()->gameInfos;
	$newGameMode = $gameSettings->gameMode;

	if ($newGameMode != $this->lastGameMode) {
	    Dispatcher::dispatch(new GameSettingsEvent(GameSettingsEvent::ON_GAME_MODE_CHANGE, $this->lastGameMode, $newGameMode));

	    $this->lastGameMode = $newGameMode;
	    $this->lastGameSettings = clone $gameSettings;

	    $this->checkLoadedPlugins();
	    $this->checkPluginsOnHold();
	} else {
	    //Detecting any changes in game Settings
	    if ($this->lastGameSettings == null)
		$this->lastGameSettings = clone $gameSettings;
	    else {
		$difs = $this->compareObjects($gameSettings, $this->lastGameSettings, array("gameMode", "scriptName"));
		if (!empty($difs)) {
		    Dispatcher::dispatch(new GameSettingsEvent(GameSettingsEvent::ON_GAME_SETTINGS_CHANGE, $this->lastGameSettings, $gameSettings, $difs));
		    $this->lastGameSettings = clone $gameSettings;
		}
	    }
	}

	//Detecting any changes in Server Settings
	$serverSettings = \ManiaLive\Data\Storage::getInstance()->server;
	if ($this->lastServerSettings == null)
	    $this->lastServerSettings = clone $serverSettings;
	else {
	    $difs = $this->compareObjects($serverSettings, $this->lastServerSettings, array('useChangingValidationSeed'));
	    if (!empty($difs)) {
		Dispatcher::dispatch(new ServerSettingsEvent(ServerSettingsEvent::ON_SERVER_SETTINGS_CHANGE, $this->lastServerSettings, $serverSettings, $difs));
		$this->lastServerSettings = clone $serverSettings;
	    }
	}
	$this->teamScores = array();
    }

    protected function compareObjects($obj1, $obj2, $ingnoreList = array()) {
	$difs = array();

	foreach ($obj1 as $varName => $value) {
	    if (!in_array($varName, $ingnoreList)) {
		if (is_object($value)) {
		    if (!isset($obj2->$varName)) {
			$difs[$varName] = true;
		    } else {
			$newDisf = $this->compareObjects($value, $obj2->$varName, $ingnoreList);
			if (!empty($newDisf))
			    $difs[$varName] = $newDisf;
		    }
		}
		else if (!isset($obj2->$varName) || $obj2->$varName != $value) {
		    // echo $varName . " : " . $obj2->$varName . " -> " . $value;
		    $difs[$varName] = true;
		}
	    }
	}
	return $difs;
    }

    public function onGameSettingsChange(\Maniaplanet\DedicatedServer\Structures\GameInfos $oldSettings, \Maniaplanet\DedicatedServer\Structures\GameInfos $newSettings, $changes) {
	$this->saveMatchSettings();
    }

    public function onModeScriptCallback($param1, $param2) {

	switch ($param1) {
	    case 'LibXmlRpc_BeginMap':
		$this->dispatch(Event::LibXmlRpc_BeginMap, $param2);
		break;
	    case 'LibXmlRpc_BeginMatch':
		$this->dispatch(Event::LibXmlRpc_BeginMatch, $param2);
		break;
	    case 'LibXmlRpc_BeginRound':
		$this->dispatch(Event::LibXmlRpc_BeginRound, $param2);
		break;
	    case 'LibXmlRpc_BeginSubmatch':
		$this->dispatch(Event::LibXmlRpc_BeginSubmatch, $param2);
		break;
	    case 'LibXmlRpc_BeginTurn':
		$this->dispatch(Event::LibXmlRpc_BeginTurn, $param2);
		break;
	    case 'LibXmlRpc_BeginWarmUp':
		$this->dispatch(Event::LibXmlRpc_BeginWarmUp, $param2);
		break;
	    case 'LibXmlRpc_LoadingMap':
		$this->dispatch(Event::LibXmlRpc_LoadingMap, $param2);
		break;
	    case 'LibXmlRpc_OnGiveUp':
		$this->dispatch(Event::LibXmlRpc_OnGiveUp, $param2);
		break;
	    case 'LibXmlRpc_OnRespawn':
		$this->dispatch(Event::LibXmlRpc_OnRespawn, $param2);
		break;
	    case 'LibXmlRpc_OnStartLine':
		$this->dispatch(Event::LibXmlRpc_OnStartLine, $param2);
		break;
	    case 'LibXmlRpc_OnStunt':
		$this->dispatch(Event::LibXmlRpc_OnStunt, $param2);
		break;
	    case 'LibXmlRpc_OnWayPoint':
		$this->dispatch(Event::LibXmlRpc_OnWayPoint, $param2);
		break;
	    case 'LibXmlRpc_PlayerRanking':
		$this->dispatch(Event::LibXmlRpc_PlayerRanking, $param2);
		break;

	    case 'LibAFK_IsAFK':
		$this->dispatch(Event::LibAFK_IsAFK, $param2);
		break;
	    case 'LibAFK_Properties':
		$this->dispatch(Event::LibAFK_Properties, $param2);
		break;
	    case 'LibXmlRpc_Scores':
		$this->dispatch(Event::LibXmlRpc_Scores, $param2);
		break;
	    case 'LibXmlRpc_Rankings':
		$this->dispatch(Event::LibXmlRpc_Rankings, $param2);
		break;
	}
    }

    protected function dispatch($event, $param) {
	\ManiaLive\Event\Dispatcher::dispatch(new \ManiaLivePlugins\eXpansion\Core\Events\ScriptmodeEvent($event, $param));
    }

    public function onServerSettingsChange(ServerOptions $old, ServerOptions $new, $diff) {

	$dediConfig = \ManiaLive\DedicatedApi\Config::getInstance();

	$path = $this->connection->getMapsDirectory() . "/../Config/" . $this->config->dedicatedConfigFile;
	if (file_exists($path)) {
	    $oldXml = simplexml_load_file($path);

	    $xml = '<?xml version="1.0" encoding="utf-8" ?>
<dedicated>
		' . $oldXml->authorization_levels->asXml() . '
	
 	<masterserver_account>
		<login>' . $this->storage->serverLogin . '</login>
		<password>' . $oldXml->masterserver_account->password . '</password>
		<validation_key>' . $oldXml->masterserver_account->validation_key . '</validation_key>
	</masterserver_account>
	
	<server_options>
		<name>' . $new->name . '</name>
		<comment>' . $new->comment . '</comment>
		<hide_server>' . ($new->hideServer ? 1 : 0) . '</hide_server>					<!-- value is 0 (always shown), 1 (always hidden), 2 (hidden from nations) -->

		<max_players>' . $new->nextMaxPlayers . '</max_players>
		<password>' . $new->password . '</password>
		
		<max_spectators>' . $new->nextMaxSpectators . '</max_spectators>
		<password_spectator>' . $new->passwordForSpectator . '</password_spectator>
	
		<keep_player_slots>' . ($new->keepPlayerSlots ? 'True' : 'False') . '</keep_player_slots>			<!-- when a player changes to spectator, hould the server keep if player slots/scores etc.. or not. --> 	
		<ladder_mode>' . $new->nextLadderMode . '</ladder_mode>				<!-- value between \'inactive\', \'forced\' (or \'0\', \'1\') -->
		
		<enable_p2p_upload>' . ($new->isP2PUpload ? 'True' : 'False') . '</enable_p2p_upload>
		<enable_p2p_download>' . ($new->isP2PDownload ? 'True' : 'False') . '</enable_p2p_download>
		
		<callvote_timeout>' . $new->nextCallVoteTimeOut . '</callvote_timeout>
		<callvote_ratio>' . $new->callVoteRatio . '</callvote_ratio>				<!-- default ratio. value in [0..1], or -1 to forbid. -->

		' . $oldXml->server_options->callvote_ratios->asXml() . '

		<allow_map_download>' . ($new->allowMapDownload ? 'True' : 'False') . '</allow_map_download>
		<autosave_replays>' . ($new->autoSaveReplays ? 'True' : 'False') . '</autosave_replays>
		<autosave_validation_replays>' . ($new->autoSaveValidationReplays ? 'True' : 'False') . '</autosave_validation_replays>

		<referee_password>' . $new->refereePassword . '</referee_password>
		<referee_validation_mode>' . $new->refereeMode . '</referee_validation_mode>		<!-- value is 0 (only validate top3 players),  1 (validate all players) -->

		<use_changing_validation_seed>' . ($new->useChangingValidationSeed ? 'True' : 'False') . '</use_changing_validation_seed>

		<disable_horns>' . ($new->disableHorns ? 'True' : 'False') . '</disable_horns>
		<clientinputs_maxlatency>' . ($new->clientInputsMaxLatency ? 'True' : 'False') . '</clientinputs_maxlatency>		<!-- 0 mean automatic adjustement -->
	</server_options>
	
	<system_config>
		<connection_uploadrate>' . $oldXml->system_config->connection_uploadrate . '</connection_uploadrate>		<!-- Kbits per second -->
		<connection_downloadrate>' . $oldXml->system_config->connection_downloadrate . '</connection_downloadrate>		<!-- Kbits per second -->

		<allow_spectator_relays>' . $oldXml->system_config->allow_spectator_relays . '</allow_spectator_relays>

		<p2p_cache_size>' . $oldXml->system_config->p2p_cache_size . '</p2p_cache_size>

		<force_ip_address' . $oldXml->system_config->force_ip_address . '></force_ip_address>
		<server_port>' . $oldXml->system_config->server_port . '</server_port>
		<server_p2p_port>' . $oldXml->system_config->server_p2p_port . '</server_p2p_port>
		<client_port>' . $oldXml->system_config->client_port . '</client_port>
		<bind_ip_address>' . $oldXml->system_config->bind_ip_address . '</bind_ip_address>
		<use_nat_upnp>' . $oldXml->system_config->use_nat_upnp . '</use_nat_upnp>

		<gsp_name>' . $oldXml->system_config->gsp_name . '</gsp_name>						<!-- Game Server Provider name and info url -->
		<gsp_url>' . $oldXml->system_config->gsp_url . '</gsp_url>						<!-- If you\'re a server hoster, you can use this to advertise your services -->

		<xmlrpc_port>' . $oldXml->system_config->xmlrpc_port . '</xmlrpc_port>
		<xmlrpc_allowremote>' . $oldXml->system_config->xmlrpc_allowremote . '</xmlrpc_allowremote>			<!-- If you specify an ip adress here, it\'ll be the only accepted adress. this will improve security. -->
		
		<blacklist_url>' . $oldXml->system_config->blacklist_url . '</blacklist_url>
		<guestlist_filename>' . $oldXml->system_config->guestlist_filename . '</guestlist_filename>
		<blacklist_filename>' . $oldXml->system_config->blacklist_filename . '</blacklist_filename>
		
		<title>' . $this->connection->getVersion()->titleId . '</title>		<!-- SMStorm, TMCanyon, ... -->

		<minimum_client_build>' . $oldXml->system_config->minimum_client_build . '</minimum_client_build>			<!-- Only accept updated client to a specific version. ex: 2011-10-06 -->

		<disable_coherence_checks>' . $oldXml->system_config->disable_coherence_checks . '</disable_coherence_checks>	<!-- disable internal checks to detect issues/cheats, and reject race times -->

		<use_proxy>' . $oldXml->system_config->use_proxy . '</use_proxy>
		<proxy_login>' . $oldXml->system_config->proxy_login . '</proxy_login>
		<proxy_password>' . $oldXml->system_config->proxy_password . '</proxy_password>
	</system_config>
</dedicated>
';
	    file_put_contents($path, $xml);
	}
    }

    public function onMapListModified($curMapIndex, $nextMapIndex, $isListModified) {
	if ($isListModified) {
	    $this->saveMatchSettings();
	}
    }

    public function saveMatchSettings() {
	if (!empty($this->config->defaultMatchSettingsFile)) {
	    $this->connection->saveMatchSettings("MatchSettings" . DIRECTORY_SEPARATOR . $this->config->defaultMatchSettingsFile);
	}
    }

    public function onGameModeChange($oldGameMode, $newGameMode) {
	$this->showNotice("GameMode Changed");
    }

    private function showNotice($message) {
	$this->console('                         _   _       _   _          ');
	$this->console('                        | \ | | ___ | |_(_) ___ ___ ');
	$this->console('                        |  \| |/ _ \| __| |/ __/ _ \ ');
	$this->console('                        | |\  | (_) | |_| | (_|  __/ ');
	$this->console("                        |_| \_|\___/ \__|_|\___\___|");
	$fill = "";
	$firstline = explode("\n", $message, 2);
	if (!is_array($firstline))
	    $firstline = array($firstline);
	for ($x = 0; $x < ((80 - strlen($firstline[0])) / 2); $x++) {
	    $fill .= " ";
	}
	$this->console($fill . $message);
    }

    private function checkLoadedPlugins() {
	$pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();
	$this->console('Shutting down uncompatible plugins');

	foreach ($this->exp_getGameModeCompability() as $plugin => $compability) {
	    if (!$plugin::exp_checkGameCompability()) {
		try {
		    $this->callPublicMethod($plugin, 'exp_unload');
		} catch (\Exception $ex) {
		    
		}
	    }
	}
    }

    private function checkPluginsOnHold() {
	$this->console('Starting compatible plugins');

	if (!empty(types\BasicPlugin::$plugins_onHold)) {
	    $pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();
	    foreach (types\BasicPlugin::$plugins_onHold as $plugin_id) {
		//$parts = explode("\\", $plugin_id);
		//$className = '\\ManiaLivePlugins\\' . $plugin_id . '\\' . $parts[1];
		$className = $plugin_id;
		if ($className::exp_checkGameCompability() && !$this->isPluginLoaded($plugin_id)) {
		    try {
			$pHandler->load($plugin_id);
		    } catch (Exception $ex) {
			$this->console('Plugin : ' . $plugin_id . ' Maybe already loaded');
		    }
		}
	    }
	}
    }

    public function showInfo($login) {
	if ($this->isPluginLoaded('\ManiaLivePlugins\eXpansion\ServerStatistics\ServerStatistics')) {
	    Gui\Windows\InfoWindow::$statsAction = \ManiaLivePlugins\eXpansion\ServerStatistics\ServerStatistics::$serverStatAction;
	} else {
	    Gui\Windows\InfoWindow::$statsAction = -1;
	}
	$info = Gui\Windows\InfoWindow::Create($login);
	$info->setTitle("Server info");
	$info->centerOnScreen();
	$info->setSize(93, 68);
	$info->show();
    }

    public function onTick() {
	$stats = $this->connection->getNetworkStats();
	$showNotice = false;
	if (time() - $this->lastTick > 5) {
	    $this->lastTick = time();
	    foreach ($stats->playerNetInfos as $player) {
		$stat = new Structures\NetStat($player);
		self::$netStat[$player->login] = $stat;
		$showNotice = true;
		/* if ($stat->updateLatency >= 160) {

		  $showNotice = true;
		  }
		  if ($stat->updatePeriod >= 600) {
		  $showNotice = true;
		  } */
	    }
	    if ($showNotice) {
		Gui\Widgets\Widget_Netstat::EraseAll();

		$info = Gui\Widgets\Widget_Netstat::Create(\ManiaLive\Gui\Window::RECIPIENT_ALL);
		$info->setLayer(\ManiaLive\Gui\Window::LAYER_SCORES_TABLE);
		$info->setPosition(-158, 60);
		$info->setScale(0.7);
		$info->show();
	    } else {
		Gui\Widgets\Widget_Netstat::EraseAll();
	    }
	}
    }

    public function showExpSettings($login) {
	if (AdminGroups::hasPermission($login, 'expansion_settings')) {
	    Gui\Windows\ExpSettings::Erase($login);
	    $win = Gui\Windows\ExpSettings::Create($login);
	    $win->setTitle("Expansion Settings");
	    $win->centerOnScreen();
	    $win->setSize(140, 100);
	    $win->populate($this->configManager, 'General');
	    $win->show();
	}
    }

    public function onPostLoop() {
	// check for update conditions	
	if ($this->enableCalculation == false)
	    return;
	if ($this->storage->serverStatus->code == 4 && $this->update && (microtime(true) - $this->loopTimer) > 0.35) {
	    $this->update = false;
	    $this->loopTimer = microtime(true);
	    $this->calculatePositions();
	}  //Testint conf manager;
    }

    function echo_memory_usage() {
	gc_enable();
	gc_collect_cycles();
	$mem_usage = memory_get_usage(true);

	if ($mem_usage < 1024)
	    return $mem_usage . " bytes";
	elseif ($mem_usage < 1048576)
	    return round($mem_usage / 1024, 2) . " kilobytes";
	else
	    return round($mem_usage / 1048576, 2) . " megabytes";
    }

    public function onPlayerConnect($login, $isSpectator) {
	//echo "Player connected. Memory usage : ".$this->echo_memory_usage()."\n";
    }

    public function onPlayerDisconnect($login, $disconnectionReason) {

	//echo "Player Disconnect. Memory usage : ".$this->echo_memory_usage()."\n";

	$this->update = true;
	if (array_key_exists($login, self::$netStat)) {
	    unset(self::$netStat[$login]);
	}
	if (array_key_exists($login, $this->expPlayers)) {
	    $this->expPlayers[$login]->hasRetired = true;
	    $this->expPlayers[$login]->isPlaying = false;
	    unset($this->expPlayers[$login]);
	}
    }

    public function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex) {
	if ($this->enableCalculation == false)
	    return;

	$this->update = true;
	if (!array_key_exists($login, $this->expPlayers)) {
	    $player = $this->storage->getPlayerObject($login);
	    $this->expPlayers[$login] = Structures\ExpPlayer::fromArray($player->toArray());
	}
	self::$checkpointOrder[$checkpointIndex][] = $login;
	$this->expPlayers[$login]->checkpoints[$checkpointIndex] = $timeOrScore;
	$this->expPlayers[$login]->time = $timeOrScore;
	$this->expPlayers[$login]->curCpIndex = $checkpointIndex;
	$this->expPlayers[$login]->curLap = $curLap;
    }

    function onBeginMatch() {
	$window = new Gui\Windows\QuitWindow();
	$this->connection->customizeQuitDialog($window->getXml(), "", true, 0);
    }

    public function onBeginRound() {
	$this->update = true;
	$this->resetExpPlayers();
    }

    public function onEndRound() {
	$this->update = true;
    }

    public function onPlayerInfoChanged($playerInfo) {
	if ($this->enableCalculation == false)
	    return;

	$this->update = true;
	$player = \Maniaplanet\DedicatedServer\Structures\PlayerInfo::fromArray($playerInfo);

	if (!array_key_exists($player->login, $this->expPlayers)) {
	    $login = $player->login;
	    $pla = $this->storage->getPlayerObject($player->login);
	    if (empty($pla)) {
		return;
	    }
	    $this->expPlayers[$player->login] = Structures\ExpPlayer::fromArray($pla->toArray());

	    if (array_key_exists($login, $this->teamScores))
		$this->expPlayers[$login]->matchScore = $this->teamScores[$login];
	    $this->expPlayers[$login]->hasRetired = false;
	    $this->expPlayers[$login]->isPlaying = true;
	    $this->expPlayers[$login]->checkpoints = array(0 => 0);
	    $this->expPlayers[$login]->finalTime = -1;
	    $this->expPlayers[$login]->position = -1;
	    $this->expPlayers[$login]->time = -1;
	    $this->expPlayers[$login]->curCpIndex = -1;
	    $this->expPlayers[$login]->isFinished = false;
	    // in case player is joining to match in round, he needs to be marked as waiting
	    if ($this->storage->gameInfos->gameMode != \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK)
		$this->expPlayers[$login]->isWaiting = true;
	}

	$this->expPlayers[$player->login]->teamId = $player->teamId;
	$this->expPlayers[$player->login]->spectator = $player->spectator;
	$this->expPlayers[$player->login]->temporarySpectator = $player->temporarySpectator;
	$this->expPlayers[$player->login]->pureSpectator = $player->pureSpectator;

	// player just temp spectator
	if ($player->temporarySpectator == true && $player->spectator == false) {
	    $this->expPlayers[$player->login]->hasRetired = true;
	    $this->expPlayers[$player->login]->isPlaying = true;
	    // player is spectator
	} elseif ($player->spectator == true) {
	    $this->expPlayers[$player->login]->isPlaying = false;
	    $this->expPlayers[$player->login]->hasRetired = true;
	} else {
	    // player is not any spectator
	    $this->expPlayers[$player->login]->isPlaying = true;
	    $this->expPlayers[$player->login]->hasRetired = true;
	}
    }

    public function resetExpPlayers($readRankings = false) {
	self::$roundFinishOrder = array();
	self::$checkpointOrder = array();

	foreach ($this->storage->players as $login => $player) {
	    $this->expPlayers[$login] = Structures\ExpPlayer::fromArray($player->toArray());

	    if ($player->spectator == 1) {
		$this->expPlayers[$login]->hasRetired = true;
		$this->expPlayers[$login]->isPlaying = false;
		continue;
	    }
	    if (array_key_exists($login, $this->teamScores))
		$this->expPlayers[$login]->matchScore = $this->teamScores[$login];
	    $this->expPlayers[$login]->hasRetired = false;
	    $this->expPlayers[$login]->isPlaying = true;
	    $this->expPlayers[$login]->checkpoints = array(0 => 0);
	    $this->expPlayers[$login]->finalTime = -1;
	    $this->expPlayers[$login]->position = -1;
	    $this->expPlayers[$login]->time = -1;
	    $this->expPlayers[$login]->curCpIndex = -1;
	    $this->expPlayers[$login]->isWaiting = false;
	    $this->expPlayers[$login]->isFinished = false;
	}


	$rankings = $this->connection->getCurrentRanking(-1, 0);
	foreach ($rankings as $player) {
	    if (!empty($player->login) && array_key_exists($player->login, $this->expPlayers)) {
		$this->expPlayers[$player->login]->score = $player->score;
	    }
	}
    }

    public function onPlayerFinish($playerUid, $login, $timeOrScore) {
	if ($this->enableCalculation == false)
	    return;

// handle onPlayerfinish @ start from server. 
	$this->update = true;
	if ($playerUid == 0)
	    return;

	/* if (!array_key_exists($login, $this->expPlayers)) {
	  $player = $this->storage->getPlayerObject($login);
	  $this->expPlayers[$login] = Structures\ExpPlayer::fromArray($player->toArray());
	  } */

	if ($timeOrScore == 0) {
	    if (array_key_exists($login, $this->expPlayers)) {
		$this->expPlayers[$login]->finalTime = 0;
		if ($this->storage->gameInfos->gameMode !== \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK) {
		    $this->expPlayers[$login]->hasRetired = true;
		    Dispatcher::dispatch(new Events\PlayerEvent(Events\PlayerEvent::ON_PLAYER_GIVEUP, $this->expPlayers[$login]));
		}
	    }
	    return;
	}

	if ($timeOrScore > 0) {
	    if (array_key_exists($login, $this->expPlayers)) {
		$this->expPlayers[$login]->finalTime = $timeOrScore;
		$this->expPlayers[$login]->isFinished = true;
		if ($this->storage->gameInfos->gameMode !== \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK) {
		    self::$roundFinishOrder[] = $login;
		}
	    }

// set points
	    if ($this->storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM) {
		$maxpoints = $this->storage->gameInfos->teamMaxPoints;
		$total = 0;
// get total number if players
		foreach ($this->expPlayers as $player) {
		    if ($player->isPlaying)
			$total++;
		}
// set max points
		if ($total > $maxpoints) {
		    $total = $maxpoints;
		}

		if (array_key_exists($login, $this->expPlayers)) {

		    $player = $this->expPlayers[$login];
		    if ($player->isPlaying) {
			$points = ($total + 1) - (count(self::$roundFinishOrder));


			if ($points < 0)
			    $points = 0;

			if (!array_key_exists($player->login, $this->teamScores)) {
			    $this->teamScores[$player->login] = $points;
			} else {
			    $this->teamScores[$player->login] += $points;
			}
			$this->expPlayers[$player->login]->matchScore = $this->teamScores[$player->login];
		    }
		}
		self::$playerInfo = $this->expPlayers;
	    }
	}
    }

    function calculatePositions() {
	/** @var $playerPositions Structures\ExpPlayer[] */
	$playerPositions = array();
	/** @var $playerPositions Structures\ExpPlayer[] */
	$oldExpPlayers = $this->expPlayers;
	$oldGiveupCount = $this->giveupCount;
	$giveupCount = 0;
	$giveupPlayers = array();
	foreach ($this->expPlayers as $login => $player) {
	    if (empty($player)) {
		unset($this->expPlayers[$login]);
		continue;
	    }

	    if ($player->isPlaying == false || $player->isWaiting) {
		unset($this->expPlayers[$login]);
		continue;
	    }

	    if (isset($player->checkpoints[0])) {
		$player->time = end($player->checkpoints);
// $player->curCpIndex = key($player->checkpoints);
	    }
// is player is not playing ie. has become spectator or disconnect, remove...




	    if ($player->finalTime == 0) {
		$giveupPlayers[] = $player;
		$this->giveupCount++;
	    }
	    $playerPositions[] = $player;
	}


	usort($playerPositions, array($this, 'positionCompare'));


	$firstPlayerLogin = null;
	$previousPlayerLogin = null;
	$first = null;
	$previous = null;

	/** @var $playerPositions Structures\ExpPlayer[] */
	foreach ($playerPositions as $pos => $current) {
	    $dispatch = false;
	    $login = $current->login;
// get old position
	    $oldPos = $current->position;
// update new position
	    $this->expPlayers[$login]->position = $pos;
	    if ($firstPlayerLogin == null) {
		$this->expPlayers[$login]->deltaCpCountTop1 = 0;
		$this->expPlayers[$login]->deltaTimeTop1 = 0;
		$firstPlayerLogin = $login;
	    } else {
		$first = $this->expPlayers[$firstPlayerLogin];

		$this->expPlayers[$login]->deltaCpCountTop1 = $first->curCpIndex - $current->curCpIndex - 1;
		if ($this->expPlayers[$login]->deltaCpCountTop1 < 0)
		    $this->expPlayers[$login]->deltaCpCountTop1 = 0;

		$cpindex = $current->curCpIndex;
		if ($cpindex < 0)
		    $cpindex = 0;

		$this->expPlayers[$login]->deltaTimeTop1 = -1;
		if (isset($first->checkpoints[$cpindex]))
		    $this->expPlayers[$login]->deltaTimeTop1 = $current->time - $first->checkpoints[$cpindex];
	    }
// reset flags
	    $this->expPlayers[$login]->changeFlags = 0;

	    if ($pos != $oldPos) {
		$this->expPlayers[$login]->changeFlags |= Structures\ExpPlayer::Player_rank_position_change;
		$dispatch = true;
	    }

	    if ($oldExpPlayers[$login]->curCpIndex != $current->curCpIndex) {
		$this->expPlayers[$login]->changeFlags |= Structures\ExpPlayer::Player_cp_position_change;
		$dispatch = true;
	    }

	    if ($dispatch) {
		Dispatcher::dispatch(new Events\PlayerEvent(Events\PlayerEvent::ON_PLAYER_POSITION_CHANGE, $this->expPlayers[$login], $oldPos, $pos));
	    }
// set previous player
	    if ($previousPlayerLogin == null) {
		$previousPlayerLogin = $login;
		$previous = $current;
	    }
	} // end of foreach playerpositions;
// export infos..
	self::$playerInfo = $this->expPlayers;
	\ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortAsc(self::$playerInfo, "position");
	Dispatcher::dispatch(new Events\PlayerEvent(Events\PlayerEvent::ON_PLAYER_POSITIONS_CALCULATED, self::$playerInfo));
    }

    /** converted from fast.. */
    function positionCompare(Structures\ExpPlayer $a, Structures\ExpPlayer $b) {
// no cp
	if ($a->curCpIndex < 0 && $b->curCpIndex < 0) {
//   "no cp";
	    return strcmp($a->login, $b->login);
	}
// 2nd have del
	if ($a->finalTime > 0 && $b->finalTime <= 0) {
//    echo "2nd have del";
	    return -1;
	}
// 1st have del
	elseif ($a->finalTime <= 0 && $b->finalTime > 0) {
//  echo "1nd have del";
	    return 1;
	}
// only 1st
	if ($b->curCpIndex < 0) {
//echo "1st";
	    return -1;
	}
// only 2nd
	elseif ($a->curCpIndex < 0) {
//  echo "2nd";
	    return 1;
	}
// both ok, so...
	elseif ($a->curCpIndex > $b->curCpIndex) {
//   echo "cp a";
	    return -1;
	} elseif ($a->curCpIndex < $b->curCpIndex) {
//       echo "cp b";
	    return 1;
	}
// same check, so test time
	elseif ($a->time < $b->time) {
//        echo "time";
	    return -1;
	} elseif ($a->time > $b->time) {
//           echo "tiem";
	    return 1;
	}
// same check check and time, so test general rank
	elseif ($a->rank == 0 && $b->rank > 0) {
	    return 1;
	} elseif ($a->rank > 0 && $b->rank == 0) {
	    return -1;
	} elseif ($a->rank < $b->rank) {
	    return -1;
	} elseif ($a->rank > $b->rank) {
	    return 1;
	}

// same check check, time and rank (only in team or beginning?), so test general scores
	elseif ($a->score > 0 && $b->score > 0 && $a->score > $b->score) {
// echo "score";
	    return -1;
	} elseif ($a->score > 0 && $b->score > 0 && $a->score < $b->score) {
// echo "score";
	    return 1;
	}
// same check check, time, rank and general score, so test besttime
	elseif ($a->bestTime > 0 && $b->bestTime > 0 && $a->bestTime < $b->bestTime)
	    return -1;
	elseif ($a->bestTime > 0 && $b->bestTime > 0 && $a->bestTime > $b->bestTime)
	    return 1;
// all same... test time of previous checks
	for ($key = $a->curCpIndex - 1; $key >= 0; $key--) {
	    if ($a->checkpoints[$key] < $b->checkpoints[$key])
		return -1;
	    elseif ($a->checkpoints[$key] > $b->checkpoints[$key])
		return 1;
	}
// really all same, use login  :p
//    echo "use login";
	return strcmp($a->login, $b->login);
    }

}

?>
