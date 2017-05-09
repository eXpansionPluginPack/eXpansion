<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Config;

/**
 * @abstract
 */
abstract class WindowTextures extends \ManiaLive\Gui\Window
{
    protected $_mainWindow;

    /**
     * @var \ManiaLive\Gui\Controls\Frame
     */
    protected $mainFrame;
    protected $_closebutton;
    protected $_closeAction;
    protected $_showCoords = 'False';

    /** @var  Frame */
    protected $_windowFrame;
    private $_titlebar;
    private $_bg;
    private $_bgeff;
    private $_topcenter;
    private $_bottomcenter;
    private $_windowBorder;
    private $_title;
    private $_titleBar;
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
    private $element = 14;

    protected function onConstruct()
    {
        parent::onConstruct();
        /** @var Config $config */
        $config = Config::getInstance();

        $this->_closeAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'closeWindow'));

        $this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\\Scripts\\WindowScript");


        $this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->_windowFrame->setPosZ(-0.1);
        $this->_windowFrame->setId("windowFrame");
        $this->_windowFrame->setScriptEvents(true);
        $this->_windowFrame->setAlign("left", "top");

        $this->_windowBorder = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->element);
        $this->_windowBorder->setAlign("left", "top");
        $this->_windowBorder->setStyle("Bgs1InRace");
        $this->_windowBorder->setSubStyle("BgButtonShadow");
        $this->_windowBorder->setOpacity(0.6);
        $this->_windowFrame->addComponent($this->_windowBorder);

        $this->_bg = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->sizeY);
        $this->_bg->setAlign("left", "top");
        $this->_bg->setModulateColor($config->windowBackgroundColor);
        $this->_bg->setStyle("Bgs1");
        $this->_bg->setSubStyle('BgWindow4');
        $this->_bg->setOpacity(0.9);
        $this->_bg->setScriptEvents(true);
        $this->_bg->setId("MainWindow");
        $this->_windowFrame->addComponent($this->_bg);


        $this->_bgeff = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->sizeY);
        $this->_bgeff->setAlign("left", "top");
        $this->_bgeff->setImage('file://Media/Manialinks/TrackMania/Window/tm-structure-background.png', true);
        $this->_bgeff->setAlign("left", "top");

        $this->_bgeff->setColorize($config->windowBackgroundColor);
        //    $this->_windowFrame->addComponent($this->_bgeff);

        $this->_bottomcenter = new \ManiaLib\Gui\Elements\Quad($this->element, $this->element);
        $this->_bottomcenter->setAlign("left", "top");
        $this->_bottomcenter->setImage("file://Media/Manialinks/Common/Home/maniahome-title.dds", true);
        $this->_bottomcenter->setAttribute("rot", -180);
        $this->_bottomcenter->setAlign("right", "bottom");
        $this->_bottomcenter->setId("MainWindow");
        $this->_bottomcenter->setOpacity(0.9);
        //      $this->_windowFrame->addComponent($this->_bottomcenter);



        $this->_topcenter = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->element);
        $this->_topcenter->setAlign("left", "top");
        $this->_topcenter->setOpacity(0.8);
        $this->_topcenter->setBgcolor($config->windowTitleBackgroundColor);
