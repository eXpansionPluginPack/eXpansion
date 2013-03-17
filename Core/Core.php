<?php

namespace ManiaLivePlugins\eXpansion\Core;

    use ManiaLive\Event\Dispatcher;
    use ManiaLive\Utilities\Console;

    /**
     * Description of Core
     *
     * @author oliverde8
     * 
     */
    class Core extends types\ExpPlugin {

        /**
         * Last used game mode
         * @var \DedicatedApi\Structures\GameInfos
         */
        private $lastGameMode;

        /**
         * 
         */
        function exp_onInit() {
            parent::exp_onInit();
        }

        /**
         * 
         */
        function exp_onLoad() {
            parent::exp_onLoad();
            $config = Config::getInstance();
            
            $this->enableDedicatedEvents(\ManiaLive\DedicatedApi\Callback\Event::ON_BEGIN_MAP);
            
            i18n::getInstance()->start();
            
            Console::println(' #####################################################################');
            Console::println('[eXpansion Pack] Enabling eXpension version:' . $this->getVersion() . ' . . .');
            Console::println(' Language support detected for:' . implode(",",i18n::getInstance()->getSupportedLocales()) . '!');
            Console::println(' Enabling default locale:' . $config->defaultLanguage . '');
            i18n::getInstance()->setDefaultLanguage($config->defaultLanguage);
            
            $die = false;

            Console::println(' #####################################################################');

            if ($die)
                die();
            
            $this->lastGameMode = \ManiaLive\Data\Storage::getInstance()->gameInfos->gameMode;
        }

        /**
         * 
         */
        public function exp_onReady() {
            $this->connection->chatSendServerMessage("");
            $this->connection->chatSendServerMessage('$fff********************************');
            $this->exp_chatSendServerMessage('$fff e$a00X$fffpansion v. '.$this->getVersion().' Initialized succesfully. ');
            $this->connection->chatSendServerMessage('$fff********************************');
            $this->connection->chatSendServerMessage("");
            $this->onBeginMap(null, null, null);
        }

        /**
         * 
         * @param array $map
         * @param bool $warmUp
         * @param bool $matchContinuation
         */
        function onBeginMap($map, $warmUp, $matchContinuation) {
            $newGameMode = \ManiaLive\Data\Storage::getInstance()->gameInfos->gameMode;
            if ($newGameMode != $this->lastGameMode) {
                $this->lastGameMode = $newGameMode;

                $this->checkLoadedPlugins();
                $this->checkPluginsOnHold();
            }
        }

        function onPlayerConnect($login, $isSpectator) {
            $this->memory();
        }
        
        function onPlayerDisconnect($login) {
           $this->memory();
        }
        
        function memory() {
            $mem = "Memory Usage: ". memory_get_usage()/1024 . "Mb";
            \ManiaLive\Utilities\Logger::getLog("memory")->write($mem);
            print "\n". $mem ."\n";
        }
        
        private function checkLoadedPlugins() {
            $pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();
            Console::println('#####################################################################');
            Console::println('[eXpension Pack] GameMode Changed Shutting down uncompatible plugins');
            foreach ($this->exp_getGameModeCompability() as $plugin => $compability) {
                $parts = explode('\\', $plugin);
                $plugin_id = $parts[1] . '\\' . $parts[2];
                if (!$plugin::exp_checkGameCompability()) {
                    try {
                        $this->callPublicMethod($plugin_id, 'exp_unload');
                    } catch (\Exception $ex) {
                        
                    }
                }
            }
            Console::println('#####################################################################' . "\n");
        }

        private function checkPluginsOnHold() {
            Console::println('#####################################################################');
            Console::println('[eXpension Pack] GameMode Changed Starting compatible plugins');
            if (!empty(types\BasicPlugin::$plugins_onHold)) {
                $pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();
                foreach (types\BasicPlugin::$plugins_onHold as $plugin_id) {
                    $className = '\\ManiaLivePlugins\\' . $plugin_id;
                    //if($className::exp_checkGameCompability()){
                    $pHandler->load($plugin_id);
                    //}
                }
            }
            Console::println('#####################################################################' . "\n");
        }

    }
    
?>
