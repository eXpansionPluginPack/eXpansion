<?php

namespace ManiaLivePlugins\eXpansion\Core;

use ManiaLive\Event\Dispatcher;
use ManiaLive\PluginHandler\PluginHandler;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\Events\GameSettingsEvent;
use ManiaLivePlugins\eXpansion\Core\Events\ServerSettingsEvent;
use ManiaLivePlugins\eXpansion\Helpers\Helper;
use Maniaplanet\DedicatedServer\Structures\GameInfos;
use Maniaplanet\DedicatedServer\Structures\ServerOptions;

/**
 * Description of Core
 *
 * @author oliverde8
 * @author reaby
 *
 */
class Core extends types\ExpPlugin
{

	const EXP_VERSION = "0.9.8.6";

	const EXP_REQUIRE_MANIALIVE = "4.0.0";

	const EXP_REQUIRE_DEDIATED = "2014.7.24"; // replace dedicated 2013-7-30 to 2013.7.30

	/**
	 * Last used game mode
	 *
	 * @var \Maniaplanet\DedicatedServer\Structures\GameInfos
	 */

	private $lastGameMode;

	private $lastGameSettings;

	private $lastServerSettings;

	/** private variable to hold players infos
	 *
	 * @var Structures\ExpPlayer[]
	 */
	private $expPlayers = array();

	/** @var array() */
	private $teamScores = array();

	/**
	 * public variable to export player infos
	 *
	 * @var Structures\ExpPlayer[]
	 */
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
	private $postLoopStamp = 0;
	private $lastTick = 0;

	public static $action_serverInfo = -1;

	/** @var Config */
	private $config;

	/**
	 *
	 * @var ConfigManager
	 */
	private $configManager;

	private $scriptDispatcher;

	public static $core = null;

	private $quitDialogXml = "";

	/** @var  Analytics */
	private $analytics;

	private static $availableCallbacks = array();

	private static $enabledCallbacks = array();

	public static $optimizeEnabled = false;

