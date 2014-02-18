<?php

namespace ManiaLivePlugins\eXpansion\Core\types {
    

use Maniaplanet\DedicatedServer\Structures\GameInfos;
use ManiaLive\Utilities\Console;
use ManiaLivePlugins\eXpansion\Core\Config;
use \ManiaLivePlugins\eXpansion\Core\i18n\Message as MultiLangMsg;
use ManiaLivePlugins\eXpansion\Core\Events\GameSettingsEvent;
use \ManiaLivePlugins\eXpansion\Core\Events\PlayerEvent;
use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Core\Structures\ExpPlayer;
use ManiaLivePlugins\eXpansion\Database\Database;

    /**
     * Description of BasicPlugin
     *
     * @author oliverde8
     */
    class BasicPlugin extends \ManiaLive\PluginHandler\Plugin implements \ManiaLive\PluginHandler\WaitingCompliant, \ManiaLivePlugins\eXpansion\Core\Events\GameSettingsEventListener, \ManiaLivePlugins\eXpansion\Core\Events\PlayerEventListener, \ManiaLivePlugins\eXpansion\Core\Events\GlobalEventListener {

        /**
         * The list of Plugin id's that may need to be started
         * @var string[] 
         */
        public static $plugins_onHold = array();

        /**
         * The List of GameModes the plugins support
         */
        private static $plugin_gameModeSupport = array();

        /**
         * The list of Plugins that has chat redirect activated
         */
        private static $exp_chatRedirected = array();

        /**
         *
         * @var ManiaLivePlugins\eXpansion\Core\BillManager 
         */
        private static $exp_billManager = null;

        /**
         * THe list of plugins that have their announcement redirected
         */
        private static $exp_announceRedirected = array();
        private $exp_unloading = false;

        /**
         * The path to the directory of this plugin
         * @var String 
         */
        private $exp_dir = null;

        /**
         * The colorparser
         * @var \ManiaLivePlugins\eXpansion\Core\ColorParser 
         */
        protected $colorParser;

        /* @var \ManiaLivePlugins\eXpansion\Core\RelayLink Relay connector */
        protected $relay;
	
        private $_isReady = false;

        public final function onInit() {
	    $this->checkVersion();
	    
            $this->setVersion(\ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION);
            ErrorHandler::$server = $this->storage->serverLogin;
            try {
                $this->enableDatabase();
            } catch (\Exception $e) {
                $this->dumpException('There seems be a problem while establishing a MySQL connection.', $e);
                exit(1);
            }

            //Recovering the eXpansion pack tools
            $this->colorParser = \ManiaLivePlugins\eXpansion\Core\ColorParser::getInstance();

            $this->exp_unloading = false;
            $this->relay = \ManiaLivePlugins\eXpansion\Core\RelayLink::getInstance();
            \ManiaLivePlugins\eXpansion\Core\i18n::getInstance()->registerDirectory($this->exp_getdir());

            //All plugins need the eXpansion Core to work properly
            if ($this->getId() != '\ManiaLivePlugins\eXpansion\Core' && $this->getId() != '\ManiaLivePlugins\eXpansion\AutoLoad\AutoLoad')
                $this->addDependency(new \ManiaLive\PluginHandler\Dependency('\ManiaLivePlugins\eXpansion\Core\Core'));

            $this->setPublicMethod('exp_unload');
            $this->setPublicMethod('getDependencies');
            $this->setPublicMethod('exp_chatSendServerMessage');
            $this->setPublicMethod('exp_activateChatRedirect');
            $this->setPublicMethod('exp_deactivateChatRedirect');
            $this->setPublicMethod('exp_activateAnnounceRedirect');
            $this->setPublicMethod('exp_deactivateAnnounceRedirect');

            $this->exp_onInit();

            Dispatcher::register(GameSettingsEvent::getClass(), $this);
            Dispatcher::register(PlayerEvent::getClass(), $this);
            Dispatcher::register(\ManiaLivePlugins\eXpansion\Core\Events\GlobalEvent::getClass(), $this);
        }

        /**
         * eXpansion method invoked Manialive onInit
         * @abstract
         */
        public function exp_onInit() {
            
        }

        public final function onLoad() {
            try {
                $this->exp_onLoad();
            } catch (\Exception $e) {
                echo "onLoad exception:" . $this->getId() . " -> " . $e->getMessage() . "\n";
            }
        }

        /**
         * eXpansion method invoked at Manialive onload        
         * @abstract
         */
        public function exp_onLoad() {
            
        }

