<?php

namespace ManiaLivePlugins\eXpansion\Core\types\config;

use ManiaLive\Data\Storage;
use ManiaLivePlugins\eXpansion\Core\Core;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeString;
use ManiaLivePlugins\eXpansion\Helpers\Helper;
use Maniaplanet\DedicatedServer\Structures\GameInfos;

/**
 * Description of MetaData
 *
 * @author De Cramer Oliver
 */
abstract class MetaData
{

    private static $_instances = array();

    /**
     * @var String The plugin to whom the MetaData is affected
     */
    private $pluginId = null;

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
     * @var string Author of the plugin.
     */
    private $author = "eXpansion";

    /**
     * @var String Shortish description of what this plugin does
     */
    private $description;

    /**
     * @var String[] list of groups the plugin is in
     */
    private $groups = array();

    /**
     *
     * @var Boolean Whatever or not this plugin is part of the core of eXpansion.
     */
    private $core = false;

    /**
     * @var Variable[]
     */
    private $variables = array();

    /**
     * @var String[] The List of GameModes the plugins support
     */
    private $gameModeSupport = array();

    /**
     * @var Boolean Whatever plugins support soft script name or exact script name check
     */
    private $softScriptCompatibility = true;

    /**
     *
     * @var Boolean Whatever this plugin has script compatibility
     */
    private $scriptCompatibiliyMode = true;

    /**
     * @var String[] The list of Titles the plugin support or exact title name chack
     */
    private $titleSupport = array();

    /**
     * @var Boolean Relay support
     */
    private $relaySupport = true;

    /**
     * @var Whatever plugins support soft title name, or
     */
    private $softTitleSupport = true;

    /**
     * @var bool use environment name instead of title
     */
    private $enviAsTitle = true;

    /**
     * @param String The Id of the plugin the meta data is working for
     *
     * @return MetaData The meta data of the plugin.
     */
    public static function getInstance($pluginId = null)
    {

        $class = get_called_class();
        if (!isset(self::$_instances[$class])) {
            self::$_instances[$class] = new $class($pluginId);
        } /* else if ($plugin != null)
		  self::$_instances[$class]->setPlugin($pluginId); */

        return self::$_instances[$class];
    }

    /**
     *
     * @param String $pluginId The Id of the plugin the meta data is working for
     */
    final private function __construct($pluginId)
    {
        $this->pluginId = $pluginId;
        $this->confManager = \ManiaLivePlugins\eXpansion\Core\ConfigManager::getInstance();

        $this->onBeginLoad();
        $this->onEndLoad();
    }

    public function onBeginLoad()
    {

    }

    public function onEndLoad()
    {

    }

    /**
     * Registers a variable for this plugin. This will also add the variable to the configuration manager
     * So that the value is automatically saved
     *
     * @param \ManiaLivePlugins\eXpansion\Core\types\config\Variable $var The variable to register
     */
    public function registerVariable(Variable $var)
    {
        $this->variables[$var->getName()] = $var;
        $var->setPluginId($this->pluginId);
        $this->confManager->registerVariable($var, $this->pluginId);
    }

    /**
     * Add reference to the plugin
     *
     * @param $pluginId
     */
    public function setPlugin($pluginId)
    {
        $this->pluginId = $pluginId;
        foreach ($this->variables as $var) {
            $var->setPluginId($pluginId);
        }
    }

    /**
     * Removes reference to the plugin
     */
    public function unSetPlugin()
    {
        $this->pluginId = null;
    }

    /**
     * THe id of the plugin to whom the metadata is connected
     *
     * @return String
     */
    public function getPlugin()
    {
        return $this->pluginId;
    }

    /**
     * Returns the variable of that name, so that it value can be modified.
     *
     * @param String $name
     *
     * @return Variable
     */
    public function getVariable($name)
    {
        return isset($this->variables[$name]) ? $this->variables[$name] : null;
    }

    /**
     * Returns the variables of the plugin
     *
     * @return Variable[]
     */
    public function getAllVariables()
    {
        return $this->variables;
    }

