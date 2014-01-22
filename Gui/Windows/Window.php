<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Config;

/**
 * @abstract
 */
class Window extends \ManiaLive\Gui\Window {

    protected $_titlebar, $_titlebar2;
    protected $_title, $title2;
    protected $_mainWindow;
    protected $mainFrame;
    protected $_mainText;
    protected $_closebutton;
    protected $_minbutton;
    protected $_closeAction;
    protected $_showCoords = 'False';
    protected $_windowFrame;
    private $script;
    protected $bg;
    private $dDeclares = "";
    private $scriptLib = "";
    private $wLoop = "";
    private $dIndex = 0;
    private $_name = "window";
    private $style;

    protected function onConstruct() {
        parent::onConstruct();
        $config = Config::getInstance();
        $this->_closeAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'closeWindow'));

        $this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\WindowScript");

        $this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->_windowFrame->setScriptEvents(true);
        $this->_windowFrame->setAlign("left", "top");

        $this->style = new \ManiaLib\Gui\Elements\Format();
        $this->style->setStyle("TextCardRaceRank");
        $this->style->setTextSize(0.9);
        $this->style->setTextColor("222");
        $this->style->setAttribute("FocusAreaColor1", "000");
        $this->style->setAttribute("FocusAreaColor2", "0af");

        $this->addComponent($this->style);

        $this->_mainWindow = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->sizeY);
        $this->_mainWindow->setId("MainWindow");
        $this->_mainWindow->setStyle("Bgs1");
        $this->_mainWindow->setSubStyle(\ManiaLib\Gui\Elements\Bgs1::BgTitlePage);
        $this->_mainWindow->setScriptEvents(true);
        $this->_windowFrame->addComponent($this->_mainWindow);

        $this->bg = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->sizeY);
        $this->bg->setStyle("Bgs1InRace");
        $this->bg->setSubStyle(\ManiaLib\Gui\Elements\Bgs1InRace::BgTitleGlow);
        $this->_windowFrame->addComponent($this->bg);

        $this->_titlebar2 = new \ManiaLib\Gui\Elements\Quad($this->sizeX, 6);
        $this->_titlebar2->setStyle("Bgs1");
        $this->_titlebar2->setSubStyle(\ManiaLib\Gui\Elements\Bgs1::BgTitleGlow);
        $this->_windowFrame->addComponent($this->_titlebar2);

        $this->_titlebar = new \ManiaLib\Gui\Elements\Quad($this->sizeX, 6);
        $this->_titlebar->setId("Titlebar");
        $this->_titlebar->setStyle("Bgs1InRace");
        $this->_titlebar->setSubStyle(\ManiaLib\Gui\Elements\Bgs1InRace::BgPager);
        $this->_titlebar->setScriptEvents(true);
        $this->_windowFrame->addComponent($this->_titlebar);



        $this->_title = new \ManiaLib\Gui\Elements\Label(60, 4);
        $this->_title->setId("TitlebarText");
        $this->_title->setStyle("TextRaceMessage");
        $this->_title->setTextColor('3af');
        $this->_title->setTextSize(1);
        $this->_title->setTextEmboss();
        
        $this->_windowFrame->addComponent($this->_title);

        $this->_title2 = new \ManiaLib\Gui\Elements\Label(60, 4);
        $this->_title2->setId("TitlebarText");
        //$this->_title2->setStyle("TextRankingsBig");
        $this->_title2->setTextColor('fffd');
        $this->_title2->setTextSize(2);        
        //$this->_windowFrame->addComponent($this->_title2);

        $this->_closebutton = new \ManiaLib\Gui\Elements\Quad(5, 4);
        $this->_closebutton->setAlign('center', 'center2');
        $this->_closebutton->setStyle("Icons64x64_1");
        $this->_closebutton->setSubStyle("QuitRace");

        /*   $this->_closebutton->setStyle("TextChallengeNameMedium");
          $this->_closebutton->setScriptEvents(true);
          $this->_closebutton->setFocusAreaColor1("fff");
          $this->_closebutton->setFocusAreaColor2("000");
          $this->_closebutton->setId("Close");
          $this->_closebutton->setText(' x ');
          $this->_closebutton->setTextColor('000');
          $this->_closebutton->setTextSize(1); */
        $this->_closebutton->setScriptEvents(true);
        $this->_closebutton->setAction($this->_closeAction);
        $this->_windowFrame->addComponent($this->_closebutton);

        $this->_minbutton = new \ManiaLib\Gui\Elements\Label(7, 3);
        $this->_minbutton->setAlign('center', 'top');
        $this->_minbutton->setStyle("TextChallengeNameMedium");
        $this->_minbutton->setScriptEvents(true);
        $this->_minbutton->setText('$000-');

        $this->_minbutton->setFocusAreaColor1("fff0");
        $this->_minbutton->setFocusAreaColor2("0000");
        $this->_minbutton->setScriptEvents(true);
        $this->_minbutton->setId("Minimize");
        // $this->_windowFrame->addComponent($this->_minbutton);

        $this->mainFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->mainFrame->setPosY(-3);
        $this->_windowFrame->addComponent($this->mainFrame);

        $this->addComponent($this->_windowFrame);
        $this->xml = new \ManiaLive\Gui\Elements\Xml();
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->_windowFrame->setSize($this->sizeX, $this->sizeY);

        $this->_mainWindow->setSize($this->sizeX + 0.6, $this->sizeY + 6);
        $this->_mainWindow->setPosY(5.5);
        
        $this->bg->setSize($this->sizeX + 0.6, $this->sizeY + 6);
        $this->bg->setPosY(5.5);

        $this->_title->setSize($this->sizeX, 3.5);
        $this->_title->setPosition(($this->_title->sizeX / 2), 4.5);
        $this->_title->setHalign("center");

        $this->_title2->setSize($this->sizeX, 4);
        $this->_title2->setPosition(($this->_title->sizeX / 2), 4.5);
        $this->_title2->setHalign("center");

        $this->_titlebar->setPosY(5.5);
        $this->_titlebar->setSize($this->sizeX + 0.5, 4.5);

        $this->_titlebar2->setPosY(5.5);
        $this->_titlebar2->setSize($this->sizeX + 0.5, 4.5);


        $this->_closebutton->setSize(5, 5);
        $this->_closebutton->setPosition($this->sizeX - 2, 3.2);

        $this->_minbutton->setSize(5, 5);
        $this->_minbutton->setPosition($this->sizeX - 8, 5);

        $this->mainFrame->setSize($this->sizeX - 4, $this->sizeY - 8);
        $this->mainFrame->setPosition(2, -2);
    }

    private $calledScripts = array();

    private function detectElements($components) {
        $buttonScript = null;
        foreach ($components as $index => $component) {
            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\LinePlotter) {
                $this->addScriptToMain($component->getScript());
            }

            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\Pager) {
                $this->addScriptToMain($component->getScriptDeclares());
                $this->addScriptToWhile($component->getScriptMainLoop());
            }


            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Elements\Dropdown) {
                $this->addScriptToMain($component->getScriptDeclares($this->dIndex));
                $this->addScriptToLoop($component->getScriptMainLoop($this->dIndex));
                $this->dIndex++;
            }

            if ($component instanceof \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer) {
                $script = $component->getScript();

                $isset = !isset($this->calledScripts[$script->getRelPath()]);

                if ($isset || $script->multiply()) {
                    $this->calledScripts[$script->getRelPath()] = $script;

                    $dec = $script->getDeclarationScript($this, $component);
                    $this->addScriptToMain($dec);
                    $this->addScriptToLib($script->getlibScript($this, $component));
                    $this->addScriptToWhile($script->getWhileLoopScript($this, $component));
                }
            }

            if ($component instanceof \ManiaLive\Gui\Container) {
                $this->detectElements($component->getComponents());
            }
        }
    }

    protected function onDraw() {
        $this->nbButton = 0;
        $this->dIndex = 0;
        $this->dDeclares = "";

        $this->scriptLib = "";
        $this->calledScripts = array();

        $this->detectElements($this->getComponents());

        foreach ($this->calledScripts as $script) {
            $this->addScriptToMain($script->getEndScript($this));
        }

        $this->calledScripts = array();

        $this->script->setParam("showCoords", $this->_showCoords);
        $this->script->setParam("name", $this->_name);
        $this->script->setParam("dDeclares", $this->dDeclares);
        $this->script->setParam("scriptLib", $this->scriptLib);
        $this->script->setParam("wLoop", $this->wLoop);

        $this->removeComponent($this->xml);
        $this->xml->setContent($this->script->getDeclarationScript($this, $this->xml));

        $this->addComponent($this->xml);
        parent::onDraw();
    }

    function setDebug($bool) {
        if ($bool) {
            $this->_showCoords = 'True';
        }
    }

    function setText($text) {
        $this->_mainText->setText($text);
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

        $this->clearComponents();
        $this->_closeAction = null;
        parent::destroy();
    }

}

?>
