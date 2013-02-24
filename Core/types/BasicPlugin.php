<?php

namespace ManiaLivePlugins\eXpansion\Core\types {

    use DedicatedApi\Structures\GameInfos;
    use ManiaLive\Utilities\Console;

    /**
     * Description of BasicPlugin
     *
     * @author oliverde8
     */
    class BasicPlugin extends \ManiaLive\PluginHandler\Plugin {

        /**
         * The list of Plugin id's that may need to be started
         * @var int 
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
         * THe list of plugins that have their announcement redirected
         */
        private static $exp_announceRedirected = array();
        private $exp_unloading = false;

        /**
         * The Expansion Pack tools
         * @var \ManiaLivePlugins\eXpansion\Core\eXpansion Expansion tools
         */
        protected $exp_maxp;

        public final function onInit() {
            //Recovering the eXpansion pack tools
            $this->exp_maxp = \ManiaLivePlugins\eXpansion\Core\eXpansion::getInstance();

            $this->exp_unloading = false;

            //All plugins need the eXpansion Core to work properly
            if ($this->getId() != 'eXpansion\Core')
                $this->addDependency(new \ManiaLive\PluginHandler\Dependency('eXpansion\Core'));

            $this->setPublicMethod('exp_unload');
            $this->setPublicMethod('getDependencies');
            $this->setPublicMethod('exp_activateChatRedirect');
            $this->setPublicMethod('exp_deactivateChatRedirect');
            $this->setPublicMethod('exp_activateAnnounceRedirect');
            $this->setPublicMethod('exp_deactivateAnnounceRedirect');

            $this->exp_onInit();
        }

        /**
         * eXpansion method invoked Manialive onInit
         * @abstract
         */
        public function exp_onInit() {
            
        }

        public final function onLoad() {
            $this->exp_onLoad();
        }

        /**
         * eXpansion method invoked at Manialive onload        
         * @abstract
         */
        public function exp_onLoad() {
            
        }

        public final function onReady() {
            if (!self::exp_checkGameCompability()) {
                $this->exp_unload();
            } else {
                $this->exp_onReady();
            }
        }

        /**
         * eXpansion onReady handler
         * @abstract
         */
        public function exp_onReady() {
            
        }

        /**
         * Sending a chat message to the login.
         * 
         * @param type $msg
         * @param type $login null to send to everyone
         */
        protected function exp_chatSendServerMessage($msg, $login = null) {
            if ($login == null) {
                //If we send it to everyone we consider it as a announcement
                $this->exp_announce($msg);
            } else {
                //Check if it needs to ve redirected
                $this->exp_redirectedChatSendServerMessage($msg, $login, get_class($this));
            }
        }

        /**
         * Sends a chat message to the server or redirect t to another plugin
         * 
         * @param type $msg	The message
         * @param type $login The login to whom it needs to be sent
         */
        private function exp_redirectedChatSendServerMessage($msg, $login) {
            $sender = get_class($this);
            if (isset(self::$exp_chatRedirected[$sender])) {
                if (is_object(self::$exp_chatRedirected[$sender][0]))
                    call_user_func_array(self::$exp_chatRedirected[$sender], array($login, $this->exp_maxp->parseColors($msg)));
                else {
                    $this->callPublicMethod(self::$exp_chatRedirected[$sender][0], self::$exp_chatRedirected[$sender][1], array($login, $this->exp_maxp->parseColors($msg)));
                }
            } else {
                $this->connection->chatSendServerMessage($this->exp_maxp->parseColors($msg), $login);
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
            if (isset(self::$exp_announceRedirected[$sender])) {
                if (is_object(self::$exp_announceRedirected[$sender][0]))
                    call_user_func_array(self::$exp_announceRedirected[$sender], array($this->exp_maxp->parseColors($msg), $icon, $callback, $pluginid));
                else {
                    $this->callPublicMethod(self::$exp_chatRedirected[$sender][0], self::$exp_chatRedirected[$sender][1], array($this->exp_maxp->parseColors($msg), $icon, $callback, $pluginid));
                }
            } else {
                $this->connection->chatSendServerMessage($this->exp_maxp->parseColors($msg));
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
                self::$plugin_gameModeSupport[get_class($this)][$gameMode] = true;
            else
                self::$plugin_gameModeSupport[get_class($this)][$gameMode][$scriptName] = true;
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

                if ($gameInfo->gameMode == GameInfos::GAMEMODE_SCRIPT && is_array(self::$plugin_gameModeSupport[$class][$gameInfo->gameMode])) {
                    return isset(self::$plugin_gameModeSupport[$class][$gameInfo->gameMode][$gameInfo->scriptName]) ? self::$plugin_gameModeSupport[$class][$gameInfo->gameMode][$gameInfo->scriptName] : false;
                }
                else
                    return isset(self::$plugin_gameModeSupport[$class][$gameInfo->gameMode]) ? self::$plugin_gameModeSupport[$class][$gameInfo->gameMode] : false;
            }
            else
            //This plugin supports all GameModes
                return true;
        }

        /**
         * Unloads the plugin.
         * @abstract
         */
        public function exp_unload() {
            Console::println('[eXpension Pack] ' . $this->getId() . ' Isn\'t compatible with this GameMode. UnLoading ...');
            $pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();

            $plugins = $pHandler->getLoadedPluginsList();
            foreach ($plugins as $plugin) {
                try {
                    if ($plugin != $this->getId()) {
                        $deps = $this->callPublicMethod($plugin, 'getDependencies');
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
                    
                }
            }

            //Unloading dependencies to prevent crash
            /* $deps = $this->getDependencies();
              if(!empty($deps)){
              Console::println('[eXpension Pack] Unloading Dependencies of '.$this->getId().'');
              foreach($deps as $dep){
              $this->callPublicMethod($dep->getPluginId(), 'exp_unload');
              }
              } */
            //Unloading it self
            $this->exp_unloading = true;
            $pHandler->unload($this->getId());
            self::$plugins_onHold[] = $this->getId();
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

    }

}

namespace {

    function _() {
        $args = func_get_args();
        $string = array_shift($args);
        $lang = \ManiaLivePlugins\eXpansion\Core\i18n::getInstance()->getString($string);
        array_unshift($args, $lang);                     
        return call_user_func_array('sprintf', $args);        
    }

}
?>
