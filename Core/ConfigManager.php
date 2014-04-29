<?php

namespace ManiaLivePlugins\eXpansion\Core;

use ManiaLivePlugins\eXpansion\Core\types\config\Variable;

/**
 * This will work on centrelizing configurations in order to display 
 * them in game, and dispatch events to let know plugins that some 
 * of their configs have changed
 *
 * @author De Cramer Oliver
 */
class ConfigManager {

    private static $_instance;

    const dirName = "config/eXp/";

    /**
     *
     * @var Core
     */
    private $eXpCore;

    /**
     *
     * @var String[] Mapping of configuration class and plugin id to whom they belong 
     */
    private $configurationPlugins = array();

    /**
     * @var Config[] List of all configuration objects
     */
    private $configurations = array();

    /**
     * @var Variable[] List of all global variables
     */
    private $globalVariables = array();

    /**
     * @var Variable[] List of all server scoped variables 
     */
    private $scopedVariables = array();
    private $groupedVariables = array();

    /**
     * @var Bool Was the global variables updated? if yes need to save on next check
     */
    private $globalsUpdated = false;

    /**
     * @var Bool Was the server scoped variables updated? if yes need to save on next check
     */
    private $scopedUpdated = false;

    /**
     * @var String The login of the server
     */
    private $serverLogin;

    /**
     * 
     * @return ManiaLivePlugins\eXpansion\Core\ConfigManager
     */
    public static function getInstance(Core $eXpCore = null) {
	if (self::$_instance == null) {
	    self::$_instance = new ConfigManager($eXpCore);
	} else if ($eXpCore != null)
	    self::$_instance->setCore($eXpCore);
	return self::$_instance;
    }

    /**
     * 
     * @param Core $eXpCore The core of eXpansion, god to all. 
     */
    private function __construct($eXpCore = null) {
	$this->eXpCore = $eXpCore;

	/**
	 * @var \ManiaLive\Data\Storage;
	 */
	$storage = \ManiaLive\Data\Storage::getInstance();
	$this->serverLogin = $storage->serverLogin;

	if (!file_exists(self::dirName))
	    mkdir(self::dirName);
    }

    /**
     * 
     * @param Core $eXpCore The core of eXpansion, god to all. 
     */
    private function setCore($eXpCore) {
	$this->eXpCore = $eXpCore;
    }

    /**
     * Registers a varible to the config manager in order to allow it to be saved
     * 
     * @param \types\config\Variable $var
     */
    public function registerVariable(Variable $var, $pluginId) {
	$config = $var->getConfigInstance();

	if ($config != null) {

	    $class = get_class($config);

	    $this->configurationPlugins[$class] = $pluginId;
	    if ($var->getIsGlobal()) {
		if (!isset($this->globalVariables[$class]))
		    $this->globalVariables[$class] = array();

		$this->globalVariables[$class][$var->getName()] = $var;
	    }else {
		if (!isset($this->scopedVariables[$class]))
		    $this->scopedVariables[$class] = array();

		$this->scopedVariables[$class][$var->getName()] = $var;
	    }

	    $group = $var->getGroup();
	    if ($group == "")
		$group = 'General';

	    $this->addVariableToGroup($group, $var);

	    if (!isset($this->configurations[$class])) {
		$this->configurations[$class] = $config;
	    }
	}
    }

    private function addVariableToGroup($group, $var, $num = 1) {
	$groupName = $group;
	if ($num > 1)
	    $groupName = $group . ' #' . $num;

	if (!isset($this->groupedVariables[$groupName]))
	    $this->groupedVariables[$groupName] = array();

	if (sizeof($this->groupedVariables[$groupName]) > 64) {
	    $this->addVariableToGroup($group, $var, $num + 1);
	} else {
	    $this->groupedVariables[$groupName][$var->getName()] = $var;
	}
    }

    /**
     * Let known the manager that a value in a variable has changed, this means a save is required
     * 
     * @param \ManiaLivePlugins\eXpansion\Core\types\config\Variable $var
     */
    public function registerValueChange(Variable $var) {
	echo "Change \n";
	if ($var->getIsGlobal())
	    $this->globalsUpdated = true;
	else
	    $this->scopedUpdated = true;
    }

    public function getGroupedVariables() {
	return $this->groupedVariables;
    }

    /**
     * Loads all the settings that were saved and puts them in to the configurations
     */
    public function loadSettings() {
	$global = array();
	$scoped = array();

	if (file_exists(self::dirName . DIRECTORY_SEPARATOR . "settings.exp")) {
	    $global = unserialize(file_get_contents(self::dirName . DIRECTORY_SEPARATOR . "settings.exp"));
	}

	if (file_exists(self::dirName . DIRECTORY_SEPARATOR . "settings_" . $this->serverLogin . ".exp")) {
	    $scoped = unserialize(file_get_contents(self::dirName . DIRECTORY_SEPARATOR . "settings_" . $this->serverLogin . "exp"));
	}

	$this->applySettings($global);

	$this->applySettings($scoped, true);
    }

    /**
     * Checks whatever a save is required, if it is not it will reload settings to be on the safe side.
     */
    public function check() {
	$saved = false;
	if ($this->scopedUpdated) {
	    $this->saveSettings(self::dirName . DIRECTORY_SEPARATOR . "settings_" . $this->serverLogin . ".exp", $this->configurations);
	    $this->scopedUpdated = false;
	    $saved = true;
	}

	if ($this->globalsUpdated) {
	    $this->saveSettings(self::dirName . DIRECTORY_SEPARATOR . "settings.exp", $this->configurations);
	    $this->globalsUpdated = false;
	    $saved = true;
	}

	if (!$saved)
	    $this->loadSettings();
    }

    /**
     * Applies the settings to the current configuration
     */
    protected function applySettings($newSettings, $scoped = false) {

	foreach ($newSettings as $className => $object) {
	    if (!isset($this->configurations[$className]))
		$this->configurations[$className] = $className::getInstance();

	    $config = $this->configurations[$className];
	    foreach ($config as $key => $value) {

		if (!$scoped) {
		    if (isset($object->$key) && (!isset($this->scopedVariables[$className]) || !isset($this->scopedVariables[$className][$key])))
			$config->$key = $object->$key;
		}else {
		    if (isset($object->$key) && isset($this->scopedVariables[$className]) && isset($this->scopedVariables[$className][$key]))
			$config->$key = $object->$key;
		}
	    }
	}
    }

    /**
     * Saves the settings into the file.
     * 
     * @param String $fileName The path ot the file to save the configuration to
     * @param mixed $settings The settings to serialize and save
     */
    protected function saveSettings($fileName, $settings) {
	file_put_contents($fileName, serialize($settings));
    }

}

?>