        public final function onReady() {

//Recovering the billManager if need.
            if (self::$exp_billManager == null) {
                self::$exp_billManager = new \ManiaLivePlugins\eXpansion\Core\BillManager($this->connection, $this->db, $this);
            }

            if (!self::exp_checkGameCompability()) {
                $this->exp_unload();
                return;
            } else {
                if (!$this->_isReady) {
                    $this->_isReady = true;
                    try {
			
                        $this->exp_onReady();
                    } catch (\Exception $e) {
                        throw new \Exception("onReadyError at" .  get_class() .":\n" . $e->getFile() . ":" . $e->getLine() . "\n" . $e->getMessage(), 0, $e);
                    }
                }
            }
        }

        /**
         * eXpansion onReady handler
         * @abstract
         */
        public function exp_onReady() {
            
        }
	private function checkVersion() {
	    if (version_compare(\ManiaLive\Application\VERSION, \ManiaLivePlugins\eXpansion\Core\Core::EXP_REQUIRE_MANIALIVE, 'lt')) {
		$this->dumpException("Looks like your ManiaLive is too old to run this version of eXpansion.\n"
			. "Your ManiaLive version: ".\ManiaLive\Application\VERSION.", (required ".\ManiaLivePlugins\eXpansion\Core\Core::EXP_REQUIRE_MANIALIVE.")\n"
			. "Please update your manialive version in order to continue.", New \ManiaLive\PluginHandler\Exception("ManiaLive version is too old!"));
		exit();
	    }
	}
        private function exp_getdir() {
            if ($this->exp_dir == null) {
                $exploded = explode("\\", get_class($this));
		$trim = false;
		$this->exp_dir = "libraries/";
		if (is_dir("vendor/ml-expansion")) 
		{
		$trim = true;
		$this->exp_dir = "vendor/ml-expansion/";
		}
                $i = 0;
                while ($i < sizeof($exploded) - 2) {
                    $this->exp_dir .= $exploded[$i] . "/";
                    $i++;
                }
                $this->exp_dir .= $exploded[$i];
            }
	    if ($trim) {
	    $this->exp_dir = str_replace("\\ManiaLivePlugins/", "", $this->exp_dir);
	    $this->exp_dir = str_replace("eXpansion", "expansion", $this->exp_dir);
	    }
	    echo "directory:" . $this->exp_dir . "\n";
            return $this->exp_dir;
        }

        /**
         * 
         * to send everybody:
         * exp_chatSendServerMessage("Message with parameters %1$s %2$s", null, array("parameter1","parameter2));
         * 
         * to send login:
         * exp_chatSendServerMessage("Message with parameters %1$s %2$s", $login, array("parameter1","parameter2));
         * 
         * @param string|MultiLangMsg $msg String or MultiLangMsg to sent
         * @param null|string $login null for everybody, string for individual
         * @param array $args simple array of parameters
         */
        public function exp_chatSendServerMessage($msg, $login = null, $args = array()) {
            if (!($msg instanceof MultiLangMsg)) {
                if (DEBUG) {
                    $this->console("#Plugin " . $this->getId() . " uses chatSendServerMessage in an unoptimized way!!");
                }
                $msg = exp_getMessage($msg);
            }

            if ($login == null) {
                /* array_unshift($args, $msg->getMessage());
                  $msg = call_user_func_array('sprintf', $args);
                  $this->exp_announce($msg);
                 */
                $this->exp_multilangAnnounce($msg, $args);
            } else {
                array_unshift($args, $msg, $login);
                $msgString = call_user_func_array('__', $args);

//Check if it needs to be redirected
                $this->exp_redirectedChatSendServerMessage($msgString, $login, get_class($this));
            }
        }

        /**
         * Sends a chat message to the server or redirect to another plugin
         * 
         * @param type $msg The message
         * @param type $login The login to whom it needs to be sent
         */
        private function exp_redirectedChatSendServerMessage($msg, $login) {
            $sender = get_class($this);
            $fromPlugin = explode("\\", $sender);
            $fromPlugin = str_replace("_", " ", end($fromPlugin));

            if (isset(self::$exp_chatRedirected[$sender])) {
                $message = $msg;
                if (is_object(self::$exp_chatRedirected[$sender][0]))
                    call_user_func_array(self::$exp_chatRedirected[$sender], array($login, $this->colorParser->parseColors($message)));
                else {
                    $this->callPublicMethod(self::$exp_chatRedirected[$sender][0], self::$exp_chatRedirected[$sender][1], array($login, $this->colorParser->parseColors($message)));
                }
            } else {

                try {
                    $this->connection->chatSendServerMessage($this->colorParser->parseColors($msg), $login);
                } catch (\Exception $e) {
                    $this->console("Error while sending chat message to '" . $login . "'\n Server said:" . $e->getMessage());
                }
            }
        }