	/**
	 * Declares what is necessary for expansion ro run.
	 */
	function exp_onInit()
	{
		//File to log expansion console logs
		$logFile = "manialive-" . $this->storage->serverLogin . ".console.log";
		if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $logFile)) {
			unlink(__DIR__ . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $logFile);
		}

		//File to load expansion errors to
		$logFile = "manialive-" . $this->storage->serverLogin . ".error.log";
		if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $logFile)) {
			unlink(__DIR__ . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $logFile);
		}

		//Listen for changes on server events
		Dispatcher::register(\ManiaLivePlugins\eXpansion\Core\Events\ServerSettingsEvent::getClass(), $this);

		//Creata an action to show server information
		$aHandler = \ManiaLive\Gui\ActionHandler::getInstance();
		self::$action_serverInfo = $aHandler->createAction(array($this, 'showInfo'));

		//Starting the config manager.
		$this->configManager = ConfigManager::getInstance($this);
		//Loading the settings
		$this->configManager->loadSettings();

		if (!defined("DEBUG")) {
		    $config = Config::getInstance();
		    define("DEBUG", filter_var($config->debug, FILTER_VALIDATE_BOOLEAN));
		}

		self::$core = $this;
	}

	/**
	 * Let us let know to the worl that expanson has started
	 */
	function exp_onLoad()
	{
		//Listen to all dedicated events
		$this->enableDedicatedEvents();
		/** @var Config $config */
		$config = Config::getInstance();

		//Start multi lang system for eXpansion. Gogo languages
		i18n::getInstance()->start();

		//Started paralel download utility, thanks to xymph and other devs to have coded it. it rocks
		DataAccess::getInstance()->start();

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

		//Checking dedicated version
		$bExitApp = false;
		$dedicatedVersion = $d->year . "." . $d->month . "." . $d->day;
		if (version_compare($dedicatedVersion, self::EXP_REQUIRE_DEDIATED, "lt")) {
			$this->console('Dedicated Server: ' . $d->year . "-" . $d->month . "-" . $d->day);
			$this->console(
					'Minimum Dedicated version ' . self::EXP_REQUIRE_DEDIATED . ': Fail (' . $dedicatedVersion . ')'
			);
			$this->connection->chatSendServerMessage("Couldn't start eXpansion - Your dedicated server is too old, required version is: " . self::EXP_REQUIRE_DEDIATED);
			$bExitApp = true;
		}
		else {
			$this->console(
					'Minimum Dedicated version ' . self::EXP_REQUIRE_DEDIATED . ': Pass (' . $dedicatedVersion . ')'
			);
		}


		//Checking php version
		if (version_compare(PHP_VERSION, '5.3.3') >= 0) {
			$this->console('Minimum PHP version 5.3.3: Pass (' . PHP_VERSION . ')');
		}
		else {
			$this->console('Minimum PHP version 5.3.3: Fail (' . PHP_VERSION . ')');
			$bExitApp = true;
		}

		//Without GC we are already out of memory we stop
		if (gc_enabled()) {
			$this->console('Garbage Collector enabled: Pass ');
		}
		else {
			$this->console('Garbage Collector enabled: Fail )');
			$bExitApp = true;
		}

		$this->console('Version ' . \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION . ' build ' . date(
						"Y-m-d h:i:s A", Helper::getBuildDate()
				) . ''
		);

		//List of supported languages found
		$this->console('');
		$this->console(
				'Language support detected for: ' . implode(",", i18n::getInstance()->getSupportedLocales()) . '!'
		);
		$this->console('Enabling default locale: ' . $config->defaultLanguage . '');
		i18n::getInstance()->setDefaultLanguage($config->defaultLanguage);

		//If debug stupid, debug declaration
		$this->console('');
		$this->console('-------------------------------------------------------------------------------');
		$this->console('');
		if (DEBUG) {
			$this->console('                        RUNNING IN DEVELOPMENT MODE  ');
			$this->console('');
			$this->console('-------------------------------------------------------------------------------');
			$this->console('');
		}

		//Errors let us stop the proccess.
		if ($bExitApp) {
			$this->connection->chatSendServerMessage("Failed to init eXpansion, see consolelog for more info!");
			die();
		}

		//Save last game mode
		$this->lastGameMode = \ManiaLive\Data\Storage::getInstance()->gameInfos->gameMode;

		//let know the players they are on a great server running eXpansion
		$this->connection->chatSendServerMessage('$fff$w$oe$3afΧ$fffpansion');
		$this->connection->chatSendServerMessage('$000P L U G I N   P A C K  ');
		$this->connection->chatSendServerMessage(
				'Version ' . \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION . '  $n build ' . date(
						"Y-m-d", Helper::getBuildDate()
				) . ''
		);
		if (DEBUG) {
			$this->connection->chatSendServerMessage('$f00$w DEBUG MODE enabled');
		}

		if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT) {
			$this->connection->triggerModeScriptEvent("LibXmlRpc_UnblockAllCallbacks", "");
			try {
				$this->connection->setModeScriptSettings(array("S_UseScriptCallbacks" => true));
			} catch (Exception $ex) {
				Helper::log("[Core] script mode running, but can't enable 'S_UseScriptCallbacks'... perhaps non-nadeo script running ?");
			}
			$this->connection->triggerModeScriptEvent("LibXmlRpc_UnblockAllCallbacks", "");
			$this->enableScriptEvents("LibXmlRpc_Callbacks");
		}
	}

	/**
	 * On Manialive stopped
	 */
	public function onTerminate()
	{
		$this->connection->chatSendServerMessage("[Notice] eXpansion closed succesfully.");
		$this->connection->sendHideManialinkPage();
	}

	/**
	 * When manialive ready, register chat commands and more
	 */
	public function exp_onReady()
	{
		$this->lastTick = time();
		$this->config = Config::getInstance();

		//register some chat commands
		$this->registerChatCommand("server", "showInfo", 0, true);
		$this->registerChatCommand("serverlogin", "serverlogin", 0, true);

		//Add exterior access to the server information window
		$this->setPublicMethod("showInfo");
		$this->setPublicMethod("onSettingsChanged");

		//Add exterior access to the expansion settings
		$this->setPublicMethod("showExpSettings");
		$this->setPublicMethod("showNetStats");

		$this->updateQuitDialog();
		//trigger a begin map
		$this->onBeginMap(null, null, null);

		//Reset extra data for players
		$this->resetExpPlayers(true);

		//The data is update
		$this->update = true;
		$this->loopTimer = round(microtime(true));

		// disable netStats Widget, comment next line
		$this->enableTickerEvent();

		if ($this->config->enableRanksCalc == true) {
			$this->enableApplicationEvents();
		}
		else {
			$this->enableCalculation = false;
		}
		$this->lastServerSettings = clone $this->storage->server;
		$this->connection->dedicatedEcho("ManiaLive\\eXpansion", (string) getmypid());
		$this->connection->setForcedMusic(false, "");
		$this->connection->setRoundCustomPoints($this->config->roundsPoints);
		$this->connection->resetServerTags();
		$this->connection->setServerTag("nl.controller", "ManiaLive / eXpansion");
		$this->connection->setServerTag("nl.controller.version", \ManiaLive\Application\VERSION . " / " . Core::EXP_VERSION);
		$this->syncAdminStatus();

		// this is a fix for servers with a password, if player chooses to spectate, he can now enter back to play,
		// which is not needed anymore as of 09/2014
		// $this->connection->keepPlayerSlots(true);

		$this->analytics = new Analytics();
		if ($this->config->analytics) {
			$this->analytics->enable();
		}
		else {
			$this->analytics->disable();
		}

		if ($this->storage->gameInfos->gameMode == GameInfos::GAMEMODE_SCRIPT) {
			$this->connection->triggerModeScriptEvent("LibXmlRpc_ListCallbacks", "");
		}		
	}

	

	public function LibXmlRpc_Callbacks()
	{		
		$params = func_get_args();
		self::$availableCallbacks = $params;
		self::optimizeScriptCallbacks();
	}

	public function onSettingsChanged(types\config\Variable $var)
	{
		switch ($var->getName()) {
			case "quitDialogManialink":
				$this->updateQuitDialog();
				break;
			case "useWhiteList":
				if ($var->getRawValue()) {
					$this->doWhitelist();
				}
				break;
			case "analytics" :
				if ($var->getRawValue()) {
					$this->analytics->enable();
				}
				else {
					$this->analytics->disable();
				}
		}
	}

	/**
	 *  optimizes the script callbacks
	 */
	static public function optimizeScriptCallbacks()
	{
		$connection = \ManiaLivePlugins\eXpansion\Helpers\Singletons::getInstance()->getDediConnection();		
		if (sizeof(self::$enabledCallbacks) > 0) {
			$connection->triggerModeScriptEvent("LibXmlRpc_BlockAllCallbacks", "");
			foreach (self::$enabledCallbacks as $callback) {				
				$connection->triggerModeScriptEvent("LibXmlRpc_UnblockCallback", $callback);
			}
		}
	}

	/**
	 * enables callback from block
	 * @param array|string $callback
	 */
	static public function enableScriptCallback($callback)
	{
		if (is_string($callback)) {
			self::$enabledCallbacks[$callback] = $callback;
		}

		if (is_array($callback)) {
			foreach ($callback as $value) {
				self::$enabledCallbacks[$value] = $value;
			}
		}
	}

	public function doWhitelist()
	{
		$guests = $this->connection->getGuestList(-1, 0);
		$guestList = array();
		foreach ($guests as $player) {
			$guestList[] = $player->login;
		}

		foreach ($this->storage->players as $login => $player) {
			if (in_array($login, $guestList) || AdminGroups::getInstance()->isInList($login)) {
				// do nothing
			}
			else {
				$this->connection->kick($login, "This server is whitelisted, you are not in the list.");
			}
		}
		foreach ($this->storage->spectators as $login => $player) {
			if (in_array($login, $guestList) || AdminGroups::getInstance()->isInList($login)) {
				// do nothing
			}
			else {
				$this->connection->kick($login, "This server is whitelisted, you are not in the list.");
			}
		}
	}

	function updateQuitDialog()
	{
		$config = Config::getInstance();
		if (empty($config->quitDialogManialink)) {
			$window = new Gui\Windows\QuitWindow();
			$this->quitDialogXml = $window->getXml();
		}
		else {
			try {
				$this->quitDialogXml = file_get_contents($config->quitDialogManialink);
			} catch (\Exception $e) {
				$this->console("[eXp] error while fetching quitDialog xml: " . $e->getMessage());
				$window = new Gui\Windows\QuitWindow();
				$this->quitDialogXml = $window->getXml();
			}
		}
		$this->connection->customizeQuitDialog($this->quitDialogXml, "", true, 0);
	}

	/**
	 * Allows eXpansion to know if another instance of manialive is running on the same server
	 *
	 * @param $internal
	 * @param $public
	 */
	public function onEcho($internal, $public)
	{
		if (($public == "ManiaLive\\eXpansion") && ($internal != (string) getmypid())) {
			exit("\n\nManiaLive will now exit due new eXpansion process has been initialized.");
		}
	}

	/**
	 * Fixes error message on chat command /serverlogin
	 *
	 * @param type $login
	 */
	public function serverlogin($login)
	{
		
	}

	/**
	 *
	 * @param array $map
	 * @param bool $warmUp
	 * @param bool $matchContinuation
	 */
	function onBeginMap($map, $warmUp, $matchContinuation)
	{
		//Check if reload or save of configurations needed
		$this->configManager->check();

		//Current game mode
		$gameSettings = \ManiaLive\Data\Storage::getInstance()->gameInfos;
		$newGameMode = $gameSettings->gameMode;

		//Check if game mode change
		if ($newGameMode != $this->lastGameMode) {
			//Trigger game mode change event.
			Dispatcher::dispatch(
					new GameSettingsEvent(GameSettingsEvent::ON_GAME_MODE_CHANGE, $this->lastGameMode, $newGameMode)
			);

			$this->lastGameMode = $newGameMode;
			$this->lastGameSettings = clone $gameSettings;

			//Check currently loaded plugins to see if any needs to be unloaded
			$this->checkLoadedPlugins();

			//Check if amons stip plugins any needs to be started.
			$this->checkPluginsOnHold();
		}
		else {
			//Detecting any changes in game Settings
			if ($this->lastGameSettings == null)
				$this->lastGameSettings = clone $gameSettings;
			else {
				$difs = $this->compareObjects($gameSettings, $this->lastGameSettings, array("gameMode", "scriptName"));
				if (!empty($difs)) {
					Dispatcher::dispatch(
							new GameSettingsEvent(GameSettingsEvent::ON_GAME_SETTINGS_CHANGE, $this->lastGameSettings, $gameSettings, $difs)
					);
					$this->lastGameSettings = clone $gameSettings;
				}
			}
		}

		//Detecting any changes in Server Settings
		$serverSettings = \ManiaLive\Data\Storage::getInstance()->server;
		if ($this->lastServerSettings == null)
			$this->lastServerSettings = clone $serverSettings;
		else {
			$difs = $this->compareObjects(
					$serverSettings, $this->lastServerSettings, array('useChangingValidationSeed')
			);
			if (!empty($difs)) {
				Dispatcher::dispatch(
						new ServerSettingsEvent(ServerSettingsEvent::ON_SERVER_SETTINGS_CHANGE, $this->lastServerSettings, $serverSettings, $difs)
				);
				$this->lastServerSettings = clone $serverSettings;
			}
		}
		$this->teamScores = array();

		$this->connection->customizeQuitDialog($this->quitDialogXml, "", true, 0);
	}

	/**
	 * Compares the values  in 2 objects recursively.
	 *
	 * @param       $obj1       First object
	 * @param       $obj2       Object to compare with
	 * @param array $ignoreList Keys to ignore while comparing
	 *
	 * @return array List keys that has a different value. this is an array that has the same structure
	 *               as the objects that were compared
	 */
	protected function compareObjects($obj1, $obj2, $ignoreList = array())
	{
		$difs = array();

		foreach ($obj1 as $varName => $value) {
			if (!in_array($varName, $ignoreList)) {
				if (is_object($value)) {
					if (!isset($obj2->$varName)) {
						$difs[$varName] = true;
					}
					else {
						$newDisf = $this->compareObjects($value, $obj2->$varName, $ignoreList);
						if (!empty($newDisf))
							$difs[$varName] = $newDisf;
					}
				} else if (!isset($obj2->$varName) || $obj2->$varName != $value) {
					$difs[$varName] = true;
				}
			}
		}

		return $difs;
	}

	/**
	 * This event is called when game settings has changed
	 *
	 * @param \Maniaplanet\DedicatedServer\Structures\GameInfos $oldSettings The old Game Infos
	 * @param \Maniaplanet\DedicatedServer\Structures\GameInfos $newSettings The new Game Infos
	 * @param  array $changes                                                Differences between both of them
	 */
	public function onGameSettingsChange(
	\Maniaplanet\DedicatedServer\Structures\GameInfos $oldSettings, \Maniaplanet\DedicatedServer\Structures\GameInfos $newSettings, $changes
	)
	{
		$this->saveMatchSettings();
	}

	/**
	 * Called when server settings are changed
	 *
	 * @param ServerOptions $old  old settings
	 * @param ServerOptions $new  new settings
	 * @param               $diff The differences between the old and the new settings
	 */
	public function onServerSettingsChange(ServerOptions $old, ServerOptions $new, $diff)
	{

		$dediConfig = \ManiaLive\DedicatedApi\Config::getInstance();
		if ($this->expStorage->isRemoteControlled)
			return;

		try {
			$path = Helper::getPaths()->getDefaultMapPath() . "../Config/" . $this->config->dedicatedConfigFile;
			if (!file_exists($path))
				return;
			/** @var SimpleXMLElement */
			$oldXml = simplexml_load_file($path);

			$adapter = array("name" => "name",
				"password" => "password",
				"comment" => "comment",
				"passwordForSpectator" => "password_spectator",
				"hideServer" => "hide_server",
				"nextMaxPlayers" => "max_players",
				"nextMaxSpectatos" => "max_spectators",
				"isP2PUpload" => "enable_p2p_upload",
				"isP2PDownload" => "enable_p2p_download",
				"nextLadderMode" => "ladder_mode",
				//"ladderServerLimitMax" => "ladder_serverlimit_max",
				//"ladderServerLimitMin" => "ladder_serverlimit_min",
				"nextCallVoteTimeOut" => "callvote_timeout",
				"callVoteRatio" => "callvote_ratio",
				"allowMapDownload" => "allow_map_download",
				"autoSaveReplays" => "autosave_replays",
				"autoSaveValidationReplays" => "autosave_validation_replays",
				"refereePassword" => "referee_password",
				"refereeMode" => "referee_validation_mode",
				"disableHorns" => "disable_horns",
				"clientInputsMaxLatency" => "clientinputs_maxlatency",
				"keepPlayerSlots" => "keep_player_slots"
			);

			foreach ($new as $key => $value) {

				$search = $key;
				if (array_key_exists($key, $adapter)) {
					$search = $adapter[$key];
				}
				else
					continue;

				//			echo $key . " -> " . $search . "\n";

				$out = $new->{$key};
				if (is_bool($value)) {
					$out = "False";
					if ($value)
						$out = "True";
				}


				$oldXml->server_options->{$search}[0] = $out;
			}

			Helper::log('[Core]Saving server settings to : ' . $path);
			$xml = $oldXml->asXML();
			file_put_contents($path, $xml);
		} catch (\Exception $ex) {
			//  print_r($ex);
			$this->console("[Core]Error writing ServerSettings : " . $path . " - " . $ex->getMessage());
		}
	}

	/**
	 * When the map list is modified
	 *
	 * @param int $curMapIndex     index of the current map
	 * @param int $nextMapIndex    Index of the next map
	 * @param bool $isListModified was the list modifed
	 */
	public function onMapListModified($curMapIndex, $nextMapIndex, $isListModified)
	{
		if ($isListModified) {
			$this->saveMatchSettings();
		}
	}

	/**
	 * Saves the current match settings
	 */
	public function saveMatchSettings()
	{
		//If the admin has declared a file name to save settings in
		if (!empty($this->config->defaultMatchSettingsFile)) {
			try {
				$this->connection->saveMatchSettings(
						(empty($this->config->mapBase) ? "" : $this->config->mapBase . '/') . "MatchSettings" . DIRECTORY_SEPARATOR . $this->config->defaultMatchSettingsFile
				);
			} catch (\Exception $ex) {
				$this->console(
						"[Core]Error writing MatchSettings : " . $this->config->defaultMatchSettingsFile . " - " . $ex->getMessage()
				);
			}
		}
	}

	/**
	 * We have already done what needs to be done on game mode change
	 * Just show a notification
	 *
	 * @param $oldGameMode the old game mode
	 * @param $newGameMode the new game mode
	 */
	public function onGameModeChange($oldGameMode, $newGameMode)
	{
		$this->showNotice("GameMode Changed");
	}

	/**
	 * Show a notification message in the console
	 *
	 * @param $message the message to show
	 */
	private function showNotice($message)
	{
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

	/**
	 * Check the loaded plugins to see if any needs to be shut down due to compatibility issues
	 */
	private function checkLoadedPlugins()
	{
		$pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();
		$this->console('Shutting down uncompatible plugins');

		/**
		 * @var PluginHandler $pHandler
		 */
		$pHandler = PluginHandler::getInstance();

		foreach ($pHandler->getLoadedPluginsList() as $pluginId) {
			$exploded = explode('\\', $pluginId);
			array_pop($exploded);
			$metaData = implode('\\', $exploded) . '\\MetaData';
			if (class_exists($metaData)) {
				if (!$pluginId::getMetaData()->checkAll()) {
					try {
						$this->callPublicMethod($pluginId, 'exp_unload');
					} catch (\Exception $ex) {
						
					}
				}
			}
		}
	}

	/**
	 * Check the list of plugins that are on hold if any needs to be started
	 */
	private function checkPluginsOnHold()
	{
		$this->console('Starting compatible plugins');

		$pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();
		foreach (\ManiaLivePlugins\eXpansion\AutoLoad\Config::getInstance()->plugins as $plugin_id) {
			//$parts = explode("\\", $plugin_id);
			//$className = '\\ManiaLivePlugins\\' . $plugin_id . '\\' . $parts[1];
			$className = $plugin_id;
			if (class_exists($className)) {
				if ($className::getMetaData()->checkAll() && !$this->isPluginLoaded($plugin_id)) {
					try {
						$pHandler->load($plugin_id);
					} catch (Exception $ex) {
						$this->console('Plugin : ' . $plugin_id . ' Maybe already loaded');
					}
				}
			}
		}
	}

	/**
	 * Show server information
	 *
	 * @param $login the login to show the information to
	 */
	public function showInfo($login)
	{
		//If server statistics are loaded put an action so that we can have a button to show them.
		if ($this->isPluginLoaded('\ManiaLivePlugins\eXpansion\ServerStatistics\ServerStatistics')) {
			Gui\Windows\InfoWindow::$statsAction = \ManiaLivePlugins\eXpansion\ServerStatistics\ServerStatistics::$serverStatAction;
		}
		else {
			Gui\Windows\InfoWindow::$statsAction = -1;
		}
		$info = Gui\Windows\InfoWindow::Create($login);
		$info->setTitle("Server info");
		$info->centerOnScreen();
		$info->setSize(100, 68);
		$info->show();
	}

	public function onTick()
	{
		if ($this->storage->serverStatus->code < 3 || $this->storage->serverStatus->code > 4)
			return;

		//every 5 seconds gogo
		if (time() - $this->lastTick > 5) {
			$outPlayers = array();

			$this->lastTick = time();
			//Get network statistics from the server
			$stats = $this->connection->getNetworkStats();

			foreach ($stats->playerNetInfos as $player) {
				$netstat = new Structures\NetStat($player);
				self::$netStat[$player->login] = $netstat;

				if ($player->latestNetworkActivity >= $this->config->netLostTime) {
					$outPlayers[$player->login] = $player;
				}
			}
			if (sizeof($outPlayers < 0)) {
				Dispatcher::dispatch(new Events\PlayerEvent(Events\PlayerEvent::ON_PLAYER_NETLOST, $outPlayers));
			}
		}
	}

	/**
	 * Show netStats
	 *
	 * @param $login
	 */
	public function showNetStats($login)
	{
		if (AdminGroups::hasPermission($login, Permission::chat_adminChannel)) {
			Gui\Windows\NetStat::Erase($login);
			$win = Gui\Windows\NetStat::Create($login);
			$win->setTitle("Network Status");
			$win->setSize(140, 100);
			$win->show();
		}
	}

	/**
	 * Show main expansion settings window
	 *
	 * @param string $login    the login of the player to show the settings to
	 * @param string $pluginId
	 * @param string $confName The config window to show. either main or plugin id
	 */
	public function showExpSettings($login, $pluginId, $confName = 'main')
	{
		if (AdminGroups::hasPermission($login, Permission::expansion_pluginSettings)) {
			Gui\Windows\ExpSettings::Erase($login);
			/** @var Gui\Windows\ExpSettings $win */
			$win = Gui\Windows\ExpSettings::Create($login);
			$win->setTitle("Expansion Settings");
			$win->centerOnScreen();
			$win->setSize(170, 100);
			$win->populate($this->configManager, 'General', $confName);
			$win->show();
		}
	}

	public function onPostLoop()
	{
		// check for update conditions
		if ($this->enableCalculation == false || $this->expStorage->isRelay)
			return;
		if ($this->storage->serverStatus->code == 4 && $this->update && (microtime(true) - $this->loopTimer) > 0.35) {
			$this->update = false;
			$this->loopTimer = microtime(true);
			$this->calculatePositions();
		}
		$this->postLoopStamp = microtime(true);
	}

	/**
	 * Shows current memory usage in the console
	 *
	 * @return string
	 */
	function echo_memory_usage()
	{
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

	public function onPlayerConnect($login, $isSpectator)
	{
		$this->syncAdminStatus();
		if ($this->config->useWhitelist) {
			if (in_array($login, $guestList) || AdminGroups::getInstance()->isInList($login)) {
				// do nothing
			}
			else {
				$this->connection->kick($login, "This server is whitelisted, you are not in the list.");
			}
		}
	}

	public function syncAdminStatus($loginDisconnecting = false)
	{
		$admingroup = AdminGroups::getInstance();
		$hasAdmin = "false";
		$playerArray = array();

		foreach ($this->storage->players as $login => $player) {
			$playerArray[$login] = "present";
		}
		foreach ($this->storage->spectators as $login => $player) {
			$playerArray[$login] = "present";
		}

		if ($loginDisconnecting !== false && array_key_exists($loginDisconnecting, $playerArray))
			unset($playerArray[$loginDisconnecting]);

		foreach ($playerArray as $login => $player) {
			if (AdminGroups::hasPermission($login, Permission::player_kick)) {
				$hasAdmin = "true";
			}
		}

		$this->connection->setServerTag("server.isAdminPresent", $hasAdmin);
	}

	/**
	 * When player disconnects
	 *
	 * @param string $login              The login of the player that disconnected
	 * @param mixed $disconnectionReason The reason the player disconnected
	 */
	public function onPlayerDisconnect($login, $disconnectionReason = null)
	{
		//Player disconnects

		$this->update = true;
		if (array_key_exists($login, self::$netStat)) {
			unset(self::$netStat[$login]);
		}
		if (array_key_exists($login, $this->expPlayers)) {
			$this->expPlayers[$login]->hasRetired = true;
			$this->expPlayers[$login]->isPlaying = false;
			unset($this->expPlayers[$login]);
		}
		$this->syncAdminStatus($login);
	}

	/**
	 * When a player passes throught a checkpoint
	 *
	 * @param $playerUid
	 * @param $login
	 * @param $timeOrScore
	 * @param $curLap
	 * @param $checkpointIndex
	 */
	public function onPlayerCheckpoint($playerUid, $login, $timeOrScore, $curLap, $checkpointIndex)
	{
		if ($this->enableCalculation == false || $this->expStorage->isRelay)
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

	function onBeginMatch()
	{
		
	}

	public function onBeginRound()
	{
		$this->update = true;
		$this->resetExpPlayers();
	}

	public function onEndRound()
	{
		$this->update = true;
	}

	/**
	 * When the information of a player changes
	 *
	 * @param $playerInfo new player information
	 */
	public function onPlayerInfoChanged($playerInfo)
	{
		if ($this->enableCalculation == false || $this->expStorage->isRelay)
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
		}
		elseif ($player->spectator == true) {
			$this->expPlayers[$player->login]->isPlaying = false;
			$this->expPlayers[$player->login]->hasRetired = true;
		}
		else {
			// player is not any spectator
			$this->expPlayers[$player->login]->isPlaying = true;
			$this->expPlayers[$player->login]->hasRetired = true;
		}
	}

	/**
	 * Reset current players extended information
	 *
	 * @param bool $readRankings should rankings be read after reset
	 *
	 * @todo ^^check what this param is used for and remove if not used
	 */
	public function resetExpPlayers($readRankings = false)
	{
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

	public function onPlayerFinish($playerUid, $login, $timeOrScore)
	{
		if ($this->enableCalculation == false || $this->expStorage->isRelay)
			return;

		// handle onPlayerfinish @ start from server.
		$this->update = true;
		if ($playerUid == 0)
			return;

		if ($timeOrScore == 0) {
			if (array_key_exists($login, $this->expPlayers)) {
				$this->expPlayers[$login]->finalTime = 0;
				if ($this->storage->gameInfos->gameMode !== \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK) {
					$this->expPlayers[$login]->hasRetired = true;
					Dispatcher::dispatch(
							new Events\PlayerEvent(Events\PlayerEvent::ON_PLAYER_GIVEUP, $this->expPlayers[$login])
					);
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
						}
						else {
							$this->teamScores[$player->login] += $points;
						}
						$this->expPlayers[$player->login]->matchScore = $this->teamScores[$player->login];
					}
				}
				self::$playerInfo = $this->expPlayers;
			}
		}
	}

	function calculatePositions()
	{
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
			}
			else {
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
				Dispatcher::dispatch(
						new Events\PlayerEvent(Events\PlayerEvent::ON_PLAYER_POSITION_CHANGE, $this->expPlayers[$login], $oldPos, $pos)
				);
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
		Dispatcher::dispatch(
				new Events\PlayerEvent(Events\PlayerEvent::ON_PLAYER_POSITIONS_CALCULATED, self::$playerInfo)
		);
	}

	/** converted from fast.. */
	function positionCompare(Structures\ExpPlayer $a, Structures\ExpPlayer $b)
	{
		// no cp
		if ($a->curCpIndex < 0 && $b->curCpIndex < 0) {
			//   "no cp";
			return strcmp($a->login, $b->login);
		}
		// 2nd have del
		if ($a->finalTime > 0 && $b->finalTime <= 0) {
			return -1;
		} // 1st have del
		elseif ($a->finalTime <= 0 && $b->finalTime > 0) {
			return 1;
		}
		// only 1st
		if ($b->curCpIndex < 0) {
			return -1;
		} // only 2nd
		elseif ($a->curCpIndex < 0) {
			return 1;
		} // both ok, so...
		elseif ($a->curCpIndex > $b->curCpIndex) {
			return -1;
		}
		elseif ($a->curCpIndex < $b->curCpIndex) {
			return 1;
		} // same check, so test time
		elseif ($a->time < $b->time) {
			return -1;
		}
		elseif ($a->time > $b->time) {
			return 1;
		} // same check check and time, so test general rank
		elseif ($a->rank == 0 && $b->rank > 0) {
			return 1;
		}
		elseif ($a->rank > 0 && $b->rank == 0) {
			return -1;
		}
		elseif ($a->rank < $b->rank) {
			return -1;
		}
		elseif ($a->rank > $b->rank) {
			return 1;
		} // same check check, time and rank (only in team or beginning?), so test general scores
		elseif ($a->score > 0 && $b->score > 0 && $a->score > $b->score) {
			return -1;
		}
		elseif ($a->score > 0 && $b->score > 0 && $a->score < $b->score) {
			return 1;
		} // same check check, time, rank and general score, so test besttime
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
