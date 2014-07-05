<?php

namespace ManiaLivePlugins\eXpansion\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Gui\Widgets as WConfig;

/**
 * @abstract
 */
class Widget extends PlainWidget
{

    private $move;
    private $axisDisabled = "";
    private $script;

    /** @var Array() */
    private $positions = array();

    /** @var Array() */
    private $widgetVisible = array();
    private $visibleLayerInit = "normal";

    /** @var \ManiaLive\Data\Storage */
    private $storage;
    private static $config;
    public $currentSettings = array();

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->exp_onBeginConstruct();
        $this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\WidgetScript");
        $this->script->setParam('disablePersonalHud', \ManiaLivePlugins\eXpansion\Gui\Config::getInstance()->disablePersonalHud ? 'True' : 'False');
        $this->registerScript($this->script);

        $this->move = new \ManiaLib\Gui\Elements\Quad(45, 7);
        $this->move->setStyle("Icons128x128_Blink");
        $this->move->setSubStyle("ShareBlink");
        $this->move->setScriptEvents();
        $this->move->setId("enableMove");
        $this->addComponent($this->move);
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->xml = new \ManiaLive\Gui\Elements\Xml();
        $this->exp_onEndConstruct();
        $this->exp_loadSettings();
    }

    /**
     * Begin construct
     */
    protected function exp_onBeginConstruct()
    {

    }

    protected function exp_onEndConstruct()
    {

    }

    protected function exp_onSettingsLoaded()
    {

    }

    private function exp_loadSettings()
    {
        $widgetName = str_replace(" ", "", $this->getName());

        if (isset(self::$config[$widgetName])) {

            //Getting exact game mode
            $gameMode = $this->storage->gameInfos->gameMode;
            if ($gameMode == 0)
                $gameMode = $this->storage->gameInfos->scriptName;

            //Getting compatibility Game mode
            $compoMode = Gui::exp_getCurrentCompatibilityGameMode();

            /**
             * @var \ManiaLivePlugins\eXpansion\Helpers\Storage $storage
             */
            $storage = \ManiaLivePlugins\eXpansion\Helpers\Storage::getInstance();

            //Getting full title id
            $titleid = $storage->version->titleId;

            //Getting environnment based simple title id
            $enviTitle = $storage->simpleEnviTitle;


            $this->currentSettings = array();
            foreach (self::$config[$widgetName] as $name => $values) {
                if (isset($values[$gameMode])) {
                    $this->currentSettings[$name] = $values[$gameMode];
                } else if (isset($values[$compoMode])) {
                    $this->currentSettings[$name] = $values[$compoMode];
                } else if (isset($values[$titleid])) {
                    $this->currentSettings[$name] = $values[$titleid];
                } else if (isset($values[$enviTitle])) {
                    $this->currentSettings[$name] = $values[$enviTitle];
                } else if (isset($values[WConfig::config_default])) {
                    $this->currentSettings[$name] = $values[WConfig::config_default];
                }
            }
        }

        $this->autoSetPositions();
        $this->exp_onSettingsLoaded();
    }

    /**
     * formats number for maniascript
     * @param numeric $number
     * @return string
     */
    private function getNumber($number)
    {
        return number_format((float)$number, 2, '.', '');
    }

    private function getBoolean($boolean)
    {
        if ($boolean)
            return "True";
        return "False";
    }

    protected function onDraw()
    {

        $this->script->setParam("name", $this->getName());
        $this->script->setParam("axisDisabled", $this->axisDisabled);
        $this->script->setParam("version", \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION);

        if ($this->storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT) {
            $this->script->setParam("gameMode", Gui::fixString($this->storage->gameInfos->scriptName));
        } else {
            $this->script->setParam("gameMode", $this->storage->gameInfos->gameMode);
        }

        $this->script->setParam("visibleLayerInit", $this->visibleLayerInit);
        $this->script->setParam("forceReset", $this->getBoolean(DEBUG));

        parent::onDraw();

    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->move->setSize($this->getSizeX(), $this->getSizeY());
    }

    protected function autoSetPositions()
    {
        $posX = $this->getParameter("posX");
        $posY = $this->getParameter("posY");
        if ($posX != null & $posY != null) {
            $this->setPosition($posX, $posY);
        }
    }

    function closeWindow()
    {
        $this->erase($this->getRecipient());
    }

    function destroy()
    {
        unset($this->currentSettings);
        unset($this->widgetVisible);
        unset($this->positions);
        parent::destroy();
    }

    /**
     * disable moving for certaint axis
     * @param string $axis accepts values: "x" or "y"
     */
    function setDisableAxis($axis)
    {
        $this->axisDisabled = $axis;
    }

    /**
     * set a custom position for a gamemode
     * @param string $gameMode
     * @param float $posX
     * @param float $posY
     */
    function setPositionForGamemode($gameMode, $posX, $posY)
    {
        $this->positions[$gameMode] = array($posX, $posY);
    }

    function getWidgetVisible()
    {
        if (isset($this->widgetVisible[$this->storage->gameInfos->gameMode])) {
            $value = $this->widgetVisible[$this->storage->gameInfos->gameMode];
            return $this->getBoolean($value);
        }
        return "True";
    }

    /**
     * Sets visibility of the widget according to gamemode
     * @param string $gameMode
     * @param bool $value
     */
    function setVisibilityForGamemode($gameMode, $value)
    {
        $this->widgetVisible[$gameMode] = $value;
    }

    function setVisibleLayer($string)
    {
        $this->visibleLayerInit = $string;
    }

    function getPosX()
    {
        if (isset($this->positions[$this->storage->gameInfos->gameMode])) {
            return $this->positions[$this->storage->gameInfos->gameMode][0];
        }

        return $this->posX;
    }

    function getPosY()
    {
        if (isset($this->positions[$this->storage->gameInfos->gameMode])) {
            return $this->positions[$this->storage->gameInfos->gameMode][1];
        }
        return $this->posY;
    }

    public static function setParameter($widgetName, $name, $value)
    {
        if (!isset(self::$config[$widgetName]))
            self::$config[$widgetName] = array();

        self::$config[$widgetName][$name] = $value;
    }

    protected function getParameter($name)
    {
        return isset($this->currentSettings[$name]) ? $this->currentSettings[$name] : null;
    }

}

?>
