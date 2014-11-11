<?php

namespace ManiaLivePlugins\eXpansion\Core\types\config;
use ManiaLib\Utils\Singleton;
use ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\eXpansion\Core\Events\PluginSettingChange;

/**
 * Description of Variable
 *
 * @author De Cramer Oliver
 */
abstract class Variable
{

	const SCOPE_GLOBAL = 0;
	const SCOPE_SERVER = 1;
	const SCOPE_FILE = 2;

	private $name;

	private $visibleName;

	private $description;

	private $scope = self::SCOPE_GLOBAL;

	/**
	 * @var Variable
	 */
	private $scopeHandler = null;

	private $group;

	private $canBeNull = false;

	private $defaultValue = array();

	private $possibleValues = array();

	private $configInstance = null;

	private $value;

	private $pluginId = null;

	private $visible = true;

	private $showMain = true;

	/**
	 *
	 * @var \ManiaLivePlugins\eXpansion\Core\ConfigManager
	 */
	private $confManager = null;

	/**
	 *
	 * @param String $name              The name of the variable in the config file
	 * @param String $visibleName       The name the players should see
	 * @param Singleton $configInstance The config instance in which the value should be saved into
	 * @param int | bool $scope         Is the scope of this variable global or server only
	 * @param Boolean $showMain         Should the setting be shown in the main configuration or in the main expansion configuration
	 */
	public function __construct($name, $visibleName = "", $configInstance = null, $scope = true, $showMain = true)
	{
		$this->name = $name;
		$this->visibleName = $visibleName;
		$this->setScope($scope);
		$this->showMain = $showMain;
		$this->configInstance = $configInstance;
		$this->confManager = \ManiaLivePlugins\eXpansion\Core\ConfigManager::getInstance();
	}

	/**
	 * Adds a value that the this variable can have.
	 * Once this is used only values in the list can be set.
	 *
	 * @param mixed $value The value that can be set
	 *
	 * @return \ManiaLivePlugins\eXpansion\Core\types\config\Variable
	 */
	public function addPossibleValue($value)
	{
		$this->possibleValues[] = $value;
		return $this;
	}

	/**
	 * Returns array with all values this variable can withold.
	 * If empty array then it can get anything.
	 *
	 * @return mixed[] Array of all possible values
	 */
	public function getPossibleValues()
	{
		return $this->possibleValues;
	}

	/**
	 * Allows to set a default value to the variable.
	 *
	 * @param mixed $value The value this variable should have if not defined it the configuration.
	 *
	 * @return \ManiaLivePlugins\eXpansion\Core\types\config\Variable
	 */
	public function setDefaultValue($value)
	{
		$this->defaultValue = $value;
		return $this;
	}

	/**
	 * Allows this variable to be set to null, this will probebly trigger different comportements
	 * in the plugin that uses this value
	 *
	 * @param Boolean $canBe
	 *
	 * @return \ManiaLivePlugins\eXpansion\Core\types\config\Variable
	 */
	public function setCanBeNull($canBe)
	{
		$this->canBeNull = $canBe;
		return $this;
	}

	/**
	 * Sets the name of the group in which the variable will appear.
	 * For easier acces the settings will be grouped in the configuration interface.
	 *
	 * @param String $name The name of the group
	 */
	public function setGroup($name)
	{
		$this->group = $name;
	}


	public function setPluginId($id)
	{
		$this->pluginId = $id;
	}

	public function getPluginId()
	{
		return $this->pluginId;
	}

	public function getIsGlobal()
	{
		if ($this->scopeHandler == null)
			return $this->scope == self::SCOPE_GLOBAL;
		else
			return $this->scopeHandler->getRawValue() == self::SCOPE_GLOBAL;
	}

	public function getScope()
	{
		if ($this->scopeHandler == null)
			return $this->scope;
		else
			return $this->scopeHandler->getRawValue();
	}

	/**
	 * @param int | bool $scope The scope of this variable
	 */
	public function setScope($scope)
	{
		if (is_int($scope)) {
			$this->scope = $scope;
		} else {
			$this->scope = $scope ? self::SCOPE_GLOBAL : self::SCOPE_SERVER;
		}
		if ($this->scopeHandler != null) {
			$this->scopeHandler->setRawValue($this->scope);
		}
	}

	/**
	 * @param Variable $scopeHandler
	 */
	public function setScopeHandler(Variable $scopeHandler)
	{
		$this->scopeHandler = $scopeHandler;
		$this->scopeHandler->setRawValue($this->scope);
	}

	public function getCanBeNull()
	{
		return $this->canBeNull;
	}

	/**
	 * @param boolean $showMain
	 */
	public function setShowMain($showMain)
	{
		$this->showMain = $showMain;
	}

	/**
	 * @return boolean
	 */
	public function getShowMain()
	{
		return $this->showMain;
	}

	/**
	 * Retunrs the variable name is specified in the config, and read by the plugin.
	 *
	 * @return String The name
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * The name the user will see in game while configuring the server or in the documentation
	 *
	 * @return String The name
	 */
	public function getVisibleName()
	{
		return $this->visibleName;
	}


	public function getDescription()
	{
		return $this->description;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}


	/**
	 * For easier acces the settings will be grouped in the configuration interface. The name of the group
	 *
	 * @return String The name of the group
	 */
	public function getGroup()
	{
		return $this->group;
	}

	public function getVisible()
	{
		return $this->visible;
	}

	public function setVisible($visible)
	{
		$this->visible = $visible;
	}

	/**
	 * The default value of this variable.
	 *
	 * @return type
	 */
	public function getDefaultValue()
	{
		return $this->defaultValue;
	}


	public function getConfigInstance()
	{
		return $this->configInstance;
	}

	/**
	 * Changes the value in the configuration instance of the variable
	 *
	 * @param mixed $value the new value
	 *
	 * @return boolean
	 */
	public function setRawValue($value)
	{
		if ($this->configInstance != null) {
			$name = $this->name;
			$this->configInstance->$name = $value;
			$this->confManager->registerValueChange($this);
			$core = $this->confManager->getCore();

			/**
			 * @var \ManiaLive\PluginHandler\PluginHandler
			 */
			if ($core != null) {
				$phandler = \ManiaLive\PluginHandler\PluginHandler::getInstance();
				try {
					$phandler->callPublicMethod($core, $this->pluginId, 'onSettingsChanged', array($this));
				} catch (\Exception $ex) {
					echo "error on settings change!". $ex->getMessage()."\n";
				}

				Dispatcher::dispatch(new PluginSettingChange(PluginSettingChange::ON_SETTINGS_CHANGE, $this->pluginId, $this));
			}
		} else {
			$this->value = $value;
		}
		return true;
	}

	/**
	 * Returns the value of the variable at it's current instance
	 *
	 * @return mixed The value stored in the current configuration
	 */
	public function getRawValue()
	{
		if ($this->configInstance != null) {
			$name = $this->name;
			return isset($this->configInstance->$name) ? $this->configInstance->$name : null;
		} else {
			return $this->value;
		}
	}

	public function basicValueCheck($value)
	{
		if ($value == null && $this->canBeNull)
			return true;
		else if (!empty($this->possibleValues))
			return \in_array($value, $this->possibleValues);
		else
			return true;
	}

	public function castValue($value)
	{
		return $value;
	}

	public function showConfWindow($login)
	{
		return null;
	}

	public function hideConfWindow($login)
	{
		return null;
	}

	public function hasConfWindow()
	{
		return false;
	}

	abstract function getPreviewValues();
}

?>