        /**
         * Sends annoucement throught chat to the server or redirects it to another plugin
         * 
         * @param type $message
         * @param type $icon
         * @param type $callback
         * @param type $pluginid
         */
        protected function exp_announce($msg, $icon = null, $callback = null, $pluginid = null) {
            $sender = get_class($this);
            $fromPlugin = explode("\\", $sender);
            $fromPlugin = str_replace("_", " ", end($fromPlugin));

            if (isset(self::$exp_announceRedirected[$sender])) {
                $message = clone $msg;
                if (is_object(self::$exp_announceRedirected[$sender][0]))
                    call_user_func_array(self::$exp_announceRedirected[$sender], array($this->colorParser->parseColors($message), $icon, $callback, $pluginid));
                else {
                    $this->callPublicMethod(self::$exp_chatRedirected[$sender][0], self::$exp_chatRedirected[$sender][1], array($this->colorParser->parseColors($message), $icon, $callback, $pluginid));
                }
            } else {
                $this->connection->chatSendServerMessage('$n' . $fromPlugin . '$z$s$ff0 〉$fff' . $this->colorParser->parseColors($msg));
            }
        }

        protected function exp_multilangAnnounce(MultiLangMsg $msg, array $args) {
            $sender = get_class($this);
            $fromPlugin = explode("\\", $sender);
            $fromPlugin = str_replace("_", " ", end($fromPlugin));


            if (isset(self::$exp_chatRedirected[$sender])) {
                $message = clone $msg;
                $message->setArgs($args);
                if (is_object(self::$exp_chatRedirected[$sender][0]))
                    call_user_func_array(self::$exp_chatRedirected[$sender], array(null, $message));
                else {
                    $this->callPublicMethod(self::$exp_chatRedirected[$sender][0], self::$exp_chatRedirected[$sender][1], array(null, $message));
                }
            } else {
                $this->connection->chatSendServerMessageToLanguage($msg->getMultiLangArray($args));
            }
        }

	
	
	
        /**
         * Will force the plugin to be checked if it is compatible with the Game Mode
         * If it isn't the plugin will be unloaded From ManiaLive
         * If you change GameModes the plugin may be loaded again.
         * 
         * @param type $gameMode
         * @param type $scriptName
         */
        protected function exp_addGameModeCompability($gameMode, $scriptName = null) {

            if ($scriptName == null || $gameMode != GameInfos::GAMEMODE_SCRIPT)
                self::$plugin_gameModeSupport[get_called_class()][$gameMode] = true;
            else
                self::$plugin_gameModeSupport[get_called_class()][$gameMode][$scriptName] = true;
        }

        protected function exp_getGameModeCompability() {
            return self::$plugin_gameModeSupport;
        }

        /**
         * Check for Game compability
         * @static
         * @return boolean
         */
        public static function exp_checkGameCompability() {
            $gameInfo = \ManiaLive\Data\Storage::getInstance()->gameInfos;
            $class = get_called_class();

            if (isset(self::$plugin_gameModeSupport[$class])) {
                if ($gameInfo->gameMode == GameInfos::GAMEMODE_SCRIPT && isset(self::$plugin_gameModeSupport[$class][$gameInfo->gameMode]) && is_array(self::$plugin_gameModeSupport[$class][$gameInfo->gameMode])) {
                    return isset(self::$plugin_gameModeSupport[$class][$gameInfo->gameMode][$gameInfo->scriptName]) ? self::$plugin_gameModeSupport[$class][$gameInfo->gameMode][$gameInfo->scriptName] : false;
                } else {
                    return isset(self::$plugin_gameModeSupport[$class][$gameInfo->gameMode]) ? self::$plugin_gameModeSupport[$class][$gameInfo->gameMode] : false;
                }
            } else
//This plugin supports all GameModes
                return true;
        }

