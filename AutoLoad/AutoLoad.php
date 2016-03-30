<?php

namespace ManiaLivePlugins\eXpansion\AutoLoad;

use ManiaLive\Event\Dispatcher;
use ManiaLive\PluginHandler\PluginHandler;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\AutoLoad\Gui\Windows\PluginList;
use ManiaLivePlugins\eXpansion\AutoLoad\Structures\PluginNotFoundException;
use ManiaLivePlugins\eXpansion\Core\ConfigManager;
use ManiaLivePlugins\eXpansion\Core\Events\ConfigLoadEvent;
use ManiaLivePlugins\eXpansion\Core\types\config\MetaData as MetaDataType;

class AutoLoad extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    private $plugins;

    /**
     * @var  \ManiaLivePlugins\eXpansion\Core\types\config\MetaData[]
     */
    private $availablePlugins;

    /**
     * @var \ManiaLivePlugins\eXpansion\Core\types\config\MetaData[]
     */
    private static $allAvailablePlugins;

    /**
     * @var Config
     */
    private $config;

    private $toBeRemoved = array();

    private $configPlugins = array();

    public function exp_onLoad()
    {
        $this->setPublicMethod('showPluginsWindow');
        Dispatcher::register(ConfigLoadEvent::getClass(), $this, ConfigLoadEvent::ON_CONFIG_FILE_LOADED);
        $this->console("[eXpansion] AutoLoading eXpansion pack ... ");

        /**
         * @var Config $config
         */
        $config = Config::getInstance();
        $this->config = $config;

        //List of plugins that must be loaded always !!
        $this->plugins = array('\ManiaLivePlugins\eXpansion\Core\Core'
        , '\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups'
        , '\ManiaLivePlugins\eXpansion\AutoUpdate\AutoUpdate'
        , '\ManiaLivePlugins\eXpansion\Chat_Admin\Chat_Admin'
        , '\ManiaLivePlugins\eXpansion\Gui\Gui'
        , '\ManiaLivePlugins\eXpansion\Menu\Menu'
        , '\ManiaLivePlugins\eXpansion\Adm\Adm'
        , '\ManiaLivePlugins\eXpansion\AutoLoad\AutoLoad'
        , '\ManiaLivePlugins\eXpansion\Database\Database');

        $this->findAvailablePlugins();
        ConfigManager::getInstance()->loadSettings();

        //We Need the plugin Handler
        $pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();

        $this->autoLoadPlugins($this->plugins, $pHandler);

        
    }

    public function exp_onReady()
    {
        //We Need the plugin Handler
        $pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();

        // Normalize plugin names.
        $plugins = array();
        $changed = true;
        foreach ($this->config->plugins as $pname) {
            if ($pname[0] != '\\') {
                $pname = '\\' . $pname;
                $changed = true;
            }
            $plugins[] = $pname;
        }

        if ($changed) {
            $this->config->plugins = $plugins;
            // Save new settings
            ConfigManager::getInstance()->registerValueChange($this->getMetaData()->getVariable('plugins'));
            ConfigManager::getInstance()->check();

            // Log possible crash.
            $this->console('');
            $this->console('Settings updated !!');
            $this->console('eXpansion might crash at this point, please restart it.');
        }

        $this->autoLoadPlugins($this->config->plugins, $pHandler);

        if (!empty($this->toBeRemoved)) {
            $this->cleanPluginsArray($this->config->plugins, $this->toBeRemoved);
            ConfigManager::getInstance()->registerValueChange($this->getMetaData()->getVariable('plugins'));
            ConfigManager::getInstance()->check();
        }
        $this->configPlugins = $this->config->plugins;

        AdminGroups::addAdminCommand('plugins', $this, 'showPluginsWindow', Permission::expansion_pluginStartStop);
    }

    public function onConfigFileLoaded()
    {

        $toRemove = array();
        foreach ($this->configPlugins as $plugin) {
            if (!in_array($plugin, $this->config->plugins)) {
                $toRemove[] = $plugin;
            }
        }

        $toAdd = array();
        foreach ($this->config->plugins as $plugin) {
            if (!in_array($plugin, $this->configPlugins)) {
                $toAdd[] = $plugin;
            }
        }

        $pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();

        if (!empty($toRemove))
            foreach ($toRemove as $plugin) {
                if ($pHandler->isLoaded($plugin))
                    $pHandler->callPublicMethod($this, $plugin, 'exp_unload', array());
            }

        if (!empty($toAdd))
            $this->autoLoadPlugins($toAdd, $pHandler);
        $this->configPlugins = $this->config->plugins;
    }

    private function cleanPluginsArray(&$plugins, $toRemove)
    {

        for ($i = 0; $i < count($plugins); $i++) {
            if (isset($plugins[$i]) && in_array($plugins[$i], $toRemove)) {
                array_splice($plugins, $i, 1);
            }
        }
    }

    public function autoLoadPlugins($plugins, PluginHandler $pHandler)
    {
        //First attempt to load plugins
        $recheck = $this->loadPlugins($plugins, $pHandler);

        //New attempts to load plugins.
        do {
            $lastSize = sizeof($recheck);
            $recheck = $this->loadPlugins($plugins, $pHandler);
        } while (!empty($recheck) && $lastSize != sizeof($recheck));

        foreach ($plugins as $pname) {
            $pHandler->ready($pname);
        }

        //If all plugins couldn't be loaded
        if (!empty($recheck)) {
            //$this->dumpException("Couldn't Autoload all required plugins", new \Maniaplanet\WebServices\Exception("Autoload failed."));
            $this->exp_chatSendServerMessage(
                "couldn't Autoload all required plugins, see console log for more details."
            );
            $this->console(
                "Not all required plugins were loaded, due to unmet dependencies or errors. list of not loaded plugins: "
            );
            foreach ($recheck as $pname) {
                $this->console($pname);
                $this->connection->chatSendServerMessage('Starting ' . $pname . '........$f00 Failure');
            }
        }
    }

    /**
     * @param                                        $list        List of plugins to load
     * @param \ManiaLive\PluginHandler\PluginHandler $pHandler The manialive plugin handler
     *
     * @return array list of plugins that coudln't be loaded due to dependencies
     */
    public function loadPlugins($list, \ManiaLive\PluginHandler\PluginHandler $pHandler)
    {
        //List of plugins that we coudln't load that we will recheck
        $recheck = array();


        foreach ($list as $pname) {
            try {
                if (!$this->loadPlugin($pname, $pHandler))
                    $recheck[] = $pname;
            } catch (PluginNotFoundException $ex) {
                $this->toBeRemoved[] = $pname;
            }
        }

        return $recheck;
    }

    public function loadPlugin($pname, \ManiaLive\PluginHandler\PluginHandler $pHandler)
    {

        //List of plugins that were disabled
        $disabled = Config::getInstance()->disable;
        if (!is_array($disabled))
            $disabled = array($disabled);

        try {
            if (!$pHandler->isLoaded($pname)) {
                if (in_array($pname, $disabled)) {
                    $this->console("[" . $pname . "]..............................Disabled -> not loading");
                } else {
                    $status = true;
                    if (!class_exists($pname)) {
                        $this->console("[" . $pname . "]..............................Doesen't exist -> not loading");
                        throw new PluginNotFoundException($pname);

                        return false;
                    }
                    /** @var MetaDataType $metaData */
                    $metaData = $pname::getMetaData();

                    $this->availablePlugins[$pname] = $metaData;
                    self::$allAvailablePlugins[$pname] = $metaData;

                    if ($metaData->checkAll()) {
                        try {
                            $status = $pHandler->load($pname, false);
                        } catch (\Exception $ex) {
                            try {
                                $pHandler->unload($pname);
                            } catch (\Exception $ex) {

                            }
                            //ErrorHandling::displayAndLogError($ex);
                            $status = false;
                        }

                        if (!$status) {
                            $this->console("[" . $pname . "]..............................FAIL -> will retry");
                            //Helper::log("[AutoLoad]".ErrorHandling::computeMessage($ex));
                            $recheck[] = $pname;
                        } else {
                            $this->debug("[" . $pname . "]..............................SUCCESS");
                            //   $this->connection->chatSendServerMessage('Starting ' . $pname . '........$0f0 Success');
                        }
                    } else {
                        $otherCheckResults = $metaData->checkAll();
                        if (!empty($otherCheckResults)) {
                            return false;
                        }
                        $this->console("[" . $pname . "]..............................Disabled -> Not Compatible");
                    }
                }
            }
        } catch (PluginNotFoundException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $this->console("[" . $pname . "]..............................FAIL -> will retry");
            //Helper::log("[AutoLoad]".ErrorHandling::computeMessage($ex));
            return false;
        }


        return true;
    }

    function logMemory()
    {
        $mem = "Memory Usage: " . round(memory_get_usage() / 1024) . "Kb";
        //\ManiaLive\Utilities\Logger::getLog("memory")->write($mem);
        //print "\n" . $mem . "\n";
        //$this->connection->chatSend($mem);
    }

    /**
     * @param              $login
     * @param MetaDataType $metaData
     */
    public function tooglePlugin($login, MetaDataType $metaData)
    {
        /**
         * @var PluginHandler $pHandler
         */
        $pHandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();
        $pluginId = $metaData->getPlugin();

        if ($this->isInStartList($pluginId) || $pHandler->isLoaded($pluginId)) {
            if (in_array($pluginId, $this->plugins)) {
                $this->exp_chatSendServerMessage(
                    "#admin_error#This plugin is a core element of eXpansion. It cant be unloaded", $login
                );
            } else {
                $pos = array_search($pluginId, Config::getInstance()->plugins);
                if ($pos !== false) {
                    unset($this->config->plugins[$pos]);
                    ConfigManager::getInstance()->registerValueChange($this->getMetaData()->getVariable('plugins'));
                    ConfigManager::getInstance()->check();
                }
                if ($pHandler->isLoaded($pluginId))
                    $pHandler->callPublicMethod($this, $pluginId, 'exp_unload', array());
                $this->exp_chatSendServerMessage("#admin_action#Plugin stopped with success", $login);
            }
        } else {
            if ($this->loadPlugin($pluginId, $pHandler)) {
                $pHandler->ready($pluginId);
                $this->exp_chatSendServerMessage("#admin_action#Plugin started with success", $login);
            } else {
                $this->exp_chatSendServerMessage(
                    "#admin_error#This plugin contains errors that prevented it from starting", $login
                );
            }

            $this->config->plugins[] = $pluginId;

            ConfigManager::getInstance()->registerValueChange($this->getMetaData()->getVariable('plugins'));
            ConfigManager::getInstance()->check();
        }

        $this->showPluginsWindow($login);
        $this->configPlugins = $this->config->plugins;
    }

    /**
     * Find all available plugins in the available plugin paths
     */
    protected function findAvailablePlugins()
    {
        /**
         * @var Config $config
         */
        $config = Config::getInstance();

        foreach ($config->pluginPaths as $path => $depth) {
            $this->findAvailablePluginsInPath($path, $depth);
        }
    }

    /**
     * Search for plugins in a certain path at a certain depth
     *
     * @param $path
     * @param $depth
     */
    protected function findAvailablePluginsInPath($path, $depth)
    {
        if ($depth < 0)  {
            return;
        }else if (is_dir($path)) {
            $subFiles = scandir($path);

            if (in_array('MetaData.php', $subFiles)) {
                $this->loadAvailablePluginMetaDataFromPath($path);
            } else {
                foreach ($subFiles as $file) {
                    if (is_dir($path . '/' . $file))
                        $this->findAvailablePluginsInPath($path . '/' . $file, $depth - 1);
                }
            }
        } else {
            $this->console("Unknown plugin path : $path");
        }
    }

    /**
     * Loads plugin metadata using plugins path.
     *
     * @param $path
     */
    protected function loadAvailablePluginMetaDataFromPath($path)
    {
        $classes = get_declared_classes();
        require_once $path . '/MetaData.php';
        $diff = array_diff(get_declared_classes(), $classes);
        $className = reset($diff);

        $exploded = explode('\\', $className);
        array_pop($exploded);
        $size = sizeof($exploded);
        $pluginId = implode('\\', $exploded) . '\\' . $exploded[$size - 1];

        if ($pluginId[0] != '\\') {
            $pluginId = '\\' . $pluginId;
        }

        if (class_exists($pluginId)) {
            /**
             * @var MetaDataType $metaData
             */
            $metaData = $className::getInstance($pluginId);
            if ($metaData->getPlugin() == null) {
                $metaData->setPlugin($pluginId);
            } else {
                $pluginId = $metaData->getPlugin();
            }

            $this->availablePlugins[$pluginId] = $metaData;
            self::$allAvailablePlugins[$pluginId] = $metaData;

            uasort($this->availablePlugins, array($this, 'pluginNameCmp'));
            uasort(self::$allAvailablePlugins, array($this, 'pluginNameCmp'));
        }
    }

    public function pluginNameCmp(MetaDataType $a, MetaDataType $b)
    {
        if ($a->getName() == $b->getName()) {
            return 0;
        }
        return ($a->getName() < $b->getName()) ? -1 : 1;
    }

    public function showPluginsWindow($login)
    {
        PluginList::Erase($login);
        $win = PluginList::Create($login);
        $win->setTitle("Plugin List");
        $win->centerOnScreen();
        $win->setSize(140, 100);
        $win->populate($this, $this->availablePlugins);
        $win->show();
    }

    public function isInStartList($pluginId)
    {
        return in_array($pluginId, $this->plugins) || in_array($pluginId, Config::getInstance()->plugins);
    }

    /**
     *
     * @return ManiaLivePlugins\eXpansion\Core\types\config\MetaData[]
     */
    public static function getAvailablePlugins()
    {
        return self::$allAvailablePlugins;
    }

}

?>