    /**
     * The name is it should be seen in by players
     *
     * @return String The visual name of the plugin
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return String Describe what the plugin does
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function setName($name)
    {
        /* if (!$name instanceof String) {
          $name = new String($name, "", null);
          } */
        $this->name = $name;
    }

    /**
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    public function setDescription($description)
    {
        if (!$description instanceof TypeString) {
            $value = new TypeString("Description", $description, null);
            $value->setValue($description);
            $description = $value;

        }
        $this->description = $description;
    }

    /**
     * @param String $groups
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
        if (!is_array($groups)) {
            $this->groups = array($groups);
        }
    }

    /**
     * @return String
     */
    public function getGroups()
    {
        return $this->groups;
    }

    public function isCorePlugin()
    {
        return $this->core;
    }

    /**
     * Core plugins can't be unloaded from ManiaLive once they have been loaded.
     *
     * @param type $isCore Whatever or not this plugin is part of the core of eXpansion.
     */
    public function setIsCorePlugin($isCore)
    {
        $this->core = $isCore;
    }

    /**
     * Will force the plugin to be checked if it is compatible with the Game Mode
     * If it isn't the plugin will be unloaded From ManiaLive
     * If you change GameModes the plugin may be loaded again.
     *
     * @param int           $gameMode
     * @param string | null $scriptName
     */
    protected function addGameModeCompability($gameMode, $scriptName = null)
    {
        if ($scriptName == null || $gameMode != GameInfos::GAMEMODE_SCRIPT)
            $this->gameModeSupport[$gameMode] = true;
        else
            $this->gameModeSupport[$gameMode][$scriptName] = true;
    }

    public function getGameModeCompability()
    {
        return $this->gameModeSupport;
    }

    /**
     * Shall the script name match exactly our should it be just similar.
     * By default eXP will check for similarity, but that might change in the future
     * if scripters don't do attention to the script name conventions.
     *
     * @param Boolean $default
     */
    protected function setSoftScriptModeCheck($default = true)
    {
        $this->softScriptCompatibility = $default;
    }

    /**
     * By default if a game has legacy TimeAttack compatibility it will be considered that it
     * is compatible with all scripts that has TimeAttack in their name.
     * With this setting you can disable that.
     *
     * @param Boolean $default Whatever this plugin has script/legacy compatibility
     */
    protected function setScriptCompatibilityMode($default = true)
    {
        $this->scriptCompatibiliyMode = $default;
    }

    /**
     * Will force a check before the plugin is loaded to know if the plugin
     * is compatible with the current title.
     *
     * @param string $titleName
     */
    protected function addTitleSupport($titleName)
    {
        $this->titleSupport[] = $titleName;
    }

    /**
     * Shall the title name match exactly or should we just check for
     * similarity.
     *
     * @param bool $default
     */
    protected function setSoftTitleCheck($default = true)
    {
        $this->softTitleSupport = $default;
    }

    /**
     * Does this plugin support Relay Mode
     *
     * @return bool
     */
    protected function getRelaySupport()
    {
        return $this->relaySupport;
    }

    /**
     * Change relay mode support of the plugin
     *
     * @var bool $relaySupport Relay support
     */
    protected function setRelaySupport($relaySupport)
    {
        $this->relaySupport = $relaySupport;
    }

    /**
     * @param boolean $enviAsTitle
     */
    public function setEnviAsTitle($enviAsTitle)
    {
        $this->enviAsTitle = $enviAsTitle;
    }

    /**
     * @return boolean
     */
    public function getEnviAsTitle()
    {
        return $this->enviAsTitle;
    }

    /**
     * See if this plugin is compatible with the current game mode.
     * You can pass a game mod in parameter to check if it is compatible with that one
     *
     * @param int | null    The   game mode to check. If null will check for the current game mode
     * @param String | null The   script name to check. If null will check for the currrent.
     *                            For this to be checked game mode need to be on script mpde
     *
     * @return boolean IF is compatible with the game mode
     */
    public function checkGameCompatibility($gamemode = null, $scriptName = null)
    {

        if (!empty($this->gameModeSupport)) {
            //Get current state if need be
            if ($gamemode == null) {
                $storage = \ManiaLive\Data\Storage::getInstance();
                $gamemode = $storage->gameInfos->gameMode;
                if ($gamemode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT)
                    $scriptName = $storage->gameInfos->scriptName;
            }

            //Scrit mode special checking.
            if ($gamemode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT) {
                return $this->checkScriptGameModeCompatibility($scriptName);
            }

            return isset($this->gameModeSupport[$gamemode]) ? $this->gameModeSupport[$gamemode] : false;
        } else {
            //No rules for game compatibility, this plugin supports all modes
            return true;
        }
    }

    protected function checkScriptGameModeCompatibility($scriptName)
    {
        if ($this->scriptCompatibiliyMode) {
            $gmode = \ManiaLivePlugins\eXpansion\Core\Core::eXpGetScriptCompatibilityMode($scriptName);

            return isset($this->gameModeSupport[$gmode]) ? $this->gameModeSupport[$gmode] : false;
        }

        if (isset($this->gameModeSupport[0]) && !empty($this->gameModeSupport[0])) {
            foreach ($this->gameModeSupport[0] as $supportedScript) {
                if ($this->softScriptCompatibility && strpos($scriptName, $supportedScript) !== false) {
                    return true;
                } else if ($scriptName == $supportedScript) {
                    return true;
                }
            }
        }

        return false;
    }

    public function checkTitleCompatibility($titleName = null)
    {

        if ($titleName == null) {
            if ($this->enviAsTitle) {
                /**
                 * @var Storage $storage
                 */
                if ($this->checkTitleCompatibility(\ManiaLivePlugins\eXpansion\Helpers\Storage::getInstance()->version->titleId))
                    return true;
                $titleName = \ManiaLivePlugins\eXpansion\Helpers\Storage::getInstance()->simpleEnviTitle;
            } else {
                $titleName = \ManiaLivePlugins\eXpansion\Helpers\Storage::getInstance()->version->titleId;
            }
        }

        if (!empty($this->titleSupport)) {
            foreach ($this->titleSupport as $supportedTitle) {
                if ($this->softTitleSupport && strpos($titleName, $supportedTitle) !== false) {
                    return true;
                } else if ($titleName == $supportedTitle) {
                    return true;
                }
            }

            return false;
        }

        //No rules for game compatibility, this plugin supports all modes
        return true;
    }

    public function checkOtherCompatibility()
    {
        /** @var \ManiaLivePlugins\eXpansion\Helpers\Storage $expStorage */
        $expStorage = \ManiaLivePlugins\eXpansion\Helpers\Storage::getInstance();
        if ($expStorage->isRelay && !$this->getRelaySupport()) {
            return array('This plugin don\'t support Relay servers');
        }

        return array();
    }

    public function checkAll()
    {
        $errors = $this->checkOtherCompatibility();

        return $this->checkGameCompatibility() && $this->checkTitleCompatibility() && empty($errors);
    }

}

?>