        /**
         * Unloads the plugin.
         * @abstract
         */
        public function exp_unload() {
            $this->console('Unloading ' . $this->getId());
            $pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();

            $plugins = $pHandler->getLoadedPluginsList();

            foreach ($plugins as $plugin) {
                try {
                    if ($plugin != $this->getId()) {
                        $deps = null;
                        if (method_exists($plugin, 'getDependencies')) {
                            $deps = $this->callPublicMethod($plugin, 'getDependencies');
                        }
                        if (!empty($deps)) {
                            foreach ($deps as $dep) {
                                if ($dep->getPluginId() == $this->getId()) {                                    
                                    $this->callPublicMethod($plugin, 'exp_unload');
                                    break;
                                }
                            }
                        }
                    }
                } catch (\Exception $ex) {
                    echo "Error:" . $ex->getFile() . ":" . $ex->getLine() . "\n" . $ex->getMessage();
                }
            }

//Unloading dependencies to prevent crash
            $deps = $this->getDependencies();

            if (!empty($deps)) {
                $this->console('[eXpansion] Unloading Dependencies of ' . $this->getId() . '');
                foreach ($deps as $dep) {
                    if ($dep->getPluginId() != "\\ManiaLivePlugins\\eXpansion\\Core\\Core")
                        $this->callPublicMethod($dep->getPluginId(), 'exp_unload');
                }
            }


//Unloading it self
            $this->exp_unloading = true;
            $pHandler->unload($this->getId());
            self::$plugins_onHold[$this->getId()] = $this->getId();
        }

        public function onUnload() {
            Dispatcher::unregister(GameSettingsEvent::getClass(), $this);
            Dispatcher::unregister(PlayerEvent::getClass(), $this);
            parent::onUnload();
        }

        /**
         * Activates the message redirect for this plugin. 
         * @param type $array The Object or plugin id and the function to call
         */
        public function exp_activateChatRedirect($array) {
            self::$exp_chatRedirected[get_class($this)] = $array;
        }

        /**
         * Deactivate chat redirect to send it back throught the chat
         */
        public function exp_deactivateChatRedirect() {
            unset(self::$exp_chatRedirected[get_class($this)]);
        }

        /**
         * Activates the announcement redirect ot send it to a plugin
         * @param type $array The Object or plugin id and the function to call
         */
        public function exp_activateAnnounceRedirect($array) {
            self::$exp_announceRedirected[get_class($this)] = $array;
        }

        /**
         * Deactivate chat redirect to send it back throught the chat
         */
        public function exp_deactivateAnnounceRedirect() {
            unset(self::$exp_announceRedirected[get_class($this)]);
        }

        /**
         * Will start a billing process.
         * 
         * @param type $source_login The login to whom the planets will be taken from
         * @param type $destination_login The login to whom the planets will be send
         * @param int $amount The amoint of planets that wil be sent
         * @param type $msg The label of the bill
         * @param type $callback The callback in case of sucess
         * @param type $params The parameters to pass whith the calback
         * @return \ManiaLivePlugins\eXpansion\Core\types\Bill
         */
        public function exp_startBill($source_login, $destination_login, $amount, $msg, $callback = array(), $params = array()) {
            $bill = new Bill($source_login, $destination_login, $amount, $msg);
            self::$exp_billManager->sendBill($bill);
            $bill->setValidationCallback($callback, $params);

            $bill->setPluginName($this->exp_getOldId());
            $bill->setSubject($msg);

            return $bill;
        }
		
		final public function exp_getOldId($id = null){
			if($id == null){
				$id = $this->getId();
			}
			$e = explode("\\", $id);
			return $e[1]."\\".$e[2];
		}

        final public function debug($message) {
            $config = \ManiaLivePlugins\eXpansion\Core\Config::getInstance();
            if (!$config->debug)
                return;

            if (is_string($message)) {
                Console::println($message);
                \ManiaLive\Utilities\Logger::log($message, true, "exp-debug.txt");
            }
            if (is_array($message)) {
                $info = print_r($message, true);
                Console::println($info);
                \ManiaLive\Utilities\Logger::log($info, true, "exp-debug.txt");
            }
            if (is_object($message)) {
                $info = var_export($message, true);
                Console::println($info);
                \ManiaLive\Utilities\Logger::log($message, true, "exp-debug.txt");
            }
        }

        final public function dumpException($message, \Exception $e) {
            $this->console('                                ____                  _  ');
            $this->console('                               / __ \                | |');
            $this->console('                              | |  | | ___  _ __  ___| |');
            $this->console('                              | |  | |/ _ \|  _ \/ __| |');
            $this->console('                              | |__| | (_) | |_) \__ \_|');
            $this->console('                               \____/ \___/| .__/|___(_)');
            $this->console('                                           | | ');
            $this->console('                                           |_| ');
            $this->console('');

            $fill = "";
            $firstline = explode("\n", $message, 2);
            if (!is_array($firstline))
                $firstline = array($firstline);
            for ($x = 0; $x < ((80 - strlen($firstline[0])) / 2); $x++) {
                $fill .= " ";
            }
            $this->console($fill . $message);
            $this->console('');
            $file = explode(DIRECTORY_SEPARATOR, $e->getFile());
            $this->console('Advanced details');
            $this->console('File: ' . end($file));
            $this->console('Line: ' . $e->getLine());
            $this->console('Message: ' . $e->getMessage());
            $this->console('');
        }

