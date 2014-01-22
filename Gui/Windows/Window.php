<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Config;

/**
 * @abstract
 */
class Window extends \ManiaLive\Gui\Window {

    protected $_titlebar;
    protected $_title;
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

    protected function onConstruct() {
        parent::onConstruct();
        $config = Config::getInstance();
        $this->_closeAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'closeWindow'));

        $this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\WindowScript");
        
        $this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->_windowFrame->setScriptEvents(true);
        $this->_windowFrame->setAlign("left", "top");

        $this->_mainWindow = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->sizeY);
        $this->_mainWindow->setId("MainWindow");
        $this->_mainWindow->setStyle("Bgs1");
        $this->_mainWindow->setSubStyle(\ManiaLib\Gui\Elements\Bgs1::BgWindow2);
        $this->_mainWindow->setScriptEvents(true);
        $this->_windowFrame->addComponent($this->_mainWindow);

        $this->bg = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->sizeY);
        $this->bg->setStyle("Bgs1");
        $this->bg->setSubStyle(\ManiaLib\Gui\Elements\Bgs1::BgWindow2);

        $this->_windowFrame->addComponent($this->bg);

        $this->_titlebar = new \ManiaLib\Gui\Elements\Quad($this->sizeX, 6);
        $this->_titlebar->setId("Titlebar");
        $this->_titlebar->setStyle("Bgs1");
        $this->_titlebar->setSubStyle("ProgressBar");
        // $this->_titlebar->setBgcolor("6bf");
        //$this->_titlebar->setImage($config->windowTitlebar);
        $this->_titlebar->setScriptEvents(true);
        $this->_windowFrame->addComponent($this->_titlebar);


        $this->_title = new \ManiaLib\Gui\Elements\Label(60, 4);
        $this->_title->setId("TitlebarText");
        $this->_title->setStyle("TextStaticSmall");
        $this->_title->setTextColor('000');
        $this->_title->setTextSize(1);
        $this->_windowFrame->addComponent($this->_title);

        $this->_closebutton = new \ManiaLib\Gui\Elements\Quad(7, 3);
        $this->_closebutton->setAlign('center', 'top');
        $this->_closebutton->setStyle("Icons64x64_1");
        $this->_closebutton->setSubStyle("Close");

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

        $this->_mainWindow->setSize($this->sizeX + 0.6, $this->sizeY + 2);
        $this->_mainWindow->setPosY(1);
        $this->bg->setSize($this->sizeX + 0.6, $this->sizeY + 2);
        $this->bg->setPosY(1);

        $this->_title->setSize($this->sizeX, 4);
        $this->_title->setPosition(($this->_title->sizeX / 2), 3.5);
        $this->_title->setHalign("center");

        $this->_titlebar->setPosX(-4);
        $this->_titlebar->setPosY(6);
        $this->_titlebar->setSize($this->sizeX + 8, 7);

        $this->_closebutton->setSize(5, 5);
        $this->_closebutton->setPosition($this->sizeX - 3, 5.5);

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
            
            if($component instanceof \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer){
                $script = $component->getScript();

                if(!isset($this->calledScripts[$script->getRelPath()]) || $script->multiply()){
                    $this->calledScripts[$script->getRelPath()] = $script;
                    
                    $dec = $script->getDeclarationScript($this->id, $component);
                    $this->addScriptToMain($dec);
                    $this->addScriptToLoop($script->getMainLoopScript($this->id, $component));
                    $this->addScriptToWhile($script->getWhileLoopScript($this->id, $component));
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

        foreach($this->calledScripts as $script){
            $this->addScriptToMain($script->getEndScript());
        }
        
        $this->calledScripts = array();

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
