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
    private $_scripts = array();
    protected $_bg;
    private $dDeclares = "";
    private $scriptLib = "";
    private $wLoop = "";
    private $dIndex = 0;
    private $_name = "window";
    private $style;
    private $dicoMessages = array();

    protected function onConstruct() {
	parent::onConstruct();
	$config = Config::getInstance();
	$this->_closeAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction(array($this, 'closeWindow'));

	$this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\WindowScript");

	$this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
	$this->_windowFrame->setId("windowFrame");
	$this->_windowFrame->setScriptEvents(true);
	$this->_windowFrame->setAlign("left", "top");

	$this->style = new \ManiaLib\Gui\Elements\Format();
	$this->style->setAttribute("textsize", "0.9");
	$this->style->setAttribute("style", "TextCardRaceRank");
	$this->style->setAttribute("textcolor", "f00");
	$this->style->setAttribute("focusareacolor1", "09a");
	$this->style->setAttribute("focusareacolor2", "fff");
	//$this->addComponent($this->style); 




	$this->_mainWindow = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->sizeY);
	$this->_mainWindow->setId("MainWindow");
	$this->_mainWindow->setStyle("UIConstruction_Buttons");
	$this->_mainWindow->setSubStyle("BgTools");
//	$this->_mainWindow->setStyle("Bgs1");
	//$this->_mainWindow->setSubStyle("BgWindow4");
	$this->_mainWindow->setOpacity(0.93);
	//$this->_mainWindow->setBgColor("eee");
	$this->_mainWindow->setScriptEvents(true);
	$this->_windowFrame->addComponent($this->_mainWindow);



	$this->_titlebar = new \ManiaLib\Gui\Elements\Quad($this->sizeX, 6);
	$this->_titlebar->setId("Titlebar");
	$this->_titlebar->setStyle("Bgs1");
	$this->_titlebar->setSubStyle("BgEmpty");
	$this->_titlebar->setColorize("3af");
	$this->_titlebar->setAlign("left", "top");
	$this->_titlebar->setScriptEvents(true);
	$this->_windowFrame->addComponent($this->_titlebar);

	$this->_titlebar2 = new \ManiaLib\Gui\Elements\Quad($this->sizeX, 6);
	$this->_titlebar2->setStyle("BgRaceScore2");
	$this->_titlebar2->setSubStyle("CartoucheLine");
	$this->_titlebar2->setAlign("center", "top");
	// $this->_windowFrame->addComponent($this->_titlebar2);

	$this->_bg = new \ManiaLib\Gui\Elements\Quad($this->sizeX, $this->sizeY);
	$this->_bg->setStyle("Bgs1");
	$this->_bg->setSubStyle("BgColorContour");
	$this->_bg->setColorize("3af");
	$this->_bg->setAlign("left", "top");
	$this->_windowFrame->addComponent($this->_bg);

	$this->_title = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel(60, 4);
	$this->_title->setId("TitlebarText");
	$this->_title->setStyle("TextRaceMessageBig");
	$this->_title->setTextColor('fff');
	$this->_title->setTextSize(1);
	$this->_title->setTextEmboss();

	$this->_windowFrame->addComponent($this->_title);

	$this->_title2 = new \ManiaLib\Gui\Elements\Label(60, 4);
	$this->_title2->setId("TitlebarText");
	//$this->_title2->setStyle("TextRankingsBig");
	$this->_title2->setTextColor('fffd');
	$this->_title2->setTextSize(2);
	//$this->_windowFrame->addComponent($this->_title2);

	$this->_closebutton = new \ManiaLib\Gui\Elements\Quad(4, 4);
	$this->_closebutton->setId("Close");
	$this->_closebutton->setAlign('center', 'center2');
	$this->_closebutton->setStyle("Icons128x32_1");
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
	//$this->_closebutton->setAction($this->_closeAction);
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
	$this->mainFrame->setPosY(-7);
	$this->_windowFrame->addComponent($this->mainFrame);

	$this->addComponent($this->_windowFrame);
	$this->xml = new \ManiaLive\Gui\Elements\Xml();
    }

    function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
	$titleBarPos = 3.5;
	$titlePos = 2.5;
	$this->_windowFrame->setSize($this->sizeX, $this->sizeY);

	$this->_mainWindow->setSize($this->sizeX, $this->sizeY + 2);
	$this->_mainWindow->setPosY(0);

	$this->_bg->setPosY(4);
	$this->_bg->setSize($this->sizeX, $this->sizeY + 6);
	$this->_bg->setOpacity(1);


	$this->_title->setSize($this->sizeX, 8);
	$this->_title->setPosition(7, $titlePos);
	$this->_title->setHalign("left");

	$this->_title2->setSize($this->sizeX, 4);
	$this->_title2->setPosition(($this->_title->sizeX / 2), 3.25);
	$this->_title2->setHalign("left");

	$this->_titlebar->setSize($this->sizeX, 4);
	$this->_titlebar->setPosition(0, $titleBarPos);
	$this->_titlebar->setOpacity(1);

	$this->_titlebar2->setSize($this->sizeX / 2, 4.5);
	$this->_titlebar2->setPosition(0, $titleBarPos - 3);

	$this->_closebutton->setSize(4, 4);
	$this->_closebutton->setPosition($this->sizeX - 3, $titlePos - 1.2);

	$this->_minbutton->setSize(5, 5);
	$this->_minbutton->setPosition($this->sizeX - 8, 6);

	$this->mainFrame->setSize($this->sizeX - 4, $this->sizeY - 8);
	$this->mainFrame->setPosition(2, -4);
    }

    private $calledScripts = array();

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

		$isset = !isset($this->calledScripts[$script->getRelPath()]);

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
	    // echo "adding script...";
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
	$this->script->setParam("disableAnimations", Config::getInstance()->disableAnimations ? "True" : "False" );
	$this->script->setParam("version", \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION);
	$reset = "False";
	if (DEBUG)
	    $reset = "True";

	$this->script->setParam("forceReset", $reset);


	$this->removeComponent($this->xml);
	$this->xml->setContent($this->script->getDeclarationScript($this, $this->xml));

	$this->addComponent($this->xml);
	
	$dico = new \ManiaLivePlugins\eXpansion\Gui\Elements\Dico($this->dicoMessages);
	\ManiaLive\Gui\Manialinks::appendXML($dico->getXml());
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

    /**
     * Registers a script to widget instance
     * @param \ManiaLivePlugins\eXpansion\Gui\Structures\Script $script
     */
    public function registerScript(\ManiaLivePlugins\eXpansion\Gui\Structures\Script $script) {
	$this->_scripts[] = $script;
    }

}

?>
