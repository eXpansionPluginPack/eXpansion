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
            Console::println('[eXpension Pack] Enabling eXpension version:' . $this->getVersion() . ' . . .');
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
            $this->onBeginMap(null, null, null);
        }

        /**
         * 
         * @param type $map
         * @param type $warmUp
         * @param type $matchContinuation
         */
        function onBeginMap($map, $warmUp, $matchContinuation) {
            $newGameMode = \ManiaLive\Data\Storage::getInstance()->gameInfos->gameMode;
            if ($newGameMode != $this->lastGameMode) {
                $this->lastGameMode = $newGameMode;

                $this->checkLoadedPlugins();
                $this->checkPluginsOnHold();
            }
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
