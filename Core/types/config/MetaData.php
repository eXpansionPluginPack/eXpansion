<?php

namespace ManiaLivePlugins\eXpansion\Core\types\config;

use ManiaLivePlugins\eXpansion\Core\types\config\types\String;

/**
 * Description of MetaData
 *
 * @author De Cramer Oliver
 */
abstract class MetaData {

    private static $_instances = array();

    /**
     * @var \ManiaLive\PluginHandler\Plugin The plugin to whom the MetaData is affected 
     */
    private $plugin;

    /**
     *
     * @var \ManiaLivePlugins\eXpansion\Core\ConfigManager
     */
    private $confManager;

    /**
     * @var String Name of the plugin as it should be visible to players
     */
    private $name;

    /**
     * @var String Shortish description of what this plugin does
     */
    private $description;

    /**
     * @var InstallationStep[] Step to make the installation possible 
     */
    private $installationSteps = array();

    /**
     * @var Variable
     */
    private $variables = array();

    public static function getInstance(\ManiaLive\PluginHandler\Plugin $plugin = null) {

	$class = get_called_class();
	if (!isset(self::$_instances[$class])) {
	    self::$_instances[$class] = new $class($plugin);
	} else if ($plugin != null)
	    self::$_instances[$class]->setPlugin($plugin);

	return self::$_instances[$class];
    }

    final private function __construct(\ManiaLive\PluginHandler\Plugin $plugin) {
	$this->plugin = $plugin;
	$this->confManager = \ManiaLivePlugins\eXpansion\Core\ConfigManager::getInstance();

	$this->onBeginLoad();
	$this->onEndLoad();
    }

    public function onBeginLoad() {
	
    }

    public function onEndLoad() {
	
    }

    public function registerVariable(Variable $var) {
	$this->variables[$var->getName()] = $var;
	$this->confManager->registerVariable($var, $this->plugin->getId());
    }

    private function setPlugin(\ManiaLive\PluginHandler\Plugin $plugin) {
	$this->plugin = $plugin;
    }

    public function unSetPlugin() {
	$this->plugin = null;
    }

    public function getVariable($name) {
	return isset($this->variables[$name]) ? $this->variables[$name] : null;
    }

    public function getName() {
	return $this->name;
    }

    public function getDescription() {
	return $this->description;
    }

    public function setName($name) {
	if (!$name instanceof String) {
	    $name = new String($name, "", null);
	}
	$this->name = $name;
    }

    public function setDescription($description) {
	if (!$description instanceof String) {
	    $description = new String($description, "", null);
	}
	$this->description = $description;
    }

}

?>
