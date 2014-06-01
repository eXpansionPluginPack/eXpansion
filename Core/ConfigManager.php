<?php

namespace ManiaLivePlugins\eXpansion\Core;

use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Core\Events\ConfigLoadEvent;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Int;
use ManiaLivePlugins\eXpansion\Core\types\config\Variable;

/**
 * This will work on centrelizing configurations in order to display
 * them in game, and dispatch events to let know plugins that some
 * of their configs have changed
 *
 * @author De Cramer Oliver
 */
class ConfigManager
{

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
     * @var Variable[][] List of all global variables
     */
    private $variables = array();

    /**
     * @var Variable[] List of all server variables grouped
     */
    private $groupedVariables = array();

    /**
     * @var Bool Was the global variables updated? if yes need to save on next check
     */
    private $globalsUpdated = false;

    /**
     * @var Bool Was the global variables updated? if yes need to save on next check
     */
    private $fileUpdated = false;

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
     * @param Core $eXpCore
     *
     * @return ConfigManager
     */
    public static function getInstance(Core $eXpCore = null)
    {
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
    private function __construct($eXpCore = null)
    {
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
    private function setCore($eXpCore)
    {
	$this->eXpCore = $eXpCore;
    }

    /**
     * Registers a varible to the config manager in order to allow it to be saved
     *
     * @param types\config\Variable $var
     * @param string                $pluginId
     */
    public function registerVariable(Variable $var, $pluginId)
    {
	$config = $var->getConfigInstance();

	if ($config != null) {

	    $class = get_class($config);

	    $this->configurationPlugins[$class] = $pluginId;
	    if (!isset($this->variables[$class]))
		$this->variables[$class] = array();

	    $this->variables[$class][$var->getName()] = $var;

	    $group = $var->getGroup();
	    if ($group == "")
		$group = 'General';

	    $this->addVariableToGroup($group, $var);

	    if (!isset($this->configurations[$class])) {
		$this->configurations[$class] = $config;
	    }

	    $scopeVar = new Int($class.$var->getName(), "", Config::getInstance(), Variable::SCOPE_SERVER, false);
	    $var->setScopeHandler($scopeVar);
	    if(!isset($this->variables[get_class(Config::getInstance())]))
		$this->variables[get_class(Config::getInstance())] = array();
	    $this->variables[get_class(Config::getInstance())][$scopeVar->getName()] = $scopeVar;
	}
    }

    /**
     * @param string                $group
     * @param types\config\Variable $var
     * @param int                   $num
     */
    private function addVariableToGroup($group, $var, $num = 1)
    {
	$groupName = $group;
	if ($num > 1)
	    $groupName = $group . ' #' . $num;

	$confName = "main";
	if (!$var->getShowMain())
	    $confName = $var->getPluginId();

	if (!isset($this->groupedVariables[$confName]))
	    $this->groupedVariables[$confName] = array();

	if (!isset($this->groupedVariables[$confName][$groupName]))
	    $this->groupedVariables[$confName][$groupName] = array();

	if (sizeof($this->groupedVariables[$confName][$groupName]) > 64) {
	    $this->addVariableToGroup($group, $var, $num + 1);
	} else {
	    $this->groupedVariables[$confName][$groupName][$var->getName()] = $var;
	}
    }

    /**
     * Let known the manager that a value in a variable has changed, this means a save is required
     *
     * @param \ManiaLivePlugins\eXpansion\Core\types\config\Variable $var
     */
    public function registerValueChange(Variable $var)
    {
	if ($var->getScope() == Variable::SCOPE_GLOBAL)
	    $this->globalsUpdated = true;
	else if($var->getScope() == Variable::SCOPE_SERVER)
	    $this->scopedUpdated = true;
	else{
	    $this->fileUpdated = true;
	}
    }

    /**
     * @param string $confName The name of the configuration. By default it is "main" for all settings that appears on the main page.
     *                         IF not the name of the plugin that might have specific plugin settings.
     *
     * @return mixed[]
     */
    public function getGroupedVariables($confName = "main")
    {
	return $this->groupedVariables[$confName];
    }

    /**
     * Loads all the settings that were saved and puts them in to the configurations
     */
    public function loadSettings()
    {
	$fileS = array();

	$global = $this->getSettingsFromFile(self::dirName . DIRECTORY_SEPARATOR . "settings.exp");

	$scoped = $this->getSettingsFromFile(self::dirName . DIRECTORY_SEPARATOR . "settings_" . $this->serverLogin . ".exp");

	$conf = Config::getInstance();
	if($conf->saveSettingsFile !== ''){
	    $fileS = $this->getSettingsFromFile(self::dirName . DIRECTORY_SEPARATOR . $conf->saveSettingsFile . ".user.exp");
	}

	$this->applySettings($global, Variable::SCOPE_GLOBAL);

	$this->applySettings($scoped, Variable::SCOPE_SERVER);

	$this->applySettings($fileS, Variable::SCOPE_FILE);
    }

    protected function getSettingsFromFile($file)
    {
	return file_exists($file) ? unserialize(file_get_contents($file)) : array();
    }

    /**
     * Checks whatever a save is required, if it is not it will reload settings to be on the safe side.
     */
    public function check($forceSave = false)
    {
	$saved = false;

	if ($forceSave || $this->scopedUpdated) {
	    $this->saveSettings(self::dirName . DIRECTORY_SEPARATOR . "settings_" . $this->serverLogin . ".exp", $this->configurations, Variable::SCOPE_SERVER);
	    $this->scopedUpdated = false;
	    $saved = true;
	}

	if ($forceSave || $this->globalsUpdated) {
	    $this->saveSettings(self::dirName . DIRECTORY_SEPARATOR . "settings.exp", $this->configurations, Variable::SCOPE_GLOBAL);
	    $this->globalsUpdated = false;
	    $saved = true;
	}

	$conf = Config::getInstance();
	if($forceSave || $conf->saveSettingsFile !== '' && $this->fileUpdated){
	    $this->saveSettings(self::dirName . DIRECTORY_SEPARATOR . $conf->saveSettingsFile . ".user.exp", $this->configurations, Variable::SCOPE_FILE);
	    $this->fileUpdated = false;
	    $saved = true;
	}

	if (!$saved)
	    $this->loadSettings();
    }

    /**
     * Applies the settings to the current configuration
     */
    protected function applySettings($newSettings, $scope)
    {

	foreach ($newSettings as $className => $object) {
	    if (!isset($this->configurations[$className])) {
		if (!class_exists($className))
		    continue;
		$this->configurations[$className] = $className::getInstance();
	    }

	    $config = $this->configurations[$className];
	    foreach ($config as $key => $value) {
		if (isset($this->variables[$className]) && isset($this->variables[$className][$key])) {
		    /** @var Variable $var */
		    $var = $this->variables[$className][$key];
		    if ($var->getScope() >= $scope && isset($object->$key)) {
			$config->$key = $object->$key;
		    }
		}
	    }
	}
    }

    public function saveSettingsIn($fileName){
        $this->saveSettings(self::dirName . DIRECTORY_SEPARATOR . $fileName, $this->configurations, Variable::SCOPE_FILE);
        $this->fileUpdated = false;
    }

    public function loadSettingsFrom($fileName, $save = true){
        $fileS = array();

        $global = $this->getSettingsFromFile(self::dirName . DIRECTORY_SEPARATOR . "settings.exp");

        $scoped = $this->getSettingsFromFile(self::dirName . DIRECTORY_SEPARATOR . "settings_" . $this->serverLogin . ".exp");

        $this->applySettings($global, Variable::SCOPE_GLOBAL);

        $this->applySettings($scoped, Variable::SCOPE_SERVER);

        $conf = Config::getInstance();
        $fileS = $this->getSettingsFromFile($fileName);

        $this->applySettings($fileS, Variable::SCOPE_FILE);

        if($save)
            $this->check(true);

        Dispatcher::dispatch(new ConfigLoadEvent(ConfigLoadEvent::ON_CONFIG_FILE_LOADED));
    }

    /**
     * Saves the settings into the file.
     *
     * @param String $fileName The path ot the file to save the configuration to
     * @param mixed  $settings The settings to serialize and save
     * @param int    $scope    the scope of the file being saved
     */
    protected function saveSettings($fileName, $settings, $scope)
    {
	$toSave = $settings;
	//If saving global settings, we need to preserve original setting for variables of higher scopes
	if ($scope == Variable::SCOPE_GLOBAL && file_exists($fileName)) {
	    //We migh need the old values to replace the new ones. for lower scoped settings
	    $oldSettings = $this->getSettingsFromFile($fileName);

	    foreach ($settings as $className => $object) {
		$toSave[$className] = new \stdClass();
		foreach ($object as $key => $value) {

		    $toSave[$className]->$key =$value;

		    if (isset($this->variables[$className]) && isset($this->variables[$className][$key])) {
			/** @var Variable $ourVar */
			$ourVar = $this->variables[$className][$key];

			if ($ourVar->getScope() > $scope) {
			    //Need to get old value if possible, value of this variable is for superior scopes only
			    if (isset($oldSettings[$className]) && isset($oldSettings[$className]->$key)){
				$toSave[$className]->$key = $oldSettings[$className]->$key;
			    }
			}
		    }
		}
	    }
	}
	file_put_contents($fileName, serialize($toSave));
    }

    public function getCore()
    {
	return $this->eXpCore;
    }

}

?>
