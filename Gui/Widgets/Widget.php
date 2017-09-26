<?php

namespace ManiaLivePlugins\eXpansion\Gui\Widgets;

use ManiaLib\Gui\Elements\Label;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\ConfigManager;
use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Gui\MetaData;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Widgets as WConfig;
use ManiaLivePlugins\eXpansion\Helpers\Maniascript;

/**
 * @abstract
 */
class Widget extends PlainWidget
{


    private $script;

    /** @var array */
    private $positions = array();

    /** @var array */
    private $widgetVisible = array();
    private $visibleLayerInit = "normal";

    /** @var \ManiaLive\Data\Storage */
    private $storage;

    private static $config;

    public $currentSettings = array();

    protected function onConstruct()
    {
        parent::onConstruct();

        $this->eXpOnBeginConstruct();
        $this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\WidgetScript");
        $this->registerScript($this->script);

        $this->storage = \ManiaLive\Data\Storage::getInstance();

        $this->eXpOnEndConstruct();
        $this->eXpLoadSettings();
    }

    /**
     * When the Widget is being constructed.
     */
    protected function eXpOnBeginConstruct()
    {
    }

    /**
     * When the construction of the widget has ended
     */
    protected function eXpOnEndConstruct()
    {
    }

    /**
     * When the settings of the widget has been loaded.
     */
    protected function eXpOnSettingsLoaded()
    {
    }

    private function eXpLoadSettings()
    {
        $this->currentSettings = array();

        $widgetName = str_replace(" ", "", $this->getName());

        $config = parse_ini_file(APP_ROOT.DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."widgets.ini", true,
            INI_SCANNER_TYPED);

        //Getting exact game mode
        $gameMode = $this->storage->gameInfos->gameMode;
        if ($gameMode == 0) {
            $gameMode = $this->storage->gameInfos->scriptName;
        }

        //Getting compatibility Game mode
        $compoMode = Gui::eXpGetCurrentCompatibilityGameMode();

        /**
         * @var \ManiaLivePlugins\eXpansion\Helpers\Storage $storage
         */
        $storage = \ManiaLivePlugins\eXpansion\Helpers\Storage::getInstance();

        foreach ($config as $widgetName2 => $values) {
            if ($widgetName2 == $widgetName) {
                foreach ($values as $data => $value) {
                    $modeVariable = explode(".", $data);
                    $outCompo = "TM";
                    $outMode = 0;

                    switch ($modeVariable[0]) {
                        case "ta":
                            $outMode = \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TIMEATTACK;
                            break;
                        case "rounds":
                            $outMode = \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_ROUNDS;
                            break;
                        case "team":
                            $outMode = \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_TEAM;
                            break;
                        case "laps":
                            $outMode = \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_LAPS;
                            break;
                        case "cup":
                            $outMode = \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_CUP;
                            break;
                        case "storm":
                            if ($storage->simpleEnviTitle == "SM") {
                                $this->currentSettings[$modeVariable[1]] = $value;
                            }
                            $outMode = -1;
                            break;
                        case "default":
                            $this->currentSettings[$modeVariable[1]] = $value;
                            break;
                    }

                    if ($outMode == $gameMode || $outMode == $compoMode) {
                        $this->currentSettings[$modeVariable[1]] = $value;
                    }
                }

            }
        }

        $this->autoSetPositions();
        $this->eXpOnSettingsLoaded();
    }


    protected function onDraw()
    {
        $this->script->setParam("name", $this->getName());
        $this->script->setParam("version", \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION);

        if ($this->storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT) {
            $this->script->setParam("gameMode", Gui::fixString($this->storage->gameInfos->scriptName));
        } else {
            $this->script->setParam("gameMode", $this->storage->gameInfos->gameMode);
        }

        parent::onDraw();
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);

    }

    protected function autoSetPositions()
    {
        $posX = $this->getParameter("posX");
        $posY = $this->getParameter("posY");

        if ($posX != null && $posY != null) {
            $this->setPosition($posX, $posY);
        }

        if ($this->getParameter('layer')) {
            $this->setLayer($this->getParameter("layer"));
        }

    }

    public function closeWindow()
    {
        $this->erase($this->getRecipient());
    }

    public function destroy()
    {
        unset($this->currentSettings);
        unset($this->widgetVisible);
        unset($this->positions);
        parent::destroy();
    }

    /**
     * disable moving for certaint axis
     *
     * @param string $axis accepts values: "x" or "y"
     */
    public function setDisableAxis($axis)
    {
        $this->axisDisabled = $axis;
    }

    /**
     * set a custom position for a gamemode
     *
     * @param string $gameMode
     * @param float $posX
     * @param float $posY
     */
    public function setPositionForGamemode($gameMode, $posX, $posY)
    {
        $this->positions[$gameMode] = array($posX, $posY);
    }

    public function getWidgetVisible()
    {
        if (isset($this->widgetVisible[$this->storage->gameInfos->gameMode])) {
            $value = $this->widgetVisible[$this->storage->gameInfos->gameMode];

            return $this->getBoolean($value);
        }

        return "True";
    }

    /**
     * Sets visibility of the widget according to gamemode
     *
     * @param string $gameMode
     * @param bool $value
     */
    public function setVisibilityForGamemode($gameMode, $value)
    {
        $this->widgetVisible[$gameMode] = $value;
    }

    public function setVisibleLayer($string)
    {
        $this->visibleLayerInit = $string;
    }

    public function getPosX()
    {
        if (isset($this->positions[$this->storage->gameInfos->gameMode])) {
            return $this->positions[$this->storage->gameInfos->gameMode][0];
        }

        return $this->posX;
    }

    public function getPosY()
    {
        if (isset($this->positions[$this->storage->gameInfos->gameMode])) {
            return $this->positions[$this->storage->gameInfos->gameMode][1];
        }

        return $this->posY;
    }

    public static function setParameter($widgetName, $name, $value)
    {
        if (!isset(self::$config[$widgetName])) {
            self::$config[$widgetName] = array();
        }

        self::$config[$widgetName][$name] = $value;
    }

    protected function getParameter($name)
    {
        return isset($this->currentSettings[$name]) ? $this->currentSettings[$name] : null;
    }
}
