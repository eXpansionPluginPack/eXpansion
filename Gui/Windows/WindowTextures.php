<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Config;

/**
 * @abstract
 */
abstract class WindowTextures extends \ManiaLive\Gui\Window {

    protected $_mainWindow;

    /**
     * @var \ManiaLive\Gui\Controls\Frame
     */
    protected $mainFrame;
    protected $_closebutton;
    protected $_closeAction;
    protected $_showCoords = 'False';
    protected $_windowFrame;
    private $_titlebar, $_titlebargrad, $_bg, $_bgborder;
    private $script;
    private $_scripts = array();
    private $dDeclares = "";
    private $scriptLib = "";
    private $wLoop = "";
    private $dIndex = 0;
    private $_name = "window";
    private $dicoMessages = array();
    private $calledScripts = array();
    private $xml;
    private $element = 6;

    protected function onConstruct() {
        parent::onConstruct();
        $config = Config::getInstance();
        $baseUrl = trim($config->uiTextureBase, "/");
        $windowUrl = $baseUrl . '/window/';
        $closeUrl = $baseUrl . '/closebutton/';

        $this->_closeAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'closeWindow'));

        $this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\WindowScript");

        $this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->_windowFrame->setPosZ(-0.1);
        $this->_windowFrame->setId("windowFrame");
        $this->_windowFrame->setScriptEvents(true);
        $this->_windowFrame->setAlign("left", "top");

        $this->_bgborder = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->sizeY);
        $this->_bgborder->setAlign("left", "top");
        $this->_bgborder->setId("MainWindow");
        $this->_bgborder->setScriptEvents(true);
        $this->_bgborder->setStyle("Bgs1InRace");
        $this->_bgborder->setSubStyle("BgColorContour");
        $this->_bgborder->setColorize($config->windowTitleBackgroundColor);
        $this->_windowFrame->addComponent($this->_bgborder);


        $this->_bg = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->sizeY);
        $this->_bg->setAlign("left", "top");
        $this->_bg->setId("MainWindow");
        $this->_bg->setScriptEvents(true);
        $this->_bg->setStyle("Bgs1InRace");
        $this->_bg->setSubStyle("BgWindow4");
        $this->_bg->setColorize($config->windowBackgroundColor);
        $this->_bg->setOpacity(0.9);
        $this->_windowFrame->addComponent($this->_bg);

        $this->_titlebar = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->element);
        $this->_titlebar->setAlign("left", "bottom");
        $this->_titlebar->setScriptEvents(true);
        $this->_titlebar->setStyle('Bgs1InRace');
        $this->_titlebar->setSubStyle('BgWindow4');
        $this->_titlebar->setOpacity(0.9);
        $this->_titlebar->setModulateColor($config->windowTitleBackgroundColor);
        $this->_windowFrame->addComponent($this->_titlebar);

        $this->_titlebargrad = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->element);
        $this->_titlebargrad->setId("Titlebar");
        $this->_titlebargrad->setAlign("left", "bottom");
        $this->_titlebargrad->setScriptEvents(true);
        $this->_titlebargrad->setStyle('BgsPlayerCard');
        $this->_titlebargrad->setSubStyle('BgRacePlayerLine');
        $this->_titlebargrad->setOpacity(0.75);
        $this->_windowFrame->addComponent($this->_titlebargrad);

        $this->_title = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel(60, 4);
        $this->_title->setId("TitlebarText");
        $this->_title->setAlign("center", "bottom");
        $this->_title->setStyle("TextRaceMessageBig");
        $this->_title->setTextColor($config->windowTitleColor);
        $this->_title->setTextSize(1);
        //$this->_title->setTextEmboss();
        $this->_windowFrame->addComponent($this->_title);

        $this->_closebutton = new \ManiaLib\Gui\Elements\Quad(6, 6);
        $this->_closebutton->setId("Close");
        $this->_closebutton->setAlign('right', 'bottom');
        $this->_closebutton->setStyle("Icons64x64_1");
        $this->_closebutton->setSubStyle("Close");
        $this->_closebutton->setScriptEvents(true);
        $this->_windowFrame->addComponent($this->_closebutton);


        $this->mainFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->mainFrame->setPosY(-7);
        $this->_windowFrame->addComponent($this->mainFrame);

        $this->addComponent($this->_windowFrame);


        $this->xml = new \ManiaLive\Gui\Elements\Xml();
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $element = $this->element;
        $x = $this->sizeX;
        $y = $this->sizeY;

        $this->_windowFrame->setSize($x, $y);

        $this->_bgborder->setSize($x + 13, $y + 13);
        $this->_bgborder->setPosition(-6.5, 6.5);

        $this->_bg->setSize($x + 12, $y + 6);
        $this->_bg->setPosition(-6, 0);

        $this->_titlebar->setSize($x + 12, $element + 0.5);
        $this->_titlebar->setPosition(-6, -0.5);

        $this->_titlebargrad->setSize($x + 24, $element + 0.5);
        $this->_titlebargrad->setPosition(-12, -0.5);

        $this->_title->setPosition(($x + 6) / 2, 1);
        $this->_closebutton->setPosition($x + 5, 0);
    }

    private function detectElements($components) {
        $buttonScript = null;
        foreach ($components as $index => $component) {
            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel) {
                $this->dicoMessages[$component->getTextid()] = $component->getMessages();
            }

            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\LinePlotter) {
                $this->addScriptToMain($component->getScript());
            }

            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer) {
                $script = $component->getScript();

                $isset = !array_key_exists($script->getRelPath(), $this->calledScripts);

                if ($isset) {
                    $this->addScriptToLib($script->getlibScript($this, $component));
                }

                if ($isset || $script->multiply()) {
                    $this->calledScripts[$script->getRelPath()] = $script;

                    $dec = $script->getDeclarationScript($this, $component);
                    $this->addScriptToMain($dec);
                    $this->addScriptToWhile($script->getWhileLoopScript($this, $component));
                }
            }

            if ($component instanceof \ManiaLive\Gui\Container) {
                $this->detectElements($component->getComponents());
            }
        }
    }

    protected function onDraw() {
        parent::onDraw();

        $this->nbButton = 0;
        $this->dIndex = 0;
        $this->dDeclares = "";
        $this->scriptLib = "";
        $this->wLoop = "";
        $this->calledScripts = array();

        $this->detectElements($this->getComponents());

        foreach ($this->calledScripts as $script) {
            $this->addScriptToMain($script->getEndScript($this));
            $script->reset();
        }

        foreach ($this->_scripts as $script) {
            $this->addScriptToMain($script->getDeclarationScript($this, $this));
            $this->addScriptToLib($script->getlibScript($this, $this));
            $this->addScriptToWhile($script->getWhileLoopScript($this, $this));
        }

        $this->calledScripts = array();

        $this->script->setParam("name", $this->_name);
        $this->script->setParam("dDeclares", $this->dDeclares);
        $this->script->setParam("scriptLib", $this->scriptLib);
        $this->script->setParam("wLoop", $this->wLoop);
        $this->script->setParam("closeAction", $this->_closeAction);
        $this->script->setParam("disableAnimations", Config::getInstance()->disableAnimations ? "True" : "False");
        $this->script->setParam("version", \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION);
        $reset = "False";
        if (DEBUG) $reset = "True";

        $this->script->setParam("forceReset", $reset);


        $this->removeComponent($this->xml);
        $this->xml->setContent($this->script->getDeclarationScript($this, $this->xml));

        $this->addComponent($this->xml);

        $dico = new \ManiaLivePlugins\eXpansion\Gui\Elements\Dico($this->dicoMessages);
        \ManiaLive\Gui\Manialinks::appendXML($dico->getXml());
    }

    function setTitle($text, $parameter = "") {
        $this->_name = $text;
        $this->_title->setText($text . " " . $parameter);
        // $this->_title2->setText($text . " " . $parameter);
    }

    function closeWindow() {
        $this->erase($this->getRecipient());
    }

    function addScriptToMain($script) {
        $this->dDeclares .= $script;
    }

    function addScriptToWhile($script) {
        $this->wLoop .= $script;
    }

    function addScriptToLib($script) {
        $this->scriptLib .= $script;
    }

    function destroy() {
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->_closeAction);
        $this->_windowFrame->clearComponents();
        $this->_windowFrame->destroy();
        $this->mainFrame->destroy();

        $this->destroyComponents();
        $this->_closeAction = null;

        parent::destroy();

// echo "window: #";
        foreach ($this as $index => $value) {
            if (\is_object($value)) {

                if ($value instanceof \ManiaLive\Gui\Containable || $value instanceof \ManiaLive\Gui\Container) {
                    //			echo "!";
                    $value->destroyComponents();
                    $value->destroy();
                    unset($this->$index);
                    continue;
                }
                if ($value instanceof \ManiaLive\Gui\Control) {
                    //				echo "*";
                    $value->destroy();
                    unset($this->$index);
                    continue;
                }

                unset($this->$index);
            }
            else {
                //			echo ".";
                unset($this->$index);
            }
        }
        //	echo "\n";
    }

    /**
     * Registers a script to widget instance
     * @param \ManiaLivePlugins\eXpansion\Gui\Structures\Script $script
     */
    public function registerScript(\ManiaLivePlugins\eXpansion\Gui\Structures\Script $script) {
        $this->_scripts[] = $script;
    }

}

?>
