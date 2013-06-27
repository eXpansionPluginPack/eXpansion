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
        $this->connection->chatSendServerMessage('$fffStarting e$a00X$fffpansion v. ' . $this->getVersion());
        $config = Config::getInstance();
        i18n::getInstance()->start();

        $this->enableDedicatedEvents(\ManiaLive\DedicatedApi\Callback\Event::ON_BEGIN_MAP);

        $expansion =
                <<<'EOT'
   
--------------------------------------------------------------------------------   
                     __   __                      _             
                     \ \ / /                     (_)            
                  ___ \ V / _ __   __ _ _ __  ___ _  ___  _ __  
                 / _ \ > < | '_ \ / _` | '_ \/ __| |/ _ \| '_ \ 
                |  __// . \| |_) | (_| | | | \__ \ | (_) | | | |
                 \___/_/ \_\ .__/ \__,_|_| |_|___/_|\___/|_| |_|
                           | |         Plugin Pack for Manialive    
                           |_|                                                              

-------------------------------------------------------------------------------

EOT;

        Console::println($expansion);
        $server = $this->connection->getVersion();
        $d = (object) date_parse_from_format("Y-m-d_H_i", $server->build);
        Console::println('Dedicated Server running for title: ' . $server->titleId);
        Console::println('Dedicated Server build: ' . $d->year . "-" . $d->month . "-" . $d->day);
        Console::println('eXpansion version: ' . $this->getVersion());
        Console::println('');
        Console::println('Language support detected for: ' . implode(",", i18n::getInstance()->getSupportedLocales()) . '!');
        Console::println('Enabling default locale: ' . $config->defaultLanguage . '');
        i18n::getInstance()->setDefaultLanguage($config->defaultLanguage);
        $this->connection->setApiVersion($config->API_Version); // For SM && TM

        $bExitApp = false;

        if (version_compare(PHP_VERSION, '5.3.3') >= 0) {
            Console::println('Minimum PHP version 5.3.3: Pass (' . PHP_VERSION . ')');
        } else {
            Console::println('Minimum PHP version 5.3.3: Fail (' . PHP_VERSION . ')');
            $bExitApp = true;
        }

        if (gc_enabled()) {
            Console::println('Garbage Collector enabled: Pass ');
        } else {
            Console::println('Garbage Collector enabled: Fail )');
            $bExitApp = true;
        }
        Console::println('');
        Console::println('-------------------------------------------------------------------------------');
        Console::println('');

        if ($bExitApp) {
            $this->connection->chatSendServerMessage("Failed to init eXpansion, see consolelog for more info!");
            die();
        }

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