        /**
         * Returns player object from given playerId
         * @param integer $id
         * @return \Maniaplanet\DedicatedServer\Structures\Player
         */
        public function getPlayerObjectById($id) {
            if (!is_numeric($id))
                throw new Exception("player id is not numeric");
            foreach ($this->storage->players as $login => $player) {
                if ($player->playerId == $id)
                    return $player;
            }
            foreach ($this->storage->spectators as $login => $player) {
                if ($player->playerId == $id)
                    return $player;
            }
            return new \Maniaplanet\DedicatedServer\Structures\Player();
        }

        final public function console($message) {
            $logFile = $this->storage->serverLogin . ".console.log";
            /** @var \ManiaLive\Utilities\Logger */
            $logger = \ManiaLive\Utilities\Logger::getLog("eXpansion");

            if (is_string($message)) {
                Console::println($message);
                $logger::log($message, true, $logFile);
            }
            if (is_array($message)) {
                $info = print_r($message, true);
                Console::println($info);
                $logger::log($info, true, $logFile);
            }
            if (is_object($message)) {
                $info = var_export($message, true);
                Console::println($info);
                $logger::log($info, true, $logFile);
            }
        }

        public function onGameModeChange($oldGameMode, $newGameMode) {
            
        }

        public function onGameSettingsChange(GameInfos $oldSettings, GameInfos $newSettings, $changes) {
            
        }

        /**
         * @param ExpPlayer $player player object of the player given up
         */
        public function onPlayerGiveup(\ManiaLivePlugins\eXpansion\Core\Structures\ExpPlayer $player) {
            
        }

        /**
         * 
         * @param ExpPlayer $player player object of the player
         * @param int $oldPos old position
         * @param int $newPos new position
         */
        public function onPlayerPositionChange(\ManiaLivePlugins\eXpansion\Core\Structures\ExpPlayer $player, $oldPos, $newPos) {
            
        }

        /**
         * @param ExpPlayer[] $playerPositions array(string => ExpPlayer);
         */
        public function onPlayerNewPositions($playerPositions) {
            
        }

        public function onMapRestart() {
            
        }

        public function onMapSkip() {
            
        }

    }

}

namespace {
// fix for  php 5.5.0
    error_reporting(E_ALL ^ E_DEPRECATED);
// do custom logging also

    set_error_handler('\\ManiaLivePlugins\\eXpansion\\Core\\types\\ErrorHandler::createExceptionFromError');

    if (!defined("DEBUG")) {
        $config = ManiaLivePlugins\eXpansion\Core\Config::getInstance();
        define("DEBUG", filter_var($config->debug, FILTER_VALIDATE_BOOLEAN));
    }


    if (!function_exists('__')) {

        /**
         * $player = \ManiaLive\Data\Storage::getInstance()->getPlayerObject($login);
          if($player == null){
          array_unshift($args, $msg);
          $msgString = call_user_func_array('__', $args);
          }else{
          array_unshift($args, $msg, $login);
          $msgString = call_user_func_array('__', $args);
          }
         * 
         */
        function __() {
            $args = func_get_args();
            $message = array_shift($args);
            $language = null;
            if (sizeof($args) > 0) {
                $login = array_shift($args);
                $player = \ManiaLive\Data\Storage::getInstance()->getPlayerObject($login);
                if ($player == null) {
                    $language = null;
                } else {
                    $language = $player->language;
                }
            } else {
                $language = null;
            }

            if (is_object($message)) {
                $lang = $message->getMessage($language);
            } else {
                $lang = \ManiaLivePlugins\eXpansion\Core\i18n::getInstance()->getString($message, $language);
            }

            array_unshift($args, $lang);
            return call_user_func_array('sprintf', $args);
        }

        /**
         * exp_getMessage(string $string)
         * 
         * @param string $string
         * @return \ManiaLivePlugins\eXpansion\Core\i18n\Message
         */
        function exp_getMessage($string) {
            return \ManiaLivePlugins\eXpansion\Core\i18n::getInstance()->getObject($string);
        }

    }
}
?>