//        $this->_windowFrame->addComponent($this->_topcenter);



        $this->_titlebar = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->element);
        $this->_titlebar->setId("Titlebar");
        $this->_titlebar->setAlign("left", "top");
        $this->_titlebar->setScriptEvents();
        $this->_titlebar->setStyle("Bgs1");
        $this->_titlebar->setSubStyle('BgWindow4');
        $this->_titlebar->setOpacity(0.7);
        $this->_titlebar->setModulateColor($config->windowTitleBackgroundColor);
        $this->_windowFrame->addComponent($this->_titlebar);

        $this->_title = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel(60, 4);
        $this->_title->setId("Titlebar");
        $this->_title->setAlign("left", "center2");
        $this->_title->setAttribute("textfont", "RajdhaniMono");
        $this->_title->setTextColor($config->windowTitleColor);
        $this->_title->setTextSize(1.5);
        $this->_title->setScriptEvents();
        $this->_windowFrame->addComponent($this->_title);

        $this->_closebutton = new \ManiaLib\Gui\Elements\Label(9.6, 4);
        $this->_closebutton->setId("Close");
        $this->_closebutton->setAlign('center', 'center2');
        $this->_closebutton->setScriptEvents(true);
        $this->_closebutton->setTextcolor("fff");
        $this->_closebutton->setFocusareacolor1($config->windowTitleBackgroundColor);
        $this->_closebutton->setFocusareacolor2("D30707");
        $this->_closebutton->setAttribute("textfont", "OswaldMono");
        $this->_closebutton->setTextSize(2);
        $this->_closebutton->setText("✖");
        $this->_windowFrame->addComponent($this->_closebutton);

        $this->mainFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->mainFrame->setPosY(-7);
        $this->_windowFrame->addComponent($this->mainFrame);

        $this->addComponent($this->_windowFrame);


        $this->xml = new \ManiaLive\Gui\Elements\Xml();
    }

    protected function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $x = $this->sizeX;
        $y = $this->sizeY;
        $o = 6;

        $this->_windowFrame->setSize($x, $y);

        $this->_bg->setSize($x + $o * 2, $y + $o);
        $this->_bg->setPosition(-$o, 0);

        $this->_windowBorder->setSize($x + $o * 2+10, $y + $o * 2+10);
        $this->_windowBorder->setPosition(-$o-5, $o+5);

        $this->_titlebar->setSize($x + $o * 2, $o);
        $this->_titlebar->setPosition(-$o, $o);

        $this->_topcenter->setSize($x + $o * 2, $o);
        $this->_topcenter->setPosition(-$o, $o);

        $this->_bottomcenter->setSize($x + $o * 2, $o);
        $this->_bottomcenter->setPosition(-$o, -($y + $o - 2));

        $this->_title->setPosition(0, $o/2);
        $this->_closebutton->setPosition($x, $o/2);
    }

    private function detectElements($components)
    {
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

    protected function onDraw()
    {
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
        if (DEBUG) {
            $reset = "True";
        }

        $this->script->setParam("forceReset", $reset);


        $this->removeComponent($this->xml);
        $this->xml->setContent($this->script->getDeclarationScript($this, $this->xml));

        $this->addComponent($this->xml);

        $dico = new \ManiaLivePlugins\eXpansion\Gui\Elements\Dico($this->dicoMessages);
        \ManiaLive\Gui\Manialinks::appendXML($dico->getXml());
    }

    public function setTitle($text, $parameter = "")
    {
        $this->_name = $text;
        $this->_title->setText($text . " " . $parameter);
    }

    public function closeWindow()
    {
        $this->erase($this->getRecipient());
    }

    public function addScriptToMain($script)
    {
        $this->dDeclares .= $script;
    }

    public function addScriptToWhile($script)
    {
        $this->wLoop .= $script;
    }

    public function addScriptToLib($script)
    {
        $this->scriptLib .= $script;
    }

    public function destroy()
    {
        \ManiaLive\Gui\ActionHandler::getInstance()->deleteAction($this->_closeAction);
        $this->_windowFrame->clearComponents();
        $this->_windowFrame->destroy();
        $this->mainFrame->destroy();

        $this->destroyComponents();
        $this->_closeAction = null;

        foreach ($this as $index => $value) {
            if (\is_object($value)) {

                if ($value instanceof \ManiaLive\Gui\Containable || $value instanceof \ManiaLive\Gui\Container) {
                    $value->destroyComponents();
                    $value->destroy();
                    unset($this->$index);
                    continue;
                }
                if ($value instanceof \ManiaLive\Gui\Control) {
                    $value->destroy();
                    unset($this->$index);
                    continue;
                }
                unset($this->$index);
            } else {
                // base window class still needs this at this point...
                if ($index != "id") {
                    unset($this->$index);
                }
            }
        }

        parent::destroy();
    }

    /**
     * Registers a script to widget instance
     *
     * @param \ManiaLivePlugins\eXpansion\Gui\Structures\Script $script
     */
    public function registerScript(\ManiaLivePlugins\eXpansion\Gui\Structures\Script $script)
    {
        $this->_scripts[] = $script;
    }
}
